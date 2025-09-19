<script setup lang="ts">
import { ref, reactive } from 'vue';
import axios from '@/libs/axios';

interface Transaksi {
  id: number;
  nama_pembeli: string;
  email: string;
  nomer_telpon: string;
  kode_transaksi: string;
  status_payment: string;
  total_harga: number;
  created_at: string;
}

const kodeTicket = ref('');
const loading = ref(false);
const transaksiData = ref<Transaksi | null>(null);
const showDetail = ref(false);
const error = ref('');

const cekTiket = async () => {
  if (!kodeTicket.value.trim()) {
    error.value = 'Silakan masukkan kode tiket';
    return;
  }

  loading.value = true;
  error.value = '';
  showDetail.value = false;
  transaksiData.value = null;

  try {
    const res = await axios.get(`/transaksi/kode/${kodeTicket.value}`);
    transaksiData.value = res.data;
    showDetail.value = true;
    
    if (res.data.status_payment !== 'paid') {
      error.value = 'Transaksi belum dibayar. Tiket tidak dapat dicetak.';
    }
  } catch (err: any) {
    console.error(err);
    if (err.response?.status === 404) {
      error.value = 'Kode tiket tidak ditemukan';
    } else {
      error.value = 'Terjadi kesalahan saat mencari tiket';
    }
  } finally {
    loading.value = false;
  }
};

const cetakTiket = () => {
  if (!transaksiData.value || transaksiData.value.status_payment !== 'paid') {
    return;
  }

  // Create print content
  const printContent = `
    <div style="max-width: 400px; margin: 0 auto; font-family: 'Courier New', monospace; padding: 20px;">
      <div style="text-align: center; border: 2px solid #000; padding: 20px; background: #fff;">
        <h1 style="margin: 0 0 20px 0; font-size: 24px;">🎫 TIKET EVENT</h1>
        <hr style="border: 1px solid #000; margin: 20px 0;">
        
        <div style="text-align: left; margin: 20px 0;">
          <p><strong>Kode Tiket:</strong> ${transaksiData.value.kode_transaksi}</p>
          <p><strong>Nama:</strong> ${transaksiData.value.nama_pembeli}</p>
          <p><strong>Email:</strong> ${transaksiData.value.email}</p>
          <p><strong>Telepon:</strong> ${transaksiData.value.nomer_telpon}</p>
          <p><strong>Total Bayar:</strong> Rp ${transaksiData.value.total_harga?.toLocaleString('id-ID') || '0'}</p>
          <p><strong>Status:</strong> ${transaksiData.value.status_payment.toUpperCase()}</p>
          <p><strong>Tanggal:</strong> ${new Date(transaksiData.value.created_at).toLocaleDateString('id-ID')}</p>
        </div>
        
        <hr style="border: 1px solid #000; margin: 20px 0;">
        <p style="text-align: center; font-size: 12px; margin: 0;">
          Harap simpan tiket ini sebagai bukti<br>
          Tunjukkan saat memasuki lokasi event
        </p>
      </div>
    </div>
  `;

  // Open print dialog
  const printWindow = window.open('', '_blank');
  if (printWindow) {
    printWindow.document.write(`
      <html>
        <head>
          <title>Cetak Tiket - ${transaksiData.value.kode_transaksi}</title>
          <style>
            body { margin: 0; padding: 20px; }
            @media print {
              body { margin: 0; }
            }
          </style>
        </head>
        <body>
          ${printContent}
        </body>
      </html>
    `);
    printWindow.document.close();
    printWindow.print();
  }
};

const resetForm = () => {
  kodeTicket.value = '';
  transaksiData.value = null;
  showDetail.value = false;
  error.value = '';
};

const getStatusClass = (status: string) => {
  return status === 'paid' 
    ? 'bg-green-100 text-green-800 border-green-200' 
    : 'bg-red-100 text-red-800 border-red-200';
};

const getStatusIcon = (status: string) => {
  return status === 'paid' ? '✅' : '❌';
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-100 p-6">
    <div class="max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
        <div class="text-center">
          <div class="text-6xl mb-4">🎫</div>
          <h1 class="text-4xl font-bold text-gray-800 mb-2">
            Validasi Tiket Event
          </h1>
          <p class="text-gray-600 text-lg">
            Masukkan kode tiket untuk validasi dan pencetakan
          </p>
        </div>
      </div>

      <!-- Input Form -->
      <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
        <div class="max-w-2xl mx-auto">
          <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Input Kode Tiket</h2>
            <p class="text-gray-600">Masukkan kode tiket untuk memulai validasi</p>
          </div>

          <!-- Input Section -->
          <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 mb-6">
            <label for="kodeTicket" class="block text-sm font-semibold text-gray-700 mb-3">
              Kode Tiket
            </label>
            <div class="flex gap-3">
              <div class="flex-1 relative">
                <input
                  id="kodeTicket"
                  v-model="kodeTicket"
                  type="text"
                  placeholder="Contoh: ESYIJMQV, ABC123, dll..."
                  class="w-full px-5 py-4 text-lg font-mono border-2 border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition-all duration-200 bg-white"
                  :disabled="loading"
                  @keyup.enter="cekTiket"
                />
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl">
                  🎫
                </div>
              </div>
              <button
                @click="cekTiket"
                :disabled="loading || !kodeTicket.trim()"
                class="px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:cursor-not-allowed min-w-[140px]"
              >
                <span v-if="loading" class="flex items-center justify-center">
                  <div class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full mr-2"></div>
                  <span class="hidden sm:inline">Cek...</span>
                </span>
                <span v-else class="flex items-center justify-center">
                  <span class="mr-2">🔍</span>
                  <span class="hidden sm:inline">Cek Tiket</span>
                  <span class="sm:hidden">Cek</span>
                </span>
              </button>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="flex justify-center gap-4">
            <button
              @click="resetForm"
              class="px-6 py-3 border-2 border-gray-300 hover:border-purple-400 hover:bg-purple-50 text-gray-700 hover:text-purple-700 font-semibold rounded-xl transition-all duration-200 flex items-center"
            >
              <span class="mr-2">🔄</span>
              Reset Form
            </button>
            
            <button
              @click="kodeTicket = kodeTicket.toUpperCase()"
              :disabled="!kodeTicket.trim()"
              class="px-6 py-3 border-2 border-blue-300 hover:border-blue-400 hover:bg-blue-50 text-blue-700 hover:text-blue-800 font-semibold rounded-xl transition-all duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="mr-2">🔤</span>
              Uppercase
            </button>
          </div>

          <!-- Tips -->
          <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg">
            <div class="flex items-start">
              <div class="text-yellow-600 mr-2">💡</div>
              <div class="text-sm">
                <p class="font-semibold text-yellow-800 mb-1">Tips:</p>
                <p class="text-yellow-700">
                  Pastikan kode tiket diketik dengan benar. Kode biasanya terdiri dari huruf dan angka (contoh: ESYIJMQV, TKT001, ABC123)
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="bg-red-50 border-2 border-red-200 rounded-2xl p-6 mb-8">
        <div class="flex items-center">
          <div class="text-2xl mr-3">⚠️</div>
          <div>
            <h3 class="text-lg font-semibold text-red-800 mb-1">Oops! Ada Masalah</h3>
            <p class="text-red-600">{{ error }}</p>
          </div>
        </div>
      </div>

      <!-- Transaction Detail -->
      <div v-if="showDetail && transaksiData" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white p-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold mb-1">Detail Transaksi</h2>
              <p class="opacity-90">Informasi tiket ditemukan</p>
            </div>
            <div class="text-right">
              <div class="text-sm opacity-90">ID Transaksi</div>
              <div class="text-xl font-bold">#{{ transaksiData.id }}</div>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="p-8">
          <div class="grid md:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                  Nama Pembeli
                </label>
                <p class="text-xl font-semibold text-gray-900">{{ transaksiData.nama_pembeli }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                  Email
                </label>
                <p class="text-gray-900">{{ transaksiData.email }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                  Nomor Telepon
                </label>
                <p class="text-gray-900">{{ transaksiData.nomer_telpon }}</p>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                  Kode Tiket
                </label>
                <p class="text-lg font-mono bg-gray-100 px-4 py-2 rounded-lg">
                  {{ transaksiData.kode_transaksi }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                  Status Pembayaran
                </label>
                <span 
                  :class="getStatusClass(transaksiData.status_payment)"
                  class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border-2"
                >
                  <span class="mr-2">{{ getStatusIcon(transaksiData.status_payment) }}</span>
                  {{ transaksiData.status_payment.toUpperCase() }}
                </span>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                  Total Harga
                </label>
                <p class="text-2xl font-bold text-green-600">
                  Rp {{ transaksiData.total_harga ? transaksiData.total_harga.toLocaleString('id-ID') : '0' }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">
                  Tanggal Transaksi
                </label>
                <p class="text-gray-900">
                  {{ new Date(transaksiData.created_at).toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                  }) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Print Button -->
          <div class="mt-8 pt-8 border-t border-gray-200">
            <div class="text-center">
              <button
                @click="cetakTiket"
                :disabled="transaksiData.status_payment !== 'paid'"
                class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 disabled:from-gray-300 disabled:to-gray-400 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl disabled:cursor-not-allowed text-lg"
              >
                <span v-if="transaksiData.status_payment === 'paid'" class="flex items-center">
                  <span class="mr-3">🖨️</span>
                  Cetak Tiket
                </span>
                <span v-else class="flex items-center">
                  <span class="mr-3">❌</span>
                  Tidak Dapat Dicetak (Belum Bayar)
                </span>
              </button>
              
              <p v-if="transaksiData.status_payment === 'paid'" class="text-sm text-gray-600 mt-3">
                Klik tombol di atas untuk mencetak tiket resmi
              </p>
              <p v-else class="text-sm text-red-600 mt-3">
                Tiket hanya dapat dicetak setelah pembayaran berhasil
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Instructions -->
      <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
          <span class="mr-2">💡</span>
          Cara Penggunaan
        </h3>
        <div class="grid md:grid-cols-3 gap-6 text-sm">
          <div class="flex items-start">
            <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-1 text-xs font-bold">1</div>
            <div>
              <p class="font-semibold text-gray-800 mb-1">Masukkan Kode</p>
              <p class="text-gray-600">Input kode tiket yang valid</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="bg-purple-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-1 text-xs font-bold">2</div>
            <div>
              <p class="font-semibold text-gray-800 mb-1">Validasi Data</p>
              <p class="text-gray-600">Cek detail transaksi dan status pembayaran</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 mt-1 text-xs font-bold">3</div>
            <div>
              <p class="font-semibold text-gray-800 mb-1">Cetak Tiket</p>
              <p class="text-gray-600">Print tiket jika status sudah "PAID"</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>