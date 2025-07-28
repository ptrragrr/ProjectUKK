<template>
  <div class="checkout-container">
    <div class="checkout-header">
      <h2>Checkout Tiket</h2>
      <p>Terima kasih telah memilih tiket Anda! Silakan lanjutkan pembayaran untuk menyelesaikan pesanan.</p>
    </div>

    <div class="checkout-summary">
      <div class="summary-item">
        <span>Tiket: SOD Day 1</span>
        <span>Rp 125.000 × 2</span>
      </div>
      <div class="summary-item">
        <span>Tax (10%)</span>
        <span>Rp 25.000</span>
      </div>
      <div class="summary-item">
        <span>Platform Fee</span>
        <span>Rp 12.500</span>
      </div>
      <div class="summary-total">
        Total Bayar: Rp 287.500
      </div>

      <div class="button-group">
        <button class="btn-pay" @click="showModal">Bayar Sekarang</button>
        <button class="btn-cancel" @click="goBack">Batal</button>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="isModalVisible" class="modal" @click.self="closeModal">
      <div class="modal-content">
        <span class="close" @click="closeModal">&times;</span>
        <h4>Pilih Metode Pembayaran</h4>
        <div class="payment-options">
          <button class="dana">Bayar via DANA</button>
          <button class="ovo">Bayar via OVO</button>
          <button class="gopay">Bayar via GoPay</button>
          <button class="bca">Transfer Bank - BCA</button>
          <button class="bri">Transfer Bank - BRI</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CheckoutTiket',
  data() {
    return {
      isModalVisible: false,
    };
  },
  methods: {
    showModal() {
      this.isModalVisible = true;
    },
    closeModal() {
      this.isModalVisible = false;
    },
    goBack() {
      this.$router.push('/');
    },
  },
};
</script>

<style scoped>
/* salin semua CSS dari Blade yang kamu berikan, tanpa tag <style> dan @section */
/* ... PASTE CSS DARI BLADE DI SINI ... */
</style>
