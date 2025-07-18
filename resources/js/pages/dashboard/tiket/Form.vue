<script setup lang="ts">
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";
import { ref, onMounted, watch } from "vue";
import { Field, ErrorMessage, useForm, Form as VForm } from "vee-validate";

// Props & Emits
const props = defineProps({
  selected: {
    type: [String, Number],
    default: null,
  },
});
const emit = defineEmits(["close", "refresh"]);

// Options
const konserOptions = ref<{ id: number; nama_konser: string }[]>([]);

// Display value
const hargaTiketDisplay = ref("");

// Yup Schema
const formSchema = Yup.object({
  konser_id: Yup.string().required("Konser wajib dipilih"),
  jenis_tiket: Yup.string().required("Jenis tiket wajib diisi"),
  harga_tiket: Yup.number()
    .typeError("Harga harus berupa angka")
    .required("Harga tiket wajib diisi"),
  stok_tiket: Yup.number()
    .typeError("Stok harus berupa angka")
    .required("Stok tiket wajib diisi"),
});

// Form
const {
  values,
  setValues,
  setErrors,
  handleSubmit,
  resetForm,
} = useForm({
  validationSchema: formSchema,
  initialValues: {
    konser_id: "",
    jenis_tiket: "",
    harga_tiket: "",
    stok_tiket: "",
  },
});

// Format ke Rupiah
function formatRupiah(angka: string | number): string {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(Number(angka || 0));
}

// Handle tampilan harga saat berubah
watch(
  () => values.harga_tiket,
  (val) => {
    if (val === null || val === "") {
      hargaTiketDisplay.value = "";
    } else {
      hargaTiketDisplay.value = formatRupiah(val.toString());
    }
  },
  { immediate: true }
);

// Input harga format rupiah
const onHargaInput = (e: Event) => {
  const target = e.target as HTMLInputElement;
  const raw = target.value.replace(/\D/g, "");
  hargaTiketDisplay.value = formatRupiah(raw);
  values.harga_tiket = raw ? Number(raw) : 0;
};

// Load konser untuk select
async function loadKonserOptions() {
  try {
    const { data } = await axios.get("/konser");
    konserOptions.value = data.data || [];
  } catch {
    toast.error("Gagal memuat daftar konser");
  }
}

// Clean string format
function cleanCurrency(value: string | number): number {
  if (!value) return 0;
  return parseInt(value.toString().replace(/\D/g, ""), 10);
}

// Ambil data edit
const getEdit = async () => {
  if (!props.selected) return;
  const formEl = document.getElementById("form-tiket");
  if (formEl) block(formEl);

  try {
    const { data } = await axios.get(`/tickets/${props.selected}`);
    const tiket = data.tiket;

    setValues({
      konser_id: tiket.konser_id?.toString() || "",
      jenis_tiket: tiket.jenis_tiket || "",
      harga_tiket: cleanCurrency(tiket.harga_tiket),
      stok_tiket: tiket.stok_tiket || "",
    });
  } catch (err) {
    toast.error(err.response?.data?.message || "Gagal mengambil data tiket.");
  } finally {
    if (formEl) unblock(formEl);
  }
};

watch(() => props.selected, getEdit, { immediate: true });

// Submit
async function submit(values: any) {
  const formData = new FormData();
  formData.append("konser_id", values.konser_id);
  formData.append("jenis_tiket", values.jenis_tiket);
  formData.append("harga_tiket", values.harga_tiket.toString());
  formData.append("stok_tiket", values.stok_tiket.toString());

  block(document.getElementById("form-tiket"));
  try {
    await axios.post(
      props.selected
        ? `/tickets/${props.selected}?_method=PUT`
        : "/tickets",
      formData
    );

    toast.success("Tiket berhasil disimpan");
    emit("refresh");
    emit("close");
    resetForm();
  } catch (err: any) {
    setErrors(err.response?.data?.errors || {});
    toast.error(err.response?.data?.message || "Gagal menyimpan tiket");
  } finally {
    unblock(document.getElementById("form-tiket"));
  }
}

onMounted(() => {
  loadKonserOptions();
});
</script>

<template>
  <VForm :validation-schema="formSchema" v-slot="{ handleSubmit }">
    <form
      @submit.prevent="handleSubmit(submit)"
      id="form-tiket"
      class="form card mb-10"
    >
      <div class="card-header d-flex align-items-center">
        <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Tiket</h2>
        <button
          class="btn btn-sm btn-light-danger ms-auto"
          type="button"
          @click="emit('close')"
        >
          Batal <i class="la la-times-circle p-0"></i>
        </button>
      </div>

      <div class="row px-4 pt-4">
        <!-- Konser -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Konser</label>
          <Field
            name="konser_id"
            v-model="values.konser_id"
            as="select"
            class="form-select form-select-lg form-control-solid"
          >
            <option disabled value="">-- Pilih Konser --</option>
            <option
              v-for="konser in konserOptions"
              :key="konser.id"
              :value="konser.id.toString()"
            >
              {{ konser.nama_konser }}
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
          <input
            :value="hargaTiketDisplay"
            @input="onHargaInput"
            type="text"
            class="form-control form-control-lg form-control-solid"
            placeholder="Masukkan harga tiket"
            autocomplete="off"
          />
          <ErrorMessage name="harga_tiket" class="text-danger ps-2 text-sm" />
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
          />
          <ErrorMessage name="stok_tiket" class="text-danger ps-2 text-sm" />
        </div>
      </div>

      <div class="card-footer d-flex">
        <button class="btn btn-sm btn-primary ms-auto" type="submit">
          Simpan
        </button>
      </div>
    </form>
  </VForm>
</template>
