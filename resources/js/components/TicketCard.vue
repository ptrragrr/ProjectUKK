<template>
  <div class="ticket-card" :class="{ 'sold-out': ticket.stok_tiket === 0 }">
    <h3>{{ ticket.nama_event }}</h3>
    <p>{{ ticket.deskripsi }}</p>
    <div>Harga: <strong>{{ formatRupiah(ticket.harga_tiket) }}</strong></div>
    <div>Stok: <strong>{{ ticket.stok_tiket }}</strong></div>

    <div class="qty-controls">
      <button @click="decrement" :disabled="qty === 0">-</button>
      <input type="text" :value="qty" readonly />
      <button @click="increment" :disabled="qty >= ticket.stok_tiket">+</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from "vue";

const props = defineProps({
  ticket: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["updateQty"]);
const qty = ref(props.ticket.qty ?? 0);

watch(qty, (val) => emit("updateQty", props.ticket.id, val));

const increment = () => {
  if (qty.value < props.ticket.stok_tiket) qty.value++;
};
const decrement = () => {
  if (qty.value > 0) qty.value--;
};

const formatRupiah = (v: number) => `Rp ${v.toLocaleString("id-ID")}`;
</script>

<style scoped>
.ticket-card {
  border: 1px solid #ddd;
  padding: 1rem;
  border-radius: 1rem;
  background: white;
  text-align: center;
}
.qty-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}
.qty-controls button {
  width: 32px;
  height: 32px;
  font-size: 18px;
  border-radius: 0.25rem;
  background: #22c55e;
  color: white;
  border: none;
  cursor: pointer;
}
.qty-controls button:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>
