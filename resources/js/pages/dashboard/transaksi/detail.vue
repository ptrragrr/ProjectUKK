<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '@/libs/axios';

// interface Transaksi {
//   id: number;
//   nama_pembeli: string;
//   email: string;
//   nomer_telpon: string;
//   kode_transaksi: string;
//   status_payment: string;
//   total_harga: number;
//   created_at: string;
//   kode_tiket?: string;
// }

interface Detail {
  id: number;
  jumlah: number;
  harga_satuan: number;
  total_harga: number;
  ticket: {
    id: number;
    nama: string;
    konser: {
      id: number;
      nama_konser: string;
    };
  };
}

interface Transaksi {
  id: number;
  nama_pembeli: string;
  email: string;
  nomer_telpon: string;
  kode_transaksi: string;
  status_payment: string;
  total_harga: number;
  created_at: string;
  kode_tiket?: string;
  details?: Detail[];
}

const route = useRoute();
const router = useRouter();

const transaksi = ref<Transaksi | null>(null);
const loading = ref(true);
const kodeTiket = ref("");

const fetchDetail = async () => {
  try {
    const id = route.params.id;
    const res = await axios.get(`/transaksi/${id}`);
    transaksi.value = res.data;
    kodeTiket.value = res.data.kode_tiket ?? "";
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchDetail);

const simpanKodeTiket = async () => {
  if (!kodeTiket.value) {
    alert("Kode tiket tidak boleh kosong!");
    return;
  }
  
  try {
    const id = route.params.id;
    await axios.post(`/transaksi/${id}/kode-tiket`, {
      kode_tiket: kodeTiket.value,
    });
    alert("Kode tiket berhasil disimpan!");
  } catch (err) {
    console.error(err);
    alert("Gagal menyimpan kode tiket!");
  }
};

const cetakTiket = () => {
  if (!transaksi.value) return;
  window.open(`/transaksi/${transaksi.value.id}/cetak`, "_blank");
};

const kembali = () => {
  router.push({ name: "dashboard" });
};

const formatRupiah = (value: number) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(value);
};
</script>

<template>
  <div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-6">
      <div>
        <!-- <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
          Detail Transaksi
        </h1> -->
        <!-- <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
          <li class="breadcrumb-item text-muted">
            <a href="#" class="text-muted text-hover-primary">Dashboard</a>
          </li>
          <li class="breadcrumb-item">
            <span class="bullet bg-gray-400 w-5px h-2px"></span>
          </li>
          <li class="breadcrumb-item text-muted">Transaksi</li>
          <li class="breadcrumb-item">
            <span class="bullet bg-gray-400 w-5px h-2px"></span>
          </li>
          <li class="breadcrumb-item text-dark">Detail</li>
        </ul> -->
      </div>
      <button @click="kembali" class="btn btn-light-primary btn-sm">
        <i class="ki-duotone ki-arrow-left fs-2">
          <span class="path1"></span>
          <span class="path2"></span>
        </i>
        Kembali
      </button>
    </div>

    <!-- Concert Info -->
<div class="col-md-12 mt-5">
  <!-- <h3 class="fw-bold text-dark mb-3">Tiket / Konser</h3> -->
  <table class="table table-row-bordered align-middle">
    <thead>
      <tr class="fw-bold text-gray-600">
        <th>Nama Konser</th>
        <!-- <th>Nama Tiket</th> -->
        <th>Jumlah</th>
        <th>Harga</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="detail in transaksi?.details || []" :key="detail.id">
        <td>{{ detail.ticket.konser?.nama_konser }}</td>
        <!-- <td>{{ detail.ticket.nama }}</td> -->
        <td>{{ detail.jumlah }}</td>
        <!-- <td>Rp {{ detail.harga_satuan.toLocaleString("id-ID") }}</td>
        <td>Rp {{ detail.total_harga.toLocaleString("id-ID") }}</td> -->
        <td>{{ formatRupiah(detail.harga_satuan) }}</td>
<td>{{ formatRupiah(detail.total_harga) }}</td>
      </tr>
    </tbody>
  </table>
</div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-10">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="text-muted mt-3">Memuat detail transaksi...</p>
    </div>

    <!-- Content -->
    <div v-else-if="transaksi" class="row g-6">
      <!-- Transaction Details Card -->
      <div class="col-lg-8">
        <div class="card card-flush mb-6 mb-xl-9">
          <div class="card-header">
            <div class="card-title">
              <h2 class="fw-bold text-dark">Informasi Transaksi</h2>
            </div>
          </div>
          <div class="card-body pt-0">
            <div class="row gy-5">
              <!-- Transaction ID -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">ID Transaksi</label>
                  <span class="fw-bold fs-6 text-gray-800">#{{ transaksi.id }}</span>
                </div>
              </div>

              <!-- Transaction Code -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">Kode Transaksi</label>
                  <span class="fw-bold fs-6 text-gray-800">{{ transaksi.kode_transaksi }}</span>
                </div>
              </div>

              <!-- Buyer Name -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">Nama Pembeli</label>
                  <span class="fw-bold fs-6 text-gray-800">{{ transaksi.nama_pembeli }}</span>
                </div>
              </div>

              <!-- Email -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">Email</label>
                  <span class="fw-bold fs-6 text-gray-800">{{ transaksi.email }}</span>
                </div>
              </div>

              <!-- Phone Number -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">No. Telepon</label>
                  <span class="fw-bold fs-6 text-gray-800">{{ transaksi.nomer_telpon }}</span>
                </div>
              </div>

              <!-- Status -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">Status Pembayaran</label>
                  <div>
                    <span 
                      :class="transaksi.status_payment === 'paid' 
                        ? 'badge badge-light-success fs-7 fw-bold' 
                        : 'badge badge-light-warning fs-7 fw-bold'"
                    >
                      <i 
                        :class="transaksi.status_payment === 'paid' 
                          ? 'ki-duotone ki-check-circle fs-5 me-1' 
                          : 'ki-duotone ki-time fs-5 me-1'"
                      >
                        <span class="path1"></span>
                        <span class="path2"></span>
                      </i>
                      {{ transaksi.status_payment === 'paid' ? 'Paid' : 'Menunggu' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Total Amount -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">Total Harga</label>
                  <span class="fw-bold fs-4 text-primary">
  {{ formatRupiah(transaksi.total_harga) }}
</span>
                  <!-- <span class="fw-bold fs-4 text-primary">
                    Rp {{ transaksi.total_harga.toLocaleString("id-ID") }}
                  </span> -->
                </div>
              </div>

              <!-- Created Date -->
              <div class="col-md-6">
                <div class="d-flex flex-column">
                  <label class="fs-6 fw-semibold text-gray-600 mb-2">Tanggal Dibuat</label>
                  <span class="fw-bold fs-6 text-gray-800">{{ transaksi.created_at }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Ticket Management Card -->
     
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-10">
      <div class="card">
        <div class="card-body">
          <i class="ki-duotone ki-cross-circle fs-5x text-danger mb-5">
            <span class="path1"></span>
            <span class="path2"></span>
          </i>
          <h3 class="text-gray-800 fw-bold mb-3">Data Tidak Ditemukan</h3>
          <p class="text-gray-600 fs-6">
            Transaksi yang Anda cari tidak dapat ditemukan atau telah dihapus.
          </p>
          <button @click="kembali" class="btn btn-primary">
            <i class="ki-duotone ki-arrow-left fs-4 me-2">
              <span class="path1"></span>
              <span class="path2"></span>
            </i>
            Kembali ke Daftar Transaksi
          </button>
        </div>
      </div>
    </div>
  </div>
</template>