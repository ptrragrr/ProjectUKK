<script setup lang="ts">
import * as Yup from "yup";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";
import { block, unblock } from "@/libs/utils";
import { ref, watch, nextTick, onMounted } from "vue";
import { Field, useForm } from "vee-validate";

// Props & Emits
const props = defineProps<{ selected: number | string | null }>();
const emit = defineEmits(["close", "refresh"]);

// State
const hargaDisplay = ref("Rp 0");
const showErrors = ref(false);
const jenisTiketList = ref<{ id: number; jenis_tiket: string; harga: number }[]>([]);

// Yup Schema
const formSchema = Yup.object({
    nama_event: Yup.string().required("Nama event wajib diisi"),
    tanggal: Yup.date().required("Tanggal wajib diisi"),

    harga_tiket: Yup.number()
        .typeError("Harga tiket harus berupa angka")
        .required("Harga tiket wajib diisi")
        .min(1, "Harga tiket harus lebih dari 0"),

    jenis_tiket: Yup.string().required("Jenis tiket wajib diisi"),

    deskripsi: Yup.string().required("Deskripsi wajib diisi"),

    stok_tiket: Yup.number()
        .typeError("Stok tiket harus berupa angka")
        .required("Stok tiket wajib diisi")
        .min(0, "Stok tiket tidak boleh kurang dari 0"),
});

// VeeValidate
const {
    values,
    setValues,
    setErrors,
    handleSubmit,
    resetForm,
    setFieldValue,
    errors,
} = useForm({
    validationSchema: formSchema,
    initialValues: {
        nama_event: "",
        tanggal: "",
        harga_tiket: undefined,
        jenis_tiket: "",
        deskripsi: "",
        stok_tiket: undefined,
    },
});

// Format Rupiah
function formatRupiah(value: number): string {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value || 0);
}

// Prevent input non-angka dan hapus "Rp "
const preventRpDeletion = (e: KeyboardEvent) => {
    const target = e.target as HTMLInputElement;
    const cursorPos = target.selectionStart || 0;
    if (cursorPos <= 3 && (e.key === "Backspace" || e.key === "Delete")) {
        e.preventDefault();
    }
    const allowedKeys = ["Backspace", "Delete", "ArrowLeft", "ArrowRight", "Tab", "Home", "End"];
    if (!allowedKeys.includes(e.key) && !/^\d$/.test(e.key)) e.preventDefault();
};

// Handle input harga
const onHargaInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const raw = target.value.replace(/\D/g, "");
    const numValue = raw ? Number(raw) : 0;
    hargaDisplay.value = formatRupiah(numValue);
    setFieldValue("harga_tiket", numValue);
    nextTick(() => {
        target.selectionStart = target.selectionEnd = target.value.length;
    });
};

// Batasi input Jenis Tiket agar hanya huruf dan spasi
const onJenisTiketInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    target.value = target.value.replace(/[^A-Za-zÀ-ÿ\s]/g, "");
    setFieldValue("jenis_tiket", target.value);
};

// Fetch semua jenis tiket dari tabel jenis_tiket
// const fetchJenisTiket = async () => {
//     try {
//         const { data } = await axios.get("/api/jenis-tiket");
//         jenisTiketList.value = data; // pastikan backend return array { id, jenis_tiket, harga }
//     } catch (error) {
//         console.error(error);
//         toast.error("Gagal memuat daftar jenis tiket");
//     }
// };

const fetchJenisTiket = async () => {
    try {
        const { data } = await axios.get("/jenis-tiket/all");
        // Ambil hanya isi array-nya dari pagination
        jenisTiketList.value = data || [];
    } catch (error) {
        console.error(error);
        toast.error("Gagal memuat daftar jenis tiket");
    }
};


// Jika user pilih jenis tiket, otomatis isi harga
watch(
    () => values.jenis_tiket,
    (val) => {
        const selected = jenisTiketList.value.find((t) => t.jenis_tiket === val);
        if (selected) {
            setFieldValue("harga_tiket", selected.harga);
            hargaDisplay.value = formatRupiah(selected.harga);
        }
    }
);

// Ambil data untuk edit
const getEdit = async () => {
    if (!props.selected) return;
    block(document.getElementById("form-tiket"));
    try {
        const { data } = await axios.get(`/tickets/${props.selected}`);
        const t = data;
        const harga = Number(t.harga_tiket) || 0;

        setValues({
            nama_event: t.nama_event || "",
            tanggal: t.tanggal || "",
            harga_tiket: t.harga_tiket,
            jenis_tiket: t.jenis_tiket || "",
            deskripsi: t.deskripsi || "",
            stok_tiket: t.stok_tiket,
        });
        hargaDisplay.value = formatRupiah(harga);
    } catch (err: any) {
        toast.error(err.response?.data?.message || "Gagal mengambil data");
    } finally {
        unblock(document.getElementById("form-tiket"));
    }
};

// Watch selected ID (edit mode)
watch(
    () => props.selected,
    async (newVal) => {
        showErrors.value = false;
        if (newVal) {
            await getEdit();
        } else {
            resetForm();
            hargaDisplay.value = "Rp 0";
        }
    },
    { immediate: true }
);

// Fetch dropdown data saat mount
onMounted(() => {
    fetchJenisTiket();
});

// Submit Form
const submit = handleSubmit(
    async (values) => {
        const payload = {
            nama_event: values.nama_event.trim(),
            tanggal: values.tanggal,
            harga_tiket: Number(values.harga_tiket),
            jenis_tiket: values.jenis_tiket.trim(),
            deskripsi: values.deskripsi.trim(),
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
            hargaDisplay.value = "Rp 0";
            showErrors.value = false;
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
        showErrors.value = true;
    }
);
</script>

<template>
    <form @submit.prevent="submit" id="form-tiket" class="form card mb-10">
        <div class="card-header d-flex align-items-center">
            <h2 class="mb-0">{{ selected ? "Edit" : "Tambah" }} Tiket</h2>
            <button type="button" class="btn btn-sm btn-light-danger ms-auto" @click="emit('close')">
                Batal <i class="la la-times-circle p-0"></i>
            </button>
        </div>

        <div class="row px-4 pt-4">
            <!-- Nama Event -->
            <div class="col-md-6 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Nama Event</label>
                <Field name="nama_event" v-model="values.nama_event" type="text"
                    class="form-control form-control-lg form-control-solid" placeholder="Masukkan nama event" />
                <span v-if="showErrors && errors.nama_event" class="text-danger ps-2 text-sm">
                    {{ errors.nama_event }}
                </span>
            </div>

            <!-- Tanggal -->
            <div class="col-md-6 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Tanggal</label>
                <Field name="tanggal" v-model="values.tanggal" type="date"
                    class="form-control form-control-lg form-control-solid" />
                <span v-if="showErrors && errors.tanggal" class="text-danger ps-2 text-sm">
                    {{ errors.tanggal }}
                </span>
            </div>

            <!-- Jenis Tiket (Dropdown) -->
            <div class="col-md-6 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Jenis Tiket</label>
                <!-- <select v-model="values.jenis_tiket" class="form-select form-select-lg form-select-solid">
                    <option value="" disabled>Pilih Jenis Tiket</option>
                    <option v-for="t in jenisTiketList" :key="t.id" :value="t.jenis_tiket">
                        {{ t.jenis_tiket }}
                    </option>
                </select> -->
   <select
  :value="values.jenis_tiket"
  @change="(e) => setFieldValue('jenis_tiket', e.target.value)"
  class="form-select form-select-lg form-select-solid"
>
  <option value="" disabled>Pilih Jenis Tiket</option>
  <option v-for="t in jenisTiketList" :key="t.id" :value="t.jenis_tiket">
    {{ t.jenis_tiket }}
  </option>
</select>
                <span v-if="showErrors && errors.jenis_tiket" class="text-danger ps-2 text-sm">
                    {{ errors.jenis_tiket }}
                </span>
            </div>

            <!-- Harga Tiket (Otomatis) -->
            <div class="col-md-6 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Harga Tiket</label>
                <input :value="hargaDisplay" @input="onHargaInput" @keydown="preventRpDeletion" type="text"
                    class="form-control form-control-lg form-control-solid" placeholder="Masukkan harga" readonly />
                <span v-if="showErrors && errors.harga_tiket" class="text-danger ps-2 text-sm">
                    {{ errors.harga_tiket }}
                </span>
            </div>

            <!-- Stok Tiket -->
            <div class="col-md-6 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Stok Tiket</label>
                <Field name="stok_tiket" v-model="values.stok_tiket" type="number"
                    class="form-control form-control-lg form-control-solid"
                    placeholder="Masukkan jumlah stok tiket" />
                <span v-if="showErrors && errors.stok_tiket" class="text-danger ps-2 text-sm">
                    {{ errors.stok_tiket }}
                </span>
            </div>

            <!-- Deskripsi -->
            <div class="col-12 mb-7">
                <label class="form-label fw-bold fs-6 required ps-2">Lineup Artis</label>
                <Field name="deskripsi" v-model="values.deskripsi" as="textarea" rows="3"
                    class="form-control form-control-lg form-control-solid"
                    placeholder="Masukkan deskripsi atau lineup artis" />
                <span v-if="showErrors && errors.deskripsi" class="text-danger ps-2 text-sm">
                    {{ errors.deskripsi }}
                </span>
            </div>
        </div>

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-sm btn-primary ms-auto">Simpan</button>
        </div>
    </form>
</template>
