<script setup lang="ts">
import { block, unblock } from "@/libs/utils";
import { onMounted, ref } from "vue";
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import type { Tiket } from "@/types";
import { Field, Form as VForm, ErrorMessage } from "vee-validate";
import { useForm } from "vee-validate";

const { setValues } = useForm();

const props = defineProps({
    selected: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close", "refresh"]);

const tiket = ref<Tiket>({
    nama_destinasi: "",
    harga_tiket: "",
    stok_tiket: "",
    foto_destinasi: "",
});

const formRef = ref();
const foto_destinasi = ref<File | null>(null);
const fileTypes = ["image/jpeg", "image/png", "image/jpg"];

// Yup Schema
const formSchema = Yup.object().shape({
    nama_destinasi: Yup.string().required("Nama destinasi wajib diisi"),
    harga_tiket: Yup.string()
        .required("Harga wajib diisi")
        .matches(/^\d+$/, "Harga harus berupa angka"),
    stok_tiket: Yup.number()
        .typeError("Stok harus angka")
        .required("Stok harus diisi"),
    foto_destinasi: Yup.mixed().nullable(),
});

// Ambil data untuk edit
function getEdit() {
    block(document.getElementById("form-tiket"));
    axios
        .get(`/tickets/${props.selected}`)
        .then(({ data }) => {
            if (!data || !data.tiket) {
                throw new Error("Data tiket tidak ditemukan");
            }

            const tiketData = {
                nama_destinasi: data.tiket.nama_destinasi,
                harga_tiket: new Intl.NumberFormat("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    minimumFractionDigits: 0,
                }).format(data.tiket.harga_tiket),
                stok_tiket: data.tiket.stok_tiket,
            };

            setValues(tiketData);
        })
        .catch((err) => {
            console.error(err);
            toast.error(err.response?.data?.message || "Gagal mengambil data tiket");
        })
        .finally(() => {
            unblock(document.getElementById("form-tiket"));
        });
}

// Format tampilan harga
function formatHargaDisplay(value: string | number): string {
    const angka = typeof value === "string" ? value.replace(/\D/g, "") : value;
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(parseInt(angka || "0"));
}

function cleanCurrency(value: string): number {
    return parseInt(value.replace(/[^\d]/g, "")) || 0;
}

// Submit form
function submit(values: any) {
    const formData = new FormData();

    formData.append("nama_destinasi", values.nama_destinasi || "");
    formData.append("harga_tiket", cleanCurrency(values.harga_tiket).toString());
    formData.append("stok_tiket", values.stok_tiket.toString());

    if (foto_destinasi.value) {
        formData.append("foto_destinasi", foto_destinasi.value);
    }

    block(document.getElementById("form-tiket"));

    axios({
        method: "post",
        url: props.selected
            ? `/tickets/${props.selected}?_method=PUT`
            : "/tickets",
        data: formData,
        headers: {
            "Content-Type": "multipart/form-data",
        },
    })
        .then(() => {
            emit("close");
            emit("refresh");
            toast.success("Tiket berhasil disimpan");
            formRef.value.resetForm();
        })
        .catch((err) => {
            formRef.value.setErrors(err.response?.data?.errors || {});
            toast.error(err.response?.data?.message || "Gagal menyimpan tiket");
        })
        .finally(() => {
            unblock(document.getElementById("form-tiket"));
        });
}

onMounted(() => {
    if (props.selected) {
        getEdit();
    }
});
</script>

<template>
    <VForm
        :validation-schema="formSchema"
        ref="formRef"
        id="form-tiket"
        v-slot="{ handleSubmit }"
    >
        <form @submit.prevent="handleSubmit(submit)" class="form card mb-10">
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
                        class="form-control form-control-lg form-control-solid"
                        placeholder="Masukkan nama destinasi"
                    />
                    <ErrorMessage name="nama_destinasi" />
                </div>

                <!-- Harga Tiket -->
                <div class="fv-row mb-7">
                    <label class="form-label fw-bold fs-6">Harga Tiket</label>
                    <Field
                        class="form-control form-control-lg form-control-solid"
                        type="text"
                        name="harga_tiket"
                        autocomplete="off"
                        placeholder="Masukkan harga"
                    />
                    <div class="fv-plugins-message-container">
                        <div class="fv-help-block">
                            <ErrorMessage name="harga_tiket" />
                        </div>
                    </div>
                </div>

                <!-- Stok Tiket -->
                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required">Stok Tiket</label>
                    <Field
                        name="stok_tiket"
                        as="input"
                        type="number"
                        class="form-control form-control-lg form-control-solid"
                        placeholder="Masukkan stok tiket"
                    />
                    <ErrorMessage name="stok_tiket" />
                </div>

                <!-- Foto -->
                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6">Foto Destinasi</label>
                    <file-upload
                        :files="foto_destinasi"
                        :accepted-file-types="fileTypes"
                        @updatefiles="(file) => { if (file) foto_destinasi.value = file; }"
                    />
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
