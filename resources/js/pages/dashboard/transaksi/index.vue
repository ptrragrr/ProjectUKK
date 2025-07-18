<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from '@/libs/axios';
import { formatRupiah, formatDate } from '@/libs/formatter'; // pastikan Anda punya helper ini

const transaksiList = ref([]);

const fetchTransaksi = async () => {
  try {
    const { data } = await axios.get('/transaksi');
    transaksiList.value = data;
  } catch (err) {
    console.error('Gagal mengambil data transaksi:', err);
  }
};

onMounted(fetchTransaksi);
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h2>Daftar Transaksi</h2>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Metode</th>
            <th>Total</th>
            <th>Bayar</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in transaksiList" :key="t.id">
            <td>{{ t.kode_transaksi }}</td>
            <td>{{ t.metode_pembayaran }}</td>
            <td>{{ formatRupiah(t.total_harga) }}</td>
            <td>{{ formatRupiah(t.bayar) }}</td>
            <td>
              <span :class="{
                'badge bg-warning': t.status === 'pending',
                'badge bg-success': t.status === 'paid',
                'badge bg-danger': t.status === 'cancelled'
              }">{{ t.status }}</span>
            </td>
            <td>{{ formatDate(t.created_at) }}</td>
            <td>
              <button class="btn btn-sm btn-primary">Detail</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
