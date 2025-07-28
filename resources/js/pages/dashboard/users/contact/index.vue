<template>
  <div class="contact-container">
    <div class="contact-hero">
      <h1 class="contact-title">Kontak Kami</h1>
      <p class="contact-subtitle">Hubungi kami untuk informasi lebih lanjut atau kerja sama.</p>
    </div>

    <div class="contact-content">
      <!-- Contact Information -->
      <div class="contact-info">
        <div class="info-section">
          <div class="info-title">
            <div class="info-icon">📞</div>
            Telepon
          </div>
          <div class="info-details">
            {{ contactInfo.phone.primary }}<br>
            {{ contactInfo.phone.secondary }}
          </div>
        </div>

        <div class="info-section">
          <div class="info-title">
            <div class="info-icon">✉️</div>
            Email
          </div>
          <div class="info-details">
            {{ contactInfo.email.info }}<br>
            {{ contactInfo.email.booking }}
          </div>
        </div>

        <div class="info-section">
          <div class="info-title">
            <div class="info-icon">🌐</div>
            Media Sosial
          </div>
          <div class="social-links">
            <a 
              v-for="social in socialLinks" 
              :key="social.name"
              :href="social.url" 
              class="social-link" 
              :title="social.name"
              target="_blank"
              rel="noopener noreferrer"
            >
              <img 
                :src="social.icon" 
                :alt="social.name" 
                style="width: 24px; height: 24px;"
              >
            </a>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="contact-form">
        <h2 class="form-title">Kirim Pesan</h2>
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input 
              type="text" 
              class="form-input" 
              placeholder="Masukkan nama lengkap Anda"
              v-model="form.fullName"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Email</label>
            <input 
              type="email" 
              class="form-input" 
              placeholder="nama@email.com"
              v-model="form.email"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Nomor Telepon</label>
            <input 
              type="tel" 
              class="form-input" 
              placeholder="+62 xxx xxxx xxxx"
              v-model="form.phone"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Subjek</label>
            <input 
              type="text" 
              class="form-input" 
              placeholder="Subjek pesan Anda"
              v-model="form.subject"
              required
            >
          </div>

          <div class="form-group">
            <label class="form-label">Pesan</label>
            <textarea 
              class="form-input form-textarea" 
              placeholder="Tulis pesan Anda di sini..."
              v-model="form.message"
              required
            ></textarea>
          </div>

          <button 
            type="submit" 
            class="submit-btn"
            :disabled="isSubmitting"
          >
            {{ isSubmitting ? 'Mengirim...' : 'Kirim Pesan' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContactPage',
  data() {
    return {
      isSubmitting: false,
      contactInfo: {
        phone: {
          primary: '+62 31 1234 5678',
          secondary: '+62 812 3456 7890'
        },
        email: {
          info: 'info@oursky.konser',
          booking: 'booking@oursky.konser'
        }
      },
      socialLinks: [
        {
          name: 'Instagram',
          url: 'https://instagram.com/oursky.konser',
          icon: '/images/ig.png' // Sesuaikan path dengan project Anda
        },
        {
          name: 'WhatsApp',
          url: 'https://wa.me/6281234567890',
          icon: '/images/wa.png'
        },
        {
          name: 'Twitter',
          url: 'https://twitter.com/ourskykonser',
          icon: '/images/x.png'
        }
      ],
      form: {
        fullName: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      }
    }
  },
  methods: {
    async submitForm() {
      this.isSubmitting = true;
      
      try {
        // Simulasi pengiriman form - ganti dengan API call yang sebenarnya
        await this.sendContactForm(this.form);
        
        // Reset form setelah berhasil
        this.resetForm();
        
        // Emit success event ke parent component
        this.$emit('form-submitted', {
          success: true,
          message: 'Pesan berhasil dikirim!'
        });
        
        // Atau tampilkan notifikasi
        alert('Pesan berhasil dikirim! Kami akan segera menghubungi Anda.');
        
      } catch (error) {
        console.error('Error submitting form:', error);
        this.$emit('form-submitted', {
          success: false,
          message: 'Gagal mengirim pesan. Silakan coba lagi.'
        });
        
        alert('Gagal mengirim pesan. Silakan coba lagi.');
        
      } finally {
        this.isSubmitting = false;
      }
    },
    
    async sendContactForm(formData) {
      // Implementasi API call
      // Contoh menggunakan fetch atau axios
      const response = await fetch('/api/contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      });
      
      if (!response.ok) {
        throw new Error('Failed to send message');
      }
      
      return response.json();
    },
    
    resetForm() {
      this.form = {
        fullName: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      };
    }
  }
}
</script>

<style scoped>
:root {
  --primary-green: #1C290D;
  --sage-green: #676F53;
  --warm-beige: #B3B49A;
  --cream: #FEFAE0;
  --taupe: #A19379;
  --brown: #736046;
  --dark-brown: #381D03;
  --white: #ffffff;
  --shadow: rgba(28, 41, 13, 0.15);
  --shadow-hover: rgba(28, 41, 13, 0.25);
}

.contact-container {
  background: linear-gradient(135deg, var(--cream) 0%, var(--warm-beige) 100%);
  min-height: 100vh;
  padding: 140px 2rem 4rem 2rem;
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
}

.contact-hero {
  text-align: center;
  margin-bottom: 4rem;
  position: relative;
}

.contact-hero::before {
  content: '';
  position: absolute;
  top: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(90deg, var(--primary-green), var(--sage-green));
  border-radius: 2px;
}

.contact-title {
  font-size: 3.5rem;
  font-weight: 900;
  color: var(--primary-green);
  margin-bottom: 1.5rem;
  text-shadow: 2px 2px 4px rgba(28, 41, 13, 0.1);
  letter-spacing: -1px;
  position: relative;
}

.contact-subtitle {
  font-size: 1.3rem;
  color: var(--brown);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
  font-weight: 400;
}

.contact-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-top: 4rem;
}

.contact-info {
  background: linear-gradient(135deg, var(--white) 0%, var(--cream) 100%);
  padding: 3rem;
  border-radius: 25px;
  box-shadow: 0 15px 40px var(--shadow);
  border: 2px solid var(--warm-beige);
  position: relative;
  overflow: hidden;
}

.contact-info::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(103, 111, 83, 0.05), transparent);
  transition: left 0.8s ease;
}

.contact-info:hover::before {
  left: 100%;
}

.contact-form {
  background: linear-gradient(135deg, var(--white) 0%, var(--cream) 100%);
  padding: 3rem;
  border-radius: 25px;
  box-shadow: 0 15px 40px var(--shadow);
  border: 2px solid var(--warm-beige);
}

.info-section {
  margin-bottom: 2.5rem;
}

.info-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--primary-green);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.info-icon {
  width: 24px;
  height: 24px;
  background: linear-gradient(135deg, var(--sage-green), var(--primary-green));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--cream);
  font-size: 0.9rem;
}

.info-details {
  color: var(--brown);
  font-size: 1.1rem;
  line-height: 1.6;
  margin-left: 2rem;
}

.social-links {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  margin-left: 2rem;
}

.social-link {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, var(--sage-green), var(--primary-green));
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--cream);
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 1.2rem;
}

.social-link:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px var(--shadow-hover);
  background: linear-gradient(135deg, var(--brown), var(--dark-brown));
}

.form-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--primary-green);
  margin-bottom: 1.5rem;
  text-align: center;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-weight: 600;
  color: var(--primary-green);
  margin-bottom: 0.5rem;
  font-size: 1rem;
}

.form-input {
  width: 100%;
  padding: 1rem 1.5rem;
  border: 2px solid var(--warm-beige);
  border-radius: 12px;
  background: var(--white);
  font-size: 1rem;
  color: var(--brown);
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: var(--sage-green);
  box-shadow: 0 0 15px rgba(103, 111, 83, 0.2);
  transform: translateY(-2px);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

.submit-btn {
  width: 100%;
  padding: 1.2rem 2rem;
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  color: var(--cream);
  border: none;
  border-radius: 15px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
  box-shadow: 0 8px 25px var(--shadow);
}

.submit-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, var(--sage-green), var(--brown));
  transform: translateY(-3px);
  box-shadow: 0 15px 35px var(--shadow-hover);
}

.submit-btn:active:not(:disabled) {
  transform: translateY(-1px);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Floating decorative elements */
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-15px) rotate(180deg); }
}

.contact-container::before,
.contact-container::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  opacity: 0.2;
  animation: float 6s ease-in-out infinite;
}

.contact-container::before {
  top: 10%;
  left: 5%;
  width: 40px;
  height: 40px;
  background: var(--sage-green);
  animation-delay: 0s;
}

.contact-container::after {
  top: 20%;
  right: 8%;
  width: 30px;
  height: 30px;
  background: var(--warm-beige);
  animation-delay: 3s;
}

@media (max-width: 768px) {
  .contact-container {
    padding: 120px 1rem 2rem 1rem;
  }

  .contact-title {
    font-size: 2.5rem;
  }

  .contact-subtitle {
    font-size: 1.1rem;
  }

  .contact-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .contact-info,
  .contact-form {
    padding: 2rem;
    border-radius: 20px;
  }

  .social-links {
    justify-content: center;
    margin-left: 0;
  }
}
</style>