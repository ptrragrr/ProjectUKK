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
  last_page: 1,
});

const loading = ref(false);

// ======================= FETCH DATA =======================
const fetchData = async (params?: { page?: number; per?: number; search?: string }) => {
  loading.value = true;
  try {
    const res = await axios.get(props.url, {
      params: {
        page: params?.page ?? props.page,
        per: params?.per ?? props.per,
        search: params?.search ?? "",
      },
    });

    data.value = res.data.data || res.data;
    pagination.value = {
      total: res.data.total,
      current_page: res.data.current_page,
      per_page: res.data.per_page,
      last_page: res.data.last_page,
    };

    emit("update:page", pagination.value.current_page);
  } catch (err) {
    console.error("Fetch error:", err);
  } finally {
    loading.value = false;
  }
};

// ======================= TANSTACK TABLE =======================
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

watch(() => props.per, (val) => fetchData({ page: 1, per: val }));
watch(() => props.page, (val) => fetchData({ page: val, per: props.per }));

defineExpose({ fetchData, pagination });

// ======================= VISIBLE PAGES =======================
const visiblePages = computed(() => {
  const total = pagination.value.last_page || 1;
  const current = pagination.value.current_page;
  const delta = 2;
  const pages = [];

  for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) {
    pages.push(i);
  }

  return pages;
});
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
          <td colspan="100%" class="text-center py-5 text-muted">
            Memuat data...
          </td>
        </tr>

        <tr v-else-if="data.length === 0">
          <td colspan="100%" class="text-center py-5 text-muted">
            Tidak ada data
          </td>
        </tr>

        <tr v-else v-for="row in table.getRowModel().rows" :key="row.id">
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

    <!-- ======================= PAGINATION MODERN ======================= -->
    <div class="pagination-wrapper" v-if="pagination.total > 0">
      <!-- Tombol Sebelumnya -->
      <button
        class="page-arrow"
        :disabled="pagination.current_page <= 1"
        @click="emit('update:page', pagination.current_page - 1)"
      >
        <i class="la la-angle-left"></i>
      </button>

      <!-- Nomor Halaman -->
      <button
        v-for="page in visiblePages"
        :key="page"
        class="page-number"
        :class="{ active: pagination.current_page === page }"
        @click="emit('update:page', page)"
      >
        {{ page }}
      </button>

      <!-- Tombol Selanjutnya -->
      <button
        class="page-arrow"
        :disabled="pagination.current_page >= pagination.last_page"
        @click="emit('update:page', pagination.current_page + 1)"
      >
        <i class="la la-angle-right"></i>
      </button>
    </div>
  </div>
</template>

<style scoped>
.pagination-wrapper {
  display: flex;
  justify-content: flex-end; /* dari center -> flex-end */
  align-items: center;
  gap: 0.4rem;
  margin-top: 1.5rem;
  padding-right: 1rem; /* optional: kasih jarak dari kanan */
}

.page-number,
.page-arrow {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: none;
  background-color: transparent;
  color: #a1a5b7;
  font-weight: 600;
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-number:hover:not(.active),
.page-arrow:hover:not(:disabled) {
  background-color: rgba(102, 126, 234, 0.1);
  color: #667eea;
}

.page-number.active {
  background-color: #667eea;
  color: white;
  font-weight: 700;
  box-shadow: 0 0 8px rgba(102, 126, 234, 0.4);
}

.page-arrow:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>
