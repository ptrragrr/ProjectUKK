<template>
  <div class="about-wrapper">
    <div class="container">
      <!-- Hero Section -->
      <div class="hero-section">
        <h1>OURSKY.FEST</h1>
        <p class="subtitle">Platform Tiket Konser Terdepan di Indonesia</p>
      </div>

      <!-- Content Grid -->
      <div class="content-grid">
        <div class="about-card">
          <div class="card-header">
            <span class="card-icon">🎪</span>
            <h2>Siapa Kami?</h2>
          </div>
          <p>
            OURSKY.FEST adalah platform pemesanan tiket konser online yang hadir untuk memberikan
            kemudahan dan kenyamanan bagi para pecinta musik di seluruh Indonesia. Kami
            menghubungkan penggemar musik dengan artis favorit mereka melalui teknologi yang
            inovatif dan pengalaman yang tak terlupakan.
          </p>
        </div>

        <div class="about-card">
          <div class="card-header">
            <span class="card-icon">🎯</span>
            <h2>Visi Kami</h2>
          </div>
          <p>
            Menjadi platform tiket konser terpercaya dan terdepan di Indonesia yang menghubungkan
            jutaan penggemar musik dengan pengalaman hiburan berkelas dunia, serta mendukung
            pertumbuhan industri musik dan hiburan tanah air.
          </p>
        </div>

        <div class="about-card">
          <div class="card-header">
            <span class="card-icon">🚀</span>
            <h2>Misi Kami</h2>
          </div>
          <p>
            Kami berkomitmen untuk menyajikan pengalaman pemesanan tiket yang cepat, aman, dan
            menyenangkan, demi mendukung industri hiburan tanah air. Dengan teknologi terdepan,
            kami memastikan setiap transaksi berjalan lancar dan setiap konser menjadi pengalaman
            yang berkesan.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Component logic
</script>

<style scoped>
:root {
  --primary-color: #676F53;
  --primary-hover: #1C290D;
  --secondary-color: #A19379;
  --accent-color: #FEFAE0;
  --text-dark: #381D03;
  --text-light: #736046;
  --border-color: rgba(179, 180, 154, 0.3);
  --white: #FFFFFF;
}

.about-wrapper {
  min-height: 100vh;
  background: linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%);
  padding: 100px 20px 80px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Hero Section */
.hero-section {
  text-align: center;
  margin-bottom: 80px;
  padding: 60px 40px;
  background: linear-gradient(135deg, #1C290D 0%, #676F53 50%, #A19379 100%);
  border-radius: 32px;
  color: white;
  box-shadow: 0 20px 40px rgba(28, 41, 13, 0.15);
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -20%;
  width: 400px;
  height: 400px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
}

.hero-section::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -10%;
  width: 300px;
  height: 300px;
  background: #FEFAE0;
  opacity: 0.1;
  border-radius: 50%;
}

.hero-section h1 {
  font-size: 3.5em;
  font-weight: 700;
  margin-bottom: 20px;
  position: relative;
  z-index: 2;
  letter-spacing: -0.02em;
}

.hero-section .subtitle {
  font-size: 1.3em;
  opacity: 0.9;
  font-weight: 300;
  position: relative;
  z-index: 2;
}

/* Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 40px;
}

.about-card {
  background: white;
  padding: 40px;
  border-radius: 24px;
  box-shadow: 0 12px 30px rgba(28, 41, 13, 0.1);
  border: 2px solid var(--border-color);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.about-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, #FEFAE0, #A19379, #676F53);
}

.about-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(28, 41, 13, 0.15);
  border-color: var(--primary-color);
}

.card-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}

.card-icon {
  font-size: 36px;
}

.about-card h2 {
  font-size: 1.8em;
  color: var(--text-dark);
  font-weight: 700;
  margin: 0;
}

.about-card p {
  line-height: 1.8;
  color: var(--text-light);
  font-size: 1.05em;
}

/* Responsive */
@media (max-width: 968px) {
  .about-wrapper {
    padding: 80px 16px 60px;
  }

  .hero-section {
    padding: 50px 32px;
    margin-bottom: 60px;
  }

  .hero-section h1 {
    font-size: 2.5em;
  }

  .hero-section .subtitle {
    font-size: 1.1em;
  }

  .content-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .about-card {
    padding: 32px;
  }
}

@media (max-width: 640px) {
  .about-wrapper {
    padding: 60px 12px 48px;
  }

  .hero-section {
    padding: 40px 24px;
    margin-bottom: 48px;
  }

  .hero-section h1 {
    font-size: 2em;
  }

  .hero-section .subtitle {
    font-size: 1em;
  }

  .content-grid {
    gap: 24px;
  }

  .about-card {
    padding: 28px 20px;
  }

  .card-icon {
    font-size: 32px;
  }

  .about-card h2 {
    font-size: 1.5em;
  }

  .about-card p {
    font-size: 1em;
  }
}
</style>