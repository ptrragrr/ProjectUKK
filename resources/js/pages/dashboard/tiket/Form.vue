<script setup lang="ts">
import { ref, onMounted } from "vue";
import * as Yup from "yup";
import { Field, Form as VForm, ErrorMessage, useForm } from "vee-validate";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";

const props = defineProps({
    selected: {
        type: [String, Number],
        default: null,
    },
});
const emit = defineEmits(["close", "refresh"]);
const konserOptions = ref<{ id: number; nama_konser: string }[]>([]);

// Schema validasi
const formSchema = Yup.object({
    konser_id: Yup.string().required("Konser wajib dipilih"),
    jenis_tiket: Yup.string().required("Jenis tiket wajib diisi"),
    harga_tiket: Yup.string().required("Harga tiket wajib diisi"),
    stok_tiket: Yup.number()
        .typeError("Stok harus berupa angka")
        .required("Stok tiket wajib diisi"),
});

// Form state
const { setValues, setFieldValue, handleSubmit, resetForm, setErrors } =
    useForm({
        validationSchema: formSchema,
        initialValues: {
            konser_id: "",
            jenis_tiket: "",
            harga_tiket: "",
            stok_tiket: "",
        },
    });

// Load dropdown konser
async function loadKonserOptions() {
    try {
        const { data } = await axios.get("/konser");
        konserOptions.value = data.data || [];
    } catch {
        toast.error("Gagal memuat daftar konser");
    }
}

// Format input harga ke Rupiah
function formatInputRupiah(event: Event, field: any) {
    const input = event.target as HTMLInputElement;
    const raw = input.value.replace(/[^\d]/g, "");
    const formatted = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(parseInt(raw || "0"));

    input.value = formatted;
    field.value = formatted;
    setFieldValue("harga_tiket", formatted);
}

function cleanCurrency(value: string): number {
    return parseInt(value.replace(/[^\d]/g, "")) || 0;
}

// Load data edit
async function getEdit() {
    block(document.getElementById("form-tiket"));
    try {
        const { data } = await axios.get(`/tickets/${props.selected}`);
        const ticket = data.ticket;

        setValues({
            konser_id: ticket.konser_id,
            jenis_tiket: ticket.jenis_tiket,
            harga_tiket: formatInput(ticket.harga_tiket),
            stok_tiket: ticket.stok_tiket,
        });
    } catch {
        toast.error("Gagal mengambil data tiket");
    } finally {
        unblock(document.getElementById("form-tiket"));
    }
}

function formatInput(value: number | string): string {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(Number(value));
}

// Submit form
async function submit(values: any) {
    const formData = new FormData();
    formData.append("konser_id", values.konser_id);
    formData.append("jenis_tiket", values.jenis_tiket);
    formData.append(
        "harga_tiket",
        cleanCurrency(values.harga_tiket).toString()
    );
    formData.append("stok_tiket", values.stok_tiket.toString());
    // const formData = new FormData();
    // formData.append("konser_id", values.konser_id);
    // formData.append("jenis_tiket", values.jenis_tiket);
    // formData.append("harga_tiket", cleanCurrency(values.harga_tiket).toString());
    // formData.append("stok_tiket", values.stok_tiket);

    block(document.getElementById("form-tiket"));
    try {
        await axios.post(
            props.selected
                ? `/tickets/${props.selected}?_method=PUT`
                : "/tickets",
            formData,
            { headers: { "Content-Type": "multipart/form-data" } }
        );
        // await axios.post(
        //     props.selected
        //         ? `/tickets/${props.selected}?_method=PUT`
        //         : "/tickets",
        //     { headers: { "Content-Type": "multipart/form-data" } }
        // );
        toast.success("Tiket berhasil disimpan");
        emit("refresh");
        emit("close");
        resetForm();
    } catch (err) {
        setErrors(err.response?.data?.errors || {});
        toast.error(err.response?.data?.message || "Gagal menyimpan tiket");
    } finally {
        unblock(document.getElementById("form-tiket"));
    }
}

onMounted(() => {
    loadKonserOptions();
    if (props.selected) getEdit();
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
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Konser</label
                    >
                    <Field
                        name="konser_id"
                        as="select"
                        class="form-select form-select-lg form-control-solid"
                    >
                        <option disabled value="">-- Pilih Konser --</option>
                        <option
                            v-for="konser in konserOptions"
                            :key="konser.id"
                            :value="konser.id"
                        >
                            {{ konser.nama_konser }}
                        </option>
                    </Field>
                    <ErrorMessage
                        name="konser_id"
                        class="text-danger ps-2 text-sm"
                    />
                </div>

                <!-- Jenis Tiket -->
                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Jenis Tiket</label
                    >
                    <Field
                        name="jenis_tiket"
                        as="input"
                        type="text"
                        class="form-control form-control-lg form-control-solid"
                        placeholder="Masukkan jenis tiket"
                    />
                    <ErrorMessage
                        name="jenis_tiket"
                        class="text-danger ps-2 text-sm"
                    />
                </div>

                <!-- Harga Tiket -->
                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Harga Tiket</label
                    >
                    <Field name="harga_tiket" v-slot="{ field }">
                        <input
                            v-bind="field"
                            type="text"
                            class="form-control form-control-lg form-control-solid"
                            placeholder="Masukkan harga tiket"
                            @input="(e) => formatInputRupiah(e, field)"
                        />
                    </Field>
                    <ErrorMessage
                        name="harga_tiket"
                        class="text-danger ps-2 text-sm"
                    />
                </div>

                <!-- Stok Tiket -->
                <div class="col-md-6 mb-7">
                    <label class="form-label fw-bold fs-6 required ps-2"
                        >Stok Tiket</label
                    >
                    <Field
                        name="stok_tiket"
                        as="input"
                        type="number"
                        class="form-control form-control-lg form-control-solid"
                        placeholder="Masukkan stok tiket"
                    />
                    <ErrorMessage
                        name="stok_tiket"
                        class="text-danger ps-2 text-sm"
                    />
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
