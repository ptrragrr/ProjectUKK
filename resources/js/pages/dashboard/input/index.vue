<script setup lang="ts">
import { ref } from "vue";
import axios from "@/libs/axios";

interface TransaksiDetail {
    id: number;
    kode_tiket: string;
    created_at: string;
    ticket: {
        id: number;
        jenis_tiket: string;
        harga_tiket: number;
        stok_tiket: number;
        status: string;
    };
    transaksi: { 
        id: number;
        nama_pembeli: string;
        email: string;
        nomer_telpon: string;
        kode_transaksi?: string;
        status_payment: string;
        total_harga: number;
        total_harga_rp?: string;
        created_at: string;
    };
}

const kodeTiket = ref("");
const transaksi = ref<TransaksiDetail | null>(null);
const loading = ref(false);
const errorMessage = ref("");

const cekTiket = async () => {
    if (!kodeTiket.value) {
        errorMessage.value = "Kode tiket harus diisi!";
        return;
    }

    loading.value = true;
    errorMessage.value = "";
    transaksi.value = null;

    try {
        const res = await axios.get(`/cek-tiket/${kodeTiket.value}`);
        transaksi.value = res.data.data;
    } catch (err: any) {
        errorMessage.value =
            err.response?.data?.message || "Tiket tidak ditemukan";
    } finally {
        loading.value = false;
    }
};

const cetakTiket = () => {
    if (!transaksi.value) return;
    window.open(`/cetak-tiket/${transaksi.value.kode_tiket}`, "_blank");
};

const batalCari = () => {
    kodeTiket.value = "";
    transaksi.value = null;
    errorMessage.value = "";
    loading.value = false;
};
</script>

<template>
    <div class="container-fluid px-4 py-6">
        <!-- Header Card dengan Gradient -->
        <div class="card shadow-sm border-0 overflow-hidden mb-6">
            <div class="card-header bg-gradient-primary border-0 py-6 px-6">
                <div
                    class="d-flex align-items-center justify-content-between w-100 gap-4 flex-wrap"
                >
                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="symbol symbol-50px bg-white bg-opacity-20 rounded"
                        >
                            <i class="la la-qrcode fs-2x text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-white mb-1 fw-bold fs-1">
                                Validasi Tiket
                            </h2>
                            <p class="text-white text-opacity-75 mb-0 fs-6">
                                Periksa status dan detail tiket Anda
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="row mb-6">
            <div class="col-lg-8 col-xl-6 mx-auto">
                <div
                    class="card shadow-sm border-0"
                    style="border-radius: 12px"
                >
                    <div
                        class="card-header border-0 py-4"
                        style="background-color: #e8f4f8"
                    >
                        <div class="d-flex align-items-center">
                            <i class="la la-search text-info fs-2 me-3"></i>
                            <h4 class="fw-bold text-info mb-0">
                                Masukkan Kode Tiket
                            </h4>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="mb-4">
                            <label
                                for="kodeTiket"
                                class="form-label fw-semibold text-gray-700 mb-3"
                            >
                                Kode Tiket
                            </label>
                            <div class="position-relative">
                                <i
                                    class="la la-ticket-alt position-absolute"
                                    style="
                                        left: 12px;
                                        top: 50%;
                                        transform: translateY(-50%);
                                        color: #a1a5b7;
                                        font-size: 1.25rem;
                                        z-index: 1;
                                    "
                                ></i>
                                <input
                                    id="kodeTiket"
                                    type="text"
                                    v-model="kodeTiket"
                                    class="form-control form-control-lg"
                                    placeholder="Contoh: TKT-116-N24W0Y"
                                    style="
                                        padding-left: 2.5rem;
                                        border-radius: 8px;
                                    "
                                    @keyup.enter="cekTiket"
                                />
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button
                                @click="cekTiket"
                                class="btn btn-primary btn-lg flex-grow-1 fw-bold"
                                style="border-radius: 8px"
                                :disabled="loading || !kodeTiket"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i v-else class="la la-search me-2"></i>
                                {{ loading ? "Mencari..." : "Cek Tiket" }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="row mb-6">
            <div class="col-lg-8 col-xl-6 mx-auto">
                <div
                    class="alert alert-danger d-flex align-items-center border-0"
                    style="border-radius: 8px"
                >
                    <i class="la la-exclamation-circle fs-2 me-3"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ errorMessage }}</div>
                    </div>
                    <button
                        @click="batalCari"
                        class="btn btn-sm btn-light-danger"
                    >
                        <i class="la la-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Ticket Details -->
        <div v-if="transaksi" class="row">
            <div class="col-12">
                <!-- Success Banner -->
                <div
                    class="alert alert-success d-flex align-items-center border-0 mb-6"
                    style="border-radius: 8px"
                >
                    <i class="la la-check-circle fs-2 me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-bold">Tiket Ditemukan!</h5>
                        <p class="mb-0">
                            Data tiket berhasil ditemukan dan terverifikasi
                        </p>
                    </div>
                    <button
                        @click="batalCari"
                        class="btn btn-sm btn-light-success"
                    >
                        <i class="la la-times"></i>
                    </button>
                </div>

                <!-- Ticket Card -->
                <div
                    class="card shadow-sm border-0"
                    style="border-radius: 12px"
                >
                    <!-- Card Header -->
                    <div class="card-header bg-gradient-primary border-0 py-5">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="symbol symbol-60px bg-white bg-opacity-20 rounded me-4"
                                    >
                                        <i
                                            class="la la-ticket-alt fs-2x text-white"
                                        ></i>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-white fw-bold fs-2 mb-1"
                                        >
                                            {{ transaksi.ticket.jenis_tiket }}
                                        </h3>
                                        <p
                                            class="text-white text-opacity-75 mb-0"
                                        >
                                            Kode: {{ transaksi.kode_tiket }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span
                                    :class="
                                        transaksi.transaksi.status_payment ===
                                        'paid'
                                            ? 'badge badge-light-success px-4 py-2'
                                            : 'badge badge-light-warning px-4 py-2'
                                    "
                                    style="font-size: 0.9rem"
                                >
                                    <i
                                        class="la la-check-circle me-1"
                                        v-if="
                                            transaksi.transaksi
                                                .status_payment === 'paid'
                                        "
                                    ></i>
                                    <i class="la la-clock me-1" v-else></i>
                                    {{
                                        transaksi.transaksi.status_payment ===
                                        "paid"
                                            ? "Paid"
                                            : "Menunggu Pembayaran"
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-6">
                        <div class="row g-6">
                            <!-- Customer Info -->
                            <div class="col-lg-6">
                                <div
                                    class="bg-light-info rounded p-5 h-100"
                                    style="border-radius: 8px"
                                >
                                    <div class="d-flex align-items-center mb-4">
                                        <i
                                            class="la la-user-circle text-info fs-2 me-3"
                                        ></i>
                                        <h5 class="mb-0 fw-bold text-info">
                                            Informasi Pembeli
                                        </h5>
                                    </div>

                                    <div class="mb-4">
                                        <label
                                            class="text-muted fs-7 fw-semibold text-uppercase mb-1"
                                            >Nama Lengkap</label
                                        >
                                        <div class="text-gray-900 fw-bold">
                                            {{
                                                transaksi.transaksi.nama_pembeli
                                            }}
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label
                                            class="text-muted fs-7 fw-semibold text-uppercase mb-1"
                                            >Email</label
                                        >
                                        <div
                                            class="text-gray-900 fw-bold d-flex align-items-center"
                                        >
                                            <i
                                                class="la la-envelope me-2 text-muted"
                                            ></i>
                                            {{ transaksi.transaksi.email }}
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label
                                            class="text-muted fs-7 fw-semibold text-uppercase mb-1"
                                            >No. Telepon</label
                                        >
                                        <div
                                            class="text-gray-900 fw-bold d-flex align-items-center"
                                        >
                                            <i
                                                class="la la-phone me-2 text-muted"
                                            ></i>
                                            {{
                                                transaksi.transaksi.nomer_telpon
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction Info -->
                            <div class="col-lg-6">
                                <div
                                    class="bg-light-success rounded p-5 h-100"
                                    style="border-radius: 8px"
                                >
                                    <div class="d-flex align-items-center mb-4">
                                        <i
                                            class="la la-money-bill-wave text-success fs-2 me-3"
                                        ></i>
                                        <h5 class="mb-0 fw-bold text-success">
                                            Detail Transaksi
                                        </h5>
                                    </div>

                                    <div class="mb-4">
                                        <label
                                            class="text-muted fs-7 fw-semibold text-uppercase mb-1"
                                            >Kode Transaksi</label
                                        >
                                        <div class="text-gray-900 fw-bold">
                                            {{
                                                transaksi.transaksi
                                                    .kode_transaksi
                                            }}
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label
                                            class="text-muted fs-7 fw-semibold text-uppercase mb-1"
                                            >Total Pembayaran</label
                                        >
                                        <div
                                            class="text-success fs-3 fw-bolder"
                                        >
                                            {{
                                                transaksi.transaksi
                                                    .total_harga_rp ||
                                                "Rp " +
                                                    transaksi.transaksi.total_harga.toLocaleString(
                                                        "id-ID"
                                                    )
                                            }}
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label
                                            class="text-muted fs-7 fw-semibold text-uppercase mb-1"
                                            >Status Tiket</label
                                        >
                                        <div>
                                            <span
                                                :class="{
                                                    'badge badge-light-success':
                                                        transaksi.status ===
                                                        'Available',
                                                    'badge badge-light-danger':
                                                        transaksi.status ===
                                                        'Expired',
                                                    // 'badge badge-light-warning': transaksi.status === 'Used'
                                                }"
                                                style="font-size: 0.9rem"
                                            >
                                                <i
                                                    v-if="
                                                        transaksi.status ===
                                                        'Available'
                                                    "
                                                    class="la la-check-circle me-1"
                                                ></i>
                                                <i
                                                    v-else-if="
                                                        transaksi.status ===
                                                        'Expired'
                                                    "
                                                    class="la la-times-circle me-1"
                                                ></i>
                                                <i
                                                    v-else
                                                    class="la la-exclamation-circle me-1"
                                                ></i>
                                                {{ transaksi.status }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <label
                                            class="text-muted fs-7 fw-semibold text-uppercase mb-1"
                                            >Tanggal Pembelian</label
                                        >
                                        <div
                                            class="text-gray-900 fw-bold d-flex align-items-center"
                                        >
                                            <i
                                                class="la la-calendar me-2 text-muted"
                                            ></i>
                                            {{
                                                new Date(
                                                    transaksi.created_at
                                                ).toLocaleDateString("id-ID", {
                                                    year: "numeric",
                                                    month: "long",
                                                    day: "numeric",
                                                })
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer border-0 bg-light py-4 px-6">
                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3"
                        >
                            <div class="text-muted">
                                <i class="la la-info-circle me-2"></i>
                                Tiket valid dan dapat digunakan
                            </div>
                            <div class="d-flex gap-3">
                                <button
                                    @click="batalCari"
                                    class="btn btn-light-secondary fw-bold"
                                    style="border-radius: 8px"
                                >
                                    <i class="la la-arrow-left me-2"></i>
                                    Kembali
                                </button>
                                <button
                                    @click="cetakTiket"
                                    class="btn btn-success fw-bold"
                                    style="border-radius: 8px"
                                >
                                    <i class="la la-print me-2"></i>
                                    Cetak Tiket
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions Card -->
        <div v-if="!transaksi && !loading && !errorMessage" class="row">
            <div class="col-lg-8 col-xl-6 mx-auto">
                <div
                    class="card border border-primary bg-light-primary"
                    style="border-radius: 12px; border-style: dashed !important"
                >
                    <div class="card-body p-6 text-center">
                        <i
                            class="la la-info-circle text-primary fs-2x mb-4"
                        ></i>
                        <h5 class="text-primary fw-bold mb-3">
                            Cara Menggunakan
                        </h5>
                        <div class="text-gray-700">
                            <p class="mb-2">
                                1. Masukkan kode tiket Anda pada kolom di atas
                            </p>
                            <p class="mb-2">
                                2. Klik tombol "Cek Tiket" untuk memvalidasi
                            </p>
                            <p class="mb-0">
                                3. Jika valid, Anda dapat mencetak tiket sebagai
                                bukti
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    transition: all 0.3s ease;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert {
    border-radius: 8px;
}

.badge {
    font-weight: 600;
    border-radius: 6px;
}

.badge-light-success {
    background-color: #d4f4dd;
    color: #0a7b2e;
}

.badge-light-warning {
    background-color: #fff3cd;
    color: #856404;
}

.btn-light-secondary {
    background-color: #f3f4f6;
    color: #6b7280;
    border: none;
}

.btn-light-secondary:hover {
    background-color: #e5e7eb;
    color: #374151;
}

.btn-light-danger {
    background-color: #fef2f2;
    color: #dc2626;
    border: none;
}

.btn-light-danger:hover {
    background-color: #fecaca;
    color: #dc2626;
}

.btn-light-success {
    background-color: #d4f4dd;
    color: #0a7b2e;
    border: none;
}

.btn-light-success:hover {
    background-color: #c1f0cc;
    color: #0a7b2e;
}

.bg-light-info {
    background-color: #e8f4f8;
}

.bg-light-success {
    background-color: #d4f4dd;
}

.bg-light-primary {
    background-color: #e1e8ff;
}

.symbol {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .card-header,
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>
