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

// Filter variables
const selectedYear = ref<number | null>(null);
const selectedMonth = ref<number | null>(null);
const searchQuery = ref("");
const perPage = ref(10);
const currentPage = ref(1);

// Generate years array (2023 - current year)
const years = computed(() => {
    const currentYear = new Date().getFullYear();
    const yearsList = [];
    for (let year = 2023; year <= currentYear; year++) {
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

// Paginated data
const paginatedTransaksis = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return filteredTransaksis.value.slice(start, end);
});

// Total pages
const totalPages = computed(() => {
    return Math.ceil(filteredTransaksis.value.length / perPage.value);
});

// Pagination info
const paginationInfo = computed(() => {
    const start = (currentPage.value - 1) * perPage.value + 1;
    const end = Math.min(currentPage.value * perPage.value, filteredTransaksis.value.length);
    const total = filteredTransaksis.value.length;
    return { start, end, total };
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

// const fetchTransaksis = async () => {
//     try {
//         const res = await axios.get("/transaksi");
//         transaksis.value = res.data;
//     } catch (err) {
//         console.error(err);
//     } finally {
//         loading.value = false;
//     }
// };

const fetchTransaksis = async () => {
    try {
        const res = await axios.get("/transaksi");

        // pastikan hasilnya array, walau API kadang bungkus dengan "data"
        if (Array.isArray(res.data)) {
            transaksis.value = res.data;
        } else if (Array.isArray(res.data.data)) {
            transaksis.value = res.data.data;
        } else {
            transaksis.value = [];
        }

        console.log("Transaksi:", transaksis.value);
    } catch (err) {
        console.error("Gagal memuat transaksi:", err);
        transaksis.value = [];
    } finally {
        loading.value = false;
    }
};

const clearFilters = () => {
    selectedYear.value = null;
    selectedMonth.value = null;
    searchQuery.value = "";
    currentPage.value = 1;
};

const changePage = (page: number) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};

const clearSearch = () => {
    searchQuery.value = "";
    currentPage.value = 1;
};

// Watch for filter changes to reset page
const resetPage = () => {
    currentPage.value = 1;
};

onMounted(fetchTransaksis);

// const getStatusClass = (status: string) => {
//     return status === "paid"
//         ? "badge-light-success"
//         : "badge-light-warning";
// };
const getStatusClass = (status: string) => {
    if (status === "paid") return "badge-light-success";
    if (status === "pending") return "badge-light-warning";
    if (status === "cancelled") return "badge-light-danger";
    return "badge-light-warning"; // default
};
</script>

<template>
    <div class="container-fluid px-4 py-6">
        <!-- Header dengan Gradient -->
        <div class="card mb-6" style="border: none; box-shadow: 0 0 20px rgba(0, 0, 0, 0.08); border-radius: 12px;">
            <div 
                class="card-header d-flex align-items-center"
                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 80px; border: none; border-radius: 12px 12px 0 0;"
            >
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-50px" style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px;">
                        <i class="la la-shopping-cart fs-2x text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-0 text-white fw-bold">Dashboard Transaksi</h2>
                        <p class="mb-0 text-white opacity-75 fs-7">Kelola dan pantau semua transaksi Anda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="d-flex justify-content-center align-items-center py-20">
            <div class="text-center">
                <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted">Memuat data transaksi...</p>
            </div>
        </div>

        <!-- Main Content -->
        <div v-else>
            <!-- Stats Cards -->
            <div class="row g-4 mb-6">
                <div class="col-md-3">
                    <div class="card h-100" style="border: none; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 fs-7">Total Transaksi</p>
                                    <h3 class="mb-0 fw-bold">{{ stats.totalTransactions }}</h3>
                                </div>
                                <div class="symbol symbol-50px" style="background: #e8f4f8; border-radius: 10px;">
                                    <i class="la la-receipt fs-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100" style="border: none; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 fs-7">Paid</p>
                                    <h3 class="mb-0 fw-bold text-success">{{ stats.paidTransactions }}</h3>
                                </div>
                                <div class="symbol symbol-50px" style="background: #d4f4dd; border-radius: 10px;">
                                    <i class="la la-check-circle fs-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100" style="border: none; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 fs-7">Pending</p>
                                    <h3 class="mb-0 fw-bold text-warning">{{ stats.pendingTransactions }}</h3>
                                </div>
                                <div class="symbol symbol-50px" style="background: #fff3cd; border-radius: 10px;">
                                    <i class="la la-clock fs-2x text-warning"></i>
                                </div>
                            </div>

                             <!-- <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 fs-7">Cancelled</p>
                                    <h3 class="mb-0 fw-bold text-warning">{{ stats.pendingTransactions }}</h3>
                                </div>
                                <div class="symbol symbol-50px" style="background: #fff3cd; border-radius: 10px;">
                                    <i class="la la-clock fs-2x text-warning"></i>
                                </div>
                            </div> -->

                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100" style="border: none; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted mb-1 fs-7">Total Pendapatan</p>
                                    <h3 class="mb-0 fw-bold text-primary">Rp {{ stats.totalRevenue.toLocaleString("id-ID") }}</h3>
                                </div>
                                <div class="symbol symbol-50px" style="background: #e1e8ff; border-radius: 10px;">
                                    <i class="la la-money-bill-wave fs-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="card mb-6" style="border: none; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-auto">
                            <h5 class="mb-0 fw-semibold">Filter Data</h5>
                        </div>
                        <div class="col-md-auto">
                            <select v-model="selectedYear" @change="resetPage" class="form-select" style="min-width: 150px; border-radius: 8px;">
                                <option :value="null">Semua Tahun</option>
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <select v-model="selectedMonth" @change="resetPage" class="form-select" style="min-width: 150px; border-radius: 8px;">
                                <option :value="null">Semua Bulan</option>
                                <option v-for="month in months" :key="month.value" :value="month.value">{{ month.label }}</option>
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <select v-model="perPage" @change="resetPage" class="form-select" style="min-width: 120px; border-radius: 8px;">
                                <option :value="5">5 per halaman</option>
                                <option :value="10">10 per halaman</option>
                                <option :value="25">25 per halaman</option>
                                <option :value="50">50 per halaman</option>
                            </select>
                        </div>
                        <div class="col-md ms-auto">
                            <div class="position-relative">
                                <input
                                    v-model="searchQuery"
                                    @input="resetPage"
                                    type="text"
                                    placeholder="Cari nama, email, atau kode transaksi..."
                                    class="form-control"
                                    style="border-radius: 8px; padding-right: 2.5rem;"
                                />
                                <button
                                    v-if="searchQuery"
                                    @click="clearSearch"
                                    class="btn btn-sm btn-icon position-absolute"
                                    style="right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none;"
                                    title="Clear search"
                                >
                                    <i class="la la-times text-muted"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredTransaksis.length === 0" class="text-center py-16">
                <div class="card mx-auto" style="max-width: 500px; border: none; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                    <div class="card-body p-12">
                        <div class="fs-1 mb-4">📭</div>
                        <h3 class="fw-semibold mb-2">
                            {{ selectedYear || selectedMonth || searchQuery ? "Tidak Ada Hasil" : "Belum Ada Transaksi" }}
                        </h3>
                        <p class="text-muted mb-4">
                            {{ selectedYear || selectedMonth || searchQuery ? "Coba ubah filter atau kata kunci pencarian" : "Data transaksi akan muncul di sini" }}
                        </p>
                        <button
                            v-if="selectedYear || selectedMonth || searchQuery"
                            @click="clearFilters"
                            class="btn btn-primary"
                            style="border-radius: 8px;"
                        >
                            Reset Semua Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div v-else class="card" style="border: none; box-shadow: 0 0 15px rgba(0, 0, 0, 0.05); border-radius: 12px;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-bordered align-middle gy-4 gs-9 mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr class="fw-bold text-muted">
                                    <th class="ps-6 py-4">NO</th>
                                    <th class="py-4">ID</th>
                                    <th class="py-4">Pembeli</th>
                                    <th class="py-4">Kontak</th>
                                    <th class="py-4">Kode Transaksi</th>
                                    <th class="py-4">Status</th>
                                    <th class="py-4">Total</th>
                                    <th class="py-4">Tanggal</th>
                                    <th class="pe-6 py-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(trx, index) in paginatedTransaksis" :key="trx.id" class="hover-bg-light">
                                    <td class="ps-6">
                                        <span class="text-muted">{{ (currentPage - 1) * perPage + index + 1 }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-gray-800">#{{ trx.id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-semibold text-gray-800">{{ trx.nama_pembeli }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-gray-800 fs-7">{{ trx.email }}</div>
                                        <div class="text-muted fs-7">{{ trx.nomer_telpon }}</div>
                                    </td>
                                    <td>
                                        <code class="badge badge-light text-gray-800">{{ trx.kode_transaksi }}</code>
                                    </td>
                                    <td>
                                        <span :class="['badge', getStatusClass(trx.status_payment)]" style="padding: 0.5rem 1rem; font-weight: 600; font-size: 0.8rem; border-radius: 6px;">
                                            {{ trx.status_payment }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-gray-800">
                                            Rp {{ trx.total_harga ? trx.total_harga.toLocaleString("id-ID") : "0" }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-gray-800 fs-7">
                                            {{ new Date(trx.created_at).toLocaleDateString("id-ID", {
                                                year: "numeric",
                                                month: "short",
                                                day: "numeric",
                                            }) }}
                                        </div>
                                    </td>
                                    <td class="pe-6 text-end">
                                        <button
                                            @click="lihatDetail(trx.id)"
                                            class="btn btn-sm btn-light-primary"
                                            style="border-radius: 6px; font-weight: 600;"
                                        >
                                            <i class="la la-eye fs-4 me-1"></i>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer border-0 bg-light py-4 px-6">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="text-muted fs-7">
                                Menampilkan {{ paginationInfo.start }} - {{ paginationInfo.end }} dari {{ paginationInfo.total }} data
                            </div>
                            <div class="d-flex gap-2">
                                <button
                                    @click="changePage(currentPage - 1)"
                                    :disabled="currentPage === 1"
                                    class="btn btn-sm btn-light"
                                    style="border-radius: 6px;"
                                >
                                    <i class="la la-angle-left"></i>
                                </button>
                                
                                <button
                                    v-for="page in totalPages"
                                    :key="page"
                                    v-show="Math.abs(page - currentPage) < 3 || page === 1 || page === totalPages"
                                    @click="changePage(page)"
                                    :class="['btn btn-sm', page === currentPage ? 'btn-primary' : 'btn-light']"
                                    style="border-radius: 6px; min-width: 40px;"
                                >
                                    {{ page }}
                                </button>
                                
                                <button
                                    @click="changePage(currentPage + 1)"
                                    :disabled="currentPage === totalPages"
                                    class="btn btn-sm btn-light"
                                    style="border-radius: 6px;"
                                >
                                    <i class="la la-angle-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.hover-bg-light:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.badge-light-success {
    background-color: #d4f4dd;
    color: #0a7b2e;
}

.badge-light-warning {
    background-color: #fff3cd;
    color: #856404;
}

.btn-light-primary {
    background-color: #e1e8ff;
    color: #3f51b5;
    border: none;
}

.btn-light-primary:hover {
    background-color: #667eea;
    color: white;
}

.form-select:focus,
.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background-color: #667eea;
    border-color: #667eea;
}

.btn-primary:hover {
    background-color: #5568d3;
    border-color: #5568d3;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>