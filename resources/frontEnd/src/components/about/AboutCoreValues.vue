<template>
  <section
    class="relative py-28 px-6 overflow-hidden"
    style="background: var(--bg-section-1)"
  >
    <!-- Background -->
    <div
      class="absolute inset-0 pointer-events-none z-0"
      style="
        background: radial-gradient(
          ellipse 70% 50% at 50% -5%,
          rgba(62, 127, 202, 0.35) 0%,
          transparent 70%
        );
      "
    ></div>

    <div
      class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full h-40 pointer-events-none z-0"
      style="
        background: radial-gradient(
          ellipse 80% 100% at 50% 100%,
          rgba(30, 80, 180, 0.18) 0%,
          transparent 80%
        );
      "
    ></div>

    <div class="max-w-7xl mx-auto relative z-10">
      <!-- Header -->
      <div class="text-center mb-16 reveal reveal-fade-up">
        <div class="reveal reveal-fade-up py-12 lg:py-0">
          <div class="mb-3">
            <span
              v-if="!isLoading"
              class="inline-flex items-center gap-[11px] font-mono text-[12px] font-semibold uppercase tracking-[0.26em] text-[var(--accent-blue)]"
            >
              <span
                class="block h-px w-6 bg-gradient-to-r from-[var(--accent-blue)] to-transparent"
              ></span>

              {{ coreValues.tagline }}
            </span>

            <!-- Tagline Skeleton -->
            <Skeleton
              v-else
              class="mx-auto h-4 w-40 rounded"
            />
          </div>

          <!-- Headline -->
          <h2
            v-if="!isLoading"
            class="text-5xl md:text-6xl font-black text-sis-text mb-5 leading-tight tracking-tight"
          >
            {{ coreValues.headline }}
          </h2>

          <!-- Headline Skeleton -->
          <Skeleton
            v-else
            class="mx-auto mb-5 h-16 w-[420px] max-w-full rounded-xl"
          />

          <!-- Subheadline -->
          <p
            v-if="!isLoading"
            class="text-sis-muted text-lg max-w-xl mx-auto"
          >
            {{ coreValues.subheadline }}
          </p>

          <!-- Subheadline Skeleton -->
          <div
            v-else
            class="mx-auto max-w-xl space-y-2"
          >
            <Skeleton class="mx-auto h-5 w-full rounded" />
            <Skeleton class="mx-auto h-5 w-4/5 rounded" />
          </div>
        </div>
      </div>

      <!-- Cards -->
      <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 stagger-container"
      >
        <!-- Real Cards -->
        <template v-if="!isLoading">
          <div
            v-for="(card, i) in coreValues.principles"
            :key="card.step || card.title || i"
            class="reveal reveal-fade-up group relative rounded-xl p-7 flex flex-col cursor-default transition-all duration-400 hover:shadow-lg"
            :style="{
              backdropFilter: 'blur(12px)',
              transitionDelay: `${i * 0.08}s`,
              background: 'var(--bg-card-1)',
              border: '1px solid var(--border-glass)',
            }"
          >
            <!-- Number / Category -->
            <div
              class="text-[11px] font-bold tracking-[0.15em] uppercase text-sis-muted mb-6"
            >
              {{ card.step }} &mdash; Principle
            </div>

            <!-- Icon -->
            <div
              class="w-14 h-14 rounded-xl border border-[var(--bg-accent-40)]  bg-[var(--bg-accent-10) flex items-center justify-center mb-7 group-hover:bg-[var(--bg-accent-20)] group-hover:border-[var(--border-accent-70)] transition-all duration-300"
            >
              <span
                class="material-symbols-outlined text-sis-accent"
                style="font-size: 26px"
              >
                {{ card.icon }}
              </span>
            </div>

            <!-- Title -->
            <h3
              class="text-xl font-bold text-sis-text  mb-2"
            >
              {{ card.title }}
            </h3>

            <!-- Line -->
            <div
              class="w-8 h-0.5 bg-sis-accent mb-4 rounded-full"
            ></div>

            <!-- Description -->
            <p
              class="text-sis-muted text-sm leading-relaxed"
            >
              {{ card.description }}
            </p>
          </div>
        </template>

        <!-- Skeleton Cards -->
        <template v-else>
          <div
            v-for="i in 3"
            :key="i"
            class="rounded-xl p-7 border border-[var(--border-glass)]"
            style="background: var(--bg-card-1)"
          >
            <!-- Number -->
            <Skeleton class="h-4 w-28 rounded mb-6" />

            <!-- Icon -->
            <Skeleton class="w-14 h-14 rounded-xl mb-7" />

            <!-- Title -->
            <Skeleton class="h-6 w-32 rounded mb-4" />

            <!-- Line -->
            <Skeleton class="h-0.5 w-8 rounded mb-4" />

            <!-- Description -->
            <div class="space-y-2">
              <Skeleton class="h-4 w-full rounded" />
              <Skeleton class="h-4 w-full rounded" />
              <Skeleton class="h-4 w-4/5 rounded" />
            </div>
          </div>
        </template>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from "vue";
import Skeleton from "@/components/common/Skeleton.vue";
import { api } from "../../api/index.js";
import { normalizeAboutCore } from "../../api/normalizers.js";

const isLoading = ref(true);

const coreValues = ref({
  tagline: "",
  headline: "",
  subheadline: "",
  principles: [],
});

onMounted(async () => {
  try {
    const response = await api.getAboutCore();
    const payload = response?.data || response;
    coreValues.value = payload || {};
  } catch (error) {
    console.error("Failed to fetch core values:", error);

    const fallback = (typeof normalizeAboutCore === "function" ? normalizeAboutCore() : null) || {};
    coreValues.value = fallback?.data || fallback;
  } finally {
    isLoading.value = false;
  }
});
</script>