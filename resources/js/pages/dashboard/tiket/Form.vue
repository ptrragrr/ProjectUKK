<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Tiket } from "@/types";
import { Field, Form as VForm, ErrorMessage, useForm } from "vee-validate";

// Props dan Emit
const props = defineProps({
  selected: {
    type: [String, Number],
    default: null,
  },
});
const emit = defineEmits(["close", "refresh"]);
const formRef = ref();

// Yup Schema
const formSchema = Yup.object({
  nama_destinasi: Yup.string().required("Nama destinasi wajib diisi"),
  harga_tiket: Yup.string().required("Harga wajib diisi"),
  stok_tiket: Yup.number().typeError("Stok harus angka").required("Stok wajib diisi"),
});

// VeeValidate
const { setValues, setFieldValue, handleSubmit, resetForm, setErrors } = useForm({
  validationSchema: formSchema,
  initialValues: {
    nama_destinasi: "",
    harga_tiket: "",
    stok_tiket: "",
  },
});

// Format saat mengetik
function formatInputRupiah(event: Event, field: any) {
  const input = event.target as HTMLInputElement;
  const raw = input.value.replace(/[^\d]/g, "");
  const formatted = new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(parseInt(raw || "0"));

  input.value = formatted;
  field.value = formatted; // sync ke VeeValidate
  setFieldValue("harga_tiket", formatted); // just in case
}

// Hapus format sebelum kirim
function cleanCurrency(value: string): number {
  return parseInt(value.replace(/[^\d]/g, "")) || 0;
}

// Load data untuk edit
function getEdit() {
  block(document.getElementById("form-tiket"));
  axios.get(`/tickets/${props.selected}`)
    .then(({ data }) => {
      if (!data?.ticket) return toast.error("Tiket tidak ditemukan");

      setValues({
        nama_destinasi: data.ticket.nama_destinasi,
        harga_tiket: formatHarga(data.ticket.harga_tiket),
        stok_tiket: data.ticket.stok_tiket,
      });
    })
    .catch((err) => {
      toast.error(err.response?.data?.message || "Gagal ambil data tiket");
    })
    .finally(() => unblock(document.getElementById("form-tiket")));
}

// Format dari angka ke Rupiah
function formatHarga(harga: number | string) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(Number(harga));
}

// Submit form
function submit(values: any) {
  const formData = new FormData();
  formData.append("nama_destinasi", values.nama_destinasi || "");
  formData.append("harga_tiket", cleanCurrency(values.harga_tiket).toString());
  formData.append("stok_tiket", values.stok_tiket.toString());

  block(document.getElementById("form-tiket"));
  axios({
    method: "post",
    url: props.selected
      ? `/tickets/${props.selected}?_method=PUT`
      : "/tickets/store",
    data: formData,
    headers: { "Content-Type": "multipart/form-data" },
  })
    .then(() => {
      toast.success("Tiket berhasil disimpan");
      emit("refresh");
      emit("close");
      resetForm();
    })
    .catch((err) => {
      setErrors(err.response?.data?.errors || {});
      toast.error(err.response?.data?.message || "Gagal simpan tiket");
    })
    .finally(() => unblock(document.getElementById("form-tiket")));
}

onMounted(() => {
  if (props.selected) getEdit();
});
</script>


<template>
  <VForm :validation-schema="formSchema" v-slot="{ handleSubmit }">
    <form @submit.prevent="handleSubmit(submit)" id="form-tiket" class="form card mb-10">
      <div class="card-header align-items-center">
        <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Tiket</h2>
        <button
          type="button"
          class="btn btn-sm btn-light-danger ms-auto"
          @click="emit('close')"
        >
          Batal <i class="la la-times-circle p-0"></i>
        </button>
      </div>

      <div class="row">
        <!-- Nama Destinasi -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Nama Destinasi</label>
          <Field
            name="nama_destinasi"
            as="input"
            type="text"
            class="form-control form-control-lg form-control-solid px-4"
            placeholder="Masukkan nama destinasi"
          />
          <ErrorMessage name="nama_destinasi" />
        </div>

        <!-- Harga Tiket dengan v-slot -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Harga Tiket</label>
          <Field name="harga_tiket" v-slot="{ field }">
            <input
              v-bind="field"
              type="text"
              class="form-control form-control-lg form-control-solid px-4"
              placeholder="Masukkan harga"
              @input="e => formatInputRupiah(e, field)"
            />
          </Field>
          <ErrorMessage name="harga_tiket" />
        </div>

        <!-- Stok Tiket -->
        <div class="col-md-6 mb-7">
          <label class="form-label fw-bold fs-6 required">Stok Tiket</label>
          <Field
            name="stok_tiket"
            as="input"
            type="number"
            class="form-control form-control-lg form-control-solid px-4"
            placeholder="Masukkan stok tiket"
          />
          <ErrorMessage name="stok_tiket" />
        </div>
      </div>

      <div class="card-footer d-flex">
        <button type="submit" class="btn btn-primary btn-sm ms-auto">
          Simpan
        </button>
      </div>
    </form>
  </VForm>
</template>

