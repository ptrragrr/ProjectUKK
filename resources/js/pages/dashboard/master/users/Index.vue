<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", {
        header: () => h("div", { class: "text-center fw-bold" }, "NO"),
        cell: (info) =>
            h("div", { class: "text-center fw-bold text-primary" }, info.row.index + 1),
    }),
    column.accessor("name", {
        header: () => h("div", { class: "fw-bold" }, "Nama"),
        cell: ({ getValue }) =>
            h("div", { class: "fw-bold text-gray-800" }, getValue()),
    }),
    column.accessor("email", {
        header: () => h("div", { class: "fw-bold" }, "Email"),
        cell: ({ getValue }) =>
            h("div", { class: "text-muted" }, getValue()),
    }),
    column.accessor("phone", {
        header: () => h("div", { class: "fw-bold" }, "No. Telp"),
        cell: ({ getValue }) =>
            h("div", { class: "text-muted" }, getValue() || "-"),
    }),
    column.accessor("uuid", {
        header: () => h("div", { class: "text-center fw-bold" }, "Aksi"),
        cell: (cell) =>
            h("div", { class: "d-flex gap-2 justify-content-center" }, [
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-light-primary",
                        onClick: () => {
                            selected.value = cell.getValue();
                            openForm.value = true;
                        },
                        title: "Edit User",
                    },
                    h("i", { class: "la la-pencil fs-3" })
                ),
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-light-danger",
                        onClick: () =>
                            deleteUser(`/master/users/${cell.getValue()}`),
                        title: "Hapus User",
                    },
                    h("i", { class: "la la-trash fs-3" })
                ),
            ]),
    }),
];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = "";
    }
    window.scrollTo({ top: 0, behavior: "smooth" });
});
</script>

<template>
    <!-- Form Section dengan Animasi -->
    <transition name="slide-fade">
        <Form
            :selected="selected"
            @close="openForm = false"
            v-if="openForm"
            @refresh="refresh"
            class="mb-6"
        />
    </transition>

    <!-- Main Card dengan Design Modern -->
    <div class="card shadow-sm border-0 overflow-hidden">
        <!-- Header dengan Gradient -->
        <div class="card-header bg-gradient-primary border-0 py-6 px-6">
            <div class="d-flex align-items-center justify-content-between w-100 gap-4">
                <!-- Title Section -->
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-50px bg-white bg-opacity-20 rounded">
                        <i class="la la-users fs-2x text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-white mb-1 fw-bold fs-1">Manajemen Users</h2>
                        <p class="text-white text-opacity-75 mb-0 fs-6">
                            Kelola semua pengguna sistem
                        </p>
                    </div>
                </div>

                <!-- Action Button -->
                <button
                    type="button"
                    class="btn btn-light btn-lg shadow-sm"
                    v-if="!openForm"
                    @click="openForm = true"
                >
                    <i class="la la-plus fs-3 me-2"></i>
                    Tambah User
                </button>
            </div>
        </div>

        <!-- Table Body dengan Styling Modern -->
        <div class="card-body p-6">
            <!-- Info Banner -->
            <div class="alert alert-primary d-flex align-items-center border-0 bg-light-primary mb-6" v-if="!openForm">
                <i class="la la-info-circle fs-2x text-primary me-4"></i>
                <div class="text-primary">
                    <div class="fw-bold mb-1">Informasi</div>
                    <div class="fs-7">Klik tombol "Tambah User" untuk menambahkan pengguna baru. Gunakan tombol aksi untuk mengedit atau menghapus user.</div>
                </div>
            </div>

            <!-- Search Bar and Filters -->
            <div class="card border mb-6" style="border-radius: 8px;">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <!-- Search Input -->
                        <div class="col-md-6">
                            <div class="position-relative">
                                <i class="la la-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #a1a5b7; font-size: 1.25rem;"></i>
                                <input
                                    type="text"
                                    class="form-control ps-10"
                                    placeholder="Cari nama, email, atau nomor telepon..."
                                    style="border-radius: 8px; padding-left: 2.5rem;"
                                />
                            </div>
                        </div>

                        <!-- Entries per page -->
                        <!-- <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="text-muted fs-7 text-nowrap mb-0">Tampilkan:</label>
                                <select class="form-select form-select-sm" style="border-radius: 8px; min-width: 80px;">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="25">5250</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div> -->

                        <!-- Filter Button -->
                        <!-- <div class="col-md-3">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-light-primary btn-sm" style="border-radius: 8px;">
                                    <i class="la la-filter fs-4 me-1"></i>
                                    Filter
                                </button>
                                <button class="btn btn-light-secondary btn-sm" style="border-radius: 8px;" @click="refresh" title="Refresh Data">
                                    <i class="la la-sync fs-4"></i>
                                </button>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Table Wrapper -->
            <div class="table-responsive rounded border">
                <paginate
                    ref="paginateRef"
                    id="table-users"
                    url="/master/users"
                    :columns="columns"
                />
            </div>
        </div>

        <!-- Footer dengan Info -->
        <div class="card-footer border-0 bg-light py-4 px-6">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="text-muted fs-7">
                    <i class="la la-info-circle me-1"></i>
                    Data diperbarui secara real-time
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Gradient Background */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Card Animations */
.card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

/* Slide Fade Animation untuk Form */
.slide-fade-enter-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 1, 1);
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.98);
}

/* Button Effects */
.btn {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-icon {
    width: 35px;
    height: 35px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Symbol/Icon Containers */
.symbol {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Table Styling */
.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

:deep(.table) {
    margin-bottom: 0;
}

:deep(.table thead th) {
    background-color: #f8f9fa;
    border-bottom: 2px solid #e4e6ef;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: #3f4254;
}

:deep(.table tbody td) {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
}

:deep(.table tbody tr) {
    transition: all 0.2s ease;
}

:deep(.table tbody tr:hover) {
    background-color: #f8f9fb;
    transform: scale(1.001);
}

/* Alert Styling */
.alert {
    border-radius: 8px;
    border-left: 4px solid;
}

.alert-primary {
    border-left-color: #667eea;
}

/* Form Control Enhancement */
.form-control:focus,
.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.ps-10 {
    padding-left: 2.5rem !important;
}

/* Button Color Variants */
.btn-light {
    background: white;
    color: #667eea;
    font-weight: 600;
    border: none;
}

.btn-light:hover {
    background: #f8f9fa;
    color: #667eea;
}

.btn-light-primary {
    background-color: #e1e8ff;
    color: #667eea;
    border: none;
}

.btn-light-primary:hover {
    background-color: #667eea;
    color: white;
}

.btn-light-danger {
    background-color: #ffe1e1;
    color: #dc3545;
    border: none;
}

.btn-light-danger:hover {
    background-color: #dc3545;
    color: white;
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

/* Responsive Adjustments */
@media (max-width: 768px) {
    .card-header {
        padding: 1.5rem !important;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
    }
}
</style>