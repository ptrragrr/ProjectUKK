<script setup lang="ts">
import { ref, watch, onMounted, computed } from "vue";
import axios from "@/libs/axios";
import {
  useVueTable,
  getCoreRowModel,
  FlexRender,
} from "@tanstack/vue-table";

const props = defineProps({
  url: { type: String, required: true },
  columns: { type: Array, required: true },
  per: { type: Number, default: 10 },
  page: { type: Number, default: 1 },
});

const emit = defineEmits(["update:page"]);

const data = ref<any[]>([]);
const pagination = ref({
  total: 0,
  current_page: 1,
  per_page: props.per,
});

const loading = ref(false);

const fetchData = async (params?: { page?: number; per?: number }) => {
  loading.value = true;
  try {
    const res = await axios.get(props.url, {
      params: {
        page: params?.page ?? props.page,
        per: params?.per ?? props.per,
      },
    });

    data.value = res.data.data || res.data;
    pagination.value = {
      total: res.data.total,
      current_page: res.data.current_page,
      per_page: res.data.per_page,
      last_page: res.data.last_page, // ✅ tambahkan ini
    };

    emit("update:page", pagination.value.current_page); // ✅ sinkronkan ke parent
  } catch (err) {
    console.error("Fetch error:", err);
  } finally {
    loading.value = false;
  }
};

// buat reactive table
const table = useVueTable({
  get data() {
    return data.value;
  },
  get columns() {
    return props.columns;
  },
  getCoreRowModel: getCoreRowModel(),
});

onMounted(() => fetchData());

watch(
  () => props.per,
  (val) => {
    fetchData({ page: 1, per: val });
  }
);

watch(
  () => props.page,
  (val) => {
    fetchData({ page: val, per: props.per });
  }
);

defineExpose({ fetchData });
</script>

<template>
  <div class="p-3">
    <table class="table table-striped align-middle mb-0">
      <thead>
        <tr>
          <th
            v-for="header in table.getHeaderGroups()[0].headers"
            :key="header.id"
            class="text-center fw-bold text-uppercase"
          >
            <FlexRender
              :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
          </th>
        </tr>
      </thead>

      <tbody>
        <tr v-if="loading">
          <td colspan="100%" class="text-center py-5 text-muted">Memuat data...</td>
        </tr>

        <tr v-else-if="data.length === 0">
          <td colspan="100%" class="text-center py-5 text-muted">Tidak ada data</td>
        </tr>

        <tr
          v-else
          v-for="row in table.getRowModel().rows"
          :key="row.id"
        >
          <td
            v-for="cell in row.getVisibleCells()"
            :key="cell.id"
            class="align-middle"
          >
            <FlexRender
              :render="cell.column.columnDef.cell"
              :props="cell.getContext()"
            />
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
      <div class="text-muted fs-7">
        Menampilkan {{ data.length }} dari {{ pagination.total }} data
      </div>

      <div class="btn-group">
        <button
          class="btn btn-light btn-sm"
          :disabled="pagination.current_page <= 1"
          @click="emit('update:page', pagination.current_page - 1)"
        >
          <i class="la la-angle-left"></i>
        </button>

        <span class="btn btn-outline-secondary btn-sm disabled">
          Hal. {{ pagination.current_page }}
        </span>

        <button
          class="btn btn-light btn-sm"
          :disabled="pagination.current_page >= Math.ceil(pagination.total / pagination.per_page)"
          @click="emit('update:page', pagination.current_page + 1)"
        >
          <i class="la la-angle-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>
