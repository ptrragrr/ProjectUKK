<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Ticket } from "@/types";

const column = createColumnHelper<Ticket>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteTicket } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
  column.display({
    id: "no",
    header: () => h("div", { class: "text-center fw-bold" }, "No"),
    cell: (info) =>
      h("div", { class: "text-center fw-bold text-primary" }, info.row.index + 1),
  }),
  column.accessor("nama_event", {
    header: () => h("div", { class: "fw-bold" }, "Nama Event"),
    cell: ({ getValue }) =>
      h("div", { class: "fw-bold text-gray-800" }, getValue()),
  }),
  column.accessor("tanggal", {
    header: () => h("div", { class: "fw-bold" }, "Tanggal"),
    cell: ({ getValue }) =>
      h("div", { class: "text-muted" }, new Date(getValue()).toLocaleDateString("id-ID")),
  }),
  column.accessor("harga_tiket", {
    header: () => h("div", { class: "fw-bold" }, "Harga"),
    cell: ({ getValue }) => {
      const formatted = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
      }).format(getValue()).replace(/,00$/, "");
      return h("span", { class: "badge badge-light-success fw-bold" }, formatted);
    },
  }),
  column.accessor("jenis_tiket", {
    header: () => h("div", { class: "fw-bold" }, "Jenis Tiket"),
    cell: ({ getValue }) =>
      h("span", { class: "badge badge-light-primary fw-bold" }, getValue()),
  }),
  column.accessor("deskripsi", {
    header: () => h("div", { class: "fw-bold" }, "Line up Artis"),
    cell: ({ getValue }) =>
      h("div", { class: "text-muted fs-7" }, getValue() || "-"),
  }),
  column.accessor("id", {
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
            title: "Edit Tiket",
          },
          h("i", { class: "la la-pencil fs-3" })
        ),
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-light-danger",
            onClick: () =>
              deleteTicket(`/tickets/${cell.getValue()}`),
            title: "Hapus Tiket",
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
    <!-- Header dengan Gradient dan Stats -->
    <div class="card-header bg-gradient-primary border-0 py-6 px-6">
      <div class="d-flex align-items-center justify-content-between w-100 gap-4">
        <!-- Title Section -->
        <div class="d-flex align-items-center gap-3">
          <div class="symbol symbol-50px bg-white bg-opacity-20 rounded">
            <i class="la la-ticket-alt fs-2x text-white"></i>
          </div>
          <div>
            <h2 class="text-white mb-1 fw-bold fs-1">Manajemen Tiket</h2>
            <p class="text-white text-opacity-75 mb-0 fs-6">
              Kelola semua tiket konser Anda
            </p>
          </div>
        </div>

        <!-- Action Button - Positioned at the right -->
        <button
          type="button"
          class="btn btn-light btn-lg shadow-sm"
          v-if="!openForm"
          @click="openForm = true"
        >
          <i class="la la-plus fs-3 me-2"></i>
          Tambah Tiket Baru
        </button>
      </div>

      <!-- Stats Cards -->
      <!-- <div class="row g-4 mt-2">
        <div class="col-md-4">
          <div class="card bg-white bg-opacity-10 border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-3">
                <div class="symbol symbol-45px bg-white bg-opacity-20 rounded">
                  <i class="la la-tags fs-2x text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <span class="text-white text-opacity-75 fs-7 d-block">Total Tiket</span>
                  <span class="text-white fw-bold fs-3">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-white bg-opacity-10 border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-3">
                <div class="symbol symbol-45px bg-white bg-opacity-20 rounded">
                  <i class="la la-cubes fs-2x text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <span class="text-white text-opacity-75 fs-7 d-block">Stok Tersedia</span>
                  <span class="text-white fw-bold fs-3">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-white bg-opacity-10 border-0 shadow-sm">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-3">
                <div class="symbol symbol-45px bg-white bg-opacity-20 rounded">
                  <i class="la la-music fs-2x text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <span class="text-white text-opacity-75 fs-7 d-block">Konser Aktif</span>
                  <span class="text-white fw-bold fs-3">-</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> -->
    </div>

    <!-- Table Body dengan Styling Modern -->
    <div class="card-body p-6">
      <!-- Info Banner -->
      <div class="alert alert-primary d-flex align-items-center border-0 bg-light-primary mb-6" v-if="!openForm">
        <i class="la la-info-circle fs-2x text-primary me-4"></i>
        <div class="text-primary">
          <div class="fw-bold mb-1">Informasi</div>
          <div class="fs-7">Klik tombol "Tambah Tiket Baru" untuk menambahkan tiket konser. Gunakan tombol aksi untuk mengedit atau menghapus tiket.</div>
        </div>
      </div>

      <!-- Table Wrapper -->
      <div class="table-responsive rounded border">
        <paginate
          ref="paginateRef"
          id="table-tickets"
          url="/tickets"
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
        <div class="d-flex gap-2">
          <button 
            class="btn btn-sm btn-light-primary"
            @click="refresh"
            title="Refresh Data"
          >
            <i class="la la-sync"></i>
          </button>
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

.symbol-circle {
    border-radius: 50%;
}

/* Badge Enhancements */
.badge {
    border-radius: 6px;
    font-weight: 600;
    letter-spacing: 0.5px;
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

/* Stats Cards in Header */
.card-header .card {
    transition: all 0.3s ease;
}

.card-header .card:hover {
    transform: translateY(-2px);
    background-color: rgba(255, 255, 255, 0.15) !important;
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
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

/* Loading State Enhancement */
:deep(.loading-overlay) {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(4px);
}

/* Empty State */
:deep(.empty-state) {
    padding: 3rem;
    text-align: center;
}

:deep(.empty-state i) {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 1rem;
}
</style>