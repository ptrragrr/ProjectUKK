<template>
  <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid px-4">
      <a class="navbar-brand fw-bold me-auto" href="#">
        OURSKY.FEST
      </a>

      <button
        class="navbar-toggler"
        type="button"
        @click="toggleNavbar"
        :aria-expanded="isExpanded.toString()"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" :class="{ show: isExpanded }" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item px-3">
            <router-link :to="{ name: 'home' }" class="nav-link fw-bold" style="color: #FEFAE0 !important;">
              Beranda
            </router-link>
          </li>
          <li class="nav-item px-3">
            <router-link :to="{ name: 'ticket' }" class="nav-link fw-bold" style="color: #FEFAE0 !important;">
              Tiket
            </router-link>
          </li>
          <li class="nav-item px-3">
            <router-link :to="{ name: 'contact' }" class="nav-link fw-bold" style="color: #FEFAE0 !important;">
              Kontak
            </router-link>
          </li>
          <li class="nav-item px-3">
            <router-link :to="{ name: 'about' }" class="nav-link fw-bold" style="color: #FEFAE0 !important;">
              Tentang
            </router-link>
          </li>
          <li v-if="!authStore.isAuthenticated" class="nav-item px-3">
            <router-link :to="{ name: 'login' }" class="nav-link fw-bold" style="color: #FEFAE0 !important;">
              Login / Register
            </router-link>
          </li>
          <li v-else class="nav-item px-3 user-menu-wrapper">
            <div class="user-menu">
              <div class="user-info">
                <i class="fas fa-user-circle user-icon"></i>
                <span class="user-name">{{ authStore.user?.name }}</span>
              </div>
              <div class="logout-dropdown" @click="signOut()">
                <i class="fas fa-sign-out-alt"></i> Logout
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <RouterView></RouterView>
</template>

<script setup lang="ts">
import router from "@/router";
import { useAuthStore } from "@/stores/auth";
import Swal from "sweetalert2";
import { ref } from "vue";
import { RouterView } from "vue-router";

const authStore = useAuthStore();

const isExpanded = ref(false);

function toggleNavbar() {
  isExpanded.value = !isExpanded.value;
}

const signOut = async () => {
    const result = await Swal.fire({
        title: '<span style="color: #1c290d; font-weight: 800;">Konfirmasi Logout</span>',
        html: '<p style="color: #676f53; font-size: 1.1rem; font-weight: 500;">Apakah Anda yakin ingin keluar?</p>',
        icon: "warning",
        iconColor: "#736046",
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-sign-out-alt me-2"></i>Ya, Keluar',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            popup: 'custom-swal-popup',
            confirmButton: "custom-swal-confirm",
            cancelButton: "custom-swal-cancel",
            icon: 'custom-swal-icon'
        },
        background: 'linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%)',
        backdrop: `
            rgba(28, 41, 13, 0.6)
            center
            no-repeat
        `
    });

    if (result.isConfirmed) {
        await authStore.logout();

        Swal.fire({
            title: '<span style="color: #1c290d; font-weight: 800;">Berhasil!</span>',
            html: '<p style="color: #676f53; font-size: 1.1rem; font-weight: 500;">Anda telah keluar dari akun</p>',
            icon: "success",
            iconColor: "#4CAF50",
            confirmButtonText: '<i class="fas fa-check me-2"></i>OK',
            buttonsStyling: false,
            customClass: {
                popup: 'custom-swal-popup',
                confirmButton: "custom-swal-success",
                icon: 'custom-swal-icon'
            },
            background: 'linear-gradient(135deg, #FEFAE0 0%, #B3B49A 100%)',
            backdrop: `
                rgba(28, 41, 13, 0.6)
                center
                no-repeat
            `,
            timer: 2000,
            timerProgressBar: true
        });
    }
};
</script>

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

body {
  background: linear-gradient(135deg, var(--cream) 0%, var(--warm-beige) 100%);
  min-height: 100vh;
}

/* Navbar dengan background konsisten */
.navbar-custom {
  background: linear-gradient(135deg, #1c290d 0%, #676f53 100%) !important;
  backdrop-filter: blur(10px);
  border-bottom: 3px solid var(--brown);
  box-shadow: 0 8px 32px rgba(28, 41, 13, 0.3);
  transition: all 0.3s ease;
  padding: 1rem 0;
  z-index: 1000;
}

.navbar-custom:hover {
  box-shadow: 0 12px 40px rgba(28, 41, 13, 0.4);
}

.navbar-brand {
  font-size: 2.2rem !important;
  font-weight: 800 !important;
  color: #FEFAE0 !important;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
  letter-spacing: 2px;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  text-decoration: none;
}

.navbar-brand::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(254, 250, 224, 0.3), transparent);
  transition: left 0.5s;
}

.navbar-brand:hover::before {
  left: 100%;
}

.navbar-brand:hover {
  color: #FEFAE0 !important;
  transform: translateY(-2px);
}

.nav-link {
  color: #FEFAE0 !important;
  font-weight: 600;
  font-size: 1.1rem;
  margin: 0 0.5rem;
  padding: 0.8rem 1.5rem !important;
  border-radius: 50px;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.nav-link::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #A19379, #736046);
  transition: left 0.3s ease;
  z-index: -1;
  border-radius: 50px;
}

.nav-link:hover::before {
  left: 0;
}

.nav-link:hover {
  color: #FEFAE0 !important;
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(115, 96, 70, 0.4);
}

.navbar-toggler {
  border: 2px solid var(--cream);
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.navbar-toggler:hover {
  background-color: var(--brown);
  transform: rotate(180deg);
}

.navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23FEFAE0' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='m4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* User Menu Styles */
.user-menu-wrapper {
  position: relative;
}

.user-menu {
  position: relative;
  display: flex;
  align-items: center;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.5rem;
  border-radius: 50px;
  background: linear-gradient(135deg, rgba(161, 147, 121, 0.3), rgba(115, 96, 70, 0.3));
  transition: all 0.3s ease;
  cursor: pointer;
  color: #FEFAE0;
}

.user-icon {
  font-size: 1.8rem;
  transition: transform 0.3s ease;
}

.user-name {
  font-weight: 600;
  font-size: 1.1rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.user-info:hover {
  background: linear-gradient(135deg, #A19379, #736046);
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(115, 96, 70, 0.4);
}

.user-info:hover .user-icon {
  transform: rotate(360deg);
}

.logout-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 0.5rem;
  background: linear-gradient(135deg, #1c290d, #676f53);
  color: #FEFAE0;
  padding: 0.8rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s ease;
  cursor: pointer;
  font-weight: 600;
  white-space: nowrap;
  border: 2px solid var(--brown);
  z-index: 1001;
}

.user-menu:hover .logout-dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.logout-dropdown:hover {
  background: linear-gradient(135deg, #A19379, #736046);
  transform: scale(1.05);
}

.logout-dropdown i {
  margin-right: 0.5rem;
}

/* Responsive */
@media (max-width: 992px) {
  .navbar-collapse {
    background: linear-gradient(135deg, rgba(28, 41, 13, 0.98), rgba(103, 111, 83, 0.98));
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 16px;
    backdrop-filter: blur(10px);
  }

  .user-menu {
    flex-direction: column;
    align-items: stretch;
  }

  .logout-dropdown {
    position: relative;
    top: auto;
    right: auto;
    margin-top: 0.5rem;
    opacity: 1;
    visibility: visible;
    transform: none;
  }
}

@media (max-width: 768px) {
  .navbar-brand {
    font-size: 1.5rem !important;
  }

  .nav-link {
    margin: 0.2rem 0;
    text-align: center;
  }

  .user-info {
    justify-content: center;
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