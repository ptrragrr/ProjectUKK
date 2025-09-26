<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import axios from "@/libs/axios";
import router from "@/router";

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
const viewMode = ref<"grid" | "table">("table");

// Filter variables
const selectedYear = ref<number | null>(null);
const selectedMonth = ref<number | null>(null);
const searchQuery = ref("");

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
    { value: 1, label: "Januari" },
    { value: 2, label: "Februari" },
    { value: 3, label: "Maret" },
    { value: 4, label: "April" },
    { value: 5, label: "Mei" },
    { value: 6, label: "Juni" },
    { value: 7, label: "Juli" },
    { value: 8, label: "Agustus" },
    { value: 9, label: "September" },
    { value: 10, label: "Oktober" },
    { value: 11, label: "November" },
    { value: 12, label: "Desember" },
];

// Filtered transactions with search
const filteredTransaksis = computed(() => {
    let filtered = transaksis.value;

    // Filter by search query
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (trx) =>
                trx.nama_pembeli.toLowerCase().includes(query) ||
                trx.email.toLowerCase().includes(query) ||
                trx.kode_transaksi.toLowerCase().includes(query)
        );
    }

    // Filter by year
    if (selectedYear.value) {
        filtered = filtered.filter((trx) => {
            const trxYear = new Date(trx.created_at).getFullYear();
            return trxYear === selectedYear.value;
        });
    }

    // Filter by month
    if (selectedMonth.value) {
        filtered = filtered.filter((trx) => {
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
    const paidTransactions = filtered.filter(
        (t) => t.status_payment === "paid"
    ).length;
    const pendingTransactions = filtered.filter(
        (t) => t.status_payment === "pending"
    ).length;
    const totalRevenue = filtered
        .filter((t) => t.status_payment === "paid")
        .reduce((sum, t) => sum + t.total_harga, 0);

    return {
        totalTransactions,
        paidTransactions,
        pendingTransactions,
        totalRevenue,
    };
});

const lihatDetail = (id: number) => {
    router.push({
        name: "dashboard.transaksi.detail",
        params: { id },
    });
};

const fetchTransaksis = async () => {
    try {
        const res = await axios.get("/transaksi");
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
    searchQuery.value = "";
};

onMounted(fetchTransaksis);

const getStatusClass = (status: string) => {
    return status === "paid"
        ? "bg-green-100 text-green-800 border border-green-200"
        : "bg-yellow-100 text-yellow-800 border border-yellow-200";
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Dashboard Transaksi
                </h1>
                <p class="text-gray-600">
                    Kelola dan pantau semua transaksi Anda
                </p>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-32">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                    <p class="text-gray-600">Memuat data transaksi...</p>
                </div>
            </div>

            <!-- Main Content -->
            <div v-else class="space-y-6">

                <!-- Filters -->
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <h3 class="text-lg font-semibold text-gray-900">Filter Data</h3>
                            
                            <div class="flex flex-wrap gap-3">
                                <!-- Year Filter -->
                                <select
                                    v-model="selectedYear"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option :value="null">Semua Tahun</option>
                                    <option v-for="year in years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>

                                <!-- Month Filter -->
                                <select
                                    v-model="selectedMonth"
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option :value="null">Semua Bulan</option>
                                    <option v-for="month in months" :key="month.value" :value="month.value">
                                        {{ month.label }}
                                    </option>
                                </select>

                                <!-- Clear Filters -->
                                <button
                                    v-if="selectedYear || selectedMonth || searchQuery"
                                    @click="clearFilters"
                                    class="px-4 py-2 text-black-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                                >
                                    Reset
                                </button>
                            </div>
                        </div>

                        <!-- Search -->
                        <div class="flex justify-end">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari transaksi..."
                                class="w-80 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredTransaksis.length === 0" class="text-center py-16">
                    <div class="bg-white rounded-xl shadow-sm border p-12 max-w-md mx-auto">
                        <div class="text-6xl mb-4">📭</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ selectedYear || selectedMonth || searchQuery ? "Tidak Ada Hasil" : "Belum Ada Transaksi" }}
                        </h3>
                        <p class="text-gray-600 mb-6">
                            {{ selectedYear || selectedMonth || searchQuery ? "Coba ubah filter atau kata kunci pencarian" : "Data transaksi akan muncul di sini" }}
                        </p>
                        <button
                            v-if="selectedYear || selectedMonth || searchQuery"
                            @click="clearFilters"
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-black rounded-lg transition-colors"
                        >
                            Reset Semua Filter
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div v-else class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">ID</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Pembeli</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Kontak</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Kode Transaksi</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Total</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="trx in filteredTransaksis" :key="trx.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">#{{ trx.id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ trx.nama_pembeli.charAt(0).toUpperCase() }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-semibold text-gray-900">{{ trx.nama_pembeli }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ trx.email }}</div>
                                        <div class="text-sm text-gray-500">{{ trx.nomer_telpon }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="text-sm bg-gray-100 px-2 py-1 rounded text-gray-800">
                                            {{ trx.kode_transaksi }}
                                        </code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', getStatusClass(trx.status_payment)]">
                                            {{ trx.status_payment }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">
                                            Rp {{ trx.total_harga ? trx.total_harga.toLocaleString("id-ID") : "0" }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ new Date(trx.created_at).toLocaleDateString("id-ID", {
                                                year: "numeric",
                                                month: "short",
                                                day: "numeric",
                                            }) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="lihatDetail(trx.id)"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-black text-sm font-medium rounded-lg transition-colors"
                                        >
                                            Detail
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
</template>