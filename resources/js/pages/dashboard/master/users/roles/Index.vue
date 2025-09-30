<script setup lang="ts">
import { h, ref, watch } from "vue";
import { useDelete } from "@/libs/hooks";
import Form from "./Form.vue";
import { createColumnHelper } from "@tanstack/vue-table";
import type { User } from "@/types";

const column = createColumnHelper<User>();
const paginateRef = ref<any>(null);
const selected = ref<number>(0);
const openForm = ref<boolean>(false);

const { delete: deleteUser } = useDelete({
    onSuccess: () => paginateRef.value.refetch(),
});

const columns = [
    column.accessor("no", {
        header: "NO",
        cell: (cell) => cell.row.index + 1,
    }),
        column.accessor("name", {
        header: "Nama",
    }),
    column.accessor("full_name", {
        header: "full name",
    }),
    column.accessor("id", {
        header: "AKSI",
        cell: (cell) =>
            h("div", { class: "d-flex gap-2" }, [
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-light-primary",
                        onClick: () => {
                            selected.value = cell.getValue();
                            openForm.value = true;
                        },
                    },
                    h("i", { class: "la la-pencil fs-4" })
                ),
                h(
                    "button",
                    {
                        class: "btn btn-sm btn-icon btn-light-danger",
                        onClick: () =>
                            deleteUser(`/master/roles/${cell.getValue()}`),
                    },
                    h("i", { class: "la la-trash fs-4" })
                ),
            ]),
    }),
];

const refresh = () => paginateRef.value.refetch();

watch(openForm, (val) => {
    if (!val) {
        selected.value = 0;
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
    <div class="card" v-else>
        <!-- Header dengan gradient ungu -->
        <div 
            class="card-header d-flex align-items-center"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 80px; border: none;"
        >
            <div class="d-flex align-items-center gap-3">
                <div class="symbol symbol-50px" style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 10px;">
                    <i class="la la-ticket fs-2x text-white"></i>
                </div>
                <div>
                    <h2 class="mb-0 text-white fw-bold">Manajemen Roles</h2>
                    <!-- <p class="mb-0 text-white opacity-75 fs-7">Kelola Roles</p> -->
                </div>
            </div>
            <button
                type="button"
                class="btn btn-light ms-auto"
                @click="openForm = true"
            >
                <i class="la la-plus"></i>
                Tambah Roles
            </button>
        </div>

        <div class="card-body">
            <!-- Info Box -->
            <div class="alert alert-info d-flex align-items-center mb-6" style="background-color: #e8f4f8; border: 1px solid #bee5eb; border-radius: 8px;">
                <i class="la la-info-circle fs-2 text-info me-3"></i>
                <div>
                    <strong class="text-info">Informasi</strong>
                    <p class="mb-0 text-info">
                        Klik tombol "Tambah Roles" untuk menambahkan roles. Gunakan tombol aksi untuk mengedit atau menghapus roles.
                    </p>
                </div>
            </div>

            <!-- Table -->
            <paginate
                ref="paginateRef"
                id="table-role"
                url="/master/roles"
                :columns="columns"
            ></paginate>
        </div>
    </div>
</template>

<style scoped>
.card {
    border: none;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
    border-radius: 12px;
}

.card-header {
    border-bottom: none;
    padding: 1.5rem 2rem;
}

.card-body {
    padding: 2rem;
}

.btn-light {
    background: white;
    color: #667eea;
    font-weight: 600;
    padding: 0.65rem 1.5rem;
    border-radius: 8px;
    border: none;
}

.btn-light:hover {
    background: #f8f9fa;
    color: #667eea;
}

.badge {
    padding: 0.5rem 1rem;
    font-weight: 600;
    font-size: 0.8rem;
    border-radius: 6px;
}

.badge-light-success {
    background-color: #d4f4dd;
    color: #0a7b2e;
}

.badge-light-primary {
    background-color: #e1e8ff;
    color: #3f51b5;
}

.badge-light-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.badge-light-warning {
    background-color: #fff3cd;
    color: #856404;
}

.btn-icon {
    width: 35px;
    height: 35px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
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

.btn-light-danger {
    background-color: #ffe1e1;
    color: #dc3545;
    border: none;
}

.btn-light-danger:hover {
    background-color: #dc3545;
    color: white;
}
</style>