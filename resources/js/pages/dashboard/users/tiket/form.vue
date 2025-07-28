<template>
  <div class="container py-5">
    <h1 class="mb-4">Pilih Tiket</h1>

    <div v-if="tickets.length" class="row">
      <div
        class="col-md-4 mb-4"
        v-for="ticket in tickets"
        :key="ticket.id"
      >
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">{{ ticket.nama }}</h5>
            <p class="card-text">Harga: Rp {{ formatRupiah(ticket.harga) }}</p>
            <div class="input-group">
              <button class="btn btn-outline-secondary" @click="decrement(ticket)">-</button>
              <input
                type="number"
                class="form-control text-center"
                v-model.number="ticket.quantity"
                min="0"
              />
              <button class="btn btn-outline-secondary" @click="increment(ticket)">+</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="totalPrice > 0" class="mt-4">
      <h4>Ringkasan</h4>
      <ul class="list-group mb-3">
        <li
          class="list-group-item d-flex justify-content-between align-items-center"
          v-for="ticket in selectedTickets"
          :key="ticket.id"
        >
          {{ ticket.nama }} x {{ ticket.quantity }}
          <span>Rp {{ formatRupiah(ticket.harga * ticket.quantity) }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
          <strong>Total</strong>
          <strong>Rp {{ formatRupiah(totalPrice) }}</strong>
        </li>
      </ul>

      <button class="btn btn-primary" @click="checkout">Checkout</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketSelectionUser',
  data() {
    return {
      tickets: [],
    };
  },
  computed: {
    selectedTickets() {
      return this.tickets.filter((t) => t.quantity > 0);
    },
    totalPrice() {
      return this.selectedTickets.reduce(
        (sum, t) => sum + t.harga * t.quantity,
        0
      );
    },
  },
  methods: {
    fetchTickets() {
      fetch('/api/tiket')
        .then((res) => res.json())
        .then((data) => {
          this.tickets = data.map((t) => ({ ...t, quantity: 0 }));
        });
    },
    increment(ticket) {
      ticket.quantity++;
    },
    decrement(ticket) {
      if (ticket.quantity > 0) ticket.quantity--;
    },
    formatRupiah(value) {
      return value.toLocaleString('id-ID');
    },
    checkout() {
      const payload = this.selectedTickets.map((t) => ({
        id: t.id,
        quantity: t.quantity,
      }));

      fetch('/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.head.querySelector('[name=csrf-token]').content,
        },
        body: JSON.stringify({ tickets: payload }),
      }).then((res) => {
        if (res.ok) {
          window.location.href = '/checkout/confirm';
        }
      });
    },
  },
  mounted() {
    this.fetchTickets();
  },
};
</script>

<style scoped>
.card-title {
  font-weight: 600;
  font-size: 18px;
}
</style>
