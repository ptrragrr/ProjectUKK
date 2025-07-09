<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";          // masih dipakai bila admin boleh menghapus order
// import DetailOrder from "./DetailOrder.vue";    // contoh komponen detail ↙︎
import { createColumnHelper } from "@tanstack/vue-table";
import type { Order } from "@/types";              // pastikan interface Order sudah memuat properti di bawah

/* -------------------------------------------------
   STATE & HOOK
------------------------------------------------- */
const column = createColumnHelper<Order>();

const paginateRef = ref<any>(null);
const selected   = ref<string>("");     // id order dipilih (untuk modal detail)
const openDetail = ref<boolean>(false); // true = tampilkan modal/detail

// opsi: kalau admin tak perlu delete, boleh dihapus useDelete-nya
const { delete: deleteOrder } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

/* -------------------------------------------------
   UTIL
------------------------------------------------- */
function resolveImageUrl(img: string): string {
  if (!img) return "";
  if (img.startsWith("http"))      return img;
  if (img.startsWith("/storage"))  return `${window.location.origin}${img}`;
  return `${window.location.origin}/storage/${img}`;
}

/* -------------------------------------------------
   TABLE COLUMNS
------------------------------------------------- */
const columns = [
  column.accessor("no",             { header: "No" }),
  column.accessor("kode_booking",   { header: "Kode Booking" }),
  column.accessor("nama_pemesan",   { header: "Pemesan" }),
  column.accessor("nama_destinasi", { header: "Destinasi" }),
  column.accessor("jumlah_tiket",   { header: "Qty" }),
  column.accessor("total_harga", {
    header: "Total Harga",
    cell: ({ getValue }) =>
      new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" })
        .format(getValue())
        .replace(/,00$/, ""),
  }),
  column.accessor("status_pembayaran", {
    header: "Status Bayar",
    // badge hijau / merah kecil
    cell: ({ getValue }) => {
      const paid  = getValue() === "PAID";
      const label = paid ? "Lunas" : "Belum Bayar";
      const cls   = paid ? "badge badge-success" : "badge badge-danger";
      return h("span", { class: cls }, label);
    },
  }),
  column.accessor("bukti_pembayaran", {
    header: "Bukti",
    cell: ({ getValue }) => {
      const url = resolveImageUrl(getValue());
      return url
        ? h("img", {
            src: url,
            alt: "Bukti Bayar",
            style:
              "width: 60px; height: 60px; object-fit: cover; border-radius: 4px;",
          })
        : "-";
    },
  }),
  column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        // tombol detail
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-info",
            onClick: () => {
              selected.value  = cell.getValue();
              openDetail.value = true;
            },
          },
          h("i", { class: "la la-eye fs-2" })
        ),
        // contoh tombol delete, opsional
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () => deleteOrder(`/api/order/${cell.getValue()}`), // ✅ benar
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      ]),
  }),
];

/* -------------------------------------------------
   MISC
------------------------------------------------- */
const refresh = () => paginateRef.value.refetch();

watch(openDetail, (val) => {
  if (!val) selected.value = "";
  window.scrollTo(0, 0);
});
</script>

<template>
  <!-- ===== MODAL / SIDEPANEL DETAIL ORDER ===== -->
  <!--
  <DetailOrder
    :id="selected"
    v-if="openDetail"
    @close="openDetail = false"
    @refresh="refresh"
  />
  -->

  <div class="card">
    <div class="card-header align-items-center">
      <h2 class="mb-0">Daftar Order Masuk</h2>
      <!-- tak ada tombol “Tambah” karena order dibuat user -->
    </div>

    <div class="card-body">
      <paginate
        ref="paginateRef"
        id="table-orders"
        url="/api/orders"          
        :columns="columns"
      />
    </div>
  </div>
</template>
