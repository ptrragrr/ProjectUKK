<!-- index.vue -->
<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";
import FormTransaksi from "./form.vue";
import type { Transaksi } from "@/types";

const tickets = ref<Transaksi[]>([]);
const selectedTicket = ref<Transaksi | null>(null);

const getTickets = async () => {
  try {
    const res = await axios.get("/api/tickets");
    tickets.value = res.data.data;
  } catch (err) {
    console.error("Gagal mengambil tiket:", err);
  }
};

onMounted(() => {
  getTickets();
});
</script>

<template>
  <div>
    <h2>Daftar Tiket</h2>
    <ul>
      <li v-for="ticket in tickets" :key="ticket.id">
        {{ ticket.nama_ticket }} -
        Rp {{ ticket.harga_ticket.toLocaleString() }}
        <button @click="selectedTicket = ticket">Pilih</button>
      </li>
    </ul>

    <!-- ⬇️ Render form hanya kalau selectedTicket tidak null -->
    <FormTransaksi :selectedTicket="selectedTicket" @refresh="getTickets" />
  </div>
</template>
