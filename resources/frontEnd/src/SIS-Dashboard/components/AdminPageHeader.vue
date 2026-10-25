<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: "",
  },
  category: {
    type: String,
    default: "",
  },
  icon: {
    type: String,
    default: "",
  },
  breadcrumb: {
    type: String,
    default: "",
  },
});
</script>

<template>
  <div class="relative mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <!-- Subtle ambient glow in background -->
    <div class="pointer-events-none absolute -left-6 -top-6 h-28 w-28 rounded-full bg-blue-500/5 blur-2xl"></div>

    <div class="relative min-w-0">
      <!-- Breadcrumb or Category Badge -->
      <div v-if="category || breadcrumb" class="mb-2.5 flex items-center gap-2 text-xs font-semibold tracking-wide">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-500/20 bg-gradient-to-r from-blue-50 to-indigo-50/60 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-blue-700 shadow-[0_1px_2px_rgba(37,99,235,0.05)]">
          <span v-if="icon" class="material-symbols-outlined text-[14px] leading-none text-blue-600">{{ icon }}</span>
          {{ category || breadcrumb }}
        </span>
      </div>

      <!-- Main Title -->
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
        {{ title }}
      </h1>

      <!-- Subtitle Description -->
      <p v-if="subtitle" class="mt-1.5 max-w-3xl text-sm leading-relaxed text-slate-500">
        {{ subtitle }}
      </p>
    </div>

    <!-- Actions Slot -->
    <div v-if="$slots.actions" class="relative flex shrink-0 flex-wrap items-center gap-3">
      <slot name="actions" />
    </div>
  </div>
</template>
