<template>
  <div class="flex min-h-[70vh] flex-col items-center justify-center px-4 py-16 text-center sm:px-6 lg:px-8">
    <div class="inline-flex items-center rounded-full border border-cyan-500/20 bg-cyan-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-cyan-500 dark:text-cyan-400">
      Error 404
    </div>
    <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-sis-text sm:text-5xl dark:text-white">
      Page Not Found
    </h1>
    <p class="mt-3 max-w-md text-sm text-sis-muted sm:text-base dark:text-slate-400">
      The page you are looking for doesn't exist, was removed, or is temporarily unavailable.
    </p>
    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
      <router-link
        to="/"
        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#0099ff] to-[#0055ff] px-6 py-3 text-xs font-bold uppercase tracking-wider text-white shadow-lg transition-all duration-200 hover:brightness-110"
      >
        Return to Home
      </router-link>
      <router-link
        to="/contact"
        class="inline-flex items-center justify-center rounded-xl border border-sis-border bg-white px-6 py-3 text-xs font-bold uppercase tracking-wider text-sis-text transition-all duration-200 hover:bg-slate-50 dark:border-white/10 dark:bg-[#07131f] dark:text-slate-300 dark:hover:bg-[#0b1c2d]"
      >
        Contact Support
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { useSeo } from "@/composables/useSeo.js";
import { PRIVATE_ROBOTS } from "@/utils/seo.js";

useSeo({
  title: "Page Not Found | SiS",
  description: "The page you are looking for could not be found.",
  robots: PRIVATE_ROBOTS,
});
</script>
