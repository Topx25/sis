<template>
  <section ref="sectionRef"
    class="relative overflow-hidden bg-sis-panel px-6 py-20 font-['Plus_Jakarta_Sans','Inter',sans-serif] dark:bg-dark-bg sm:py-24">
    <!-- Background Grid -->
    <div class="pointer-events-none absolute inset-0 z-0" style="
        background-image:
          linear-gradient(
            90deg,
            rgba(
              var(--accent-blue-r),
              var(--accent-blue-g),
              var(--accent-blue-b),
              0.025
            ) 1px,
            transparent 1px
          ),
          linear-gradient(
            0deg,
            rgba(
              var(--accent-blue-r),
              var(--accent-blue-g),
              var(--accent-blue-b),
              0.025
            ) 1px,
            transparent 1px
          );
        background-size: 60px 60px;
      " aria-hidden="true"></div>

    <!-- Ambient Glow -->
    <div class="pointer-events-none absolute z-0 animate-pulse rounded-full blur-[120px]" style="
        width: 600px;
        height: 600px;
        top: -15rem;
        left: -10rem;
        background: rgba(
          var(--accent-blue-r),
          var(--accent-blue-g),
          var(--accent-blue-b),
          0.05
        );
      " aria-hidden="true"></div>

    <div class="pointer-events-none absolute z-0 rounded-full blur-[130px]" style="
        width: 500px;
        height: 500px;
        bottom: -12rem;
        right: -8rem;
        background: rgba(
          var(--accent-teal-r),
          var(--accent-teal-g),
          var(--accent-teal-b),
          0.05
        );
      " aria-hidden="true"></div>

    <div class="relative z-10 mx-auto max-w-[1200px]">
      <!-- Header -->
      <div class="mb-12 flex flex-col items-center gap-4 text-center sm:mb-14" data-reveal>
        <div class="mb-1 inline-flex items-center gap-4">
          <span class="block h-px w-10 bg-[var(--accent-blue)] sm:w-12"></span>

          <span
            class="font-sans text-[10px] font-semibold uppercase tracking-[0.24em] text-[var(--accent-blue)] sm:text-xs sm:tracking-[0.28em]">
            {{ homeSubtitle }}
          </span>

          <span class="block h-px w-10 bg-[var(--accent-blue)] sm:w-12"></span>
        </div>

        <h2
          class="m-0 font-['Plus_Jakarta_Sans','Inter',sans-serif] text-[clamp(2rem,5vw,3.25rem)] font-bold leading-tight -tracking-[0.02em] text-sis-text dark:text-[#dae2fd]">
          {{ homeTitle }}
          <em
            v-if="homeTitleHighlight"
            class="not-italic font-bold text-sis-accent dark:text-dark-accent-teal"
            style="
              text-shadow:
                0 0 30px
                rgba(
                  var(--accent-teal-r),
                  var(--accent-teal-g),
                  var(--accent-teal-b),
                  0.3
                );
            ">
            {{ homeTitleHighlight }}
          </em>
        </h2>

        <p
          class="m-0 max-w-[38rem] font-sans text-sm leading-relaxed text-sis-muted dark:text-dark-text-muted sm:text-base">
          {{ homeDescription }}
        </p>
      </div>

      <!-- Featured Partners -->
      <div data-reveal>
        <!-- Loading -->
        <div v-if="isLoading" class="flex flex-wrap justify-center gap-6">
          <div v-for="i in 3" :key="i"
            class="flex min-h-[240px] w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] max-w-[380px] flex-col items-center justify-center gap-5 rounded-2xl border border-[rgba(126,165,224,0.12)] p-7"
            style="background: rgba(126,165,224,0.04)">
            <Skeleton class="h-28 w-44 rounded-xl" />
            <Skeleton class="h-4 w-28" />
          </div>
        </div>

        <!-- Partners -->
        <div v-else-if="ecosystemPartners.length" class="relative">
          <div class="flex flex-wrap justify-center gap-6">
            <template v-for="(partner, index) in ecosystemPartners" :key="partner.id ?? index">
              <a v-if="isDesktop || index === currentPartner" :href="partner.website_url || '#'"
                :target="partner.website_url ? '_blank' : undefined" :rel="partner.website_url
                    ? 'noopener noreferrer'
                    : undefined
                  "
                class="group relative flex min-h-[240px] w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] max-w-[380px] flex-col items-center justify-center gap-5 overflow-hidden rounded-2xl border border-[rgba(126,165,224,0.12)] p-7 backdrop-blur-[12px] transition-all duration-[400ms] hover:-translate-y-1 hover:border-[rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.3)] hover:shadow-[0_12px_40px_rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.1)]"
                style="
                  background: rgba(126,165,224,0.04);
                  transition-timing-function: cubic-bezier(0.4,0,0.2,1);
                " @mousemove="onTilt($event)" @mouseleave="onTiltReset($event)">
                <!-- Logo -->
                <div v-if="
                  partner.logo_url &&
                  !errored.has(partner.id ?? partner.name)
                "
                  class="flex h-28 w-44 items-center justify-center overflow-hidden rounded-xl border border-[rgba(126,165,224,0.12)] bg-[rgba(126,165,224,0.08)] p-3 transition-all duration-300 group-hover:border-[rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.25)] group-hover:bg-[rgba(126,165,224,0.14)] group-hover:shadow-[0_0_24px_rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.1)]">
                  <img :src="resolveImageUrl(partner.logo_url)" :alt="partner.name"
                    class="max-h-full max-w-full object-contain transition duration-300 group-hover:scale-105"
                    loading="lazy"
                    @error="onImgError(partner.id ?? partner.name)" />
                </div>

                <!-- Fallback -->
                <div v-else
                  class="flex h-28 w-44 items-center justify-center rounded-xl border transition-all duration-300 group-hover:scale-105"
                  :style="{
                    background: `${partner.glow || '#00d4ff'}15`,
                    borderColor: `${partner.glow || '#00d4ff'}30`,
                  }">
                  <span class="font-['Plus_Jakarta_Sans','Inter',sans-serif] text-xl font-extrabold tracking-[0.05em]"
                    :style="{
                      color: partner.glow || '#00d4ff',
                    }">
                    {{ getInitials(partner.name) }}
                  </span>
                </div>

                <!-- Name -->
                <span
                  class="text-center font-sans text-sm font-semibold leading-tight text-sis-text transition-colors duration-300 dark:text-dark-text">
                  {{ partner.name }}
                </span>

                <!-- Top Accent -->
                <div
                  class="absolute left-0 right-0 top-0 h-[2px] opacity-0 transition-opacity duration-300 group-hover:opacity-70"
                  :style="{
                    background: `linear-gradient(
                      90deg,
                      ${partner.glow || '#00d4ff'},
                      transparent
                    )`,
                  }"></div>

                <!-- Bottom Glow -->
                <div
                  class="pointer-events-none absolute -bottom-10 h-20 w-3/4 rounded-full opacity-0 blur-2xl transition-opacity duration-500 group-hover:opacity-30"
                  :style="{
                    background: partner.glow || '#00d4ff',
                  }"></div>
              </a>
            </template>
          </div>

          <!-- Mobile Navigation -->
          <div v-if="ecosystemPartners.length > 1" class="mt-6 flex items-center justify-center gap-4 lg:hidden">
            <button type="button" aria-label="Previous partner"
              class="flex h-9 w-9 items-center justify-center rounded-full border border-[rgba(126,165,224,0.16)] text-sis-muted transition-all duration-300 hover:border-[var(--accent-blue)] hover:text-[var(--accent-blue)] dark:text-dark-text-muted"
              @click="previousPartner">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                  d="M12.79 15.79a.75.75 0 0 1-1.06 0l-5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 0 1 0-1.06l5-5a.75.75 0 1 1 1.06 1.06L8.32 10.5l4.47 4.47a.75.75 0 0 1 0 1.06Z"
                  clip-rule="evenodd" />
              </svg>
            </button>

            <div class="flex items-center gap-2">
              <button v-for="(_, index) in ecosystemPartners" :key="index" type="button"
                :aria-label="`Go to partner ${index + 1}`" class="h-1.5 rounded-full transition-all duration-300"
                :class="currentPartner === index
                    ? 'w-6 bg-[var(--accent-blue)]'
                    : 'w-1.5 bg-sis-muted/30 dark:bg-dark-text-muted/30'
                  " @click="goToPartner(index)"></button>
            </div>

            <button type="button" aria-label="Next partner"
              class="flex h-9 w-9 items-center justify-center rounded-full border border-[rgba(126,165,224,0.16)] text-sis-muted transition-all duration-300 hover:border-[var(--accent-blue)] hover:text-[var(--accent-blue)] dark:text-dark-text-muted"
              @click="nextPartner">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                  d="M7.21 4.21a.75.75 0 0 1 1.06 0l5 5a.75.75 0 0 1 0 1.06l-5 5a.75.75 0 1 1-1.06-1.06L11.68 10.5 7.21 6.03a.75.75 0 0 1 0-1.06Z"
                  clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="rounded-2xl border border-[rgba(126,165,224,0.12)] p-10 text-center"
          style="background: rgba(126,165,224,0.04)">
          <p class="m-0 font-sans text-sm text-sis-muted dark:text-dark-text-muted">
            No featured partners available at the moment.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, reactive, ref } from "vue";
import { api } from "../../api/index.js";
import { normalizeEcosystem } from "../../api/normalizers.js";
import Skeleton from "@/components/common/Skeleton.vue";
import { resolveImageUrl } from "@/utils/imageUrl.js";

const ecosystemPartners = ref([]);
const secondaryPartners = ref([]);
const benefits = ref([]);
const isLoading = ref(true);

const currentPartner = ref(0);
const isDesktop = ref(true);
const partnerHeader = ref(null);

const homeSubtitle = computed(
  () => partnerHeader.value?.home_subtitle || "Our Partners"
);
const homeTitle = computed(
  () => partnerHeader.value?.home_title || "Building stronger"
);
const homeTitleHighlight = computed(
  () => partnerHeader.value?.home_title_highlight ?? "ecosystems"
);
const homeDescription = computed(
  () =>
    partnerHeader.value?.home_description ||
    "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients."
);

const errored = reactive(new Set());

let mediaQuery = null;

onMounted(async () => {
  mediaQuery = window.matchMedia("(min-width: 1024px)");

  updateBreakpoint(mediaQuery);

  mediaQuery.addEventListener("change", updateBreakpoint);

  try {
    const response = await api.getPartners();

    const data = response;

    partnerHeader.value = data.header;
    ecosystemPartners.value = data.featured_partners;
    secondaryPartners.value = data.partners;
    benefits.value = data.value_badges;
  } catch (error) {
    console.error("Failed to fetch partners:", error);

    const data = normalizeEcosystem();

partnerHeader.value = data.header;
ecosystemPartners.value = data.featured_partners;
secondaryPartners.value = data.partners;
benefits.value = data.value_badges;
  } finally {
    isLoading.value = false;
  }
});

onBeforeUnmount(() => {
  mediaQuery?.removeEventListener("change", updateBreakpoint);
});

function updateBreakpoint(event) {
  isDesktop.value = event.matches;
}

function getInitials(name = "") {
  return name
    .split(" ")
    .filter(Boolean)
    .map((word) => word[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();
}

function onImgError(id) {
  errored.add(id);
}

function previousPartner() {
  if (!ecosystemPartners.value.length) return;

  currentPartner.value =
    currentPartner.value === 0
      ? ecosystemPartners.value.length - 1
      : currentPartner.value - 1;
}

function nextPartner() {
  if (!ecosystemPartners.value.length) return;

  currentPartner.value =
    currentPartner.value === ecosystemPartners.value.length - 1
      ? 0
      : currentPartner.value + 1;
}

function goToPartner(index) {
  currentPartner.value = index;
}

function onTilt(e) {
  if (!isDesktop.value) return;

  const card = e.currentTarget;
  const rect = card.getBoundingClientRect();

  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;

  const cx = rect.width / 2;
  const cy = rect.height / 2;

  const rotX = (cy - y) / 25;
  const rotY = (x - cx) / 25;

  card.style.transform = `
    perspective(800px)
    rotateX(${rotX}deg)
    rotateY(${rotY}deg)
    scale3d(1.02, 1.02, 1.02)
  `;
}

function onTiltReset(e) {
  e.currentTarget.style.transform =
    "perspective(800px) rotateX(0) rotateY(0) scale3d(1,1,1)";
}
</script>
