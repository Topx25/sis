<template>
  <div v-show="navigating" class="fixed inset-0 pointer-events-none z-[9999]">
    <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-blue-400 dark:via-[var(--accent-blue)] to-transparent animate-[sweep-x_1.8s_ease-in-out_infinite]" />
    <div class="absolute bottom-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-teal-400 dark:via-[var(--accent-teal)] to-transparent animate-[sweep-x_1.8s_ease-in-out_infinite_reverse]" />
    <div class="absolute top-0 bottom-0 left-0 w-[2px] bg-gradient-to-b from-transparent via-teal-400 dark:via-[var(--accent-teal)] to-transparent animate-[sweep-y_1.8s_ease-in-out_infinite]" />
    <div class="absolute top-0 bottom-0 right-0 w-[2px] bg-gradient-to-b from-transparent via-blue-400 dark:via-[var(--accent-blue)] to-transparent animate-[sweep-y_1.8s_ease-in-out_infinite_reverse]" />
  </div>
</template>

<script setup>
import { navigating } from '@/stores/navigationState'
</script>
