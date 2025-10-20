<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import TicketCard from "@/components/TicketCard.vue";
import axios from "@/libs/axios";
import { toast } from "vue3-toastify";

interface Ticket {
  id: number;
  nama_event: string;
  jenis_tiket: string;
  deskripsi: string;
  harga_tiket: number;
  stok_tiket: number;
  qty?: number;
}

const tickets = ref<Ticket[]>([]);
const loading = ref(false);
const checkoutLoading = ref(false);
const errorMessage = ref("");
const router = useRouter();

const loadTickets = async () => {
  loading.value = true;
  errorMessage.value = "";
  
  try {
    let response;
    try {
      response = await axios.get("/tickets");
    } catch (e) {
      response = await axios.get("/api/tickets");
    }
    
    let ticketData = [];
    if (Array.isArray(response.data)) {
      ticketData = response.data;
    } else if (response.data.success && response.data.data) {
      ticketData = response.data.data;
    } else if (response.data.data && Array.isArray(response.data.data)) {
      ticketData = response.data.data;
    } else if (response.data.tickets) {
      ticketData = response.data.tickets;
    }
    
    tickets.value = ticketData.map((t: Ticket) => ({ ...t, qty: 0 }));
  } catch (error: any) {
    if (error.response) {
      errorMessage.value = `Error ${error.response.status}: ${error.response.data?.message || 'Gagal memuat data tiket'}`;
    } else if (error.request) {
      errorMessage.value = "Tidak dapat terhubung ke server. Pastikan backend Laravel berjalan.";
    } else {
      errorMessage.value = error.message || "Terjadi kesalahan saat memuat data";
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadTickets();
});

const vvipTickets = computed(() =>
  tickets.value.filter((t) => t.jenis_tiket.toUpperCase().includes("VVIP"))
);

const vipTickets = computed(() =>
  tickets.value.filter(
    (t) =>
      t.jenis_tiket.toUpperCase().includes("VIP") &&
      !t.jenis_tiket.toUpperCase().includes("VVIP")
  )
);

const regulerTickets = computed(() =>
  tickets.value.filter(
    (t) => 
      !t.jenis_tiket.toUpperCase().includes("VIP") &&
      !t.jenis_tiket.toUpperCase().includes("VVIP")
  )
);

const updateQty = (id: number, qty: number) => {
  const ticket = tickets.value.find((t) => t.id === id);
  if (ticket) ticket.qty = qty;
};

const selectedTickets = computed(() =>
  tickets.value.filter((t) => (t.qty ?? 0) > 0)
);

const subtotal = computed(() =>
  selectedTickets.value.reduce((sum, t) => sum + t.harga_tiket * (t.qty ?? 0), 0)
);

const tax = computed(() => Math.round(subtotal.value * 0.1));
const fee = computed(() => Math.round(subtotal.value * 0.05));
const total = computed(() => subtotal.value + tax.value + fee.value);

const totalQty = computed(() =>
  selectedTickets.value.reduce((sum, t) => sum + (t.qty ?? 0), 0)
);

const formatRupiah = (v: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(v);
};

const checkout = async () => {
  if (!selectedTickets.value.length) {
    toast.warning("Pilih tiket terlebih dahulu");
    return;
  }

  checkoutLoading.value = true;

  try {
    const payload = {
      tickets: selectedTickets.value.map((t) => ({
        id: Number (t.id),
        qty: Number (t.qty),
        nama_event: t.nama_event,
        harga_tiket: t.harga_tiket,
      })),
      subtotal: subtotal.value,
      tax: tax.value,
      fee: fee.value,
      total: total.value,
    };

    const res = await axios.post("/checkout", payload);
    await localStorage.setItem('keranjang', JSON.stringify(payload));

    if (res.data.success) {
      toast.success(res.data.message || "Checkout berhasil!");
      router.push("/dashboard_pengguna/checkout");
    } else {
      toast.error("Checkout gagal, coba lagi.");
    }
  } catch (err: any) {
    toast.error(err.response?.data?.message || "Terjadi kesalahan saat checkout");
  } finally {
    checkoutLoading.value = false;
  }
};
</script>

<template>
  <div class="page-wrapper">
    <div class="page-container">
      <div class="page-hero">
        <div class="hero-content">
          <div class="hero-badge">
            <span class="sparkle">✨</span>
            <span class="badge-text">OURSKY.FEST 2025</span>
          </div>
          <h1 class="page-title">Pilih Tiket Festival</h1>
          <p class="page-subtitle">Dapatkan tiket Anda sekarang dan rasakan pengalaman tak terlupakan</p>
        </div>
      </div>

      <div class="banner-section">
        <img src="/storage/photo/banner1.jpg" alt="Festival Banner" class="banner-image" />
        <!-- <div class="banner-overlay">
          <div class="banner-info">
            <span class="info-badge">📅 15-17 Desember 2025</span>
            <span class="info-badge">📍 Surabaya, Indonesia</span>
          </div>
        </div> -->
      </div>

      <div v-if="loading" class="state-container">
        <div class="loading-card">
          <div class="spinner-large"></div>
          <h3>Memuat Tiket</h3>
          <p>Mohon tunggu sebentar...</p>
        </div>
      </div>

      <div v-else-if="errorMessage" class="state-container">
        <div class="error-card">
          <div class="error-icon">❌</div>
          <h3>Gagal Memuat Data</h3>
          <p class="error-message">{{ errorMessage }}</p>
          <button @click="loadTickets" class="btn-retry">
            <span class="btn-icon">🔄</span>
            Coba Lagi
          </button>
        </div>
      </div>

      <div v-else-if="!tickets.length" class="state-container">
        <div class="empty-card">
          <div class="empty-icon">🎫</div>
          <h3>Tidak Ada Tiket Tersedia</h3>
          <p>Mohon maaf, saat ini belum ada tiket yang tersedia untuk dijual.</p>
          <p class="hint-text">Silakan hubungi panitia atau coba lagi nanti.</p>
          <button @click="loadTickets" class="btn-retry secondary">
            <span class="btn-icon">🔄</span>
            Muat Ulang
          </button>
        </div>
      </div>

      <div v-else class="main-content">
        <div class="tickets-section">
          <section v-if="vvipTickets.length" class="category-section vvip-section">
            <div class="category-header">
              <div class="header-left">
                <span class="category-icon">👑</span>
                <div class="header-text">
                  <h2 class="category-title">VVIP Tickets</h2>
                  <p class="category-desc">Pengalaman eksklusif dengan fasilitas premium</p>
                </div>
              </div>
              <span class="category-count premium">{{ vvipTickets.length }} tersedia</span>
            </div>
            <div class="tickets-grid">
              <TicketCard v-for="ticket in vvipTickets" :key="ticket.id" :ticket="ticket" @updateQty="updateQty" />
            </div>
          </section>

          <section v-if="vipTickets.length" class="category-section vip-section">
            <div class="category-header">
              <div class="header-left">
                <span class="category-icon">⭐</span>
                <div class="header-text">
                  <h2 class="category-title">VIP Tickets</h2>
                  <p class="category-desc">Nikmati kenyamanan ekstra dengan akses VIP</p>
                </div>
              </div>
              <span class="category-count">{{ vipTickets.length }} tersedia</span>
            </div>
            <div class="tickets-grid">
              <TicketCard v-for="ticket in vipTickets" :key="ticket.id" :ticket="ticket" @updateQty="updateQty" />
            </div>
          </section>

          <section v-if="regulerTickets.length" class="category-section regular-section">
            <div class="category-header">
              <div class="header-left">
                <span class="category-icon">🎫</span>
                <div class="header-text">
                  <h2 class="category-title">Regular Tickets</h2>
                  <p class="category-desc">Akses penuh ke semua area festival</p>
                </div>
              </div>
              <span class="category-count">{{ regulerTickets.length }} tersedia</span>
            </div>
            <div class="tickets-grid">
              <TicketCard v-for="ticket in regulerTickets" :key="ticket.id" :ticket="ticket" @updateQty="updateQty" />
            </div>
          </section>
        </div>

        <aside class="summary-sidebar">
          <div class="summary-card">
            <div class="summary-header">
              <div class="summary-title">
                <span class="summary-icon">🛒</span>
                <h3>Ringkasan Pemesanan</h3>
              </div>
              <span v-if="totalQty > 0" class="item-count">{{ totalQty }} item</span>
            </div>

            <div v-if="selectedTickets.length" class="summary-body">
              <div class="selected-items">
                <div v-for="item in selectedTickets" :key="item.id" class="selected-item">
                  <div class="item-header">
                    <span class="item-name">{{ item.nama_event }}</span>
                    <span class="item-price">{{ formatRupiah(item.harga_tiket * item.qty) }}</span>
                  </div>
                  <div class="item-details">
                    <span class="item-type">{{ item.jenis_tiket }}</span>
                    <span class="item-qty">{{ formatRupiah(item.harga_tiket) }} × {{ item.qty }}</span>
                  </div>
                </div>
              </div>

              <div class="summary-divider"></div>

              <div class="calculations">
                <div class="calc-row">
                  <span class="calc-label">Subtotal</span>
                  <span class="calc-value">{{ formatRupiah(subtotal) }}</span>
                </div>
                <div class="calc-row">
                  <span class="calc-label">Pajak (10%)</span>
                  <span class="calc-value">{{ formatRupiah(tax) }}</span>
                </div>
                <div class="calc-row">
                  <span class="calc-label">Platform Fee (5%)</span>
                  <span class="calc-value">{{ formatRupiah(fee) }}</span>
                </div>
              </div>

              <div class="summary-divider bold"></div>

              <div class="total-row">
                <span class="total-label">Total Pembayaran</span>
                <span class="total-value">{{ formatRupiah(total) }}</span>
              </div>

              <button class="btn-checkout" :disabled="checkoutLoading" @click="checkout">
                <span v-if="checkoutLoading" class="btn-loading">
                  <span class="btn-spinner"></span>
                  Memproses...
                </span>
                <span v-else class="btn-content">
                  <span class="btn-icon">💳</span>
                  Lanjut ke Pembayaran
                </span>
              </button>
            </div>

            <div v-else class="empty-summary">
              <div class="empty-illustration">🛒</div>
              <h4>Keranjang Kosong</h4>
              <p>Pilih tiket di sebelah kiri untuk memulai pemesanan</p>
            </div>
          </div>

          <div class="info-card">
            <div class="info-icon">💡</div>
            <div class="info-content">
              <h4>Tips Pemesanan</h4>
              <ul>
                <li>Periksa tanggal dan kategori tiket</li>
                <li>Pastikan data pemesanan benar</li>
                <li>Tiket terbatas, pesan sekarang!</li>
              </ul>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<style>
:root {
  --primary-color: #676F53;
  --primary-hover: #1C290D;
  --secondary-color: #A19379;
  --accent-color: #FEFAE0;
  --text-dark: #381D03;
  --text-light: #736046;
  --border-color: rgba(179, 180, 154, 0.3);
  --bg-light: #FEFAE0;
  --white: #FFFFFF;
}
</style>

<style scoped>
.page-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%);
  padding: 40px 20px 60px;
}

.page-container {
  max-width: 1400px;
  margin: 0 auto;
}

.page-hero {
  text-align: center;
  margin-bottom: 40px;
}

.hero-content {
  max-width: 800px;
  margin: 0 auto;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  color: white;
  padding: 8px 20px;
  border-radius: 50px;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(103, 111, 83, 0.3);
}

.sparkle {
  font-size: 16px;
  animation: sparkle 2s ease-in-out infinite;
}

@keyframes sparkle {
  0%, 100% { transform: scale(1) rotate(0deg); }
  50% { transform: scale(1.2) rotate(180deg); }
}

.badge-text {
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.page-title {
  font-size: 48px;
  font-weight: 800;
  color: var(--text-dark);
  margin-bottom: 16px;
  letter-spacing: -0.02em;
}

.page-subtitle {
  font-size: 18px;
  color: var(--text-light);
  line-height: 1.6;
}

.banner-section {
  position: relative;
  margin-bottom: 48px;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 20px 40px rgba(28, 41, 13, 0.15);
}

.banner-image {
  width: 100%;
  height: 400px;
  object-fit: cover;
  display: block;
  border-radius: 24px;
}

.banner-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(28, 41, 13, 0.9), transparent);
  padding: 32px;
}

.banner-info {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.info-badge {
  background: rgba(254, 250, 224, 0.2);
  backdrop-filter: blur(10px);
  color: white;
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  border: 1px solid rgba(254, 250, 224, 0.3);
}

.state-container {
  max-width: 600px;
  margin: 80px auto;
}

.loading-card, .error-card, .empty-card {
  background: white;
  border-radius: 24px;
  padding: 60px 40px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(28, 41, 13, 0.1);
  border: 2px solid var(--border-color);
}

.spinner-large {
  width: 80px;
  height: 80px;
  border: 6px solid var(--border-color);
  border-top-color: var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 32px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-card h3 {
  font-size: 24px;
  color: var(--text-dark);
  margin-bottom: 12px;
  font-weight: 700;
}

.loading-card p {
  color: var(--text-light);
  font-size: 16px;
}

.error-card {
  background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%);
  border-color: #FCA5A5;
}

.error-icon {
  font-size: 64px;
  margin-bottom: 24px;
}

.error-card h3 {
  font-size: 24px;
  color: #DC2626;
  margin-bottom: 16px;
  font-weight: 700;
}

.error-message {
  color: var(--text-dark);
  margin-bottom: 32px;
  line-height: 1.6;
  font-size: 15px;
}

.empty-card {
  border: 2px dashed var(--border-color);
}

.empty-icon {
  font-size: 80px;
  opacity: 0.3;
  margin-bottom: 24px;
}

.empty-card h3 {
  font-size: 24px;
  color: var(--text-dark);
  margin-bottom: 12px;
  font-weight: 700;
}

.empty-card p {
  color: var(--text-light);
  margin-bottom: 8px;
  line-height: 1.6;
}

.hint-text {
  font-size: 14px;
  color: var(--text-light);
  margin-bottom: 32px !important;
}

.btn-retry {
  padding: 14px 32px;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  color: white;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 4px 12px rgba(103, 111, 83, 0.3);
}

.btn-retry:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(103, 111, 83, 0.4);
}

.btn-retry.secondary {
  background: linear-gradient(135deg, var(--text-dark), #4A3F2E);
}

.main-content {
  display: grid;
  grid-template-columns: 1fr 450px;
  gap: 32px;
  align-items: start;
}

.tickets-section {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.category-section {
  background: white;
  border-radius: 24px;
  padding: 32px;
  border: 2px solid var(--border-color);
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(28, 41, 13, 0.05);
}

.category-section:hover {
  box-shadow: 0 10px 30px rgba(28, 41, 13, 0.1);
  border-color: var(--primary-color);
}

.vvip-section {
  background: linear-gradient(135deg, #FEFAE0 0%, #F5F1E8 100%);
  border-color: #A19379;
}

.vvip-section:hover {
  border-color: #736046;
}

.category-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 28px;
  padding-bottom: 24px;
  border-bottom: 2px solid var(--border-color);
}

.vvip-section .category-header {
  border-bottom-color: rgba(161, 147, 121, 0.3);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.category-icon {
  font-size: 40px;
}

.header-text {
  flex: 1;
}

.category-title {
  font-size: 24px;
  font-weight: 800;
  color: var(--text-dark);
  margin-bottom: 4px;
  letter-spacing: -0.01em;
}

.category-desc {
  font-size: 14px;
  color: var(--text-light);
}

.category-count {
  background: var(--primary-color);
  color: white;
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(103, 111, 83, 0.2);
}

.category-count.premium {
  background: linear-gradient(135deg, #A19379, #736046);
  box-shadow: 0 4px 12px rgba(161, 147, 121, 0.3);
}

.tickets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 24px;
}

.summary-sidebar {
  position: sticky;
  top: 40px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.summary-card {
  background: white;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(28, 41, 13, 0.1);
  border: 2px solid var(--border-color);
}

.summary-header {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  color: white;
  padding: 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.summary-title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.summary-icon {
  font-size: 24px;
}

.summary-title h3 {
  font-size: 20px;
  font-weight: 700;
  margin: 0;
}

.item-count {
  background: rgba(254, 250, 224, 0.2);
  backdrop-filter: blur(10px);
  padding: 6px 16px;
  border-radius: 50px;
  font-size: 14px;
  font-weight: 600;
  border: 1px solid rgba(254, 250, 224, 0.3);
}

.summary-body {
  padding: 24px;
}

.selected-items {
  margin-bottom: 20px;
}

.selected-item {
  background: var(--bg-light);
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 12px;
  border: 1px solid var(--border-color);
  transition: all 0.2s ease;
}

.selected-item:hover {
  background: #F5F1E8;
  border-color: rgba(179, 180, 154, 0.5);
}

.selected-item:last-child {
  margin-bottom: 0;
}

.item-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 8px;
}

.item-name {
  font-weight: 700;
  color: var(--text-dark);
  font-size: 15px;
  flex: 1;
  line-height: 1.4;
}

.item-price {
  font-weight: 800;
  color: var(--primary-color);
  font-size: 16px;
  white-space: nowrap;
  margin-left: 12px;
}

.item-details {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

.item-type {
  color: var(--text-light);
  background: white;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 600;
}

.item-qty {
  color: var(--text-light);
}

.summary-divider {
  height: 1px;
  background: var(--border-color);
  margin: 20px 0;
}

.summary-divider.bold {
  height: 2px;
  background: rgba(179, 180, 154, 0.5);
}

.calculations {
  margin-bottom: 20px;
}

.calc-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  font-size: 15px;
}

.calc-label {
  color: var(--text-light);
}

.calc-value {
  color: var(--text-dark);
  font-weight: 600;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
  margin-bottom: 24px;
}

.total-label {
  font-size: 16px;
  color: var(--text-dark);
  font-weight: 600;
}

.total-value {
  font-size: 28px;
  font-weight: 800;
  color: var(--secondary-color);
}

.btn-checkout {
  width: 100%;
  padding: 18px;
  background: linear-gradient(135deg, var(--secondary-color), #736046);
  color: white;
  border: none;
  border-radius: 14px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(161, 147, 121, 0.3);
}

.btn-checkout:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(161, 147, 121, 0.4);
  background: linear-gradient(135deg, #736046, #5C4D39);
}

.btn-checkout:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-loading, .btn-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.btn-spinner {
  width: 18px;
  height: 18px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.btn-icon {
  font-size: 18px;
}

.empty-summary {
  padding: 60px 24px;
  text-align: center;
}

.empty-illustration {
  font-size: 64px;
  opacity: 0.2;
  margin-bottom: 20px;
}

.empty-summary h4 {
  font-size: 18px;
  color: var(--text-dark);
  margin-bottom: 8px;
  font-weight: 700;
}

.empty-summary p {
  color: var(--text-light);
  font-size: 14px;
  line-height: 1.6;
}

.info-card {
  background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
  border: 2px solid #BBF7D0;
  border-radius: 20px;
  padding: 24px;
  display: flex;
  gap: 16px;
}

.info-icon {
  font-size: 32px;
  flex-shrink: 0;
}

.info-content h4 {
  font-size: 16px;
  color: var(--text-dark);
  margin-bottom: 12px;
  font-weight: 700;
}

.info-content ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.info-content li {
  color: var(--text-light);
  font-size: 14px;
  line-height: 1.8;
  padding-left: 20px;
  position: relative;
}

.info-content li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: var(--primary-color);
  font-weight: 700;
}

/* Responsive */
@media (max-width: 1200px) {
  .main-content {
    grid-template-columns: 1fr 400px;
    gap: 24px;
  }

  .tickets-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
}

@media (max-width: 968px) {
  .page-wrapper {
    padding: 32px 16px 48px;
  }

  .page-title {
    font-size: 36px;
  }

  .page-subtitle {
    font-size: 16px;
  }

  .banner-image {
    height: 300px;
  }

  .main-content {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .summary-sidebar {
    position: static;
  }

  .tickets-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  }

  .category-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
  }

  .category-count {
    align-self: flex-start;
  }
}

@media (max-width: 640px) {
  .page-wrapper {
    padding: 24px 12px 40px;
  }

  .page-title {
    font-size: 28px;
  }

  .page-subtitle {
    font-size: 15px;
  }

  .banner-section {
    margin-bottom: 32px;
    border-radius: 16px;
  }

  .banner-image {
    height: 220px;
  }

  .banner-overlay {
    padding: 20px;
  }

  .info-badge {
    font-size: 12px;
    padding: 8px 16px;
  }

  .state-container {
    margin: 40px auto;
  }

  .loading-card, .error-card, .empty-card {
    padding: 40px 24px;
    border-radius: 20px;
  }

  .spinner-large {
    width: 60px;
    height: 60px;
    border-width: 5px;
  }

  .error-icon, .empty-icon {
    font-size: 56px;
  }

  .loading-card h3, .error-card h3, .empty-card h3 {
    font-size: 20px;
  }

  .tickets-section {
    gap: 24px;
  }

  .category-section {
    padding: 20px;
    border-radius: 20px;
  }

  .category-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
  }

  .header-left {
    gap: 12px;
  }

  .category-icon {
    font-size: 32px;
  }

  .category-title {
    font-size: 20px;
  }

  .category-desc {
    font-size: 13px;
  }

  .category-count {
    font-size: 12px;
    padding: 6px 16px;
  }

  .tickets-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .summary-card {
    border-radius: 20px;
  }

  .summary-header {
    padding: 20px;
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .summary-title {
    gap: 10px;
  }

  .summary-icon {
    font-size: 20px;
  }

  .summary-title h3 {
    font-size: 18px;
  }

  .item-count {
    align-self: flex-start;
    font-size: 12px;
    padding: 4px 12px;
  }

  .summary-body {
    padding: 20px;
  }

  .selected-item {
    padding: 14px;
  }

  .item-name {
    font-size: 14px;
  }

  .item-price {
    font-size: 15px;
  }

  .item-details {
    font-size: 12px;
  }

  .calc-row {
    font-size: 14px;
    padding: 8px 0;
  }

  .total-row {
    padding: 16px 0;
    margin-bottom: 20px;
  }

  .total-label {
    font-size: 15px;
  }

  .total-value {
    font-size: 24px;
  }

  .btn-checkout {
    padding: 16px;
    font-size: 15px;
  }

  .empty-summary {
    padding: 48px 20px;
  }

  .empty-illustration {
    font-size: 56px;
  }

  .empty-summary h4 {
    font-size: 16px;
  }

  .empty-summary p {
    font-size: 13px;
  }

  .info-card {
    padding: 20px;
    border-radius: 16px;
    flex-direction: column;
    gap: 12px;
  }

  .info-icon {
    font-size: 28px;
  }

  .info-content h4 {
    font-size: 15px;
    margin-bottom: 10px;
  }

  .info-content li {
    font-size: 13px;
    line-height: 1.6;
    padding-left: 18px;
  }

  .btn-retry {
    padding: 12px 24px;
    font-size: 15px;
    gap: 8px;
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 24px;
  }

  .banner-image {
    height: 180px;
  }

  .banner-info {
    flex-direction: column;
    gap: 10px;
  }

  .info-badge {
    font-size: 11px;
    padding: 6px 12px;
  }

  .category-section {
    padding: 16px;
  }

  .category-icon {
    font-size: 28px;
  }

  .category-title {
    font-size: 18px;
  }

  .header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .summary-header {
    padding: 16px;
  }

  .summary-body {
    padding: 16px;
  }

  .selected-item {
    padding: 12px;
  }

  .item-header {
    flex-direction: column;
    gap: 6px;
    align-items: flex-start;
  }

  .item-price {
    margin-left: 0;
  }

  .total-value {
    font-size: 20px;
  }

  .info-card {
    padding: 16px;
  }
}
</style>