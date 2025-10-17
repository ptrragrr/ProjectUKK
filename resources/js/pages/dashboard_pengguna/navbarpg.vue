<template>
  <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid px-4">
      <a class="navbar-brand fw-bold me-auto" href="#">
        <i class="fas fa-music me-2"></i>OURSKY.FEST
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
                        <router-link :to="{ name: 'home' }" class="nav-link fw-bold">
                            Home
                        </router-link>
                    </li>
                    <li class="nav-item px-3">
                        <router-link :to="{ name: 'ticket' }" class="nav-link fw-bold">
                            Ticket
                        </router-link>
                    </li>
                    <li class="nav-item px-3">
                        <router-link :to="{ name: 'contact' }" class="nav-link fw-bold">
                            Contact
                        </router-link>
                    </li>
                    <li class="nav-item px-3">
                        <router-link :to="{ name: 'about' }" class="nav-link fw-bold">
                            About Us
                        </router-link>
                    </li>
        </ul>
      </div>
    </div>
  </nav>
  <RouterView></RouterView>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { RouterView } from "vue-router";

const links = [
  { name: "home", label: "Home", icon: "fa-home" },
  { name: "tickets", label: "Tiket", icon: "fa-ticket-alt" },
  { name: "about", label: "About Us", icon: "fa-users" },
  { name: "contact", label: "Contact", icon: "fa-envelope" },
];

const activeLink = ref("home");
const isExpanded = ref(false);

function toggleNavbar() {
  isExpanded.value = !isExpanded.value;
}

function setActive(name: string) {
  activeLink.value = name;
  if (window.innerWidth < 992) {
    isExpanded.value = false; // Tutup navbar di mobile
  }
}

// Efek scroll
function handleScroll() {
  const navbar = document.querySelector(".navbar-custom") as HTMLElement;
  if (window.scrollY > 50) {
    navbar.style.background =
      "linear-gradient(135deg, rgba(28, 41, 13, 0.95), rgba(103, 111, 83, 0.95))";
  } else {
    navbar.style.background =
      "linear-gradient(135deg, var(--primary-green), var(--sage-green))";
  }
}

onMounted(() => {
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
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

/* ==== STYLE PERSIS DARI HTML ==== */
.navbar-custom {
  background: linear-gradient(135deg, var(--primary-green) 0%, var(--sage-green) 100%);
  backdrop-filter: blur(10px);
  border-bottom: 3px solid var(--brown);
  box-shadow: 0 8px 32px rgba(28, 41, 13, 0.3);
  transition: all 0.3s ease;
  padding: 1rem 0;
}

.navbar-custom:hover {
  box-shadow: 0 12px 40px rgba(28, 41, 13, 0.4);
}

.navbar-brand {
  font-size: 2.2rem !important;
  font-weight: 800 !important;
  color: var(--cream) !important;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
  letter-spacing: 2px;
  position: relative;
  overflow: hidden;
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
  color: var(--cream) !important;
  transform: translateY(-2px);
}

.nav-link {
  color: var(--cream) !important;
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
  background: linear-gradient(135deg, var(--taupe), var(--brown));
  transition: left 0.3s ease;
  z-index: -1;
}

.nav-link:hover::before {
  left: 0;
}

.nav-link:hover {
  color: var(--cream) !important;
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(115, 96, 70, 0.4);
}

.nav-link.active {
  background: linear-gradient(135deg, var(--brown), var(--dark-brown));
  color: var(--cream) !important;
  box-shadow: 0 4px 15px rgba(56, 29, 3, 0.5);
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
