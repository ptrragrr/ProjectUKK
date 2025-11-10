<script setup lang="ts">
import { ref, onMounted, watch } from "vue";

interface Props {
    selected?: string;
}

const props = defineProps<Props>();
const emit = defineEmits(["close", "refresh"]);

const formData = ref({
    jenis_tiket: "",
    harga: "",
});

const isEdit = ref(false);
const processing = ref(false);

// Fungsi format ke Rupiah
const formatRupiah = (value: string | number) => {
    const numberString = value
        .toString()
        .replace(/[^,\d]/g, "")
        .replace(",", ".");
    const number = parseFloat(numberString);
    if (isNaN(number)) return "";
    return number.toLocaleString("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    });
};

// Fungsi untuk menghapus format rupiah (ambil angka asli)
const parseRupiah = (value: string) => {
    return value.replace(/[^0-9]/g, "");
};

// Ambil data untuk edit
// const fetchData = async () => {
//     if (!props.selected) return;

//     try {
//         // const response = await fetch(`/jenis-tiket/${props.selected}`);
//         const response = await fetch(`/api/jenis-tiket/${props.selected}`);
//         if (response.ok) {
//             const data = await response.json();
//             formData.value = {
//                 jenis_tiket: data.jenis_tiket || "",
//                 harga: formatRupiah(data.harga || 0),
//             };
//             isEdit.value = true;
//         }
//     } catch (error) {
//         console.error("Error fetching data:", error);
//     }
// };

const fetchData = async () => {
    if (!props.selected) return;

    try {
        const response = await fetch(`/api/jenis-tiket/${props.selected}`);

        if (!response.ok) {
            console.error("Gagal ambil data:", await response.text());
            return;
        }

        const data = await response.json();
        formData.value = {
            jenis_tiket: data.jenis_tiket || "",
            harga: formatRupiah(data.harga || 0),
        };
        isEdit.value = true;
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

// Submit form
const handleSubmit = async () => {
    processing.value = true;
    const url = isEdit.value
        ? `/api/jenis-tiket/${props.selected}`
        : `/api/jenis-tiket`;
    const method = isEdit.value ? "PUT" : "POST";

    const payload = {
        jenis_tiket: formData.value.jenis_tiket,
        harga: parseRupiah(formData.value.harga),
    };

    try {
        const response = await fetch(url, {
            method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
        });

        if (response.ok) {
            emit("refresh");
            emit("close");
            resetForm();
        }
    } catch (err) {
        console.error(err);
    } finally {
        processing.value = false;
    }
};

const resetForm = () => {
    formData.value = {
        jenis_tiket: "",
        harga: "",
    };
    isEdit.value = false;
};

// Watch prop selected
watch(
    () => props.selected,
    (val) => {
        if (val) {
            fetchData();
        } else {
            resetForm();
        }
    },
    { immediate: true }
);

onMounted(() => {
    if (props.selected) fetchData();
});
</script>

<template>
    <div class="card shadow-sm border-0 mb-6 overflow-hidden">
        <div class="card-header bg-gradient-primary border-0 py-4 px-6">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-45px bg-white bg-opacity-20 rounded">
                        <i class="la la-tag fs-2x text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white mb-0 fw-bold">
                            {{ isEdit ? "Edit" : "Tambah" }} Jenis Tiket
                        </h3>
                        <p class="text-white text-opacity-75 mb-0 fs-7">
                            Isi form di bawah untuk
                            {{ isEdit ? "mengubah" : "menambahkan" }} jenis tiket
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-6">
            <form @submit.prevent="handleSubmit" class="form">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold required">Jenis Tiket</label>
                            <input
                                v-model="formData.jenis_tiket"
                                type="text"
                                class="form-control"
                                placeholder="Contoh: VIP, Regular, Festival Pass"
                                required
                            />
                            <div class="form-text">
                                Masukkan nama atau kategori jenis tiket
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold required">Harga (Rp)</label>
                            <input
                                :value="formData.harga"
                                @input="formData.harga = formatRupiah($event.target.value)"
                                type="text"
                                class="form-control"
                                placeholder="Rp 0"
                                required
                            />
                            <div class="form-text">
                                Masukkan harga tiket dalam Rupiah
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-6 justify-content-end">
                    <button
                        type="button"
                        class="btn btn-light-secondary"
                        @click="emit('close')"
                        :disabled="processing"
                    >
                        <i class="la la-times me-2"></i>
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="processing"
                    >
                        <span v-if="!processing">
                            <i class="la la-save me-2"></i>
                            {{ isEdit ? "Update" : "Simpan" }}
                        </span>
                        <span v-else>
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.card {
    border-radius: 12px;
    animation: slideDown 0.3s ease-out;
}
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.form-label.required::after {
    content: " *";
    color: #dc3545;
}
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
.btn {
    border-radius: 8px;
    transition: all 0.3s ease;
}
.btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.btn-primary {
    background-color: #667eea;
    border: none;
}
.btn-primary:hover:not(:disabled) {
    background-color: #5568d3;
}
.btn-light-secondary {
    background-color: #f3f4f6;
    color: #6b7280;
    border: none;
}
.btn-light-secondary:hover:not(:disabled) {
    background-color: #e5e7eb;
}
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.symbol {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
