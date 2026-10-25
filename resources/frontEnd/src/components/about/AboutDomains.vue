<template>
  <section class="relative py-24 px-6 overflow-hidden">
    <div
      class="absolute inset-0 z-0 dark:bg-gradient-to-b dark:from-black dark:via-[#0a1a3a] dark:to-[#1a3a6e]"
      style="background: var(--bg-section-1)"
    ></div>

    <canvas
      ref="starsCanvas"
      class="absolute inset-0 w-full h-full z-10 pointer-events-none hidden dark:block"
    ></canvas>

    <div
      class="absolute inset-0 z-20 pointer-events-none hidden dark:block"
      style="background: rgba(10, 30, 80, 0.55)"
    ></div>

    <div class="max-w-7xl mx-auto relative z-30">
      <div class="text-center mb-16 reveal reveal-fade-up">
        <span
          class="inline-block text-xs font-semibold tracking-[0.2em] uppercase text-sis-accent dark:text-[var(--accent-blue)]/70 mb-3"
        >
          {{ domainData.tagline }}
        </span>

        <h2
          class="text-4xl md:text-5xl font-bold text-sis-text dark:text-white mb-6 tracking-tight"
        >
          {{ domainData.headline }}
        </h2>

        <p class="text-sis-muted dark:text-[#a1a1aa] text-lg max-w-2xl mx-auto">
          {{ domainData.subheadline }}
        </p>
      </div>

      <!-- Loading Skeleton -->
      <div
        v-if="isLoading"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6"
      >
        <div
          v-for="i in 5"
          :key="i"
          class="rounded-xl p-8 border border-[var(--border-glass)]"
          style="background: var(--bg-card-1)"
        >
          <!-- Icon -->
          <Skeleton class="w-12 h-12 rounded-lg mb-8" />

          <!-- Title -->
          <Skeleton class="h-7 w-3/4 mb-4" />

          <!-- Description -->
          <Skeleton class="h-4 w-full mb-2" />
          <Skeleton class="h-4 w-full mb-2" />
          <Skeleton class="h-4 w-5/6" />
        </div>
      </div>

      <!-- Actual Cards -->
      <div
        v-else
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 stagger-container"
      >
        <div
          v-for="(sector, i) in sectors"
          :key="sector.title"
          class="reveal reveal-fade-up group rounded-xl p-8 transition-all duration-300 cursor-pointer backdrop-blur-sm"
          :style="{
            background: isDark ? '' : 'var(--bg-card-1)',
            border: isDark
              ? '1px solid rgba(255,255,255,0.1)'
              : '1px solid var(--border-glass)',
            transitionDelay: `${i * 0.1}s`,
          }"
          :class="
            isDark
              ? 'bg-white/5 hover:bg-[#0d6efd] border-white/10 hover:border-[#0d6efd]'
              : 'hover:shadow-xl border-[var(--border-30)]'
          "
        >
          <div
            class="w-12 h-12 rounded-lg flex items-center justify-center mb-8 transition-colors duration-300"
            :class="
              isDark
                ? 'bg-white/10 group-hover:bg-white/20'
                : 'bg-[var(--bg-accent-10)] group-hover:bg-[var(--bg-accent-20)]'
            "
          >
            <span
              class="material-symbols-outlined"
              :class="isDark ? 'text-white' : 'text-sis-accent'"
              style="font-size: 22px"
            >
              {{ sector.icon }}
            </span>
          </div>

          <h3 class="text-2xl font-bold text-sis-text dark:text-white mb-4">
            {{ sector.title }}
          </h3>

          <p
            class="text-sis-muted dark:text-[#a1a1aa] group-hover:text-sis-text dark:group-hover:text-white/90 leading-relaxed text-sm transition-colors duration-300"
          >
            {{ sector.description }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, inject } from "vue";
import { api } from "../../api/index.js";
import { normalizeDomains } from "../../api/normalizers.js";
import Skeleton from "@/components/common/Skeleton.vue";

const starsCanvas = ref(null);
let animationId = null;

const isDark = inject("isDark", ref(false));

const isLoading = ref(true);

const domainData = ref({
  tagline: "",
  headline: "",
  subheadline: "",
});

const sectors = ref([]);

const getDomainIcon = (icon) => {
  const icons = {
    oil: "local_gas_station",
    science: "science",
    engineering: "handyman",
    water: "water_drop",
    factory: "factory",
  };

  return icons[icon] || icon;
};

onMounted(async () => {
  try {
    const response = await api.getDomains();
    const payload = response?.data || response;

    domainData.value = {
      tagline: payload?.tagline || "",
      headline: payload?.headline || "",
      subheadline: payload?.subheadline || "",
    };

    sectors.value = (payload?.domains || []).map((domain) => ({
      ...domain,
      icon: getDomainIcon(domain.icon),
    }));
  } catch (error) {
    console.error("Failed to fetch domains:", error);

    const fallback = (typeof normalizeDomains === "function" ? normalizeDomains() : null) || {};
    const fallbackData = fallback?.data || fallback;

    domainData.value = {
      tagline: fallbackData?.tagline || "",
      headline: fallbackData?.headline || "",
      subheadline: fallbackData?.subheadline || "",
    };

    sectors.value = (fallbackData?.domains || []).map((domain) => ({
      ...domain,
      icon: getDomainIcon(domain.icon),
    }));
  } finally {
    isLoading.value = false;
    initializeStars();
  }
});

const initializeStars = () => {
  const canvas = starsCanvas.value;

  if (!canvas) return;

  const ctx = canvas.getContext("2d");

  let width = canvas.offsetWidth;
  let height = canvas.offsetHeight;

  canvas.width = width;
  canvas.height = height;

  const STAR_COUNT = 120;

  const stars = Array.from({ length: STAR_COUNT }, () => ({
    x: Math.random() * width,
    y: Math.random() * height,
    r: Math.random() * 1.8 + 0.3,
    opacity: Math.random() * 0.7 + 0.2,
    speedX: (Math.random() - 0.5) * 0.25,
    speedY: (Math.random() - 0.5) * 0.25,
    twinkleSpeed: Math.random() * 0.02 + 0.005,
    twinkleDir: Math.random() > 0.5 ? 1 : -1,
  }));

  function draw() {
    ctx.clearRect(0, 0, width, height);

    for (const star of stars) {
      star.opacity += star.twinkleSpeed * star.twinkleDir;

      if (star.opacity >= 0.95 || star.opacity <= 0.1) {
        star.twinkleDir *= -1;
      }

      star.x += star.speedX;
      star.y += star.speedY;

      if (star.x < 0) star.x = width;
      if (star.x > width) star.x = 0;

      if (star.y < 0) star.y = height;
      if (star.y > height) star.y = 0;

      ctx.beginPath();

      const grd = ctx.createRadialGradient(
        star.x,
        star.y,
        0,
        star.x,
        star.y,
        star.r * 3
      );

      grd.addColorStop(
        0,
        `rgba(180, 210, 255, ${star.opacity})`
      );

      grd.addColorStop(
        1,
        `rgba(100, 160, 255, 0)`
      );

      ctx.arc(
        star.x,
        star.y,
        star.r * 3,
        0,
        Math.PI * 2
      );

      ctx.fillStyle = grd;
      ctx.fill();

      ctx.beginPath();

      ctx.arc(
        star.x,
        star.y,
        star.r,
        0,
        Math.PI * 2
      );

      ctx.fillStyle = `rgba(220, 235, 255, ${star.opacity})`;

      ctx.fill();
    }

    animationId = requestAnimationFrame(draw);
  }

  draw();

  const handleResize = () => {
    width = canvas.offsetWidth;
    height = canvas.offsetHeight;

    canvas.width = width;
    canvas.height = height;
  };

  window.addEventListener("resize", handleResize);

  onBeforeUnmount(() => {
    cancelAnimationFrame(animationId);
    window.removeEventListener("resize", handleResize);
  });
};
</script>