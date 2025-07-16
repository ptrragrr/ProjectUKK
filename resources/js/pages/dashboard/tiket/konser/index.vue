<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Konser } from "@/types";

const column = createColumnHelper<Konser>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteKonser } = useDelete({
  onSuccess: () => paginateRef.value.refetch(),
});

function resolveImageUrl(foto: string): string {
  if (!foto) return "";
  if (foto.startsWith("http")) return foto;
  if (foto.startsWith("/storage")) {
    return `${window.location.origin}${foto}`;
  }
  return `${window.location.origin}/storage/${foto}`;
}

// ✅ Kolom Tabel
const columns = [
  column.display({
    id: "no",
    header: "No",
    cell: (info) => info.row.index + 1,
  }),
  column.accessor("nama_konser", {
    header: "Nama Konser",
  }),
  column.accessor("lokasi", {
    header: "Lokasi",
  }),
  column.accessor("tanggal", {
    header: "Tanggal",
    cell: ({ getValue }) =>
      new Date(getValue()).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
      }),
  }),
  column.accessor("banner", {
    header: "Banner",
    cell: ({ getValue }) => {
        const url = resolveImageUrl(getValue());
        return h("img", {
            src: url,
            alt: "Banner",
            style: "width: 60px; height: 60px; object-fit: cover; border-radius: 4px;",
        });
    },
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
            onClick: () => deleteKonser(`/konser/${cell.getValue()}`),
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
      <h2 class="mb-0">Menambahkan Konser</h2>
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
        id="table-konser"
        url="/konser"
        :columns="columns"
      ></paginate>
    </div>
  </div>
</template>
