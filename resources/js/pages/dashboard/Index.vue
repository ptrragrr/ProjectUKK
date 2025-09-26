<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import axios from '@/libs/axios';
import router from '@/router';

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

const transaksis = ref<Transaksi[]>([]);
const loading = ref(true);
const viewMode = ref<'grid' | 'table'>('grid');

// Filter variables
const selectedYear = ref<number | null>(null);
const selectedMonth = ref<number | null>(null);
const searchQuery = ref('');

// Generate years array (2021 - current year)
const years = computed(() => {
  const currentYear = new Date().getFullYear();
  const yearsList = [];
  for (let year = 2021; year <= currentYear; year++) {
    yearsList.push(year);
  }
  return yearsList.reverse();
});

// Months array
const months = [
  { value: 1, label: 'Januari' },
  { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },
  { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },
  { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' }
];

// Filtered transactions with search
const filteredTransaksis = computed(() => {
  let filtered = transaksis.value;

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(trx => 
      trx.nama_pembeli.toLowerCase().includes(query) ||
      trx.email.toLowerCase().includes(query) ||
      trx.kode_transaksi.toLowerCase().includes(query)
    );
  }

  // Filter by year
  if (selectedYear.value) {
    filtered = filtered.filter(trx => {
      const trxYear = new Date(trx.created_at).getFullYear();
      return trxYear === selectedYear.value;
    });
  }

  // Filter by month
  if (selectedMonth.value) {
    filtered = filtered.filter(trx => {
      const trxMonth = new Date(trx.created_at).getMonth() + 1;
      return trxMonth === selectedMonth.value;
    });
  }

  return filtered;
});

// Statistics
const stats = computed(() => {
  const filtered = filteredTransaksis.value;
  const totalTransactions = filtered.length;
  const paidTransactions = filtered.filter(t => t.status_payment === 'paid').length;
  const pendingTransactions = filtered.filter(t => t.status_payment === 'pending').length;
  const totalRevenue = filtered
    .filter(t => t.status_payment === 'paid')
    .reduce((sum, t) => sum + t.total_harga, 0);

  return {
    totalTransactions,
    paidTransactions,
    pendingTransactions,
    totalRevenue
  };
});

const lihatDetail = (id: number) => {
  router.push({
     name: 'dashboard.transaksi.detail',
     params: { id }
  });
};

const fetchTransaksis = async () => {
  try {
    const res = await axios.get('/transaksi');
    transaksis.value = res.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const clearFilters = () => {
  selectedYear.value = null;
  selectedMonth.value = null;
  searchQuery.value = '';
};

onMounted(fetchTransaksis);

const getStatusClass = (status: string) => {
  return status === 'paid' 
    ? 'bg-emerald-100 text-emerald-800 border-emerald-200' 
    : 'bg-amber-100 text-amber-800 border-amber-200';
};

const getStatusIcon = (status: string) => {
  return status === 'paid' ? '✅' : '⏳';
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header Section -->
      <div class="mb-8">
        <div class="text-center md:text-left">
          <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent mb-2">
            Dashboard Transaksi
          </h1>
          <p class="text-gray-600 text-lg">Kelola dan pantau semua transaksi dengan mudah</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl p-12 text-center border border-white/20">
          <div class="relative mb-6">
            <div class="animate-spin w-16 h-16 border-4 border-blue-500/30 border-t-blue-500 rounded-full mx-auto"></div>
            <div class="animate-pulse absolute inset-0 w-16 h-16 border-4 border-transparent border-t-purple-500/50 rounded-full mx-auto"></div>
          </div>
          <p class="text-gray-700 text-xl font-medium">Memuat data transaksi...</p>
        </div>
      </div>

      <!-- Main Content -->
      <div v-else class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          <!-- Total Transactions -->
          <!-- <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-blue-100 text-sm font-medium">Total Transaksi</p>
                <p class="text-3xl font-bold">{{ stats.totalTransactions }}</p>
              </div>
              <div class="bg-white/20 rounded-full p-3">
                <span class="text-2xl">📊</span>
              </div>
            </div>
          </div> -->

          <!-- Paid Transactions -->
          <!-- <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-emerald-100 text-sm font-medium">Transaksi Lunas</p>
                <p class="text-3xl font-bold">{{ stats.paidTransactions }}</p>
              </div>
              <div class="bg-white/20 rounded-full p-3">
                <span class="text-2xl">✅</span>
              </div>
            </div>
          </div> -->

          <!-- Pending Transactions -->
          <!-- <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-amber-100 text-sm font-medium">Menunggu Bayar</p>
                <p class="text-3xl font-bold">{{ stats.pendingTransactions }}</p>
              </div>
              <div class="bg-white/20 rounded-full p-3">
                <span class="text-2xl">⏳</span>
              </div>
            </div>
          </div> -->

          <!-- Total Revenue -->
          <!-- <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-purple-100 text-sm font-medium">Total Pendapatan</p>
                <p class="text-xl font-bold">Rp {{ stats.totalRevenue.toLocaleString('id-ID') }}</p>
              </div>
              <div class="bg-white/20 rounded-full p-3">
                <span class="text-2xl">💰</span>
              </div>
            </div>
          </div>
        </div> -->

        <!-- Filters and Controls -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
          <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
              <!-- Search Bar -->
              <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-400 text-lg">🔍</span>
                </div>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Cari nama, email, atau kode transaksi..."
                  class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white/50"
                />
              </div>

              <!-- Year Filter -->
              <select 
                v-model="selectedYear" 
                class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50 transition-all duration-200"
              >
                <option :value="null">📅 Semua Tahun</option>
                <option v-for="year in years" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>

              <!-- Month Filter -->
              <select 
                v-model="selectedMonth"
                class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50 transition-all duration-200"
              >
                <option :value="null">📆 Semua Bulan</option>
                <option v-for="month in months" :key="month.value" :value="month.value">
                  {{ month.label }}
                </option>
              </select>

              <!-- Clear Filters -->
              <button 
                v-if="selectedYear || selectedMonth || searchQuery"
                @click="clearFilters"
                class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all duration-200 font-medium whitespace-nowrap"
              >
                🔄 Reset
              </button>
            </div>

            <!-- View Toggle -->
            <!-- <div class="flex bg-gray-100 rounded-xl p-1">
              <button
                @click="viewMode = 'grid'"
                :class="[
                  'px-4 py-2 rounded-lg transition-all duration-200 font-medium',
                  viewMode === 'grid' ? 'bg-white text-blue-600 shadow-md' : 'text-gray-600 hover:text-gray-800'
                ]"
              >
                🔲 Grid
              </button>
              <button
                @click="viewMode = 'table'"
                :class="[
                  'px-4 py-2 rounded-lg transition-all duration-200 font-medium',
                  viewMode === 'table' ? 'bg-white text-blue-600 shadow-md' : 'text-gray-600 hover:text-gray-800'
                ]"
              >
                📋 Table
              </button>
            </div> -->
          </div>

          <!-- Active Filters Info -->
          <div v-if="selectedYear || selectedMonth || searchQuery" class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex flex-wrap gap-2">
              <span class="text-sm text-gray-600">Filter aktif:</span>
              <span v-if="selectedYear" class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                Tahun {{ selectedYear }}
                <button @click="selectedYear = null" class="ml-2 hover:text-blue-600">×</button>
              </span>
              <span v-if="selectedMonth" class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full">
                {{ months.find(m => m.value === selectedMonth)?.label }}
                <button @click="selectedMonth = null" class="ml-2 hover:text-purple-600">×</button>
              </span>
              <span v-if="searchQuery" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                "{{ searchQuery }}"
                <button @click="searchQuery = ''" class="ml-2 hover:text-green-600">×</button>
              </span>
            </div>
            <div class="mt-2 text-sm font-medium text-blue-600">
              Menampilkan {{ filteredTransaksis.length }} dari {{ transaksis.length }} transaksi
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredTransaksis.length === 0" class="text-center py-16">
          <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-lg p-12 max-w-md mx-auto border border-white/20">
            <div class="text-8xl mb-6">📭</div>
            <h3 class="text-2xl font-bold text-gray-700 mb-4">
              {{ (selectedYear || selectedMonth || searchQuery) ? 'Tidak Ada Hasil' : 'Belum Ada Transaksi' }}
            </h3>
            <p class="text-gray-500 mb-6">
              {{ (selectedYear || selectedMonth || searchQuery) ? 'Coba ubah filter atau kata kunci pencarian' : 'Data transaksi akan muncul di sini' }}
            </p>
            <button 
              v-if="selectedYear || selectedMonth || searchQuery"
              @click="clearFilters"
              class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-1"
            >
              🔄 Reset Semua Filter
            </button>
          </div>
        </div>

        <!-- Grid View -->
        

        <!-- Table View -->
        <div v-else class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg overflow-hidden border border-white/20">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Pembeli</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Kontak</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Kode</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Harga</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                  <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="trx in filteredTransaksis" :key="trx.id" class="hover:bg-blue-50/50 transition-all duration-200">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900">#{{ trx.id }}</div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                      <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                        {{ trx.nama_pembeli.charAt(0).toUpperCase() }}
                      </div>
                      <div class="text-sm font-semibold text-gray-900">{{ trx.nama_pembeli }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">📧 {{ trx.email }}</div>
                    <div class="text-sm text-gray-500">📱 {{ trx.nomer_telpon }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <code class="text-xs font-mono bg-gray-100 px-2 py-1 rounded border">
                      {{ trx.kode_transaksi }}
                    </code>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="['inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border', getStatusClass(trx.status_payment)]"
                    >
                      {{ getStatusIcon(trx.status_payment) }} {{ trx.status_payment }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900">
                      Rp {{ trx.total_harga ? trx.total_harga.toLocaleString("id-ID") : '0' }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ new Date(trx.created_at).toLocaleDateString('id-ID', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                      }) }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button 
                      @click="lihatDetail(trx.id)"
                      class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-black text-sm font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                    >
                      👁️ Detail
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>