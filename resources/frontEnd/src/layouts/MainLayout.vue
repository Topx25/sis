<template>
  <div class="min-h-screen overflow-x-hidden bg-sis-panel text-sis-text dark:bg-[#070d1a] dark:text-[#eaf1fb]">
    <NavBar />
    <main class="pt-6">
      <router-view />
    </main>
    <FooterSection />
  </div>
</template>

<script setup>
import NavBar from '@/components/Layout/NavBar.vue'
import FooterSection from '@/components/Layout/FooterSection.vue'
</script>
