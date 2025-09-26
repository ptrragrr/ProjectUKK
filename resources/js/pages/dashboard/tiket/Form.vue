<script setup lang="ts">
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";
import { ref, onMounted, watch } from "vue";
import { Field, ErrorMessage, useForm, Form as VForm, validate } from "vee-validate";
import Select2 from "@/components/Select2.vue";

// Props & Emits
const props = defineProps<{ selected: number | string | null }>();
const emit = defineEmits(["close", "refresh"]);

// State
const konserOptions = ref([]);
const hargaTiketDisplay = ref("");

// Yup Schema - DIPERBAIKI
const formSchema = Yup.object({
  konser_id: Yup.mixed().required("Konser wajib dipilih"),
  jenis_tiket: Yup.string().required("Jenis tiket wajib diisi"),
  harga_tiket: Yup.number()
    .transform((value, originalValue) => {
      console.log("Transform input:", { value, originalValue, type: typeof originalValue });
      
      // Jika sudah number dan valid, kembalikan langsung
      if (typeof originalValue == "number" && !isNaN(originalValue)) {
        return originalValue;
      }
      
      // Jika string, bersihkan dan konversi
      if (typeof originalValue == "string") {
        const digits = originalValue.replace(/\D/g, "");
        const result = digits == "" ? NaN : Number(digits);
        console.log("String transform result:", result);
        return result;
      }
      
      // Default return NaN untuk trigger error
      return NaN;
    })
    .typeError("Harga harus berupa angka")
    .required("Harga tiket wajib diisi")
    .min(1, "Harga tiket harus lebih dari 0"),
  stok_tiket: Yup.number()
    .typeError("Stok harus berupa angka")
    .required("Stok tiket wajib diisi")
    .min(1, "Stok tiket harus lebih dari 0"),
});

// VeeValidate Form Setup - DIPERBAIKI
const { values, setValues, setErrors, handleSubmit, resetForm, setFieldValue, validate } = useForm({
  validationSchema: formSchema,
  initialValues: {
    konser_id: "",
    jenis_tiket: "",
    harga_tiket: 0, // PERBAIKAN: Gunakan 0 sebagai default, bukan string kosong
    stok_tiket: 0,   // PERBAIKAN: Gunakan 0 sebagai default
  },
});

// Format & Clean Rupiah - DIPERBAIKI
function formatRupiah(value: string | number): string {
  const numValue = typeof value === 'string' ? parseFloat(value.replace(/\D/g, '')) || 0 : value || 0;
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(numValue);
}

function cleanCurrency(value: string | number): number {
  if (typeof value === 'number') return value;
  // PERBAIKAN: Gunakan parseFloat bukan parseInt untuk mendukung desimal
  return parseFloat(value.toString().replace(/\D/g, "")) || 0;
}

// Harga input - DIPERBAIKI
const onHargaInput = (e: Event, handleChange: any) => {
  const target = e.target as HTMLInputElement;
  const raw = target.value.replace(/\D/g, "");
  
  console.log("Input event:", { raw, isEmpty: raw === "" });
  
  // Update display
  hargaTiketDisplay.value = raw ? formatRupiah(raw) : "Rp 0";
  
  // PERBAIKAN: Kirim number ke form, 0 jika kosong
  const numValue = raw ? Number(raw) : 0;
  console.log("Sending to form:", numValue);
  handleChange(numValue);
};

// Watch harga_tiket → update display - DIPERBAIKI  
watch(
  () => values.harga_tiket,
  (val) => {
    console.log("Watch harga_tiket:", val, typeof val);
    if (val !== null && val !== undefined) {
      hargaTiketDisplay.value = formatRupiah(val);
    }
  },
  { immediate: true }
);

// Load konser dropdown
const loadKonserOptions = async () => {
  try {
    const { data } = await axios.get("/konser");
    konserOptions.value = (data.data || []).map((k: any) => ({
      id: k.id,
      text: k.nama_konser,
    }));
  } catch {
    toast.error("Gagal memuat konser");
  }
};

// Load data untuk edit - DIPERBAIKI
const getEdit = async () => {
  if (!props.selected) return;
  block(document.getElementById("form-tiket"));
  try {
    const { data } = await axios.get(`/tickets/${props.selected}`);
    const tiket = data.tiket;

    console.log("Data from API:", tiket);

    const harga = typeof tiket.harga_tiket === "number"
      ? tiket.harga_tiket
      : cleanCurrency(tiket.harga_tiket.toString());

    console.log("Processed harga:", harga);

    setValues({
      konser_id: tiket.konser_id?.toString() || "",
      jenis_tiket: tiket.jenis_tiket || "",
      harga_tiket: harga, // Sudah berupa number
      stok_tiket: Number(tiket.stok_tiket) || 0, // PERBAIKAN: Pastikan number
    });

    hargaTiketDisplay.value = formatRupiah(harga);

    // PERBAIKAN: Tunggu sebentar sebelum validasi
    setTimeout(async () => {
      await validate();
    }, 100);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Gagal mengambil data");
  } finally {
    unblock(document.getElementById("form-tiket"));
  }
};

// Watch selected (edit / reset)
watch(
  () => props.selected,
  async () => {
    if (props.selected) {
      await loadKonserOptions();
      await getEdit();
    } else {
      resetForm();
      hargaTiketDisplay.value = "Rp 0";
    }
  },
  { immediate: true }
);

// Submit - DIPERBAIKI
// Final submit function - Menggunakan JSON payload
const submit = async (values: any) => {
  console.log("Submit values:", values);

  // Validasi manual sebelum submit
  if (!values.konser_id) {
    toast.error("Konser wajib dipilih");
    return;
  }
  if (!values.jenis_tiket?.trim()) {
    toast.error("Jenis tiket wajib diisi");
    return;
  }
  if (!values.harga_tiket || values.harga_tiket <= 0) {
    toast.error("Harga tiket wajib diisi dan harus lebih dari 0");
    return;
  }
  if (!values.stok_tiket || values.stok_tiket <= 0) {
    toast.error("Stok tiket wajib diisi dan harus lebih dari 0");
    return;
  }

  // Payload JSON (lebih reliable daripada FormData)
  const payload = {
    konser_id: parseInt(values.konser_id),
    jenis_tiket: values.jenis_tiket.trim(),
    harga_tiket: Number(values.harga_tiket),
    stok_tiket: Number(values.stok_tiket),
  };

  console.log("Payload yang akan dikirim:", payload);

  block(document.getElementById("form-tiket"));
  try {
    let response;
    
    if (props.selected) {
      // UPDATE - gunakan PUT method langsung
      response = await axios.put(`/tickets/${props.selected}`, payload);
    } else {
      // CREATE - gunakan POST method
      response = await axios.post("/tickets", payload);
    }

    console.log("Response sukses:", response);
    toast.success("Tiket berhasil disimpan");
    emit("refresh");
    emit("close");
    resetForm();
    hargaTiketDisplay.value = "Rp 0";
  } catch (err: any) {
    console.error("Submit error:", err.response?.data);
    
    if (err.response?.data?.errors) {
      setErrors(err.response.data.errors);
      // Show first validation error
      const firstError = Object.values(err.response.data.errors)[0];
      if (Array.isArray(firstError) && firstError.length > 0) {
        toast.error(firstError[0]);
      }
    } else {
      toast.error(err.response?.data?.message || "Gagal menyimpan tiket");
    }
  } finally {
    unblock(document.getElementById("form-tiket"));
  }
};

onMounted(loadKonserOptions);
</script>

<template>
  <VForm :validation-schema="formSchema" v-slot="{ handleSubmit }">
    <form @submit.prevent="handleSubmit(submit)" id="form-tiket" class="form card mb-10">
      <div class="card-header d-flex align-items-center">
        <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Tiket</h2>
        <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
          Batal <i class="la la-times-circle p-0"></i>
        </button>
      </div>

      <div class="row px-4 pt-4">
        <!-- Konser -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Konser</label>
          <Field as="select" name="konser_id" v-model="values.konser_id" class="form-control form-control-lg form-control-solid">
            <option value="">-- Pilih Konser --</option>
            <option v-for="konser in konserOptions" :key="konser.id" :value="konser.id">
              {{ konser.text }}
            </option>
          </Field>
          <ErrorMessage name="konser_id" class="text-danger ps-2 text-sm" />
        </div>

        <!-- Jenis Tiket -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Jenis Tiket</label>
          <Field
            name="jenis_tiket"
            v-model="values.jenis_tiket"
            as="input"
            type="text"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan jenis tiket"
          />
          <ErrorMessage name="jenis_tiket" class="text-danger ps-2 text-sm" />
        </div>

        <!-- Harga Tiket -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Harga Tiket</label>
          <Field name="harga_tiket" v-slot="{ handleChange, field }">
            <input
              :value="hargaTiketDisplay"
              name="harga_tiket"
              @input="(e) => onHargaInput(e, handleChange)"
              type="text"
              class="form-control form-control-lg form-control-solid"
              placeholder="Masukkan harga tiket"
              autocomplete="off"
            />
          </Field>
          <ErrorMessage name="harga_tiket" class="text-danger ps-2 text-sm" />
          <!-- Debug info (hapus setelah selesai debugging) -->
          <small class="text-muted">Debug: {{ values.harga_tiket }} ({{ typeof values.harga_tiket }})</small>
        </div>

        <!-- Stok Tiket -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Stok Tiket</label>
          <Field
            name="stok_tiket"
            v-model="values.stok_tiket"
            as="input"
            type="number"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan stok tiket"
            min="1"
          />
          <ErrorMessage name="stok_tiket" class="text-danger ps-2 text-sm" />
        </div>
      </div>

      <div class="card-footer d-flex">
        <button type="submit" class="btn btn-sm btn-primary ms-auto">Simpan</button>
      </div>
    </form>
  </VForm>
</template>