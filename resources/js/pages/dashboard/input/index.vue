<script setup lang="ts">
import { ref } from "vue";
import axios from "@/libs/axios";

// Struktur sesuai response backend
interface TransaksiDetail {
  id: number;
  kode_tiket: string;
  created_at: string;
  ticket: {
    id: number;
    jenis_tiket: string;
    harga_tiket: number;
    stok_tiket: number;
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

// Cek tiket berdasarkan kode_tiket
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
    errorMessage.value = err.response?.data?.message || "Tiket tidak ditemukan";
  } finally {
    loading.value = false;
  }
};

// Cetak tiket
const cetakTiket = () => {  
  if (!transaksi.value) return;
  window.open(`/cetak-tiket/${transaksi.value.kode_tiket}`, "_blank");
};

// Reset/Batal pencarian
const batalCari = () => {
  kodeTiket.value = "";
  transaksi.value = null;
  errorMessage.value = "";
  loading.value = false;
};
</script>

<template>
  <div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="d-flex align-items-center">
            <div class="symbol symbol-50px me-3">
              <div class="symbol-label bg-light-primary">
                <i class="ki-duotone ki-scanner text-primary fs-2">
                  <span class="path1"></span>
                  <span class="path2"></span>
                </i>
              </div>
            </div>
            <div>
              <h1 class="text-gray-900 fw-bold fs-2 mb-1">Validasi Tiket</h1>
              <p class="text-gray-600 fs-6 mb-0">Periksa status dan detail tiket Anda</p>
            </div>
          </div>
          
          <!-- Back Button (shown when ticket is displayed or error exists) -->
          <!-- <div v-if="transaksi || errorMessage">
            <button @click="batalCari" class="btn btn-light-primary btn-sm fw-bold">
              <i class="ki-duotone ki-arrow-left me-2">
                <span class="path1"></span>
                <span class="path2"></span>
              </i>
              Kembali
            </button>
          </div> -->
        </div>
      </div>
    </div>

    <!-- Search Section -->
    <div class="row mb-6">
      <div class="col-lg-8 col-xl-6 mx-auto">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-light-primary border-0 py-4">
            <div class="card-title m-0">
              <i class="ki-duotone ki-magnifier text-primary fs-3 me-2">
                <span class="path1"></span>
                <span class="path2"></span>
              </i>
              <h3 class="fw-bold text-primary mb-0">Masukkan Kode Tiket</h3>
            </div>
          </div>
          <div class="card-body p-6">
            <div class="mb-4">
              <label for="kodeTiket" class="form-label fw-semibold text-gray-700 fs-6 mb-3">
                Kode Tiket
              </label>
              <div class="position-relative">
                <input
                  id="kodeTiket"
                  type="text"
                  v-model="kodeTiket"
                  class="form-control form-control-lg border-gray-300 fs-6"
                  placeholder="Contoh: TKT-2024-001"
                  style="padding-left: 3rem;"
                  @keyup.enter="cekTiket"
                />
                <i class="ki-duotone ki-code position-absolute top-50 translate-middle-y start-0 ms-4 text-gray-500">
                  <span class="path1"></span>
                  <span class="path2"></span>
                </i>
              </div>
            </div>
            
            <!-- Button Group -->
            <div class="d-flex gap-3">
              <button 
                @click="cekTiket" 
                class="btn btn-primary btn-lg flex-grow-1 fw-bold" 
                :disabled="loading || !kodeTiket"
              >
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="ki-duotone ki-magnifier me-2">
                  <span class="path1"></span>
                  <span class="path2"></span>
                </i>
                {{ loading ? 'Mencari...' : 'Cek Tiket' }}
              </button>
              
              <!-- Cancel button (shown when there's input or results) -->
              <!-- <button 
                v-if="kodeTiket || transaksi || errorMessage"
                @click="batalCari" 
                class="btn btn-light-danger btn-lg fw-bold"
                :disabled="loading"
              >
                <i class="ki-duotone ki-cross me-2">
                  <span class="path1"></span>
                  <span class="path2"></span>
                </i>
                Batal
              </button> -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Message -->
    <div v-if="errorMessage" class="row mb-6">
      <div class="col-lg-8 col-xl-6 mx-auto">
        <div class="alert alert-danger d-flex align-items-center p-4">
          <i class="ki-duotone ki-information-2 fs-2 me-3">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
          </i>
          <div class="flex-grow-1">
            <div class="fw-semibold">{{ errorMessage }}</div>
          </div>
          <button @click="batalCari" class="btn btn-sm btn-light-danger ms-3">
            <i class="ki-duotone ki-cross">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </button>
        </div>
      </div>
    </div>

    <!-- Ticket Details -->
    <div v-if="transaksi" class="row">
      <div class="col-12">
        <!-- Success Banner -->
        <div class="alert alert-success d-flex align-items-center p-4 mb-6">
          <i class="ki-duotone ki-shield-tick fs-2 me-3">
            <span class="path1"></span>
            <span class="path2"></span>
          </i>
          <div class="flex-grow-1">
            <h5 class="mb-1 fw-bold">Tiket Ditemukan!</h5>
            <p class="mb-0 fs-6">Data tiket berhasil ditemukan dan terverifikasi</p>
          </div>
          <button @click="batalCari" class="btn btn-sm btn-light-success ms-3">
            <i class="ki-duotone ki-cross">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
          </button>
        </div>

        <!-- Ticket Card -->
        <div class="card shadow-lg border-0">
          <!-- Card Header -->
          <div class="card-header bg-gradient-primary border-0 py-5">
            <div class="row align-items-center">
              <div class="col-md-8">
                <div class="d-flex align-items-center">
                  <div class="symbol symbol-60px me-4">
                    <div class="symbol-label bg-light-success">
                      <i class="ki-duotone ki-ticket text-success fs-2x">
                        <span class="path1"></span>
                        <span class="path2"></span>
                      </i>
                    </div>
                  </div>
                  <div>
                    <h3 class="text-white fw-bold fs-2 mb-1">{{ transaksi.ticket.jenis_tiket }}</h3>
                    <p class="text-white-50 mb-0 fs-6">Kode: {{ transaksi.kode_tiket }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-md-end">
                <span 
                  :class="transaksi.transaksi.status_payment === 'paid'
                    ? 'badge badge-light-success fs-6 px-4 py-2'
                    : 'badge badge-light-warning fs-6 px-4 py-2'"
                >
                  <i class="ki-duotone ki-check-circle me-1" v-if="transaksi.transaksi.status_payment === 'paid'">
                    <span class="path1"></span>
                    <span class="path2"></span>
                  </i>
                  <i class="ki-duotone ki-time me-1" v-else>
                    <span class="path1"></span>
                    <span class="path2"></span>
                  </i>
                  {{ transaksi.transaksi.status_payment === 'paid' ? 'Paid' : 'Menunggu Pembayaran' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Card Body -->
          <div class="card-body p-6">
            <div class="row g-6">
              <!-- Customer Info -->
              <div class="col-lg-6">
                <div class="bg-light-info rounded p-4 h-100">
                  <div class="d-flex align-items-center mb-4">
                    <i class="ki-duotone ki-profile-user text-info fs-2 me-3">
                      <span class="path1"></span>
                      <span class="path2"></span>
                      <span class="path3"></span>
                      <span class="path4"></span>
                    </i>
                    <h5 class="mb-0 fw-bold text-info">Informasi Pembeli</h5>
                  </div>
                  
                  <div class="mb-4">
                    <label class="text-gray-600 fs-7 fw-semibold text-uppercase mb-1">Nama Lengkap</label>
                    <div class="text-gray-900 fs-6 fw-bold">{{ transaksi.transaksi.nama_pembeli }}</div>
                  </div>
                  
                  <div class="mb-4">
                    <label class="text-gray-600 fs-7 fw-semibold text-uppercase mb-1">Email</label>
                    <div class="text-gray-900 fs-6 fw-bold d-flex align-items-center">
                      <i class="ki-duotone ki-sms me-2 text-gray-500">
                        <span class="path1"></span>
                        <span class="path2"></span>
                      </i>
                      {{ transaksi.transaksi.email }}
                    </div>
                  </div>
                  
                  <div class="mb-0">
                    <label class="text-gray-600 fs-7 fw-semibold text-uppercase mb-1">No. Telepon</label>
                    <div class="text-gray-900 fs-6 fw-bold d-flex align-items-center">
                      <i class="ki-duotone ki-phone me-2 text-gray-500">
                        <span class="path1"></span>
                        <span class="path2"></span>
                      </i>
                      {{ transaksi.transaksi.nomer_telpon }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Transaction Info -->
              <div class="col-lg-6">
                <div class="bg-light-success rounded p-4 h-100">
                  <div class="d-flex align-items-center mb-4">
                    <i class="ki-duotone ki-wallet text-success fs-2 me-3">
                      <span class="path1"></span>
                      <span class="path2"></span>
                      <span class="path3"></span>
                      <span class="path4"></span>
                    </i>
                    <h5 class="mb-0 fw-bold text-success">Detail Transaksi</h5>
                  </div>
                  
                  <div class="mb-4">
                    <label class="text-gray-600 fs-7 fw-semibold text-uppercase mb-1">Kode Transaksi</label>
                    <div class="text-gray-900 fs-6 fw-bold">{{ transaksi.transaksi.kode_transaksi }}</div>
                  </div>
                  
                  <div class="mb-4">
                    <label class="text-gray-600 fs-7 fw-semibold text-uppercase mb-1">Total Pembayaran</label>
                    <div class="text-gray-900 fs-4 fw-bolder text-success">
                      {{
                        transaksi.transaksi.total_harga_rp ||
                        'Rp ' + transaksi.transaksi.total_harga.toLocaleString('id-ID')
                      }}
                    </div>
                  </div>
                  
                  <div class="mb-0">
                    <label class="text-gray-600 fs-7 fw-semibold text-uppercase mb-1">Tanggal Pembelian</label>
                    <div class="text-gray-900 fs-6 fw-bold d-flex align-items-center">
                      <i class="ki-duotone ki-calendar me-2 text-gray-500">
                        <span class="path1"></span>
                        <span class="path2"></span>
                      </i>
                      {{ new Date(transaksi.created_at).toLocaleDateString('id-ID', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                      }) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card Footer -->
          <div class="card-footer bg-light border-0 p-4">
            <div class="d-flex flex-stack">
              <div class="text-gray-600">
                <i class="ki-duotone ki-information-2 me-2">
                  <span class="path1"></span>
                  <span class="path2"></span>
                  <span class="path3"></span>
                </i>
                Tiket valid dan dapat digunakan
              </div>
              <div class="d-flex gap-3">
                <button @click="batalCari" class="btn btn-light-secondary btn-lg fw-bold">
                  <i class="ki-duotone ki-arrow-left fs-4 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                  </i>
                  Kembali
                </button>
                <button @click="cetakTiket" class="btn btn-success btn-lg fw-bold">
                  <i class="ki-duotone ki-printer fs-4 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                  </i>
                  Cetak Tiket
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Instructions Card (when no ticket is shown) -->
    <div v-if="!transaksi && !loading && !errorMessage" class="row">
      <div class="col-lg-8 col-xl-6 mx-auto">
        <div class="card border-dashed border-primary bg-light-primary">
          <div class="card-body p-6 text-center">
            <i class="ki-duotone ki-information-2 text-primary fs-2x mb-4">
              <span class="path1"></span>
              <span class="path2"></span>
              <span class="path3"></span>
            </i>
            <h5 class="text-primary fw-bold mb-3">Cara Menggunakan</h5>
            <div class="text-gray-700 fs-6">
              <p class="mb-2">1. Masukkan kode tiket Anda pada kolom di atas</p>
              <p class="mb-2">2. Klik tombol "Cek Tiket" untuk memvalidasi</p>
              <p class="mb-0">3. Jika valid, Anda dapat mencetak tiket sebagai bukti</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.bg-gradient-primary {
  background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
}

.card {
  transition: all 0.3s ease;
}

.card:hover {
  transform: translateY(-2px);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-control:focus {
  border-color: #3B82F6;
  box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.alert {
  border: none;
  border-radius: 0.5rem;
}

.badge {
  font-weight: 600;
  letter-spacing: 0.025rem;
}

.btn-light-secondary {
  background-color: #f1f3f4;
  border-color: #f1f3f4;
  color: #5e6278;
}

.btn-light-secondary:hover {
  background-color: #e9ecef;
  border-color: #e9ecef;
  color: #5e6278;
}

.btn-light-danger {
  background-color: #fef2f2;
  border-color: #fef2f2;
  color: #dc2626;
}

.btn-light-danger:hover {
  background-color: #fecaca;
  border-color: #fecaca;
  color: #dc2626;
}
</style>