<script setup lang="ts">
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";
import { ref, onMounted, watch } from "vue";
import { Field, ErrorMessage, useForm, Form as VForm } from "vee-validate";

// Props & Emits
const props = defineProps<{ selected: number | string | null }>();
const emit = defineEmits(["close", "refresh"]);

// State
const konserOptions = ref([]);
const hargaTiketDisplay = ref("");

// Yup Schema
const formSchema = Yup.object({
  konser_id: Yup.string().required("Konser wajib dipilih"),
  jenis_tiket: Yup.string().required("Jenis tiket wajib diisi"),
  harga_tiket: Yup.number()
    .transform((value, originalValue) =>
      Number(String(originalValue).replace(/\D/g, "")) || 0
    )
    .typeError("Harga harus berupa angka")
    .required("Harga tiket wajib diisi"),
  stok_tiket: Yup.number()
    .typeError("Stok harus berupa angka")
    .required("Stok tiket wajib diisi"),
});
// const formSchema = Yup.object({
//   konser_id: Yup.string().required("Konser wajib dipilih"),
//   jenis_tiket: Yup.string().required("Jenis tiket wajib diisi"),
//   harga_tiket: Yup.number()
//     .typeError("Harga harus berupa angka")
//     .required("Harga tiket wajib diisi"),
//   stok_tiket: Yup.number()
//     .typeError("Stok harus berupa angka")
//     .required("Stok tiket wajib diisi"),
// });

// VeeValidate
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

// Format Rupiah
function formatRupiah(value: string | number): string {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(Number(value || 0));
}

// Clean rupiah format
function cleanCurrency(value: string | number): number {
  return parseInt(value.toString().replace(/\D/g, ""), 10) || 0;
}

// Watch harga_tiket → update tampilan
watch(
  () => values.harga_tiket,
  (val) => {
    hargaTiketDisplay.value = formatRupiah(val);
  },
  { immediate: true }
);

// Harga input
const onHargaInput = (e: Event, handleChange: any) => {
  const target = e.target as HTMLInputElement;
  const raw = target.value.replace(/\D/g, "");
  hargaTiketDisplay.value = formatRupiah(raw);
  handleChange(raw ? Number(raw) : 0);
};

// Load konser
const loadKonserOptions = async () => {
  try {
    const { data } = await axios.get("/konser");
    konserOptions.value = data.data || [];
  } catch {
    toast.error("Gagal memuat konser");
  }
};

// Ambil data edit
const getEdit = async () => {
  if (!props.selected) return;
  block(document.getElementById("form-tiket"));
  try {
    const { data } = await axios.get(`/tickets/${props.selected}`);
    const tiket = data.tiket;

    const harga =
      typeof tiket.harga_tiket === "number"
        ? tiket.harga_tiket
        : parseInt(cleanCurrency(tiket.harga_tiket.toString()));

    setValues({
      konser_id: tiket.konser_id?.toString() || "",
      jenis_tiket: tiket.jenis_tiket || "",
      harga_tiket: harga,
      stok_tiket: tiket.stok_tiket || "",
    });

    hargaTiketDisplay.value = formatRupiah(harga);
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Gagal mengambil data");
  } finally {
    unblock(document.getElementById("form-tiket"));
  }
};

// Watch saat edit dibuka
watch(
  () => props.selected,
  async () => {
    if (props.selected) {
      await loadKonserOptions();
      await getEdit();
    } else {
      resetForm();
      hargaTiketDisplay.value = "";
    }
  },
  { immediate: true }
);

// Submit
const submit = async (values: any) => {
  const formData = new FormData();
  formData.append("konser_id", values.konser_id);
  formData.append("jenis_tiket", values.jenis_tiket);
  formData.append("harga_tiket", values.harga_tiket.toString());
  formData.append("stok_tiket", values.stok_tiket.toString());

  block(document.getElementById("form-tiket"));
  try {
    const url = props.selected
      ? `/tickets/${props.selected}?_method=PUT`
      : "/tickets";

    await axios.post(url, formData);

    toast.success("Tiket berhasil disimpan");
    emit("refresh");
    emit("close");
    resetForm();
    hargaTiketDisplay.value = "";
  } catch (err: any) {
    setErrors(err.response?.data?.errors || {});
    toast.error(err.response?.data?.message || "Gagal menyimpan tiket");
  } finally {
    unblock(document.getElementById("form-tiket"));
  }
};

onMounted(loadKonserOptions);
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
          type="button"
          class="btn btn-sm btn-light-danger ms-auto"
          @click="emit('close')"
        >
          Batal <i class="la la-times-circle p-0"></i>
        </button>
      </div>

      <div class="row px-4 pt-4">
        <!-- Konser -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required ps-2">Konser</label>
          <Field name="konser_id" v-model="values.konser_id" as="select" class="form-select">
            <option value="">-- Pilih Konser --</option>
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
          <Field name="harga_tiket" v-slot="{ handleChange }">
            <input
              :value="hargaTiketDisplay"
              @input="(e) => onHargaInput(e, handleChange)"
              type="text"
              class="form-control form-control-lg form-control-solid"
              placeholder="Masukkan harga tiket"
              autocomplete="off"
            />
          </Field>
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
        <button type="submit" class="btn btn-sm btn-primary ms-auto">
          Simpan
        </button>
      </div>
    </form>
  </VForm>
</template>
