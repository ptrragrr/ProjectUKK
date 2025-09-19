<script setup lang="ts">
import { ref, onMounted } from 'vue';
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

onMounted(fetchTransaksis);

const getStatusClass = (status: string) => {
  return status === 'paid' 
    ? 'text-green-600 font-semibold' 
    : 'text-yellow-600 font-semibold';
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header Section -->
      <!-- <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
              💳 Daftar Transaksi
            </h1>
            <p class="text-gray-600 text-lg">
              Kelola dan pantau semua transaksi dengan mudah
            </p>
          </div>
          <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl">
            <div class="text-sm opacity-90">Total Transaksi</div>
            <div class="text-2xl font-bold">{{ transaksis.length }}</div>
          </div>
        </div>
      </div> -->

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
          <div class="animate-spin w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4"></div>
          <p class="text-gray-600 text-lg">Memuat data transaksi...</p>
        </div>
      </div>

      <!-- Transactions Table -->
      <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b">
          <h2 class="text-xl font-semibold text-gray-800">Data Transaksi</h2>
        </div>

        <!-- Empty State -->
        <div v-if="transaksis.length === 0" class="text-center py-16">
          <div class="text-6xl mb-4">📋</div>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Transaksi</h3>
          <p class="text-gray-500">Data transaksi akan muncul di sini</p>
        </div>

        <!-- Desktop Table -->
        <div v-else class="hidden lg:block overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  ID
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Nama Pembeli
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Email
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Kode Transaksi
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Status Pembayaran
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Total Harga
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="trx in transaksis" :key="trx.id" class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">#{{ trx.id }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900">{{ trx.nama_pembeli }}</div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900">{{ trx.email }}</div>
                  <div class="text-sm text-gray-500">{{ trx.nomer_telpon }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">
                    {{ trx.kode_transaksi }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :style="trx.status_payment === 'paid' ? 'color: #16a34a; font-weight: 600;' : 'color: #d97706; font-weight: 600;'"
                    :class="trx.status_payment === 'paid' ? 'text-green-600 font-semibold' : 'text-yellow-600 font-semibold'"
                  >
                    {{ trx.status_payment }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900">
                    Rp {{ trx.total_harga ? trx.total_harga.toLocaleString("id-ID") : '0' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <router-link 
                    :to="{ name: 'dashboard.transaksi.detail', params: { id: trx.id } }"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
                  >
                    <span class="mr-2">👁️</span>
                    Lihat Detail
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        </div>

        <!-- Mobile Cards -->
        <!-- <div class="lg:hidden space-y-4 p-6">
          <div 
            v-for="trx in transaksis" 
            :key="trx.id"
            class="bg-gradient-to-r from-white to-gray-50 rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-200"
          >
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ trx.nama_pembeli }}</h3>
                <p class="text-sm text-gray-600">ID: #{{ trx.id }}</p>
              </div>
              <span 
                :style="trx.status_payment === 'paid' ? 'color: #16a34a; font-weight: 600;' : 'color: #d97706; font-weight: 600;'"
                :class="trx.status_payment === 'paid' ? 'text-green-600 font-semibold' : 'text-yellow-600 font-semibold'"
              >
                {{ trx.status_payment }}
              </span>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Email</label>
                <p class="text-sm text-gray-900">{{ trx.email }}</p>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Telepon</label>
                <p class="text-sm text-gray-900">{{ trx.nomer_telpon }}</p>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kode Transaksi</label>
              <p class="text-sm font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded inline-block">{{ trx.kode_transaksi }}</p>
            </div>

            <div class="flex justify-between items-center">
              <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total</label>
                <p class="text-lg font-semibold text-gray-900">
                  Rp {{ trx.total_harga ? trx.total_harga.toLocaleString("id-ID") : '0' }}
                </p>
              </div>
              <router-link 
                :to="{ name: 'dashboard.transaksi.detail', params: { id: trx.id } }"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-md hover:shadow-lg"
              >
                <span class="mr-2">👁️</span>
                Detail
              </router-link>
            </div>
          </div>
        </div>
      </div> -->

      <!-- Footer Stats -->
      <!-- <div v-if="!loading && transaksis.length > 0" class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center">
            <div class="text-3xl mr-4">💰</div>
            <div>
              <p class="text-sm font-medium text-gray-600">Total Paid</p>
              <p class="text-xl font-bold text-green-600">
                {{ transaksis.filter(t => t.status_payment === 'paid').length }}
              </p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center">
            <div class="text-3xl mr-4">⏳</div>
            <div>
              <p class="text-sm font-medium text-gray-600">Pending</p>
              <p class="text-xl font-bold text-yellow-600">
                {{ transaksis.filter(t => t.status_payment !== 'paid').length }}
              </p>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center">
            <div class="text-3xl mr-4">📊</div>
            <div>
              <p class="text-sm font-medium text-gray-600">Total Revenue</p>
              <p class="text-xl font-bold text-blue-600">
                Rp {{ transaksis.reduce((sum, t) => sum + (t.total_harga || 0), 0).toLocaleString('id-ID') }}
              </p>
            </div>
          </div>
        </div>
      </div> -->
    </div>
  </div>
</template>