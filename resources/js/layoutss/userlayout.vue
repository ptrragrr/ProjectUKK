<template>
  <div>
    <Navbar />
    <main class="p-4">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from '@/components/user/Navbar.vue'
</script>
