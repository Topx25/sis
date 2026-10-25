<template>
  <section
    class="relative border-y border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] py-24 overflow-hidden"
    style="background: var(--bg-section-1)"
  >
    <!-- Background decorations -->
    <div
      class="absolute top-0 left-0 w-96 h-96 bg-[var(--accent-blue)]/5 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div
      class="absolute bottom-0 right-0 w-96 h-96 bg-[var(--accent-teal)]/5 rounded-full blur-3xl pointer-events-none"
    ></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">

      <!-- Header -->
      <div
        class="text-center mb-12 reveal reveal-fade-up"
      >
        <span
          class="inline-block text-xs font-semibold tracking-[0.2em] uppercase text-sis-accent dark:text-[var(--accent-blue)]/70 mb-3"
        >
          {{ isLoading ? "" : about.title }}
        </span>

        <Skeleton
          v-if="isLoading"
          class="h-4 w-32 mx-auto mb-3"
        />

        <h2
          v-if="!isLoading"
          class="text-4xl font-bold text-sis-accent dark:text-[var(--accent-blue)] mb-4"
        >
          {{ about.headline }}
        </h2>

        <Skeleton
          v-else
          class="h-10 w-80 max-w-full mx-auto mb-4"
        />

        <p
          v-if="!isLoading"
          class="text-sis-muted dark:text-[#aebfd8] text-lg max-w-2xl mx-auto"
        >
          {{ about.subheadline }}
        </p>

        <Skeleton
          v-else
          class="h-6 w-full max-w-2xl mx-auto"
        />
      </div>

      <!-- Mission & Vision -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">

        <!-- Mission -->
        <div
          v-if="!isLoading"
          class="reveal reveal-fade-up rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] shadow-md p-10 relative overflow-hidden group hover:shadow-xl transition-all duration-300"
          style="background: var(--bg-card-1)"
        >
          <div
            class="absolute top-0 right-0 w-56 h-56 bg-[var(--accent-blue)]/5 rounded-full blur-3xl pointer-events-none group-hover:bg-[var(--accent-blue)]/10 transition-colors duration-300"
          ></div>

          <div
            class="flex items-center justify-center w-14 h-14 rounded-xl bg-sis-accent text-white dark:bg-[var(--accent-blue)] dark:text-[#04121f] mb-7 shadow-lg group-hover:scale-105 transition-transform duration-300"
          >
            <span
              class="material-symbols-outlined"
              style="font-size: 26px;"
            >
              {{ about.mission.icon }}
            </span>
          </div>

          <span
            class="text-xs font-semibold tracking-widest uppercase text-sis-accent dark:text-[var(--accent-blue)]/60 mb-2 block"
          >
            {{ about.mission.tagline }}
          </span>

          <h3
            class="text-2xl font-bold text-sis-text dark:text-[#eaf1fb] mb-4 leading-tight"
          >
            {{ about.mission.title }}
          </h3>

          <p
            class="text-sis-muted dark:text-[#aebfd8] text-base leading-relaxed"
          >
            {{ about.mission.description }}
          </p>
        </div>

        <!-- Mission Skeleton -->
        <div
          v-else
          class="rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] shadow-md p-10"
          style="background: var(--bg-card-1)"
        >
          <Skeleton class="w-14 h-14 rounded-xl mb-7" />
          <Skeleton class="h-3 w-28 mb-3" />
          <Skeleton class="h-8 w-48 mb-5" />
          <Skeleton class="h-5 w-full mb-2" />
          <Skeleton class="h-5 w-5/6" />
        </div>

        <!-- Vision -->
        <div
          v-if="!isLoading"
          id="mission-vision"
          class="reveal reveal-fade-up rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] shadow-md p-10 relative overflow-hidden group hover:shadow-xl transition-all duration-300"
          style="background: var(--bg-card-1); transition-delay: 0.12s"
        >
          <div
            class="absolute top-0 right-0 w-56 h-56 bg-[var(--accent-teal)]/5 rounded-full blur-3xl pointer-events-none group-hover:bg-[var(--accent-teal)]/10 transition-colors duration-300"
          ></div>

          <div
            class="flex items-center justify-center w-14 h-14 rounded-xl bg-[var(--accent-teal)] text-[#04121f] mb-7 shadow-lg group-hover:scale-105 transition-transform duration-300"
          >
            <span
              class="material-symbols-outlined"
              style="font-size: 26px;"
            >
              {{ about.vision.icon }}
            </span>
          </div>

          <span
            class="text-xs font-semibold tracking-widest uppercase text-[var(--accent-teal)]/60 mb-2 block"
          >
            {{ about.vision.tagline }}
          </span>

          <h3
            class="text-2xl font-bold text-sis-text dark:text-[#eaf1fb] mb-4 leading-tight"
          >
            {{ about.vision.title }}
          </h3>

          <p
            class="text-sis-muted dark:text-[#aebfd8] text-base leading-relaxed"
          >
            {{ about.vision.description }}
          </p>
        </div>

        <!-- Vision Skeleton -->
        <div
          v-else
          class="rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] shadow-md p-10"
          style="background: var(--bg-card-1)"
        >
          <Skeleton class="w-14 h-14 rounded-xl mb-7" />
          <Skeleton class="h-3 w-28 mb-3" />
          <Skeleton class="h-8 w-48 mb-5" />
          <Skeleton class="h-5 w-full mb-2" />
          <Skeleton class="h-5 w-5/6" />
        </div>

      </div>

      <!-- Why Choose SiS -->
      <div
        v-if="!isLoading"
        class="rounded-xl bg-sis-accent dark:bg-[var(--accent-blue)] px-10 py-12 text-center relative overflow-hidden"
      >
        <div
          class="absolute inset-0 opacity-10 pointer-events-none"
          style="
            background-image: radial-gradient(
              #04121f 1px,
              transparent 1px
            );
            background-size: 32px 32px;
          "
        ></div>

        <div class="relative z-10">
          <h3
            class="text-2xl font-bold text-white dark:text-[#04121f] mb-3"
          >
            {{ about.why_choose_us.title }}
          </h3>

          <p
            class="text-white/70 dark:text-[#04121f]/70 text-base max-w-2xl mx-auto leading-relaxed"
          >
            {{ about.why_choose_us.description }}
          </p>
        </div>
      </div>

      <!-- Why Choose Skeleton -->
      <div
        v-else
        class="rounded-xl px-10 py-12 relative overflow-hidden"
        style="background: var(--bg-card-1)"
      >
        <div class="relative z-10 flex flex-col items-center">
          <Skeleton class="h-8 w-56 mb-4" />

          <Skeleton class="h-5 w-full max-w-2xl mb-2" />
          <Skeleton class="h-5 w-5/6 max-w-2xl" />
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Skeleton from "@/components/common/Skeleton.vue";
import { api } from "@/api/index.js";
import { normalizeAboutVision } from "../../api/normalizers.js";

const about = ref({
  title: "",
  headline: "",
  subheadline: "",
  paragraphs: [],
  mission: {
    tagline: "",
    title: "",
    description: "",
    icon: "",
  },
  vision: {
    tagline: "",
    title: "",
    description: "",
    icon: "",
  },
  why_choose_us: {
    title: "",
    description: "",
  },
});

const isLoading = ref(true);

onMounted(async () => {
  try {
    const response = await api.getAboutVision();
    const payload = response?.data || response;
    about.value = payload || {};
  } catch (error) {
    console.error("Failed to fetch about:", error);

    const fallback = (typeof normalizeAboutVision === "function" ? normalizeAboutVision() : null) || {};
    about.value = fallback?.data || fallback;
  } finally {
    isLoading.value = false;
  }
});
</script>