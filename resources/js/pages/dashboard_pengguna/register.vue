<script setup lang="ts">
import { ref } from "vue";
import axios from "@/libs/axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");
const authStore = useAuthStore();

const loading = ref(false);
const error = ref("");
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const register = async () => {
  error.value = "";
  
  if (password.value !== password_confirmation.value) {
    error.value = "Password dan konfirmasi password tidak sama";
    return;
  }
  
  loading.value = true;

  try {
    await axios.post("/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });

    router.push("/login");
  } catch (err: any) {
    error.value =
      err.response?.data?.message ||
      "Gagal melakukan registrasi";
  } finally {
    loading.value = false;
  }
};

const submit = async () => {
  await authStore.login(form.value);

  if (!authStore.error) {
    router.push("/dashboard_pengguna");
  }
};

const goToLogin = () => {
  router.push("/login");
};
</script>

<template>
  <div class="register-wrapper">
    <div class="register-container">
      <div class="register-header">
        <div class="logo-circle">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
          </svg>
        </div>
        <h1>Buat Akun Baru</h1>
        <p>Daftar untuk memulai perjalanan Anda</p>
      </div>

      <form @submit.prevent="register" class="register-form">
        <div v-if="error" class="error-message">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          {{ error }}
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <div class="input-wrapper">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              <input
                id="name"
                v-model="name"
                type="text"
                placeholder="Masukkan nama lengkap"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
              <input
                id="email"
                v-model="email"
                type="email"
                placeholder="nama@email.com"
                required
              />
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Minimal 8 karakter"
                required
              />
              <button
                type="button"
                class="toggle-password"
                @click="showPassword = !showPassword"
              >
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                  <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <div class="input-wrapper">
              <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <input
                id="password_confirmation"
                v-model="password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Ulangi password"
                required
              />
              <button
                type="button"
                class="toggle-password"
                @click="showConfirmPassword = !showConfirmPassword"
              >
                <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                  <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="action-row">
          <button type="submit" class="btn-register" :disabled="loading">
            <span v-if="!loading">Daftar Sekarang</span>
            <span v-else class="loading-spinner">
              <svg class="spinner" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="2" x2="12" y2="6"></line>
                <line x1="12" y1="18" x2="12" y2="22"></line>
                <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                <line x1="2" y1="12" x2="6" y2="12"></line>
                <line x1="18" y1="12" x2="22" y2="12"></line>
                <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
              </svg>
              Memproses...
            </span>
          </button>

          <div class="login-divider">
            <span>atau</span>
          </div>

          <button type="button" class="btn-login" @click="goToLogin">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            Login Di Sini
          </button>
        </div>
      </form>

      <p class="footer-text">
        Dengan mendaftar, Anda menyetujui <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a> kami
      </p>
    </div>
  </div>
</template>

<style scoped>
:root {
  --primary-green: #1c290d;
  --sage-green: #676f53;
  --warm-beige: #b3b49a;
  --cream: #fefae0;
  --taupe: #a19379;
  --brown: #736046;
  --dark-brown: #381d03;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.register-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--cream) 0%, var(--warm-beige) 100%);
  padding: 30px 20px;
}

.register-container {
  width: 100%;
  max-width: 800px;
  background: white;
  padding: 40px 50px;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(28, 41, 13, 0.3);
  border: 3px solid var(--brown);
}

.register-header {
  text-align: center;
  margin-bottom: 32px;
}

.logo-circle {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  color: var(--cream);
  box-shadow: 0 8px 16px rgba(28, 41, 13, 0.3);
  transition: all 0.3s ease;
}

.logo-circle:hover {
  transform: scale(1.05);
  box-shadow: 0 10px 20px rgba(28, 41, 13, 0.4);
}

h1 {
  font-size: 28px;
  font-weight: 800;
  color: var(--primary-green);
  margin-bottom: 8px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
}

.register-header p {
  color: var(--sage-green);
  font-size: 15px;
  font-weight: 500;
}

.register-form {
  margin-bottom: 20px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  width: 100%;
}

label {
  display: block;
  font-size: 14px;
  font-weight: 700;
  color: var(--primary-green);
  margin-bottom: 8px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  color: var(--sage-green);
  pointer-events: none;
}

input {
  width: 100%;
  padding: 12px 14px 12px 44px;
  font-size: 15px;
  border: 2px solid var(--brown);
  border-radius: 12px;
  background: var(--cream);
  transition: all 0.3s ease;
  outline: none;
  color: var(--primary-green);
  font-weight: 500;
}

input::placeholder {
  color: var(--sage-green);
  opacity: 0.7;
}

input:focus {
  border-color: var(--sage-green);
  box-shadow: 0 0 0 3px rgba(103, 111, 83, 0.2);
  background: white;
}

.toggle-password {
  position: absolute;
  right: 14px;
  background: none;
  border: none;
  color: var(--sage-green);
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  border-radius: 6px;
}

.toggle-password:hover {
  color: var(--primary-green);
  background: var(--cream);
}

.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: linear-gradient(135deg, #FFEBEE, #FFCDD2);
  border: 2px solid #EF5350;
  border-radius: 12px;
  color: #C62828;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 20px;
  box-shadow: 0 4px 12px rgba(198, 40, 40, 0.15);
}

.action-row {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  gap: 16px;
  align-items: center;
  margin-top: 24px;
}

.btn-register {
  padding: 14px;
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  color: var(--cream);
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 6px 20px rgba(28, 41, 13, 0.3);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-register:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(28, 41, 13, 0.4);
  background: linear-gradient(135deg, var(--sage-green), var(--primary-green));
}

.btn-register:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.login-divider {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-divider span {
  background: white;
  padding: 0 12px;
  color: var(--sage-green);
  font-size: 14px;
  font-weight: 600;
  position: relative;
  z-index: 1;
}

.btn-login {
  padding: 14px;
  background: transparent;
  color: var(--sage-green);
  border: 2px solid var(--sage-green);
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-login:hover {
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  color: var(--cream);
  border-color: var(--brown);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(115, 96, 70, 0.3);
}

.footer-text {
  text-align: center;
  font-size: 13px;
  color: var(--sage-green);
  margin-top: 20px;
  line-height: 1.6;
  font-weight: 500;
}

.footer-text a {
  color: var(--brown);
  text-decoration: none;
  font-weight: 700;
  transition: all 0.3s ease;
}

.footer-text a:hover {
  color: var(--primary-green);
  text-decoration: underline;
}

@media (max-width: 768px) {
  .register-container {
    padding: 32px 24px;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .action-row {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .login-divider {
    order: 1;
  }

  .btn-register {
    order: 0;
  }

  .btn-login {
    order: 2;
  }

  h1 {
    font-size: 24px;
  }
}
</style>