<template>
  <section id="hero"
    class="relative min-h-dvh w-full overflow-hidden bg-sis-panel text-sis-text dark:bg-[#070d1a] dark:text-white">
    <!-- Background grid pattern with radial mask -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute inset-2" style="
          background-image:
            linear-gradient(rgba(126, 165, 224, 0.14) 1px, transparent 1px),
            linear-gradient(
              90deg,
              rgba(126, 165, 224, 0.14) 1px,
              transparent 1px
            );
          background-size: 56px 56px;
          mask-image: radial-gradient(
            120% 90% at 70% 0%,
            #000 30%,
            transparent 75%
          );
          -webkit-mask-image: radial-gradient(
            120% 90% at 70% 0%,
            #000 30%,
            transparent 75%
          );
        "></div>

      <div class="absolute -right-[140px] -top-[220px] h-[640px] w-[640px] rounded-full opacity-70" style="
          background: radial-gradient(
            circle,
            rgba(
              var(--accent-blue-r),
              var(--accent-blue-g),
              var(--accent-blue-b),
              0.45
            ),
            transparent 62%
          );
          filter: blur(30px);
        "></div>

      <div class="absolute -bottom-[260px] -left-[160px] h-[560px] w-[560px] rounded-full" style="
          background: radial-gradient(
            circle,
            rgba(95, 224, 196, 0.16),
            transparent 65%
          );
          filter: blur(30px);
        "></div>
    </div>

    <Skeleton v-if="isLoading"
      class="absolute inset-0 z-[1] flex min-h-dvh items-center !bg-transparent">
      <div class="w-full max-w-[1140px] px-7 pb-20 pt-24 sm:pb-24 lg:px-24 xl:px-32">
        <div class="max-w-[680px]">
          <div class="flex items-center gap-3">
            <Skeleton class="h-px w-8 rounded-full" />
            <Skeleton class="h-3 w-48 rounded-full" />
          </div>

          <div class="mt-6 space-y-3">
            <Skeleton class="h-16 w-full rounded-2xl sm:h-20" />
            <Skeleton class="h-16 w-3/4 rounded-2xl sm:h-20" />
          </div>

          <div class="mt-6 space-y-2">
            <Skeleton class="h-4 w-full rounded-full" />
            <Skeleton class="h-4 w-2/3 rounded-full" />
          </div>

          <div class="mt-8 flex flex-wrap gap-3">
            <Skeleton class="h-12 w-44 rounded-full" />
            <Skeleton class="h-12 w-40 rounded-full" />
          </div>
        </div>
      </div>
    </Skeleton>

    <Swiper
      v-else
      :modules="[Autoplay, EffectFade, Navigation, Pagination]"
      :effect="'fade'"
      :loop="true"
      :autoplay="{ delay: 4200, disableOnInteraction: false, pauseOnMouseEnter: false }"
      :navigation="true"
      :pagination="{ clickable: true }"
      :speed="1000"
      class="hero-full-carousel absolute inset-0 z-[1] min-h-dvh"
    >
      <SwiperSlide v-for="(slide, index) in carouselSlides" :key="`hero-${slide.id}`">
        <article class="relative flex min-h-dvh items-center overflow-hidden">
          <img :src="slide.src" :alt="slide.alt" class="absolute inset-0 h-full w-full min-w-full object-cover object-[68%_center]" />
          <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(3,12,24,0.96)_0%,rgba(3,12,24,0.76)_35%,rgba(3,12,24,0.18)_70%,rgba(3,12,24,0.04)_100%)]" />
          <div class="absolute inset-0 bg-gradient-to-t from-[#030c18]/75 via-transparent to-[#030c18]/20" />

          <div class="relative z-10 w-full max-w-[1140px] px-7 pb-20 pt-24 text-white sm:pb-24 lg:px-24 xl:px-32">
            <div class="max-w-[680px]">
              <span class="inline-flex items-center gap-3 font-mono text-[11px] font-semibold uppercase tracking-[0.28em] text-[var(--accent-teal)]">
                <span class="h-px w-8 bg-[var(--accent-teal)]" />
                SIS INDUSTRIAL EXCELLENCE
              </span>

              <h1 class="mt-6 max-w-[650px] text-[clamp(40px,6vw,78px)] font-extrabold leading-[0.98] tracking-tight">
                {{ slide.title }}
              </h1>

              <p class="mt-6 max-w-[540px] text-base leading-[1.75] text-white/80 sm:text-lg">
                {{ slide.description }}
              </p>

              <div class="mt-8 flex flex-wrap gap-3">
                <button @click="router.push({ name: 'projects' })"
                  class="inline-flex items-center gap-2 rounded-full bg-[var(--accent-blue)] px-6 py-3.5 text-sm font-bold text-[#04121f] shadow-[0_10px_30px_-10px_rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.6)] transition hover:-translate-y-0.5 hover:brightness-110">
                  Explore Our Work
                  <span aria-hidden="true">→</span>
                </button>

                <button @click="router.push({ name: 'contact' })"
                  class="inline-flex items-center rounded-full border border-white/35 bg-white/5 px-6 py-3.5 text-sm font-bold text-white backdrop-blur-sm transition hover:border-[var(--accent-teal)] hover:text-[var(--accent-teal)]">
                  Get In Touch
                </button>
              </div>
            </div>

          </div>
        </article>
      </SwiperSlide>
    </Swiper>

    <div
      v-if="false"
      class="hidden">
      <div class="grid grid-cols-1 items-center gap-[46px] lg:grid-cols-2 lg:items-stretch">
        <!-- =======================================================
             LEFT COLUMN: TEXT
        ======================================================== -->
        <div class="flex flex-col justify-center">
          <span
            class="inline-flex items-center gap-[11px] font-mono text-[12px] font-semibold uppercase tracking-[0.26em] text-[var(--accent-blue)]">
            <span class="block h-px w-6 bg-gradient-to-r from-[var(--accent-blue)] to-transparent"></span>

            {{ hero?.tagline }}
          </span>

          <h1
            class="mt-[22px] text-[clamp(36px,5.2vw,62px)] font-extrabold leading-[1.03] -tracking-[0.035em] text-balance">
            Where
            <span
              class="bg-gradient-to-r from-[var(--accent-blue)] to-[var(--accent-teal)] bg-clip-text text-transparent">
              operational
            </span>
            meets
            <span
              class="bg-gradient-to-r from-[var(--accent-blue)] to-[var(--accent-teal)] bg-clip-text text-transparent">
              information
            </span>
            technology
          </h1>

          <p
            class="mt-[22px] max-w-[50ch] text-[15.5px] leading-[1.72] text-[var(--text-muted-90)] dark:text-[rgba(167,184,212,0.9)]">
            {{ hero?.subheadline }}
          </p>

          <div class="mt-8 flex flex-wrap gap-[13px]">
            <button @click="router.push({ name: 'projects' })"
              class="inline-flex items-center gap-[9px] rounded-full bg-[var(--accent-blue)] px-[26px] py-[14px] text-[14px] font-bold text-[#04121f] shadow-[0_10px_30px_-10px_rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.45)] transition-all duration-200 hover:-translate-y-0.5 hover:brightness-110">
              Explore Our Work

              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M13 6l6 6-6 6" />
              </svg>
            </button>

            <button @click="router.push({ name: 'contact' })"
              class="inline-flex items-center gap-[9px] rounded-full border border-[var(--border-40)] bg-[var(--bg-accent-5)] px-[26px] py-[14px] text-[14px] font-bold text-sis-text transition-all duration-200 hover:border-sis-accent hover:text-sis-accent dark:border-[rgba(126,165,224,0.3)] dark:bg-[rgba(126,165,224,0.04)] dark:text-white">
              Get In Touch
            </button>
          </div>

          <div class="mt-9 flex items-center gap-3 font-mono text-[12.5px] text-sis-muted dark:text-[#7688a6]">
            <span
              class="inline-block h-[6px] w-[6px] rounded-full bg-[var(--accent-teal)] shadow-[0_0_8px_var(--accent-teal)]"></span>

            {{ hero?.badge_text }}
          </div>
        </div>

        <!-- =======================================================
             RIGHT COLUMN: INDUSTRIAL IMAGE CAROUSEL
        ======================================================== -->
        <div class="relative min-h-[420px] overflow-hidden rounded-xl border border-[rgba(126,165,224,0.18)] bg-[#071525] shadow-[0_24px_70px_rgba(3,12,25,0.25)] dark:border-[rgba(126,165,224,0.3)] lg:h-full">
          <Swiper
            :modules="[Autoplay, EffectFade, Navigation, Pagination]"
            :effect="'fade'"
            :loop="true"
            :autoplay="{ delay: 4200, disableOnInteraction: false, pauseOnMouseEnter: true }"
            :navigation="true"
            :pagination="{ clickable: true }"
            :speed="900"
            class="hero-image-carousel h-full min-h-[420px]"
          >
            <SwiperSlide v-for="slide in carouselSlides" :key="slide.src">
              <div class="relative h-full min-h-[420px]">
                <img :src="slide.src" :alt="slide.alt" class="h-full w-full object-cover" />

                <div class="absolute inset-0 bg-gradient-to-t from-[#04111f]/95 via-[#04111f]/25 to-[#04111f]/10" />

                <div class="absolute inset-x-6 bottom-12 z-10 text-white sm:inset-x-8 sm:bottom-14">
                  <span class="font-mono text-[10px] font-semibold uppercase tracking-[0.24em] text-[var(--accent-teal)]">
                    SIS INDUSTRIAL EXCELLENCE
                  </span>
                  <h2 class="mt-2 max-w-[18rem] text-2xl font-bold leading-tight sm:text-3xl">
                    {{ slide.title }}
                  </h2>
                  <p class="mt-2 max-w-[28rem] text-sm leading-relaxed text-white/75">
                    {{ slide.description }}
                  </p>
                </div>
              </div>
            </SwiperSlide>
          </Swiper>

          <div class="pointer-events-none absolute left-6 top-5 z-10 flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.16em] text-white/75 sm:left-8">
            <span class="h-2 w-2 rounded-full bg-[var(--accent-teal)] shadow-[0_0_10px_var(--accent-teal)]" />
            Featured solutions
          </div>

          <div class="pointer-events-none absolute bottom-5 right-6 z-10 font-mono text-[10px] uppercase tracking-[0.16em] text-white/60 sm:right-8">
            01 / 04
          </div>

        <div class="hidden">
          <!-- Background image -->
          <div class="absolute inset-0">
            <img :src="factoryImage" alt="" class="h-full w-full object-cover"
              style="filter: saturate(0.9) brightness(0.66)" @load="factoryLoaded = true" />

            <Skeleton v-show="!factoryLoaded" class="absolute inset-0 h-full w-full" />
          </div>

          <!-- Veil overlay -->
          <div class="absolute inset-0" style="
              background: linear-gradient(
                180deg,
                rgba(6, 12, 24, 0.8),
                rgba(6, 12, 24, 0.6) 45%,
                rgba(6, 12, 24, 0.9)
              );
            "></div>

          <!-- Layer labels -->
          <div
            class="pointer-events-none absolute inset-0 z-[2] font-mono text-[8.5px] uppercase tracking-[0.2em] text-[#7688a6]">
            <span class="absolute left-4" style="top: 16%">Cloud</span>
            <span class="absolute left-4" style="top: 47%">
              SCADA / Edge
            </span>
            <span class="absolute left-4" style="top: 80%">Field</span>
          </div>

          <!-- Caption -->
          <div
            class="absolute left-4 right-4 top-[14px] z-[4] flex justify-between font-mono text-[11px] tracking-[0.08em] text-[#cfe0f6]"
            style="text-shadow: 0 1px 6px #000">
            <span>SIS · OT–IT CONTROL MESH</span>

            <span class="flex items-center gap-[7px] text-[var(--accent-teal)]">
              <i
                class="inline-block h-[7px] w-[7px] animate-[blink_1.6s_ease-in-out_infinite] rounded-full bg-[var(--accent-teal)] shadow-[0_0_8px_var(--accent-teal)]"></i>

              LIVE
            </span>
          </div>

          <!-- SVG mesh -->
          <svg class="absolute inset-0 z-[3] h-full w-full" viewBox="0 0 600 440" preserveAspectRatio="xMidYMid meet">
            <defs>
              <linearGradient id="meshGrad" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0" stop-color="var(--accent-blue)" />
                <stop offset="1" stop-color="var(--accent-teal)" />
              </linearGradient>

              <filter id="meshGlow">
                <feGaussianBlur stdDeviation="2.6" result="b" />

                <feMerge>
                  <feMergeNode in="b" />
                  <feMergeNode in="SourceGraphic" />
                </feMerge>
              </filter>
            </defs>

            <!-- Connection paths -->
            <g stroke="url(#meshGrad)" stroke-width="1.4" fill="none" opacity="0.5">
              <path id="e1" d="M120 360 L160 270" />
              <path id="e2" d="M300 372 L300 268" />
              <path id="e3" d="M480 360 L440 270" />
              <path id="e4" d="M160 250 L300 240" />
              <path id="e5" d="M440 250 L300 240" />
              <path id="e6" d="M300 218 L190 110" />
              <path id="e7" d="M300 218 L410 110" />
            </g>

            <!-- Faint base paths -->
            <g stroke="url(#meshGrad)" stroke-width="1.4" fill="none" opacity="0.2" stroke-dasharray="3 4">
              <path d="M120 360 L300 372 L480 360" />
              <path d="M190 110 L410 110" />
            </g>

            <!-- Animated telemetry dots -->
            <circle r="3.3" fill="var(--accent-teal)">
              <animateMotion dur="2.6s" repeatCount="indefinite">
                <mpath href="#e1" />
              </animateMotion>
            </circle>

            <circle r="3.3" fill="var(--accent-teal)">
              <animateMotion dur="2.2s" begin="-0.5s" repeatCount="indefinite">
                <mpath href="#e2" />
              </animateMotion>
            </circle>

            <circle r="3.3" fill="var(--accent-teal)">
              <animateMotion dur="2.8s" begin="-1s" repeatCount="indefinite">
                <mpath href="#e3" />
              </animateMotion>
            </circle>

            <circle r="3" fill="var(--accent-teal)">
              <animateMotion dur="2.4s" begin="-0.7s" repeatCount="indefinite">
                <mpath href="#e4" />
              </animateMotion>
            </circle>

            <circle r="3" fill="var(--accent-teal)">
              <animateMotion dur="2.4s" begin="-1.4s" repeatCount="indefinite">
                <mpath href="#e5" />
              </animateMotion>
            </circle>

            <circle r="3.5" fill="#fff">
              <animateMotion dur="3s" begin="-0.3s" repeatCount="indefinite">
                <mpath href="#e6" />
              </animateMotion>
            </circle>

            <circle r="3.5" fill="#fff">
              <animateMotion dur="3.2s" begin="-1.6s" repeatCount="indefinite">
                <mpath href="#e7" />
              </animateMotion>
            </circle>

            <!-- Animated control dots -->
            <circle r="3" fill="var(--accent-blue)">
              <animateMotion dur="3s" begin="-1.5s" repeatCount="indefinite" keyPoints="1;0" keyTimes="0;1">
                <mpath href="#e6" />
              </animateMotion>
            </circle>

            <circle r="3" fill="var(--accent-blue)">
              <animateMotion dur="2.2s" begin="-0.9s" repeatCount="indefinite" keyPoints="1;0" keyTimes="0;1">
                <mpath href="#e2" />
              </animateMotion>
            </circle>

            <!-- CLOUD nodes -->
            <g>
              <rect x="150" y="86" width="80" height="48" rx="12" fill="rgba(9,17,32,0.86)" stroke="#3a5a80"
                stroke-width="1.4" />

              <text x="190" y="108" text-anchor="middle" font-family="Consolas,monospace" font-size="10.5"
                font-weight="700" fill="#e7f0fb">
                IIoT
              </text>

              <text x="190" y="121" text-anchor="middle" font-family="Consolas,monospace" font-size="8" fill="#9fb4d2">
                CLOUD
              </text>
            </g>

            <g>
              <rect x="370" y="86" width="80" height="48" rx="12" fill="rgba(9,17,32,0.86)" stroke="#3a5a80"
                stroke-width="1.4" />

              <text x="410" y="108" text-anchor="middle" font-family="Consolas,monospace" font-size="10.5"
                font-weight="700" fill="#e7f0fb">
                ERP
              </text>

              <text x="410" y="121" text-anchor="middle" font-family="Consolas,monospace" font-size="8" fill="#9fb4d2">
                MES
              </text>
            </g>

            <!-- SCADA CORE -->
            <g filter="url(#meshGlow)">
              <circle cx="300" cy="243" r="33" fill="rgba(9,17,32,0.86)" stroke="url(#meshGrad)" stroke-width="2" />
            </g>

            <text x="300" y="242" text-anchor="middle" font-family="Consolas,monospace" font-size="11" font-weight="700"
              fill="#e7f0fb">
              SCADA
            </text>

            <text x="300" y="255" text-anchor="middle" font-family="Consolas,monospace" font-size="8" fill="#9fb4d2">
              CORE
            </text>

            <!-- EDGE gateways -->
            <g>
              <rect x="118" y="228" width="84" height="44" rx="11" fill="rgba(9,17,32,0.86)" stroke="#3a5a80"
                stroke-width="1.4" />

              <text x="160" y="247" text-anchor="middle" font-family="Consolas,monospace" font-size="9"
                font-weight="700" fill="#e7f0fb">
                GATEWAY
              </text>

              <text x="160" y="259" text-anchor="middle" font-family="Consolas,monospace" font-size="7.5"
                fill="#9fb4d2">
                OPC UA
              </text>
            </g>

            <g>
              <rect x="398" y="228" width="84" height="44" rx="11" fill="rgba(9,17,32,0.86)" stroke="#3a5a80"
                stroke-width="1.4" />

              <text x="440" y="247" text-anchor="middle" font-family="Consolas,monospace" font-size="9"
                font-weight="700" fill="#e7f0fb">
                GATEWAY
              </text>

              <text x="440" y="259" text-anchor="middle" font-family="Consolas,monospace" font-size="7.5"
                fill="#9fb4d2">
                MQTT
              </text>
            </g>

            <!-- FIELD nodes -->
            <g>
              <rect x="82" y="340" width="76" height="42" rx="11" fill="rgba(9,17,32,0.86)" stroke="#3a5a80"
                stroke-width="1.4" />

              <text x="120" y="359" text-anchor="middle" font-family="Consolas,monospace" font-size="9"
                font-weight="700" fill="#e7f0fb">
                PLC
              </text>

              <text x="120" y="371" text-anchor="middle" font-family="Consolas,monospace" font-size="7.5"
                fill="#9fb4d2">
                CONTROL
              </text>
            </g>

            <g>
              <rect x="262" y="352" width="76" height="42" rx="11" fill="rgba(9,17,32,0.86)" stroke="#3a5a80"
                stroke-width="1.4" />

              <text x="300" y="371" text-anchor="middle" font-family="Consolas,monospace" font-size="9"
                font-weight="700" fill="#e7f0fb">
                SENSOR
              </text>

              <text x="300" y="383" text-anchor="middle" font-family="Consolas,monospace" font-size="7.5"
                fill="#9fb4d2">
                HART
              </text>
            </g>

            <g>
              <rect x="442" y="340" width="76" height="42" rx="11" fill="rgba(9,17,32,0.86)" stroke="#3a5a80"
                stroke-width="1.4" />

              <text x="480" y="359" text-anchor="middle" font-family="Consolas,monospace" font-size="9"
                font-weight="700" fill="#e7f0fb">
                VFD
              </text>

              <text x="480" y="371" text-anchor="middle" font-family="Consolas,monospace" font-size="7.5"
                fill="#9fb4d2">
                DRIVE
              </text>
            </g>
          </svg>

          <!-- Legend -->
          <div class="absolute bottom-3 left-4 z-[4] flex gap-[14px] font-mono text-[10px] text-[#aebfd8]">
            <span class="flex items-center gap-[6px]">
              <i class="inline-block h-[2px] w-[14px] rounded bg-[var(--accent-teal)]"></i>
              Telemetry &#9650;
            </span>

            <span class="flex items-center gap-[6px]">
              <i class="inline-block h-[2px] w-[14px] rounded bg-[var(--accent-blue)]"></i>
              Control &#9660;
            </span>
          </div>
        </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, EffectFade, Navigation, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/effect-fade";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { api } from "../../api/index.js";
import { normalizeHero } from "../../api/normalizers.js";
import Skeleton from "@/components/common/Skeleton.vue";

const router = useRouter();
const carouselSlides = ref([]);
const isLoading = ref(true);


const API_BASE = (import.meta.env.VITE_API_BASE_URL || "").replace(
  /\/+$/,
  "",
);

function getHeroImageUrl(src) {
  if (!src) return "";

  if (
    src.startsWith("http://") ||
    src.startsWith("https://") ||
    src.startsWith("blob:")
  ) {
    return src;
  }

  const normalizedSrc = src
    .replace(/^\/+/, "")
    .replace(/^storage\/+/, "");

  // Uploaded/backend images
  if (normalizedSrc.startsWith("collections/")) {
    const apiUrl = new URL(API_BASE || "/", window.location.origin);

    return `${apiUrl.origin}/storage/${normalizedSrc}`;
  }

  // Existing frontend static images
  return `${import.meta.env.BASE_URL}${normalizedSrc}`;
}


onMounted(async () => {
  try {
    const response = await api.getHero();

    console.log("hero response:", response);

    carouselSlides.value = response.map((slide) => ({
      ...slide,
      src: getHeroImageUrl(slide.src),
    }));
  } catch (error) {
    console.error("Failed to fetch hero:", error);

    const fallback = normalizeHero();

    carouselSlides.value = fallback;
  } finally {
    isLoading.value = false;
  }
});
</script>
