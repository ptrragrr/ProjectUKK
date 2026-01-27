<script setup lang="ts">
import { ref } from "vue";
import axios from "@/libs/axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();

const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref("");
const showPassword = ref(false);
const authStore = useAuthStore();

const form = ref({
  email: "",
  password: "",
});

const login = async () => {
  error.value = "";
  loading.value = true;

  try {
    await axios.post("/auth/login", {
      email: email.value,
      password: password.value,
    }).then((res) => {
      authStore.setAuth(res.data.user, res.data.token);
      router.push("/dashboard_pengguna"); // 🔥 REDIRECT SETELAH LOGIN
    });
  } catch (err: any) {
    error.value =
      err.response?.data?.message ||
      "Email atau password salah";
  } finally {
    loading.value = false;
  }
};

const submit = async () => {
  await authStore.login(form.value);

  if (!authStore.error) {
    router.push("/dashboard_pengguna"); // 🔥 REDIRECT SETELAH LOGIN
  }
};

const goToRegister = () => {
  router.push("/register");
};
</script>

<template>
  <div class="login-wrapper">
    <div class="login-container">
      <div class="login-header">
        <div class="logo-circle">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
            <polyline points="10 17 15 12 10 7"></polyline>
            <line x1="15" y1="12" x2="3" y2="12"></line>
          </svg>
        </div>
        <h1>Selamat Datang</h1>
        <p>Silakan login untuk melanjutkan</p>
      </div>

      <form @submit.prevent="login" class="login-form">
        <div v-if="error" class="error-message">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          {{ error }}
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
              placeholder="Masukkan password"
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

        <button type="submit" class="btn-login" :disabled="loading">
          <span v-if="!loading">Login</span>
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
      </form>

      <div class="register-section">
        <p>Belum punya akun?</p>
        <button type="button" class="btn-register" @click="goToRegister">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
          </svg>
          Buat Akun Baru
        </button>
      </div>

      <p class="footer-text">
        Dengan login, Anda menyetujui <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a> kami
      </p>
    </div>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  /* background: linear-gradient(135deg, #8B7355 0%, #A0826D 50%, #C9B8A8 100%); */
  padding: 20px;
}

.login-container {
  width: 100%;
  max-width: 440px;
  background: #FAF8F5;
  padding: 40px;
  border-radius: 24px;
  box-shadow: 0 20px 60px rgba(72, 52, 37, 0.3);
}

.login-header {
  text-align: center;
  margin-bottom: 32px;
}

.logo-circle {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, #8B7355 0%, #A0826D 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  color: #FAF8F5;
  box-shadow: 0 8px 16px rgba(139, 115, 85, 0.3);
}

h1 {
  font-size: 28px;
  font-weight: 700;
  color: #3E2723;
  margin-bottom: 8px;
}

.login-header p {
  color: #6D5D52;
  font-size: 15px;
}

.login-form {
  margin-bottom: 24px;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  color: #4E3B31;
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
  color: #9B8778;
  pointer-events: none;
}

input {
  width: 100%;
  padding: 12px 14px 12px 44px;
  font-size: 15px;
  border: 2px solid #D7CCC8;
  border-radius: 12px;
  background: #FFFFFF;
  transition: all 0.3s ease;
  outline: none;
  color: #3E2723;
}

input::placeholder {
  color: #A1887F;
}

input:focus {
  border-color: #8B7355;
  box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.15);
  background: #FFF;
}

.toggle-password {
  position: absolute;
  right: 14px;
  background: none;
  border: none;
  color: #9B8778;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}

.toggle-password:hover {
  color: #8B7355;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: #FFEBEE;
  border: 1px solid #EF9A9A;
  border-radius: 10px;
  color: #C62828;
  font-size: 14px;
  margin-bottom: 20px;
}

.btn-login {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #8B7355 0%, #A0826D 100%);
  color: #FAF8F5;
  border: none;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(139, 115, 85, 0.4);
}

.btn-login:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(139, 115, 85, 0.5);
  background: linear-gradient(135deg, #7A6449 0%, #8F7560 100%);
}

.btn-login:disabled {
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

.register-section {
  text-align: center;
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #D7CCC8;
}

.register-section p {
  color: #6D5D52;
  font-size: 14px;
  margin-bottom: 12px;
}

.btn-register {
  background: transparent;
  color: #8B7355;
  border: 2px solid #8B7355;
  border-radius: 12px;
  padding: 12px 24px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-register:hover {
  background: #8B7355;
  color: #FAF8F5;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(139, 115, 85, 0.3);
}

.footer-text {
  text-align: center;
  font-size: 13px;
  color: #6D5D52;
  margin-top: 24px;
  line-height: 1.6;
}

.footer-text a {
  color: #8B7355;
  text-decoration: none;
  font-weight: 600;
}

.footer-text a:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .login-container {
    padding: 32px 24px;
  }

  h1 {
    font-size: 24px;
  }
}
</style>