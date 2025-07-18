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
    header: "No",
    cell: (info) => info.row.index + 1,
  }),
  column.accessor("jenis_tiket", {
    header: "Jenis Tiket",
  }),
  column.accessor("harga_tiket", {
    header: "Harga Tiket",
    cell: ({ getValue }) => {
      const harga = getValue();
      return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
      }).format(harga).replace(/,00$/, "");
    },
  }),
  column.accessor("stok_tiket", {
    header: "Stok",
  }),
  column.accessor("konser_id", {
    header: "ID Konser",
  }),
  column.accessor("id", {
    header: "Aksi",
    cell: (cell) =>
      h("div", { class: "d-flex gap-2" }, [
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-info",
            onClick: () => {
              selected.value = cell.getValue();
              openForm.value = true;
            },
          },
          h("i", { class: "la la-pencil fs-2" })
        ),
        h(
          "button",
          {
            class: "btn btn-sm btn-icon btn-danger",
            onClick: () =>
              deleteTicket(`/tickets/${cell.getValue()}`),
          },
          h("i", { class: "la la-trash fs-2" })
        ),
      ]),
  }),
];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
  if (!val) {
    selected.value = "";
  }
  window.scrollTo(0, 0);
});
</script>

<template>
  <Form
    :selected="selected"
    @close="openForm = false"
    v-if="openForm"
    @refresh="refresh"
  />

  <div class="card">
    <div class="card-header align-items-center">
      <h2 class="mb-0">Menambahkan Tiket</h2>
      <button
        type="button"
        class="btn btn-sm btn-primary ms-auto"
        v-if="!openForm"
        @click="openForm = true"
      >
        Tambah
        <i class="la la-plus"></i>
      </button>
    </div>
    <div class="card-body">
      <paginate
        ref="paginateRef"
        id="table-tickets"
        url="/tickets"
        :columns="columns"
      />
    </div>
  </div>
</template>
