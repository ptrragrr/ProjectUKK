<template>
  <nav class="bg-gray-800 text-white px-4 py-3 flex gap-4">
    <RouterLink to="/home" class="hover:underline">Home</RouterLink>
    <RouterLink to="/tiket" class="hover:underline">Tiket</RouterLink>
    <RouterLink to="/contact" class="hover:underline">Contact</RouterLink>
    <RouterLink to="/about" class="hover:underline">About Us</RouterLink>
  </nav>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>
