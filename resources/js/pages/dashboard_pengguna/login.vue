<script setup lang="ts">
import { ref } from "vue";
import axios from "@/libs/axios";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import Swal from "sweetalert2";

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
      router.push("/dashboard_pengguna");
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
    router.push("/dashboard_pengguna");
  }
};

const goToRegister = () => {
  router.push("/register");
};

const forgotPassword = async () => {
  const { value: emailInput } = await Swal.fire({
    title: '<span style="color: #1c290d; font-weight: 800;">Lupa Password?</span>',
    html: '<p style="color: #676f53; font-size: 1rem; font-weight: 500; margin-bottom: 1rem;">Masukkan email Anda dan kami akan mengirimkan link reset password</p>',
    input: 'email',
    inputPlaceholder: 'nama@email.com',
    inputAttributes: {
      autocapitalize: 'off',
      autocomplete: 'email'
    },
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-paper-plane me-2"></i>Kirim Link',
    cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
    buttonsStyling: false,
    customClass: {
      popup: 'custom-swal-popup',
      input: 'custom-swal-input',
      confirmButton: 'custom-swal-confirm',
      cancelButton: 'custom-swal-cancel',
    },
    background: 'linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%)',
    backdrop: `
      rgba(28, 41, 13, 0.6)
      center
      no-repeat
    `,
    inputValidator: (value) => {
      if (!value) {
        return 'Email tidak boleh kosong!'
      }
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        return 'Format email tidak valid!'
      }
    }
  });

  if (emailInput) {
    try {
      // Loading state
      Swal.fire({
        title: '<span style="color: #1c290d; font-weight: 800;">Mengirim...</span>',
        html: '<p style="color: #676f53; font-size: 1rem; font-weight: 500;">Mohon tunggu sebentar</p>',
        allowOutsideClick: false,
        showConfirmButton: false,
        customClass: {
          popup: 'custom-swal-popup',
        },
        background: 'linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%)',
        didOpen: () => {
          Swal.showLoading();
        }
      });

      // Call API untuk forgot password
      await axios.post("/auth/forgot-password", {
        email: emailInput
      });

      // Success
      Swal.fire({
        title: '<span style="color: #1c290d; font-weight: 800;">Link Terkirim!</span>',
        html: `<p style="color: #676f53; font-size: 1rem; font-weight: 500;">Link reset password telah dikirim ke <strong>${emailInput}</strong>. Silakan cek email Anda.</p>`,
        icon: 'success',
        iconColor: '#4CAF50',
        confirmButtonText: '<i class="fas fa-check me-2"></i>OK',
        buttonsStyling: false,
        customClass: {
          popup: 'custom-swal-popup',
          confirmButton: 'custom-swal-success',
          icon: 'custom-swal-icon'
        },
        background: 'linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%)',
        backdrop: `
          rgba(28, 41, 13, 0.6)
          center
          no-repeat
        `,
        timer: 5000,
        timerProgressBar: true
      });

    } catch (err: any) {
      // Error
      Swal.fire({
        title: '<span style="color: #1c290d; font-weight: 800;">Gagal!</span>',
        html: `<p style="color: #676f53; font-size: 1rem; font-weight: 500;">${err.response?.data?.message || 'Email tidak ditemukan atau terjadi kesalahan'}</p>`,
        icon: 'error',
        iconColor: '#EF5350',
        confirmButtonText: '<i class="fas fa-times me-2"></i>Tutup',
        buttonsStyling: false,
        customClass: {
          popup: 'custom-swal-popup',
          confirmButton: 'custom-swal-cancel',
          icon: 'custom-swal-icon'
        },
        background: 'linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%)',
        backdrop: `
          rgba(28, 41, 13, 0.6)
          center
          no-repeat
        `
      });
    }
  }
};
</script>

<template>
  <div class="login-wrapper">
    <div class="login-container">
      <div class="login-header">
        <div class="logo-circle">
          <i class="fas fa-sign-in-alt"></i>
        </div>
        <h1>Selamat Datang</h1>
        <p>Silakan login untuk melanjutkan</p>
      </div>

      <form @submit.prevent="login" class="login-form">
        <div v-if="error" class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          {{ error }}
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
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
            <i class="fas fa-lock input-icon"></i>
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
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </button>
          </div>
        </div>

        <div class="forgot-password-wrapper">
          <button type="button" class="forgot-password-link" @click="forgotPassword">
            <i class="fas fa-key me-1"></i>Lupa Password?
          </button>
        </div>

        <button type="submit" class="btn-login" :disabled="loading">
          <span v-if="!loading">
            <i class="fas fa-sign-in-alt me-2"></i>Login
          </span>
          <span v-else class="loading-spinner">
            <i class="fas fa-spinner fa-spin me-2"></i>Memproses...
          </span>
        </button>
      </form>

      <div class="register-section">
        <p>Belum punya akun?</p>
        <button type="button" class="btn-register" @click="goToRegister">
          <i class="fas fa-user-plus me-2"></i>Buat Akun Baru
        </button>
      </div>

      <p class="footer-text">
        Dengan login, Anda menyetujui <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a> kami
      </p>
    </div>
  </div>
</template>

<style scoped>
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
  background: linear-gradient(135deg, var(--cream) 0%, var(--warm-beige) 100%);
  padding: 20px;
}

.login-container {
  width: 100%;
  max-width: 420px;
  background: white;
  padding: 35px 30px;
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(28, 41, 13, 0.25);
  border: 3px solid var(--brown);
}

.login-header {
  text-align: center;
  margin-bottom: 28px;
}

.logo-circle {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  color: var(--cream);
  box-shadow: 0 6px 20px rgba(28, 41, 13, 0.3);
  font-size: 1.8rem;
  transition: all 0.3s ease;
}

.logo-circle:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 25px rgba(28, 41, 13, 0.4);
}

h1 {
  font-size: 1.8rem;
  font-weight: 800;
  color: var(--primary-green);
  margin-bottom: 6px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
}

.login-header p {
  color: var(--sage-green);
  font-size: 0.95rem;
  font-weight: 500;
}

.login-form {
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 18px;
}

label {
  display: block;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--primary-green);
  margin-bottom: 6px;
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
  font-size: 1rem;
}

input {
  width: 100%;
  padding: 11px 14px 11px 42px;
  font-size: 0.95rem;
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
  right: 12px;
  background: none;
  border: none;
  color: var(--sage-green);
  cursor: pointer;
  padding: 6px;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  border-radius: 6px;
  font-size: 1rem;
}

.toggle-password:hover {
  color: var(--primary-green);
  background: var(--cream);
}

.error-message {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: linear-gradient(135deg, #FFEBEE, #FFCDD2);
  border: 2px solid #EF5350;
  border-radius: 12px;
  color: #C62828;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 18px;
  box-shadow: 0 4px 12px rgba(198, 40, 40, 0.15);
}

.error-message i {
  font-size: 1.1rem;
}

.forgot-password-wrapper {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 18px;
  margin-top: -8px;
}

.forgot-password-link {
  background: none;
  border: none;
  color: var(--brown);
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 4px 8px;
  border-radius: 6px;
}

.forgot-password-link:hover {
  color: var(--primary-green);
  background: var(--cream);
  transform: translateX(-2px);
}

.forgot-password-link i {
  font-size: 0.8rem;
}

.btn-login {
  width: 100%;
  padding: 13px;
  background: linear-gradient(135deg, var(--primary-green), var(--sage-green));
  color: var(--cream);
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 6px 20px rgba(28, 41, 13, 0.3);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.btn-login:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(28, 41, 13, 0.4);
  background: linear-gradient(135deg, var(--sage-green), var(--primary-green));
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

.register-section {
  text-align: center;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 2px solid var(--warm-beige);
}

.register-section p {
  color: var(--sage-green);
  font-size: 0.9rem;
  margin-bottom: 10px;
  font-weight: 500;
}

.btn-register {
  background: transparent;
  color: var(--sage-green);
  border: 2px solid var(--sage-green);
  border-radius: 12px;
  padding: 11px 24px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-register:hover {
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  color: var(--cream);
  border-color: var(--brown);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(115, 96, 70, 0.3);
}

.footer-text {
  text-align: center;
  font-size: 0.8rem;
  color: var(--sage-green);
  margin-top: 18px;
  line-height: 1.5;
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

.me-1 {
  margin-right: 0.25rem;
}

.me-2 {
  margin-right: 0.5rem;
}

@media (max-width: 480px) {
  .login-container {
    padding: 28px 24px;
    max-width: 380px;
  }

  h1 {
    font-size: 1.5rem;
  }

  .logo-circle {
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
  }
}
</style>

<style>
/* Global SweetAlert2 Custom Styles */
.custom-swal-popup {
  border-radius: 24px !important;
  border: 3px solid #736046 !important;
  box-shadow: 0 15px 50px rgba(28, 41, 13, 0.4) !important;
  padding: 2rem !important;
}

.custom-swal-icon {
  border-width: 4px !important;
  width: 80px !important;
  height: 80px !important;
}

.custom-swal-input {
  border: 2px solid #736046 !important;
  border-radius: 12px !important;
  padding: 12px 16px !important;
  font-size: 1rem !important;
  background: #fefae0 !important;
  color: #1c290d !important;
  font-weight: 500 !important;
  transition: all 0.3s ease !important;
}

.custom-swal-input:focus {
  border-color: #676f53 !important;
  box-shadow: 0 0 0 3px rgba(103, 111, 83, 0.2) !important;
  background: white !important;
  outline: none !important;
}

.custom-swal-input::placeholder {
  color: #676f53 !important;
  opacity: 0.7 !important;
}

.custom-swal-confirm {
  background: linear-gradient(135deg, #1c290d, #676f53) !important;
  color: #FEFAE0 !important;
  border: none !important;
  padding: 0.9rem 2rem !important;
  border-radius: 50px !important;
  font-weight: 700 !important;
  font-size: 1.1rem !important;
  transition: all 0.3s ease !important;
  box-shadow: 0 6px 20px rgba(28, 41, 13, 0.3) !important;
  margin: 0 0.5rem !important;
  text-transform: uppercase !important;
  letter-spacing: 1px !important;
}

.custom-swal-confirm:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 8px 25px rgba(28, 41, 13, 0.5) !important;
  background: linear-gradient(135deg, #676f53, #1c290d) !important;
}

.custom-swal-cancel {
  background: linear-gradient(135deg, #A19379, #736046) !important;
  color: #FEFAE0 !important;
  border: none !important;
  padding: 0.9rem 2rem !important;
  border-radius: 50px !important;
  font-weight: 700 !important;
  font-size: 1.1rem !important;
  transition: all 0.3s ease !important;
  box-shadow: 0 6px 20px rgba(115, 96, 70, 0.3) !important;
  margin: 0 0.5rem !important;
  text-transform: uppercase !important;
  letter-spacing: 1px !important;
}

.custom-swal-cancel:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 8px 25px rgba(115, 96, 70, 0.5) !important;
  background: linear-gradient(135deg, #736046, #381d03) !important;
}

.custom-swal-success {
  background: linear-gradient(135deg, #4CAF50, #45a049) !important;
  color: white !important;
  border: none !important;
  padding: 0.9rem 2.5rem !important;
  border-radius: 50px !important;
  font-weight: 700 !important;
  font-size: 1.1rem !important;
  transition: all 0.3s ease !important;
  box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3) !important;
  text-transform: uppercase !important;
  letter-spacing: 1px !important;
}

.custom-swal-success:hover {
  transform: translateY(-3px) !important;
  box-shadow: 0 8px 25px rgba(76, 175, 80, 0.5) !important;
  background: linear-gradient(135deg, #45a049, #388e3c) !important;
}

.swal2-timer-progress-bar {
  background: linear-gradient(90deg, #1c290d, #676f53) !important;
}

/* Animasi untuk popup */
.swal2-show {
  animation: swal2-show 0.3s !important;
}

@keyframes swal2-show {
  0% {
    transform: scale(0.7);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>