<template>
  <section
    class="relative min-h-screen lg:pt-10 flex items-center overflow-hidden"
    style="background: var(--bg-section-1)"
  >
    <div
      class="pointer-events-none absolute inset-0"
      style="
        background-image: radial-gradient(
          rgba(126, 165, 224, 0.06) 1px,
          transparent 1px
        );
        background-size: 28px 28px;
      "
      aria-hidden="true"
    ></div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">
      <div
        class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center"
      >
        <!-- Left: Text -->
        <div class="reveal reveal-fade-up py-12 lg:py-0">
          <!-- Tagline -->
          <template v-if="!isLoading">
            <span
              class="inline-flex items-center gap-[11px] font-mono text-[12px] font-semibold uppercase tracking-[0.26em] text-[var(--accent-blue)]"
            >
              <span
                class="block h-px w-6 bg-gradient-to-r from-[var(--accent-blue)] to-transparent"
              ></span>

              {{ aboutHero.tagline }}
            </span>
          </template>

          <Skeleton v-else class="h-4 w-64 mb-2" />

          <!-- Headline -->
          <template v-if="!isLoading">
            <h1
              class="mt-[22px] text-[clamp(36px,5.2vw,62px)] font-extrabold leading-[1.03] -tracking-[0.035em] text-balance text-sis-text dark:text-dark-text"
            >
              {{ aboutHero.headline }}
            </h1>
          </template>

          <Skeleton
            v-else
            class="mt-[22px] h-32 w-full max-w-[600px] rounded-xl"
          />

          <!-- Subheadline -->
          <template v-if="!isLoading">
            <p
              class="mt-[22px] max-w-[50ch] text-[15.5px] leading-[1.72] text-[var(--text-muted-90)] dark:text-[rgba(167,184,212,0.9)]"
            >
              {{ aboutHero.subheadline }}
            </p>
          </template>

          <div v-else class="mt-[22px] max-w-[50ch] space-y-2">
            <Skeleton class="h-5 w-full" />
            <Skeleton class="h-5 w-full" />
            <Skeleton class="h-5 w-4/5" />
          </div>

          <!-- CTA -->
          <div class="flex flex-wrap gap-[13px] mt-8">
            <template v-if="!isLoading">
              <router-link
                :to="aboutHero.cta_link"
                class="inline-flex items-center gap-[9px] rounded-full bg-[var(--accent-blue)] px-[26px] py-[14px] text-[14px] font-bold text-[#04121f] shadow-[0_10px_30px_-10px_rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.45)] transition-all duration-200 hover:-translate-y-0.5 hover:brightness-110 no-underline"
              >
                {{ aboutHero.cta_text }}

                <svg
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2.2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M5 12h14M13 6l6 6-6 6" />
                </svg>
              </router-link>
            </template>

            <Skeleton v-else class="h-[48px] w-52 rounded-full" />
          </div>
        </div>

        <!-- Right: Image -->
        <div
          v-if="!isLoading"
          class="hidden lg:block reveal reveal-fade-up rounded-xl overflow-hidden border border-[var(--border-30)] dark:border-white/10 shadow-xl"
          style="transition-delay: 0.15s"
        >
          <img
            :alt="aboutHero.headline || 'About SIS'"
            class="w-full h-auto object-cover block"
            :src="heroImageSrc"
            loading="lazy"
            @error="handleImageError"
          />
        </div>

        <!-- Image Skeleton -->
        <div
          v-else
          class="hidden lg:block rounded-xl overflow-hidden border border-[var(--border-30)] dark:border-white/10 shadow-xl"
        >
          <Skeleton class="w-full aspect-video rounded-xl" />
        </div>
      </div>

      <!-- Scroll Indicator -->
      <div class="flex justify-center">
        <router-link
          to="#why-it-ot"
          class="flex flex-col items-center animate-bounce opacity-60 hover:opacity-100 transition-opacity"
        >
          <span
            class="text-sis-muted dark:text-white/60 text-sm mb-2 font-semibold tracking-widest uppercase"
          >
            Why ITOT
          </span>

          <span
            class="material-symbols-outlined text-sis-muted dark:text-white/60"
          >
            keyboard_arrow_down
          </span>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import Skeleton from "@/components/common/Skeleton.vue";
import { api } from "@/api/index.js";
import { normalizeAboutHero } from "@/api/normalizers.js";
import { resolveImageUrl } from "@/utils/imageUrl.js";

const defaultHeroImage = `${import.meta.env.BASE_URL}images/about-hero.webp`;

const aboutHero = ref({
  tagline: "",
  headline: "",
  subheadline: "",
  cta_text: "",
  cta_link: "",
  image: "",
  image_path: "",
  image_url: "",
});

const heroImageSrc = computed(() => {
  const raw =
    aboutHero.value?.image_url ||
    aboutHero.value?.image_path ||
    aboutHero.value?.image;
  return raw ? resolveImageUrl(raw) : defaultHeroImage;
});

function handleImageError(e) {
  if (e?.target && e.target.src !== defaultHeroImage) {
    e.target.src = defaultHeroImage;
  }
}

const isLoading = ref(true);

onMounted(async () => {
  try {
    const response = await api.getAboutHero();
    const payload = response?.data || response;
    aboutHero.value = payload || {};
  } catch (error) {
    console.error("Failed to fetch about hero:", error);

    const fallback = (typeof normalizeAboutHero === "function" ? normalizeAboutHero() : null) || {};
    aboutHero.value = fallback?.data || fallback;
  } finally {
    isLoading.value = false;
  }
});
</script>
