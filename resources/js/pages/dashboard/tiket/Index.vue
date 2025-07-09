<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { Barang } from "@/types";

const column = createColumnHelper<Barang>();
const paginateRef = ref<any>(null);
const selected = ref<string>("");
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

function resolveImageUrl(foto: string): string {
    if (!foto) return "";

    // Jika sudah URL absolut
    if (foto.startsWith("http")) return foto;

    // Pastikan path tidak double dan bisa digunakan langsung
    if (foto.startsWith("/storage")) {
        return `${window.location.origin}${foto}`;
    }

    return `${window.location.origin}/storage/${foto}`;
}

// function resolveImageUrl(foto: string): string {
//     if (!foto) return "";

//     // Jika sudah URL absolut
//     if (foto.startsWith("http")) return foto;

//     // Jika masih nama file atau path lokal
//     return `${window.location.origin}${foto.startsWith("/storage") ? foto : "/storage/" + foto}`;
// }

const columns = [
    column.accessor("no", {
        header: "No",
    }),
    column.accessor("nama_destinasi", {
        header: "Nama Destinasi",
    }),
    // column.accessor("kategori", {
    //     header: "Kategori Tiket",
    // }),
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
//     column.accessor("foto_destinasi", {
//     header: "Foto Destinasi",
//     cell: ({ getValue }) => {
//         const url = resolveImageUrl(getValue());
//         return h("img", {
//             src: url,
//             alt: "Foto Destinasi",
//             style: "width: 60px; height: 60px; object-fit: cover; border-radius: 4px;",
//         });
//     },
// }),

    column.accessor("stok_tiket", {
        header: "Stok Tiket",
    }),
    // column.accessor("foto_barang", {
    //     header: "Foto Barang",
    // }),
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
                            deleteUser(`/tickets/${cell.getValue()}`)
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
            ></paginate>
        </div>
    </div>
</template>
