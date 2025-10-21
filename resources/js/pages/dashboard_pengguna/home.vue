<template>
  <div class="home-wrapper">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-background">
        <div class="hero-gradient"></div>
        <div class="hero-pattern"></div>
      </div>
      
      <div class="hero-container">
        <div class="hero-badge">
          <span class="sparkle">✨</span>
          <span class="badge-text">Festival Musik 2025</span>
        </div>
        
        <h1 class="hero-title">
          Selamat Datang di<br />
          <span class="hero-title-gradient">OURSKY.FEST</span>
        </h1>
        
        <p class="hero-description">
          Ikuti panduan lengkap di bawah ini untuk mendapatkan tiket OURSKY.FEST 2025 dengan mudah dan aman
        </p>

        <!-- <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-icon">📅</span>
            <div class="stat-content">
              <div class="stat-label">Tanggal</div>
              <div class="stat-value">15-17 Des 2025</div>
            </div>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-icon">📍</span>
            <div class="stat-content">
              <div class="stat-label">Lokasi</div>
              <div class="stat-value">Surabaya, ID</div>
            </div>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-item">
            <span class="stat-icon">👥</span>
            <div class="stat-content">
              <div class="stat-label">Pengunjung</div>
              <div class="stat-value">10,000+</div>
            </div>
          </div>
        </div> -->
      </div>
    </section>

    <!-- Steps Section -->
    <section class="steps-section">
      <div class="main-container">
        <div class="section-header">
          <div class="section-badge">Panduan Pemesanan</div>
          <h2 class="section-title">Langkah-langkah Pemesanan</h2>
          <p class="section-subtitle">Proses mudah dan cepat, hanya 5 langkah untuk mendapatkan tiket Anda</p>
        </div>

        <div class="steps-timeline">
          <div 
            v-for="(item, i) in steps" 
            :key="i" 
            class="step-item"
          >
            <div class="step-connector" v-if="i < steps.length - 1"></div>
            <div class="step-circle">
              <span class="step-number">{{ i + 1 }}</span>
            </div>
            <div class="step-card">
              <h3 class="step-title">{{ item.title }}</h3>
              <p class="step-desc">{{ item.desc }}</p>
              <ul class="step-list">
                <li v-for="(li, j) in item.list" :key="j">
                  <span class="check-icon">✓</span>
                  <span>{{ li }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Ticket Types Section -->
    <section class="tickets-section">
      <div class="main-container">
        <div class="section-header">
          <div class="section-badge">Pilihan Tiket</div>
          <h2 class="section-title">Kategori Tiket</h2>
          <p class="section-subtitle">Pilih paket yang sesuai dengan kebutuhan dan budget Anda</p>
        </div>

        <div class="tickets-grid">
          <div
            v-for="(ticket, i) in tickets"
            :key="i"
            @mouseenter="activeTicket = i"
            @mouseleave="activeTicket = null"
            :class="[
              'ticket-card',
              { 'ticket-premium': ticket.premium },
              { 'ticket-active': activeTicket === i }
            ]"
          >
            <!-- <div v-if="ticket.premium" class="ticket-badge-top">
              <span class="badge-star">⭐</span>
              MOST POPULAR
            </div> -->
            
            <div class="ticket-icon-large">{{ ticket.icon }}</div>
            <h4 class="ticket-name">{{ ticket.name }}</h4>
            <div class="ticket-price-wrapper">
              <div class="ticket-price">{{ ticket.price }}</div>
              <div class="ticket-per">per tiket</div>
            </div>

            <div class="ticket-divider"></div>

            <ul class="ticket-features">
              <li v-for="(f, j) in ticket.features" :key="j">
                <span class="feature-icon">✓</span>
                <span>{{ f }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Payment Section -->
    <section class="payment-section">
      <div class="main-container">
        <div class="section-header">
          <div class="section-badge">Pembayaran</div>
          <h2 class="section-title">Metode Pembayaran</h2>
          <p class="section-subtitle">Berbagai pilihan pembayaran yang mudah, cepat, dan aman</p>
        </div>

        <div class="payment-grid">
          <div
            v-for="(pay, i) in payments"
            :key="i"
            class="payment-card"
          >
            <div class="payment-icon-wrapper">
              <div class="payment-icon">{{ pay.icon }}</div>
            </div>
            <h4 class="payment-title">{{ pay.title }}</h4>
            <p class="payment-desc">{{ pay.desc }}</p>
            <div class="payment-arrow">→</div>
          </div>
        </div>

        <div class="payment-security">
          <div class="security-icon">🔒</div>
          <div class="security-text">
            <strong>Pembayaran 100% Aman</strong>
            <span>Transaksi Anda dilindungi dengan enkripsi SSL dan teknologi keamanan tingkat bank</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Rules Section -->
    <section class="rules-section">
      <div class="main-container">
        <div class="section-header">
          <div class="section-badge">Peraturan</div>
          <h2 class="section-title">🎵 Peraturan Selama Festival</h2>
          <p class="section-subtitle">Pastikan Anda membaca dan memahami peraturan berikut untuk kenyamanan bersama</p>
        </div>

        <div class="rules-grid">
          <div v-for="(rule, i) in rules" :key="i" class="rules-card">
            <div class="rules-icon">{{ i === 0 ? '🚫' : '✅' }}</div>
            <h3 class="rules-title">{{ rule.title }}</h3>
            <ul class="rules-list">
              <li v-for="(r, j) in rule.items" :key="j">
                <span class="rules-bullet">•</span>
                <span>{{ r }}</span>
              </li>
            </ul>
          </div>
        </div>

        <div class="rules-warning">
          <div class="warning-icon-wrapper">
            <div class="warning-icon">⚠️</div>
          </div>
          <div class="warning-content">
            <h4 class="warning-title">PERINGATAN PENTING</h4>
            <p class="warning-text">
              Pengunjung yang melanggar peraturan dapat dikeluarkan dari area festival tanpa pengembalian uang tiket. 
              Keputusan panitia bersifat final dan tidak dapat diganggu gugat.
            </p>
            <div class="warning-hotline">
              <span class="hotline-label">Hotline Darurat (24 Jam):</span>
              <span class="hotline-number">(031) 1234-5678</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-container">
        <div class="footer-brand">
          <h3 class="footer-logo">OURSKY.FEST</h3>
          <p class="footer-tagline">Festival musik bertama alam yang menginspirasi</p>
        </div>
        
        <div class="footer-divider"></div>
        
        <div class="footer-info">
          <div class="footer-contact">
            <span class="contact-item">
              <span class="contact-icon">📧</span>
              ourskyfestival@gmail.com
            </span>
            <span class="contact-item">
              <span class="contact-icon">📞</span>
              (031) 1234-5678
            </span>
          </div>
        </div>
        
        <div class="footer-copyright">
          <p>© 2025 OURSKY.FEST. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const activeTicket = ref(null);

const steps = [
  {
    title: "Kunjungi Website Resmi",
    desc: "Pastikan Anda mengunjungi website resmi OURSKY.FEST untuk menghindari penipuan:",
    list: [
      "www.ourskyfest.com (website resmi)",
      "Hindari membeli dari website tidak resmi",
    ],
  },
  {
    title: "Pilih Jenis Tiket",
    desc: "Tentukan kategori tiket yang sesuai dengan budget dan preferensi Anda:",
    list: ["Reguler", "VIP", "VVIP"],
  },
  {
    title: "Isi Data Pembeli",
    desc: "Lengkapi formulir pemesanan dengan data yang valid:",
    list: ["Nama lengkap sesuai KTP", "Email aktif", "Nomor telepon aktif"],
  },
  {
    title: "Pilih Metode Pembayaran",
    desc: "Selesaikan pembayaran melalui metode yang tersedia:",
    list: ["Transfer Bank", "E-wallet", "Virtual Account"],
  },
  {
    title: "Konfirmasi",
    desc: "Setelah pembayaran berhasil:",
    list: [
      "Cek email konfirmasi",
      "Perhatikan kode tiket",
      "Simpan email untuk check-in",
    ],
  },
];

const tickets = [
  {
    name: "REGULER",
    icon: "🌱",
    price: "Rp 150.000",
    features: [
      "Akses area festival",
      "Semua panggung",
      "Food court area",
      "Free merchandise",
      "Certificate kehadiran",
    ],
    premium: false,
  },
  {
    name: "VIP",
    icon: "🌿",
    price: "Rp 350.000",
    features: [
      "Semua fasilitas Reguler",
      "Akses area VIP",
      "Tempat duduk eksklusif",
      "Welcome drink",
      "VIP merchandise",
      "Priority entrance",
    ],
    premium: false,
  },
  {
    name: "VVIP",
    icon: "🌳",
    price: "Rp 750.000",
    features: [
      "Semua fasilitas VIP",
      "Meet & greet artis",
      "Photo session",
      "Signed poster",
      "Backstage access",
      "Catering premium",
      "Parkir gratis",
    ],
    premium: true,
  },
];

const payments = [
  { icon: "🏦", title: "Transfer Bank", desc: "BCA, Mandiri, BNI, BRI, dan bank lainnya" },
  { icon: "📱", title: "E-Wallet", desc: "OVO, GoPay, DANA, LinkAja, ShopeePay" },
  { icon: "💳", title: "Virtual Account", desc: "Bayar via ATM atau mobile banking" },
];

const rules = [
  {
    title: "Barang Terlarang",
    items: [
      "Minuman beralkohol",
      "Narkoba dan sejenisnya",
      "Senjata tajam",
      "Kembang api / petasan",
      "Makanan/minuman dari luar",
      "Hewan peliharaan",
      "Payung besar",
    ],
  },
  {
    title: "Peraturan Umum",
    items: [
      "Gunakan masker bila perlu",
      "Jaga kebersihan area festival",
      "Tidak merokok di area non-smoking",
      "Patuhi instruksi dari staff",
      "Hormati pengunjung lain",
    ],
  },
];
</script>

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

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
  background: linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%);
}
</style>

<style scoped>
.home-wrapper {
  width: 100%;
  overflow-x: hidden;
}

/* Hero Section */
.hero-section {
  position: relative;
  background: linear-gradient(135deg, #1C290D 0%, #676F53 50%, #A19379 100%);
  color: white;
  overflow: hidden;
  padding: 120px 20px 80px;
  min-height: 600px;
  display: flex;
  align-items: center;
}

.hero-background {
  position: absolute;
  inset: 0;
}

.hero-gradient {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.1) 0%, transparent 50%),
              radial-gradient(circle at 80% 70%, rgba(255,255,255,0.05) 0%, transparent 50%);
}

.hero-pattern {
  position: absolute;
  inset: 0;
  background-image: 
    radial-gradient(circle at 25% 25%, rgba(255,255,255,0.05) 1px, transparent 1px),
    radial-gradient(circle at 75% 75%, rgba(255,255,255,0.05) 1px, transparent 1px);
  background-size: 50px 50px;
  animation: float 20s linear infinite;
}

@keyframes float {
  0% { transform: translate(0, 0); }
  100% { transform: translate(50px, 50px); }
}

.hero-container {
  position: relative;
  max-width: 1400px;
  margin: 0 auto;
  text-align: center;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  padding: 10px 24px;
  border-radius: 50px;
  margin-bottom: 32px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.sparkle {
  font-size: 20px;
  animation: sparkle 2s ease-in-out infinite;
}

@keyframes sparkle {
  0%, 100% { transform: scale(1) rotate(0deg); }
  50% { transform: scale(1.2) rotate(180deg); }
}

.badge-text {
  color: white;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  font-size: 13px;
}

.hero-title {
  font-size: 64px;
  font-weight: 800;
  margin-bottom: 24px;
  line-height: 1.1;
  letter-spacing: -0.02em;
}

.hero-title-gradient {
  background: linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  display: inline-block;
}

.hero-description {
  font-size: 20px;
  line-height: 1.6;
  color: rgba(255, 255, 255, 0.9);
  max-width: 800px;
  margin: 0 auto 48px;
}

.hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 32px 48px;
  border-radius: 20px;
  max-width: 900px;
  margin: 0 auto;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  font-size: 32px;
}

.stat-content {
  text-align: left;
}

.stat-label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.7);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
}

/* Main Container */
.main-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Section Header */
.section-header {
  text-align: center;
  margin-bottom: 64px;
}

.section-badge {
  display: inline-block;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  color: white;
  padding: 8px 20px;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 16px;
}

.section-title {
  font-size: 48px;
  font-weight: 800;
  color: var(--text-dark);
  margin-bottom: 16px;
  letter-spacing: -0.02em;
}

.section-subtitle {
  font-size: 18px;
  color: var(--text-light);
  max-width: 700px;
  margin: 0 auto;
  line-height: 1.6;
}

/* Steps Section */
.steps-section {
  padding: 100px 0;
  background: white;
}

.steps-timeline {
  max-width: 1000px;
  margin: 0 auto;
}

.step-item {
  display: grid;
  grid-template-columns: 80px 1fr;
  gap: 32px;
  margin-bottom: 48px;
  position: relative;
}

.step-item:last-child {
  margin-bottom: 0;
}

.step-connector {
  position: absolute;
  left: 40px;
  top: 80px;
  bottom: -48px;
  width: 2px;
  background: linear-gradient(180deg, var(--primary-color), var(--primary-hover));
}

.step-circle {
  position: relative;
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 30px rgba(103, 111, 83, 0.3);
}

.step-number {
  font-size: 32px;
  font-weight: 800;
  color: white;
}

.step-card {
  background: var(--bg-light);
  padding: 32px;
  border-radius: 20px;
  border: 2px solid var(--border-color);
  transition: all 0.3s ease;
}

.step-card:hover {
  border-color: var(--primary-color);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transform: translateX(8px);
}

.step-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 12px;
}

.step-desc {
  color: var(--text-light);
  margin-bottom: 20px;
  line-height: 1.6;
}

.step-list {
  list-style: none;
}

.step-list li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  color: var(--text-dark);
  margin-bottom: 12px;
  font-size: 15px;
}

.step-list li:last-child {
  margin-bottom: 0;
}

.check-icon {
  width: 20px;
  height: 20px;
  background: var(--secondary-color);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
  margin-top: 2px;
}

/* Tickets Section */
.tickets-section {
  padding: 100px 0;
  background: linear-gradient(180deg, var(--bg-light) 0%, white 100%);
}

.tickets-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

.ticket-card {
  background: white;
  border-radius: 24px;
  padding: 40px;
  border: 2px solid var(--border-color);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.ticket-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(103, 111, 83, 0.03), transparent);
  transition: left 0.5s ease;
}

.ticket-card:hover::before {
  left: 100%;
}

.ticket-card:hover {
  border-color: var(--primary-color);
  box-shadow: 0 20px 40px rgba(103, 111, 83, 0.15);
  transform: translateY(-8px);
}

.ticket-premium {
  border: 2px solid #A19379;
  background: linear-gradient(135deg, #FEFAE0 0%, #F5F1E8 100%);
}

.ticket-premium:hover {
  border-color: #A19379;
  box-shadow: 0 20px 40px rgba(161, 147, 121, 0.25);
}

.ticket-active {
  transform: translateY(-12px) scale(1.02);
}

.ticket-badge-top {
  position: absolute;
  top: 20px;
  right: 20px;
  background: linear-gradient(135deg, #A19379, #736046);
  color: white;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.05em;
  display: flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(161, 147, 121, 0.3);
}

.badge-star {
  font-size: 14px;
}

.ticket-icon-large {
  font-size: 64px;
  text-align: center;
  margin-bottom: 20px;
}

.ticket-name {
  text-align: center;
  font-size: 28px;
  font-weight: 800;
  color: var(--text-dark);
  margin-bottom: 8px;
  letter-spacing: -0.01em;
}

.ticket-price-wrapper {
  text-align: center;
  margin-bottom: 24px;
}

.ticket-price {
  font-size: 36px;
  font-weight: 800;
  color: var(--primary-color);
  line-height: 1;
}

.ticket-premium .ticket-price {
  background: linear-gradient(135deg, #A19379, #736046);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.ticket-per {
  font-size: 14px;
  color: var(--text-light);
  margin-top: 4px;
}

.ticket-divider {
  height: 2px;
  background: var(--border-color);
  margin: 24px 0;
}

.ticket-features {
  list-style: none;
  margin-bottom: 32px;
}

.ticket-features li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  color: var(--text-dark);
  margin-bottom: 14px;
  font-size: 15px;
  line-height: 1.5;
}

.ticket-features li:last-child {
  margin-bottom: 0;
}

.feature-icon {
  width: 22px;
  height: 22px;
  background: var(--secondary-color);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
  margin-top: 1px;
}

/* Payment Section */
.payment-section {
  padding: 100px 0;
  background: white;
}

.payment-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
  margin-bottom: 64px;
}

.payment-card {
  background: var(--bg-light);
  border-radius: 24px;
  padding: 40px;
  border: 2px solid var(--border-color);
  text-align: center;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.payment-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.payment-card:hover::before {
  opacity: 1;
}

.payment-card:hover {
  border-color: var(--primary-color);
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(103, 111, 83, 0.2);
}

.payment-icon-wrapper {
  position: relative;
  z-index: 1;
  margin-bottom: 24px;
}

.payment-icon {
  font-size: 64px;
  transition: transform 0.3s ease;
}

.payment-card:hover .payment-icon {
  transform: scale(1.15);
}

.payment-title {
  position: relative;
  z-index: 1;
  font-size: 22px;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 12px;
  transition: color 0.3s ease;
}

.payment-card:hover .payment-title {
  color: white;
}

.payment-desc {
  position: relative;
  z-index: 1;
  color: var(--text-light);
  font-size: 15px;
  line-height: 1.6;
  transition: color 0.3s ease;
}

.payment-card:hover .payment-desc {
  color: rgba(255, 255, 255, 0.9);
}

.payment-arrow {
  position: relative;
  z-index: 1;
  font-size: 24px;
  color: var(--primary-color);
  margin-top: 20px;
  transition: all 0.3s ease;
}

.payment-card:hover .payment-arrow {
  color: white;
  transform: translateX(8px);
}

.payment-security {
  display: flex;
  align-items: center;
  gap: 24px;
  background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
  padding: 32px 40px;
  border-radius: 20px;
  border: 2px solid #BBF7D0;
  max-width: 900px;
  margin: 0 auto;
}

.security-icon {
  font-size: 48px;
  flex-shrink: 0;
}

.security-text {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.security-text strong {
  font-size: 18px;
  color: #059669;
}

.security-text span {
  color: #047857;
  font-size: 15px;
  line-height: 1.5;
}

/* Rules Section */
.rules-section {
  padding: 100px 0;
  background: var(--bg-light);
}

.rules-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 32px;
  margin-bottom: 48px;
}

.rules-card {
  background: white;
  border-radius: 24px;
  padding: 40px;
  border: 2px solid var(--border-color);
  transition: all 0.3s ease;
}

.rules-card:hover {
  border-color: var(--primary-color);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
}

.rules-icon {
  font-size: 48px;
  margin-bottom: 20px;
}

.rules-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 24px;
}

.rules-list {
  list-style: none;
}

.rules-list li {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  color: var(--text-dark);
  margin-bottom: 14px;
  font-size: 15px;
  line-height: 1.6;
}

.rules-list li:last-child {
  margin-bottom: 0;
}

.rules-bullet {
  color: var(--primary-color);
  font-size: 20px;
  font-weight: 700;
  flex-shrink: 0;
}

.rules-warning {
  background: linear-gradient(135deg, #FEF2F2 0%, #FEE2E2 100%);
  border: 2px solid #FCA5A5;
  border-radius: 24px;
  padding: 40px;
  display: flex;
  gap: 24px;
  align-items: flex-start;
}

.warning-icon-wrapper {
  flex-shrink: 0;
}

.warning-icon {
  font-size: 48px;
}

.warning-content {
  flex: 1;
}

.warning-title {
  font-size: 24px;
  font-weight: 800;
  color: #DC2626;
  margin-bottom: 16px;
  letter-spacing: 0.02em;
}

.warning-text {
  color: var(--text-dark);
  line-height: 1.7;
  margin-bottom: 20px;
  font-size: 15px;
}

.warning-hotline {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.hotline-label {
  font-weight: 600;
  color: var(--text-dark);
  font-size: 15px;
}

.hotline-number {
  font-size: 20px;
  font-weight: 800;
  color: #DC2626;
  background: white;
  padding: 8px 20px;
  border-radius: 12px;
  border: 2px solid #FCA5A5;
}

/* Footer */
.footer {
  background: #1C290D;
  color: white;
  padding: 60px 20px 40px;
}

.footer-container {
  max-width: 1400px;
  margin: 0 auto;
}

.footer-brand {
  text-align: center;
  margin-bottom: 32px;
}

.footer-logo {
  font-size: 32px;
  font-weight: 800;
  background: linear-gradient(135deg, #FEFAE0, #B3B49A);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 12px;
}

.footer-tagline {
  color: #9CA3AF;
  font-size: 16px;
}

.footer-divider {
  height: 1px;
  background: #374151;
  margin: 32px 0;
}

.footer-info {
  margin-bottom: 32px;
}

.footer-contact {
  display: flex;
  justify-content: center;
  gap: 40px;
  flex-wrap: wrap;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  color: #D1D5DB;
}

.contact-icon {
  font-size: 18px;
}

.footer-copyright {
  text-align: center;
  padding-top: 32px;
  border-top: 1px solid #374151;
}

.footer-copyright p {
  color: #6B7280;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 1200px) {
  .tickets-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 968px) {
  .hero-title {
    font-size: 48px;
  }

  .section-title {
    font-size: 36px;
  }

  .tickets-grid,
  .payment-grid,
  .rules-grid {
    grid-template-columns: 1fr;
  }

  .step-item {
    grid-template-columns: 60px 1fr;
    gap: 20px;
  }

  .step-circle {
    width: 60px;
    height: 60px;
  }

  .step-number {
    font-size: 24px;
  }

  .step-connector {
    left: 30px;
  }

  .hero-stats {
    flex-direction: column;
    gap: 24px;
    padding: 24px;
  }

  .stat-divider {
    width: 100%;
    height: 1px;
  }
}

@media (max-width: 640px) {
  .hero-section {
    padding: 80px 16px 60px;
  }

  .hero-title {
    font-size: 36px;
  }

  .hero-description {
    font-size: 16px;
  }

  .section-title {
    font-size: 28px;
  }

  .section-subtitle {
    font-size: 16px;
  }

  .main-container {
    padding: 0 16px;
  }

  .steps-section,
  .tickets-section,
  .payment-section,
  .rules-section {
    padding: 60px 0;
  }

  .section-header {
    margin-bottom: 48px;
  }

  .step-card,
  .ticket-card,
  .payment-card,
  .rules-card {
    padding: 24px;
  }

  .rules-warning {
    flex-direction: column;
    padding: 32px 24px;
  }

  .payment-security {
    flex-direction: column;
    text-align: center;
    padding: 24px;
  }
}
</style>