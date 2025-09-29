<script setup lang="ts">
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";
import { ref, onMounted, watch } from "vue";
import { Field, ErrorMessage, useForm } from "vee-validate";
import Select2 from "@/components/Select2.vue";

// Props & Emits
const props = defineProps<{ selected: number | string | null }>();
const emit = defineEmits(["close", "refresh"]);

// State
const konserOptions = ref([]);
const hargaTiketDisplay = ref("Rp 0");
const showErrors = ref(false); // ✅ Tambah flag untuk kontrol error

// Yup Schema
const formSchema = Yup.object({
    konser_id: Yup.string().required("Konser wajib dipilih"),
    jenis_tiket: Yup.string().required("Jenis tiket wajib diisi"),
    harga_tiket: Yup.number()
        .transform((value, originalValue) => {
            if (typeof originalValue === "number") return originalValue;
            if (typeof originalValue === "string") {
                const digits = originalValue.replace(/\D/g, "");
                return digits ? Number(digits) : undefined;
            }
            return undefined;
        })
        .required("Harga tiket wajib diisi")
        .min(1, "Harga tiket harus lebih dari 0"),
    stok_tiket: Yup.number()
        .required("Stok tiket wajib diisi")
        .min(1, "Stok tiket harus lebih dari 0"),
});

// VeeValidate Form Setup
const { values, setValues, setErrors, handleSubmit, resetForm, setFieldValue, errors } = useForm({
    validationSchema: formSchema,
    initialValues: {
        konser_id: "",
        jenis_tiket: "",
        harga_tiket: undefined,
        stok_tiket: undefined,
    },
    validateOnMount: false,
});

// Format Rupiah
function formatRupiah(value: number): string {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value || 0);
}

// Handle input harga
const onHargaInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const raw = target.value.replace(/\D/g, "");
    const numValue = raw ? Number(raw) : 0;
    
    hargaTiketDisplay.value = formatRupiah(numValue);
    setFieldValue("harga_tiket", numValue);
};

// Load konser dropdown
const loadKonserOptions = async () => {
    try {
        const { data } = await axios.get("/konser");
        konserOptions.value = (data.data || []).map((k: any) => ({
            id: k.id.toString(),
            text: k.nama_konser,
        }));
    } catch {
        toast.error("Gagal memuat konser");
    }
};

// Load data untuk edit
const getEdit = async () => {
    if (!props.selected) return;
    block(document.getElementById("form-tiket"));
    try {
        const { data } = await axios.get(`/tickets/${props.selected}`);
        const tiket = data.tiket;

        const harga = typeof tiket.harga_tiket === "number" 
            ? tiket.harga_tiket 
            : Number(tiket.harga_tiket.toString().replace(/\D/g, ""));

        setValues({
            konser_id: tiket.konser_id?.toString() || "",
            jenis_tiket: tiket.jenis_tiket || "",
            harga_tiket: harga,
            stok_tiket: Number(tiket.stok_tiket) || 0,
        });

        hargaTiketDisplay.value = formatRupiah(harga);
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Gagal mengambil data");
    } finally {
        unblock(document.getElementById("form-tiket"));
    }
};

// Watch selected
watch(
    () => props.selected,
    async (newVal) => {
        showErrors.value = false; // ✅ Reset error saat buka form
        if (newVal) {
            await loadKonserOptions();
            await getEdit();
        } else {
            resetForm();
            hargaTiketDisplay.value = "Rp 0";
        }
    },
    { immediate: true }
);

// Submit
const submit = handleSubmit(
    async (values) => {
        const payload = {
            konser_id: parseInt(values.konser_id),
            jenis_tiket: values.jenis_tiket.trim(),
            harga_tiket: Number(values.harga_tiket),
            stok_tiket: Number(values.stok_tiket),
        };

        block(document.getElementById("form-tiket"));
        try {
            if (props.selected) {
                await axios.put(`/tickets/${props.selected}`, payload);
            } else {
                await axios.post("/tickets", payload);
            }

            toast.success("Tiket berhasil disimpan");
            emit("refresh");
            emit("close");
            resetForm();
            hargaTiketDisplay.value = "Rp 0";
            showErrors.value = false; // ✅ Reset setelah sukses
        } catch (err: any) {
            if (err.response?.data?.errors) {
                setErrors(err.response.data.errors);
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
    },
    () => {
        showErrors.value = true; // ✅ Tampilkan error jika validasi gagal
    }
);

onMounted(loadKonserOptions);
</script>

<template>
    <form @submit.prevent="submit" id="form-tiket" class="form card mb-10">
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
                <Select2
                    :model-value="values.konser_id"
                    @update:model-value="(val) => setFieldValue('konser_id', val)"
                    :options="konserOptions"
                    placeholder="-- Pilih Konser --"
                    :settings="{ width: '100%' }"
                />
                <span v-if="showErrors && errors.konser_id" class="text-danger ps-2 text-sm">
                    {{ errors.konser_id }}
                </span>
            </div>

            <!-- Jenis Tiket -->
            <div class="col-md-6 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Jenis Tiket</label>
                <Field
                    name="jenis_tiket"
                    v-model="values.jenis_tiket"
                    type="text"
                    class="form-control form-control-lg form-control-solid"
                    placeholder="Masukkan jenis tiket"
                />
                <span v-if="showErrors && errors.jenis_tiket" class="text-danger ps-2 text-sm">
                    {{ errors.jenis_tiket }}
                </span>
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
                <span v-if="showErrors && errors.harga_tiket" class="text-danger ps-2 text-sm">
                    {{ errors.harga_tiket }}
                </span>
            </div>

            <!-- Stok Tiket -->
            <div class="col-md-6 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Stok Tiket</label>
                <Field
                    name="stok_tiket"
                    v-model.number="values.stok_tiket"
                    type="number"
                    class="form-control form-control-lg form-control-solid"
                    placeholder="Masukkan stok tiket"
                    min="1"
                />
                <span v-if="showErrors && errors.stok_tiket" class="text-danger ps-2 text-sm">
                    {{ errors.stok_tiket }}
                </span>
            </div>
        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-sm btn-primary ms-auto">
                Simpan
            </button>
        </div>
    </form>
</template>