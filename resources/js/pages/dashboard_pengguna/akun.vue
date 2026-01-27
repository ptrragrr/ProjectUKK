<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import axios from '@/libs/axios'

const loading = ref(true)

const userData = reactive({
  name: '',
  email: '',
  phone: '',
  joinDate: '',
  stats: {
    tickets: 0,
    events: 0,
    favorites: 0
  }
})

const passwordData = reactive({
  current: '',
  new: '',
  confirm: ''
})

const sections = reactive({
  personal: true,
  security: false,
  preferences: false
})

const twoFactorEnabled = ref(false)

const preferences = reactive({
  emailNotifications: true,
  pushNotifications: false,
  language: 'id'
})

const fetchUser = async () => {
  try {
    const { data } = await axios.get('/me')

    userData.name = data.name
    userData.email = data.email
    userData.phone = data.phone
    userData.joinDate = data.joined_at
    userData.stats = data.stats
  } catch (e) {
    alert('Gagal memuat data user')
  } finally {
    loading.value = false
  }
}

const updatePersonalInfo = async () => {
  try {
    await axios.put('/profile', {
      name: userData.name,
      email: userData.email,
      phone: userData.phone
    })

    alert('Profil berhasil diperbarui')
  } catch (e) {
    alert('Gagal update profil')
  }
}

const updatePassword = async () => {
  if (passwordData.new !== passwordData.confirm) {
    alert('Konfirmasi password tidak cocok')
    return
  }

  try {
    await axios.put('/password', passwordData)
    alert('Password berhasil diubah')

    passwordData.current = ''
    passwordData.new = ''
    passwordData.confirm = ''
  } catch (e) {
    alert('Gagal update password')
  }
}

function toggleSection(section: 'personal' | 'security' | 'preferences') {
  sections[section] = !sections[section]
}

onMounted(fetchUser)
</script>

<template>
  <div class="account-page">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <!-- Header Section -->
          <div class="page-header mb-5">
            <h1 class="display-4 fw-bold">
              <i class="fas fa-user-circle me-3"></i>
              Akun Saya
            </h1>
            <p class="lead">Kelola informasi profil dan preferensi Anda</p>
          </div>

          <!-- Profile Card -->
          <div class="profile-card mb-4">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-md-3 text-center mb-4 mb-md-0">
                  <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                      <i class="fas fa-user"></i>
                    </div>
                    <button class="btn btn-upload-photo">
                      <i class="fas fa-camera me-2"></i>
                      Ubah Foto
                    </button>
                  </div>
                </div>
                <div class="col-md-9">
                  <div class="profile-info">
                    <h2 class="profile-name">{{ userData.name }}</h2>
                    <p class="profile-email">
                      <i class="fas fa-envelope me-2"></i>
                      {{ userData.email }}
                    </p>
                    <p class="profile-joined">
                      <i class="fas fa-calendar-alt me-2"></i>
                      Bergabung sejak {{ userData.joinDate }}
                    </p>
                    <div class="profile-badges">
                      <span class="badge-item">
                        <i class="fas fa-star me-1"></i>
                        Member Premium
                      </span>
                      <span class="badge-item verified">
                        <i class="fas fa-check-circle me-1"></i>
                        Terverifikasi
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Stats Cards -->
          <div class="row mb-4">
            <div class="col-md-4 mb-3">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                  <h3>{{ userData.stats.tickets }}</h3>
                  <p>Tiket Aktif</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                  <h3>{{ userData.stats.events }}</h3>
                  <p>Event Dihadiri</p>
                </div>
              </div>
            </div>
            <div class="col-md-4 mb-3">
              <div class="stat-card">
                <div class="stat-icon">
                  <i class="fas fa-heart"></i>
                </div>
                <div class="stat-info">
                  <h3>{{ userData.stats.favorites }}</h3>
                  <p>Favorit</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Information Sections -->
          <div class="accordion custom-accordion" id="accountAccordion">
            <!-- Personal Information -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button 
                  class="accordion-button" 
                  type="button" 
                  data-bs-toggle="collapse" 
                  data-bs-target="#personalInfo"
                  @click="toggleSection('personal')"
                  :class="{ collapsed: !sections.personal }"
                >
                  <i class="fas fa-user-edit me-3"></i>
                  Informasi Pribadi
                </button>
              </h2>
              <div 
                id="personalInfo" 
                class="accordion-collapse collapse show"
                :class="{ show: sections.personal }"
              >
                <div class="accordion-body">
                  <form @submit.prevent="updatePersonalInfo">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input 
                          type="text" 
                          class="form-control custom-input" 
                          v-model="userData.name"
                          placeholder="Masukkan nama lengkap"
                        >
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input 
                          type="email" 
                          class="form-control custom-input" 
                          v-model="userData.email"
                          placeholder="email@example.com"
                        >
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input 
                          type="tel" 
                          class="form-control custom-input" 
                          v-model="userData.phone"
                          placeholder="+62 xxx xxxx xxxx"
                        >
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input 
                          type="date" 
                          class="form-control custom-input" 
                          v-model="userData.birthdate"
                        >
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea 
                          class="form-control custom-input" 
                          rows="3"
                          v-model="userData.address"
                          placeholder="Masukkan alamat lengkap"
                        ></textarea>
                      </div>
                    </div>
                    <button type="submit" class="btn btn-save">
                      <i class="fas fa-save me-2"></i>
                      Simpan Perubahan
                    </button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Security Settings -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button 
                  class="accordion-button collapsed" 
                  type="button"
                  @click="toggleSection('security')"
                  :class="{ collapsed: !sections.security }"
                >
                  <i class="fas fa-shield-alt me-3"></i>
                  Keamanan
                </button>
              </h2>
              <div 
                class="accordion-collapse collapse"
                :class="{ show: sections.security }"
              >
                <div class="accordion-body">
                  <form @submit.prevent="updatePassword">
                    <div class="mb-3">
                      <label class="form-label">Password Saat Ini</label>
                      <input 
                        type="password" 
                        class="form-control custom-input" 
                        v-model="passwordData.current"
                        placeholder="Masukkan password saat ini"
                      >
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Password Baru</label>
                      <input 
                        type="password" 
                        class="form-control custom-input" 
                        v-model="passwordData.new"
                        placeholder="Masukkan password baru"
                      >
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Konfirmasi Password Baru</label>
                      <input 
                        type="password" 
                        class="form-control custom-input" 
                        v-model="passwordData.confirm"
                        placeholder="Konfirmasi password baru"
                      >
                    </div>
                    <button type="submit" class="btn btn-save">
                      <i class="fas fa-key me-2"></i>
                      Ubah Password
                    </button>
                  </form>
                  
                  <hr class="my-4">
                  
                  <div class="security-option">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h5>Autentikasi Dua Faktor</h5>
                        <p class="mb-0">Tingkatkan keamanan akun Anda</p>
                      </div>
                      <div class="form-check form-switch">
                        <input 
                          class="form-check-input custom-switch" 
                          type="checkbox" 
                          v-model="twoFactorEnabled"
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Preferences -->
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button 
                  class="accordion-button collapsed" 
                  type="button"
                  @click="toggleSection('preferences')"
                  :class="{ collapsed: !sections.preferences }"
                >
                  <i class="fas fa-cog me-3"></i>
                  Preferensi
                </button>
              </h2>
              <div 
                class="accordion-collapse collapse"
                :class="{ show: sections.preferences }"
              >
                <div class="accordion-body">
                  <div class="preference-item">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div>
                        <h5>Notifikasi Email</h5>
                        <p class="mb-0">Terima update tentang event dan promo</p>
                      </div>
                      <div class="form-check form-switch">
                        <input 
                          class="form-check-input custom-switch" 
                          type="checkbox" 
                          v-model="preferences.emailNotifications"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <div class="preference-item">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div>
                        <h5>Notifikasi Push</h5>
                        <p class="mb-0">Terima notifikasi langsung di perangkat</p>
                      </div>
                      <div class="form-check form-switch">
                        <input 
                          class="form-check-input custom-switch" 
                          type="checkbox" 
                          v-model="preferences.pushNotifications"
                        >
                      </div>
                    </div>
                  </div>
                  
                  <div class="preference-item">
                    <div class="mb-3">
                      <label class="form-label">Bahasa</label>
                      <select class="form-select custom-input" v-model="preferences.language">
                        <option value="id">Bahasa Indonesia</option>
                        <option value="en">English</option>
                      </select>
                    </div>
                  </div>
                  
                  <button class="btn btn-save" @click="savePreferences">
                    <i class="fas fa-check me-2"></i>
                    Simpan Preferensi
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Danger Zone -->
          <div class="danger-zone mt-4">
            <h4 class="text-danger mb-3">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Zona Berbahaya
            </h4>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h5>Hapus Akun</h5>
                <p class="mb-0">Hapus akun Anda secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
              </div>
              <button class="btn btn-danger" @click="confirmDeleteAccount">
                <i class="fas fa-trash-alt me-2"></i>
                Hapus Akun
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url("https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css");
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css");

:root {
  --primary-green: #1c290d;
  --sage-green: #676f53;
  --warm-beige: #b3b49a;
  --cream: #fefae0;
  --taupe: #a19379;
  --brown: #736046;
  --dark-brown: #381d03;
}

.account-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--cream) 0%, var(--warm-beige) 100%);
  padding-top: 100px;
  padding-bottom: 50px;
}

/* Page Header */
.page-header h1 {
  color: var(--primary-green);
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.page-header p {
  color: var(--sage-green);
  font-size: 1.2rem;
}

/* Profile Card */
.profile-card {
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  border-radius: 24px;
  box-shadow: 0 10px 40px rgba(28, 41, 13, 0.3);
  padding: 2rem;
  border: 3px solid var(--brown);
  transition: all 0.3s ease;
}

.profile-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 50px rgba(28, 41, 13, 0.4);
}

.profile-avatar-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.profile-avatar {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  display: flex;
  align-items: center;
  justify-content: center;
  border: 5px solid var(--cream);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  transition: all 0.3s ease;
}

.profile-avatar:hover {
  transform: scale(1.05);
}

.profile-avatar i {
  font-size: 4rem;
  color: var(--cream);
}

.btn-upload-photo {
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  color: var(--cream);
  border: none;
  padding: 0.6rem 1.5rem;
  border-radius: 25px;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-upload-photo:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  background: linear-gradient(135deg, var(--brown), var(--dark-brown));
}

.profile-info {
  color: var(--cream);
}

.profile-name {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.profile-email,
.profile-joined {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 0.5rem;
}

.profile-badges {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-top: 1rem;
}

.badge-item {
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  padding: 0.5rem 1.2rem;
  border-radius: 25px;
  font-weight: 600;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.badge-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.badge-item.verified {
  background: linear-gradient(135deg, #4CAF50, #45a049);
}

/* Stats Cards */
.stat-card {
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  border-radius: 16px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1.5rem;
  box-shadow: 0 8px 24px rgba(28, 41, 13, 0.3);
  border: 2px solid var(--brown);
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(28, 41, 13, 0.4);
}

.stat-icon {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.stat-icon i {
  font-size: 2rem;
  color: var(--cream);
}

.stat-info h3 {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--cream);
  margin-bottom: 0;
}

.stat-info p {
  color: var(--cream);
  opacity: 0.9;
  margin-bottom: 0;
  font-weight: 600;
}

/* Custom Accordion */
.custom-accordion .accordion-item {
  background: white;
  border-radius: 16px;
  margin-bottom: 1rem;
  border: 2px solid var(--brown);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.custom-accordion .accordion-button {
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  color: var(--cream);
  font-weight: 700;
  font-size: 1.2rem;
  padding: 1.5rem;
  border: none;
  box-shadow: none;
}

.custom-accordion .accordion-button:not(.collapsed) {
  background: linear-gradient(135deg, var(--sage-green), var(--primary-green));
  color: var(--cream);
}

.custom-accordion .accordion-button:focus {
  box-shadow: none;
  border: none;
}

.custom-accordion .accordion-button::after {
  filter: brightness(0) invert(1);
}

.accordion-body {
  padding: 2rem;
  background: linear-gradient(135deg, rgba(254, 250, 224, 0.5), rgba(179, 180, 154, 0.3));
}

/* Form Inputs */
.form-label {
  font-weight: 700;
  color: var(--primary-green);
  margin-bottom: 0.5rem;
  font-size: 1rem;
}

.custom-input {
  border: 2px solid var(--brown);
  border-radius: 12px;
  padding: 0.8rem 1.2rem;
  background: white;
  color: var(--primary-green);
  font-weight: 500;
  transition: all 0.3s ease;
}

.custom-input:focus {
  border-color: var(--sage-green);
  box-shadow: 0 0 0 0.2rem rgba(103, 111, 83, 0.25);
  outline: none;
  background: var(--cream);
}

/* Buttons */
.btn-save {
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  color: var(--cream);
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 25px;
  font-weight: 700;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-save:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  background: linear-gradient(135deg, var(--brown), var(--dark-brown));
}

/* Custom Switch */
.custom-switch {
  width: 60px;
  height: 30px;
  cursor: pointer;
}

.custom-switch:checked {
  background-color: var(--sage-green);
  border-color: var(--sage-green);
}

.custom-switch:focus {
  box-shadow: 0 0 0 0.2rem rgba(103, 111, 83, 0.25);
}

/* Security Options */
.security-option {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  border: 2px solid var(--brown);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.security-option h5 {
  color: var(--primary-green);
  font-weight: 700;
  margin-bottom: 0.3rem;
}

.security-option p {
  color: var(--sage-green);
  font-size: 0.95rem;
}

/* Preference Items */
.preference-item {
  background: white;
  padding: 1.5rem;
  border-radius: 12px;
  border: 2px solid var(--brown);
  margin-bottom: 1rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.preference-item h5 {
  color: var(--primary-green);
  font-weight: 700;
  margin-bottom: 0.3rem;
}

.preference-item p {
  color: var(--sage-green);
  font-size: 0.95rem;
}

/* Danger Zone */
.danger-zone {
  background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(220, 53, 69, 0.05));
  padding: 2rem;
  border-radius: 16px;
  border: 2px solid #dc3545;
  box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
}

.danger-zone h4 {
  font-weight: 800;
}

.danger-zone h5 {
  color: var(--primary-green);
  font-weight: 700;
  margin-bottom: 0.3rem;
}

.danger-zone p {
  color: var(--sage-green);
}

.danger-zone .btn-danger {
  font-weight: 700;
  padding: 0.7rem 1.5rem;
  border-radius: 25px;
  transition: all 0.3s ease;
}

.danger-zone .btn-danger:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
  .account-page {
    padding-top: 80px;
  }

  .page-header h1 {
    font-size: 2rem;
  }

  .profile-name {
    font-size: 1.8rem;
  }

  .stat-card {
    flex-direction: column;
    text-align: center;
  }

  .stat-info h3 {
    font-size: 2rem;
  }

  .danger-zone {
    text-align: center;
  }

  .danger-zone .d-flex {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>