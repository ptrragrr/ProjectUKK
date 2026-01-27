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
          <li class="nav-item px-3">
            <router-link :to="{ name: 'login' }" class="nav-link fw-bold" style="color: #FEFAE0 !important;">
              Login / Register
            </router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <RouterView></RouterView>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { RouterView } from "vue-router";

const isExpanded = ref(false);

function toggleNavbar() {
  isExpanded.value = !isExpanded.value;
}

// Hapus semua event listener scroll yang menyebabkan masalah
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

/* Hapus styling active yang menyebabkan background bulat */

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

/* Responsive */
@media (max-width: 992px) {
  .navbar-collapse {
    background: linear-gradient(135deg, rgba(28, 41, 13, 0.98), rgba(103, 111, 83, 0.98));
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 16px;
    backdrop-filter: blur(10px);
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
}
</style>