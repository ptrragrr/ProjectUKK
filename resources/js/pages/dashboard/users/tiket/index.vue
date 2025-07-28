<template>
  <div>
    <h1>Pilih Tiket Konser</h1>

    <div v-for="(ticket, index) in tickets" :key="ticket.id" class="ticket-card">
      <h3>{{ ticket.nama }}</h3>
      <p>{{ ticket.deskripsi }}</p>
      <p>Harga: {{ formatRupiah(ticket.harga) }}</p>
      <div class="qty-control">
        <button @click="decrement(index)">-</button>
        <input type="text" :value="ticket.qty" readonly />
        <button @click="increment(index)">+</button>
      </div>
    </div>

    <button @click="goToForm" class="btn-checkout">
      Lanjutkan ke Checkout ({{ totalQty }} tiket)
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const tickets = ref([
  {
    id: 1,
    nama: 'Presale - SOD Day 1',
    deskripsi: 'Tiket berlaku untuk 1 orang pada hari 1.',
    harga: 125000,
    qty: 0
  },
  {
    id: 2,
    nama: 'Presale - VOD',
    deskripsi: 'Tiket online streaming.',
    harga: 200000,
    qty: 0
  }
])

const increment = (i) => tickets.value[i].qty++
const decrement = (i) => {
  if (tickets.value[i].qty > 0) tickets.value[i].qty--
}

const totalQty = computed(() =>
  tickets.value.reduce((sum, item) => sum + item.qty, 0)
)

const goToForm = () => {
  const selected = tickets.value.filter((item) => item.qty > 0)
  if (selected.length === 0) {
    alert('Pilih tiket terlebih dahulu.')
    return
  }

  // Simpan ke localStorage atau Pinia
  localStorage.setItem('selectedTickets', JSON.stringify(selected))
  router.push('/tiket/form')
}

const formatRupiah = (val) => 'Rp ' + val.toLocaleString('id-ID')
</script>
