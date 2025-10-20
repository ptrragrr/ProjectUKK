<script setup lang="ts">
import { ref, watch, computed, onMounted } from "vue";
import axios from "@/libs/axios";

// 🛒 Interface untuk Ticket
interface Ticket {
  id: number;
  nama_event: string;
  jenis_tiket?: string;
  harga_tiket: number;
  qty: number;
}

// 🛒 Keranjang dinamis dari localStorage
const keranjang = ref<Ticket[]>([]);

// Ambil data dari localStorage saat halaman dimuat
onMounted(() => {
  const saved = localStorage.getItem("keranjang");
  console.log("Mengambil data keranjang dari localStorage:", saved);
  
  if (saved) {
    try {
      const parsedData = JSON.parse(saved);
      
      // Sesuaikan dengan struktur: {tickets: [...]}
      if (parsedData.tickets && Array.isArray(parsedData.tickets)) {
        keranjang.value = parsedData.tickets;
      } else if (Array.isArray(parsedData)) {
        keranjang.value = parsedData;
      } else {
        console.warn("Format data keranjang tidak sesuai");
        keranjang.value = [];
      }
    } catch (error) {
      console.error("Error parsing keranjang data:", error);
      keranjang.value = [];
    }
  }
  
  console.log("Keranjang berhasil dimuat:", keranjang.value);

  // Tambahkan script Midtrans Snap
  const script = document.createElement("script");
  script.src = "https://app.sandbox.midtrans.com/snap/snap.js";
  script.dataset.clientKey = import.meta.env.VITE_MIDTRANS_CLIENT_KEY;
  document.body.appendChild(script);
});

// Simpan otomatis setiap kali keranjang berubah
watch(
  keranjang,
  (newValue) => {
    const dataToSave = {
      tickets: newValue,
      subtotal: subtotal.value,
      tax: tax.value,
      fee: platformFee.value,
      total: total.value
    };
    localStorage.setItem("keranjang", JSON.stringify(dataToSave));
    console.log("Keranjang diperbarui:", dataToSave);
  },
  { deep: true }
);

// 💰 Hitung total dinamis
const subtotal = computed(() =>
  keranjang.value.reduce((sum, item) => sum + (item.harga_tiket * item.qty), 0)
);

const tax = computed(() => Math.round(subtotal.value * 0.1));

const platformFee = ref(5000);

const total = computed(() => subtotal.value + tax.value + platformFee.value);

// 🧾 Form data
const form = ref({
  nama_pembeli: "",
  email: "",
  telepon: "",
  metode_pembayaran: "snap",
});

// ⚙️ State & fungsi UI
const loading = ref(false);
const modalVisible = ref(false);
const modalText = ref("");

// Format harga ke format Indonesia
function formatHarga(value: number) {
  return value.toLocaleString("id-ID");
}

// Fungsi untuk menambah tiket ke keranjang (jika diperlukan)
function tambahKeKeranjang(ticket: Ticket) {
  const saved = localStorage.getItem("keranjang");
  let keranjangData: any = { tickets: [] };
  
  if (saved) {
    keranjangData = JSON.parse(saved);
  }

  // Cek apakah tiket sudah ada
  const existingIndex = keranjangData.tickets.findIndex(
    (item: Ticket) => item.id === ticket.id
  );

  if (existingIndex !== -1) {
    // Jika sudah ada, tambah jumlah
    keranjangData.tickets[existingIndex].qty += ticket.qty;
  } else {
    // Jika belum ada, push baru
    keranjangData.tickets.push(ticket);
  }

  // Hitung ulang total
  const newSubtotal = keranjangData.tickets.reduce(
    (sum: number, item: Ticket) => sum + item.harga_tiket * item.qty, 0
  );
  const newTax = Math.round(newSubtotal * 0.1);
  const newTotal = newSubtotal + newTax + platformFee.value;

  keranjangData.subtotal = newSubtotal;
  keranjangData.tax = newTax;
  keranjangData.fee = platformFee.value;
  keranjangData.total = newTotal;

  // Simpan kembali ke localStorage
  localStorage.setItem("keranjang", JSON.stringify(keranjangData));
  
  // Update state
  keranjang.value = keranjangData.tickets;

  alert("Tiket berhasil ditambahkan ke keranjang!");
}

// Loading functions
function showLoading(text = "Memproses pembayaran...") {
  modalText.value = text;
  modalVisible.value = true;
  loading.value = true;
}

function hideLoading() {
  modalVisible.value = false;
  loading.value = false;
}

// Navigation functions
function batal() {
  window.location.href = "/";
}

function goToEvents() {
  window.location.href = "/events";
}

// 🚀 Fungsi pembayaran
async function startPayment() {
  // Validasi form
  if (!form.value.nama_pembeli || !form.value.email || !form.value.telepon) {
    alert("Mohon lengkapi semua data pembeli.");
    return;
  }

  // Validasi keranjang
  if (keranjang.value.length === 0) {
    alert("Keranjang masih kosong, silakan pilih tiket terlebih dahulu.");
    return;
  }

  showLoading("Memproses pembayaran...");

  try {
    console.log("Memulai proses pembayaran dengan data:", {
      form: form.value,
      keranjang: keranjang.value
    });

    // Buat payload sesuai struktur yang dibutuhkan backend
    const payload = {
      nama_pembeli: form.value.nama_pembeli,
      email: form.value.email,
      telepon: form.value.telepon,
      metode_pembayaran: form.value.metode_pembayaran,
      tickets: keranjang.value.map((item) => ({
        id: item.id,
        qty: item.qty,
        harga_tiket: item.harga_tiket,
        nama_event: item.nama_event
      })),
      total: total.value,
      subtotal: subtotal.value,
      tax: tax.value,
      fee: platformFee.value
    };

    console.log("Payload yang dikirim:", payload);

    const { data } = await axios.post("/checkout/pay", payload);

    console.log("Response dari server:", data);

    if (data.token) {
      hideLoading();
      showLoading("Membuka jendela pembayaran...");

      // eslint-disable-next-line @typescript-eslint/ban-ts-comment
      // @ts-ignore
      window.snap.pay(data.token, {
        onSuccess: async (result: any) => {
          console.log("Pembayaran berhasil:", result);
          showLoading("Memverifikasi pembayaran...");
          
          try {
            await axios.post("/checkout/callback", {
              order_id: result.order_id,
              transaction_status: result.transaction_status || "settlement",
            });
            
            localStorage.removeItem("keranjang");
            window.location.href = `/checkout/success?order_id=${result.order_id}`;
          } catch (error) {
            console.error("Error saat callback:", error);
            hideLoading();
            alert("Terjadi kesalahan saat memverifikasi pembayaran");
          }
        },
        onPending: (result: any) => {
          console.log("Pembayaran pending:", result);
          showLoading("Menunggu pembayaran...");
          setTimeout(() => {
            window.location.href = `/checkout/pending?order_id=${result.order_id}`;
          }, 2000);
        },
        onError: (result: any) => {
          console.error("Pembayaran error:", result);
          hideLoading();
          alert("Pembayaran gagal: " + (result.status_message || "Terjadi kesalahan"));
          
          if (result.order_id) {
            setTimeout(() => {
              window.location.href = `/checkout/failed?order_id=${result.order_id}`;
            }, 2000);
          }
        },
        onClose: () => {
          console.log("Modal pembayaran ditutup");
          hideLoading();
        },
      });
    } else {
      hideLoading();
      alert("Gagal mendapatkan token pembayaran dari server.");
      console.error("Token tidak ditemukan dalam response:", data);
    }
  } catch (err: any) {
    console.error("Error saat memproses pembayaran:", err);
    hideLoading();
    
    const errorMessage = err.response?.data?.message || 
                        err.message || 
                        "Terjadi kesalahan saat memproses pembayaran.";
    
    alert(errorMessage);
  }
}
</script>

<template>
  <div class="checkout-wrapper">
    <div class="checkout-container">
      <!-- Left Section - Form -->
      <div class="checkout-form-section">
        <div class="section-header">
          <h1>Informasi Pembeli</h1>
          <p>Lengkapi data diri Anda untuk melanjutkan pembayaran</p>
        </div>

        <form @submit.prevent="startPayment" id="checkout-form">
          <div class="form-group">
            <label for="nama_pembeli">
              <span class="label-icon">👤</span>
              Nama Lengkap
            </label>
            <input
              type="text"
              id="nama_pembeli"
              v-model="form.nama_pembeli"
              class="form-input"
              placeholder="Masukkan nama lengkap"
              required
            />
          </div>

          <div class="form-group">
            <label for="email">
              <span class="label-icon">✉️</span>
              Email
            </label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              class="form-input"
              placeholder="email@example.com"
              required
            />
          </div>

          <div class="form-group">
            <label for="telepon">
              <span class="label-icon">📱</span>
              Nomor Telepon
            </label>
            <input
              type="tel"
              id="telepon"
              v-model="form.telepon"
              class="form-input"
              placeholder="+62 812 3456 7890"
              required
            />
          </div>

          <div class="payment-info">
            <div class="info-icon">🔒</div>
            <div class="info-text">
              <strong>Pembayaran Aman</strong>
              <span>Transaksi Anda dilindungi dengan enkripsi SSL</span>
            </div>
          </div>
        </form>
      </div>

      <!-- Right Section - Summary -->
      <div class="checkout-summary-section">
        <div class="summary-header">
          <h2>Ringkasan Pesanan</h2>
        </div>

        <div v-if="keranjang.length > 0" class="ticket-list">
          <div
            v-for="(item, i) in keranjang"
            :key="i"
            class="ticket-item"
          >
            <div class="ticket-details">
              <div class="ticket-name">{{ item.nama_event }}</div>
              <div class="ticket-type" v-if="item.jenis_tiket">{{ item.jenis_tiket }}</div>
              <div class="ticket-qty">Jumlah: {{ item.qty }} tiket</div>
            </div>
            <div class="ticket-price">
              Rp {{ formatHarga(item.harga_tiket * item.qty) }}
            </div>
          </div>
        </div>
        <div v-else class="empty-cart">
          <div class="empty-icon">🛒</div>
          <p>Keranjang Anda kosong</p>
          <button type="button" class="btn-browse" @click="goToEvents">
            Jelajahi Event
          </button>
        </div>

        <div class="summary-calculations" v-if="keranjang.length > 0">
          <div class="calc-row">
            <span>Subtotal</span>
            <span>Rp {{ formatHarga(subtotal) }}</span>
          </div>
          <div class="calc-row">
            <span>Pajak (10%)</span>
            <span>Rp {{ formatHarga(tax) }}</span>
          </div>
          <div class="calc-row">
            <span>Biaya Platform</span>
            <span>Rp {{ formatHarga(platformFee) }}</span>
          </div>
        </div>

        <div class="summary-total" v-if="keranjang.length > 0">
          <span>Total Pembayaran</span>
          <span class="total-amount">Rp {{ formatHarga(total) }}</span>
        </div>

        <div class="action-buttons">
          <button
            type="button"
            class="btn-primary"
            @click="startPayment"
            :disabled="loading || keranjang.length === 0"
          >
            <span v-if="!loading">
              <span class="btn-icon">💳</span>
              Bayar Sekarang
            </span>
            <span v-else>
              <span class="btn-spinner"></span>
              Processing...
            </span>
          </button>
          <button type="button" class="btn-secondary" @click="batal">
            <span class="btn-icon">←</span>
            Kembali
          </button>
        </div>
      </div>
    </div>

    <!-- Loading Modal -->
    <div v-if="modalVisible" class="modal-overlay">
      <div class="modal-content">
        <div class="spinner"></div>
        <p>{{ modalText }}</p>
      </div>
    </div>
  </div>
</template>

<style>
:root {
  --primary-color: #676F53;
  --primary-hover: #1C290D;
  --secondary-color: #A19379;
  --danger-color: #EF4444;
  --text-dark: #381D03;
  --text-light: #736046;
  --border-color: rgba(179, 180, 154, 0.3);
  --bg-light: #FEFAE0;
  --white: #FFFFFF;
  --shadow-sm: 0 1px 3px rgba(28, 41, 13, 0.1);
  --shadow-md: 0 4px 6px rgba(28, 41, 13, 0.1);
  --shadow-lg: 0 10px 25px rgba(28, 41, 13, 0.1);
  --shadow-xl: 0 20px 40px rgba(28, 41, 13, 0.15);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
  background: linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%);
  min-height: 100vh;
  padding: 40px 20px;
}
</style>

<style scoped>
.checkout-wrapper {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
}

.checkout-container {
  display: grid;
  grid-template-columns: 1fr 500px;
  gap: 30px;
  padding: 80px;
  align-items: start;
}

/* Form Section */
.checkout-form-section {
  background: var(--white);
  border-radius: 24px;
  padding: 80px;
  box-shadow: var(--shadow-xl);
}

.section-header {
  margin-bottom: 40px;
}

.section-header h1 {
  font-size: 32px;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 8px;
}

.section-header p {
  color: var(--text-light);
  font-size: 16px;
}

.form-group {
  margin-bottom: 28px;
}

.form-group label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: var(--text-dark);
  margin-bottom: 10px;
  font-size: 15px;
}

.label-icon {
  font-size: 18px;
}

.form-input {
  width: 100%;
  padding: 16px 20px;
  border: 2px solid var(--border-color);
  border-radius: 12px;
  font-size: 16px;
  transition: all 0.3s ease;
  background: var(--white);
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 4px rgba(103, 111, 83, 0.1);
}

.form-input::placeholder {
  color: #D1D5DB;
}

.payment-info {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  background: linear-gradient(135deg, #FEFAE0 0%, #F5F1E3 100%);
  border-radius: 16px;
  border: 2px solid #B3B49A;
  margin-top: 32px;
}

.info-icon {
  font-size: 32px;
  flex-shrink: 0;
}

.info-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.info-text strong {
  color: #676F53;
  font-size: 15px;
}

.info-text span {
  color: #736046;
  font-size: 14px;
}

/* Summary Section */
.checkout-summary-section {
  background: var(--white);
  border-radius: 24px;
  padding: 32px;
  box-shadow: var(--shadow-xl);
  position: sticky;
  top: 40px;
}

.summary-header {
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 2px solid var(--border-color);
}

.summary-header h2 {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-dark);
}

.ticket-list {
  margin-bottom: 24px;
}

.ticket-item {
  padding: 20px;
  background: var(--bg-light);
  border-radius: 16px;
  margin-bottom: 16px;
  border: 2px solid var(--border-color);
  transition: all 0.3s ease;
}

.ticket-item:hover {
  border-color: var(--primary-color);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.ticket-details {
  margin-bottom: 12px;
}

.ticket-name {
  font-weight: 700;
  font-size: 16px;
  color: var(--text-dark);
  margin-bottom: 6px;
}

.ticket-type {
  display: inline-block;
  padding: 4px 12px;
  background: linear-gradient(135deg, #676F53, #1C290D);
  color: white;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  margin-bottom: 6px;
}

.ticket-qty {
  color: var(--text-light);
  font-size: 14px;
}

.ticket-price {
  font-weight: 700;
  font-size: 18px;
  color: var(--primary-color);
  text-align: right;
}

.summary-calculations {
  padding: 20px 0;
  border-top: 2px solid var(--border-color);
  border-bottom: 2px solid var(--border-color);
  margin-bottom: 20px;
}

.calc-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  color: var(--text-light);
  font-size: 15px;
}

.calc-row:last-child {
  margin-bottom: 0;
}

.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 20px;
  background: linear-gradient(135deg, #FEFAE0 0%, #F5F1E3 100%);
  border-radius: 16px;
  margin-bottom: 28px;
  border: 2px solid #B3B49A;
}

.summary-total span:first-child {
  font-weight: 600;
  color: var(--text-dark);
  font-size: 16px;
}

.total-amount {
  font-weight: 800;
  font-size: 28px;
  color: var(--primary-color);
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.btn-primary,
.btn-secondary {
  width: 100%;
  padding: 18px 24px;
  border: none;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.btn-primary {
  background: linear-gradient(135deg, #676F53, #1C290D);
  color: white;
  box-shadow: 0 4px 15px rgba(103, 111, 83, 0.4);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(103, 111, 83, 0.5);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: white;
  color: var(--text-dark);
  border: 2px solid var(--border-color);
}

.btn-secondary:hover {
  background: var(--bg-light);
  border-color: var(--text-light);
  transform: translateY(-2px);
}

.btn-icon {
  font-size: 18px;
}

.btn-spinner {
  display: inline-block;
  width: 18px;
  height: 18px;
  border: 3px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.empty-cart {
  text-align: center;
  padding: 40px 20px;
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.empty-cart p {
  color: var(--text-light);
  margin-bottom: 24px;
  font-size: 16px;
}

.btn-browse {
  padding: 12px 24px;
  background: linear-gradient(135deg, #676F53, #1C290D);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-browse:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(103, 111, 83, 0.4);
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(8px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-content {
  background: white;
  padding: 50px 40px;
  border-radius: 24px;
  text-align: center;
  box-shadow: var(--shadow-xl);
  max-width: 360px;
  width: 90%;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(30px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.spinner {
  border: 4px solid #E5E7EB;
  border-top: 4px solid #676F53;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
  margin: 0 auto 24px auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.modal-content p {
  margin: 0;
  font-size: 16px;
  color: var(--text-dark);
  font-weight: 600;
}

/* Responsive */
@media (max-width: 1200px) {
  .checkout-container {
    grid-template-columns: 1fr 450px;
    gap: 24px;
  }
}

@media (max-width: 968px) {
  .checkout-container {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .checkout-summary-section {
    position: static;
  }
}

@media (max-width: 640px) {
  body {
    padding: 20px 12px;
  }

  .checkout-form-section,
  .checkout-summary-section {
    padding: 24px;
    border-radius: 20px;
  }

  .section-header h1 {
    font-size: 26px;
  }

  .total-amount {
    font-size: 24px;
  }
}
</style>