<template>
  <section
    id="services"
    v-reveal
    class="relative overflow-hidden py-20 text-sis-text dark:text-dark-text lg:py-28 xl:py-32"
    style="background: var(--bg-section-1)"
  >
    <div
      class="mx-auto flex max-w-[1600px] flex-col gap-12 px-7 lg:flex-row lg:items-center lg:gap-20 lg:px-12 xl:gap-24 xl:px-16"
    >
      <!-- Section Intro -->
      <div class="flex-[0_0_36%]" data-reveal>
        <div class="mb-5 inline-flex items-center gap-4 lg:mb-7">
          <span
            class="block h-px w-12 bg-sis-accent lg:w-16"
          ></span>

          <span
            class="font-sans text-xs font-semibold uppercase tracking-[0.28em] text-sis-accent lg:text-sm"
          >
            {{ sectionHeader.subtitle || 'Services & Capabilities' }}
          </span>
        </div>

        <h2
          class="m-0 max-w-[28rem] font-['Plus_Jakarta_Sans',Inter,sans-serif] text-[clamp(2.5rem,5.2vw,4.75rem)] font-bold leading-[1.08] -tracking-[0.02em] text-sis-text dark:text-[#eaf1fb]"
        >
          <span class="block">{{ sectionHeader.title || 'One platform,' }}</span>
          <span
            v-if="sectionHeader.title_highlight"
            class="block text-sis-accent"
          >{{ sectionHeader.title_highlight }}</span>
          <span class="block">{{ sectionHeader.description || 'industrial capability' }}</span>
        </h2>
      </div>

      <!-- Services Grid -->
      <div
        class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2 lg:gap-5 xl:gap-6"
        
      >
        <template
          v-for="(card, i) in displayItems"
          :key="card?.id ?? i"
        >
          <!-- =========================
               SERVICE CARD
          ========================== -->
          <article
            v-if="servicesLoaded"
            class="service-card group relative min-h-[190px] overflow-hidden p-6 transition-all duration-300 hover:-translate-y-[5px] lg:min-h-[220px] lg:p-8 xl:min-h-[240px] xl:p-9"
            data-reveal
          >
            <!-- Main geometric background -->
            <div
              class="service-card__shape absolute inset-0"
              aria-hidden="true"
            ></div>

            <!-- Decorative industrial lines -->
            <div
              class="service-card__grid absolute inset-0 pointer-events-none"
              aria-hidden="true"
            ></div>

            <!-- Top accent line -->
            <div
              class="service-card__accent absolute left-0 top-0 h-[3px] w-full origin-left scale-x-0 bg-gradient-to-r from-sis-accent to-[var(--accent-teal)] transition-transform duration-500 group-hover:scale-x-100"
              aria-hidden="true"
            ></div>

            <!-- Number -->
            <span
              class="absolute right-7 top-6 z-10 font-['Cascadia_Code',Consolas,monospace] text-[11px] tracking-wider text-[var(--text-muted-40)] transition-colors duration-300 group-hover:text-sis-accent dark:text-[rgba(126,165,224,0.4)]"
            >
              0{{ i + 1 }}
            </span>

            <!-- Content -->
            <div class="relative z-10">
              <!-- Icon -->
              <div
                class="service-icon mb-5 grid h-12 w-12 place-items-center text-sis-accent transition-all duration-300 group-hover:scale-105 group-hover:bg-[rgba(47,155,224,0.18)] lg:h-14 lg:w-14"
              >
                <UiIcon
                  :name="card.icon"
                  class="[&_svg]:h-6 [&_svg]:w-6 lg:[&_svg]:h-7 lg:[&_svg]:w-7"
                />
              </div>

              <!-- Title -->
              <h3
                class="m-0 mb-2 font-['Plus_Jakarta_Sans',Inter,sans-serif] text-lg font-bold -tracking-[0.01em] text-sis-text dark:text-[#eaf1fb] lg:text-xl xl:text-[1.35rem]"
              >
                {{ card.title }}
              </h3>

              <!-- Description -->
              <p
                class="m-0 max-w-[34rem] text-sm leading-relaxed text-[var(--text-muted-90)] dark:text-[rgba(167,184,212,0.9)] lg:text-[15px] xl:text-base"
              >
                {{ card.description }}
              </p>
            </div>

            <!-- Bottom geometric detail -->
            <div
              class="absolute bottom-0 right-0 h-10 w-10 translate-x-5 translate-y-5 rotate-45 border border-[var(--bg-accent-30)] transition-all duration-500 group-hover:translate-x-3 group-hover:translate-y-3 group-hover:border-[var(--bg-accent-40)] dark:border-[rgba(126,165,224,0.16)]"
              aria-hidden="true"
            ></div>
          </article>

          <!-- =========================
               SKELETON
          ========================== -->
          <div
            v-else
            class="service-card relative min-h-[190px] overflow-hidden p-6 lg:min-h-[220px] lg:p-8 xl:min-h-[240px] xl:p-9"
            data-reveal
          >
            <div
              class="service-card__shape absolute inset-0"
              aria-hidden="true"
            ></div>

            <div class="relative z-10">
              <Skeleton
                class="mb-5 h-12 w-12 lg:h-14 lg:w-14"
              />

              <Skeleton
                class="mb-2 h-6 w-3/4 lg:h-7"
              />

              <Skeleton
                class="mb-1 h-4 w-full lg:h-5"
              />

              <Skeleton class="h-4 w-5/6" />
            </div>
          </div>
        </template>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";

import UiIcon from "@/components/common/UiIcon.vue";
import Skeleton from "@/components/common/Skeleton.vue";

import { api } from "@/api";
import { normalizeServices, getFallbackServices } from "../../api/normalizers";
import { useSiteStore } from "@/stores/useSiteStore";

const siteStore = useSiteStore();
const servicesLoaded = ref(false);
const services = ref([]);
const sectionHeader = ref({
  subtitle: "Services & Capabilities",
  title: "One platform,",
  title_highlight: "every",
  description: "industrial capability",
});

const displayItems = computed(() => {
  if (servicesLoaded.value) {
    return services.value;
  }

  return Array.from({ length: 4 });
});

onMounted(async () => {
  if (siteStore.servicesLoaded) {
    services.value = siteStore.services?.length ? siteStore.services : getFallbackServices();
    if (siteStore.servicesSection) {
      sectionHeader.value = {
        subtitle: siteStore.servicesSection.subtitle || "Services & Capabilities",
        title: siteStore.servicesSection.title || "One platform,",
        title_highlight: siteStore.servicesSection.title_highlight ?? "every",
        description: siteStore.servicesSection.description || "industrial capability",
      };
    }
    servicesLoaded.value = true;
    return;
  }

  try {
    const response = await api.getServices();

    const normalized = normalizeServices(response);
    services.value = normalized.services?.length ? normalized.services : getFallbackServices();
    if (normalized.section) {
      sectionHeader.value = {
        subtitle: normalized.section.subtitle || "Services & Capabilities",
        title: normalized.section.title || "One platform,",
        title_highlight: normalized.section.title_highlight ?? "every",
        description: normalized.section.description || "industrial capability",
      };
    }
  } catch (error) {
    console.error("Failed to fetch services:", error);

    services.value = getFallbackServices();
  } finally {
    servicesLoaded.value = true;
  }
});
</script>

<style scoped>
/* =========================================
   INDUSTRIAL SERVICE CARD
========================================= */

.service-card {
  isolation: isolate;
}

/* Main geometric shape */
.service-card__shape {
  background: var(--bg-card-1);

  border: 1px solid var(--border-40);

  /*
    Cut the top-right and bottom-left corners.
    This creates the asymmetric industrial shape.
  */
  clip-path: polygon(
    0 0,
    calc(100% - 30px) 0,
    100% 30px,
    100% 100%,
    30px 100%,
    0 calc(100% - 30px)
  );

  transition:
    transform 300ms ease,
    border-color 300ms ease,
    background-color 300ms ease;
}

/* Dark mode border */
:global(.dark) .service-card__shape {
  border-color: rgba(126, 165, 224, 0.14);
}

/* Hover shape */
.service-card:hover .service-card__shape {
  background: var(--bg-card-1);

  border-color: var(--bg-accent-40);
}

:global(.dark) .service-card:hover .service-card__shape {
  border-color: rgba(126, 165, 224, 0.3);
}


/* =========================================
   INDUSTRIAL GRID / TECH LINES
========================================= */

.service-card__grid {
  opacity: 0.35;

  background-image:
    linear-gradient(
      90deg,
      transparent 49.5%,
      rgba(47, 155, 224, 0.035) 50%,
      transparent 50.5%
    ),
    linear-gradient(
      0deg,
      transparent 49.5%,
      rgba(47, 155, 224, 0.035) 50%,
      transparent 50.5%
    );

  background-size: 36px 36px;

  mask-image: linear-gradient(
    135deg,
    transparent 10%,
    black 45%,
    transparent 90%
  );

  -webkit-mask-image: linear-gradient(
    135deg,
    transparent 10%,
    black 45%,
    transparent 90%
  );

  clip-path: polygon(
    0 0,
    calc(100% - 30px) 0,
    100% 30px,
    100% 100%,
    30px 100%,
    0 calc(100% - 30px)
  );
}


/* =========================================
   ICON
========================================= */

.service-icon {
  position: relative;

  width: 3rem;
  height: 3rem;

  border: 1px solid var(--bg-accent-30);

  background: rgba(47, 155, 224, 0.1);

  /*
    Small angular icon container
  */
  clip-path: polygon(
    0 0,
    calc(100% - 8px) 0,
    100% 8px,
    100% 100%,
    8px 100%,
    0 calc(100% - 8px)
  );
}

:global(.dark) .service-icon {
  border-color: rgba(126, 165, 224, 0.3);
}

@media (min-width: 1024px) {
  .service-icon {
    width: 3.5rem;
    height: 3.5rem;
  }
}


/* =========================================
   NUMBER
========================================= */

.service-card::after {
  content: "";

  position: absolute;

  right: 26px;
  top: 24px;

  width: 7px;
  height: 7px;

  border-top: 1px solid var(--bg-accent-30);
  border-right: 1px solid var(--bg-accent-30);

  opacity: 0;

  transform: translate(-6px, 6px);

  transition:
    opacity 300ms ease,
    transform 300ms ease;
}

.service-card:hover::after {
  opacity: 1;
  transform: translate(0, 0);
}


/* =========================================
   RESPONSIVE SHAPE
========================================= */

@media (max-width: 640px) {
  .service-card__shape {
    clip-path: polygon(
      0 0,
      calc(100% - 22px) 0,
      100% 22px,
      100% 100%,
      22px 100%,
      0 calc(100% - 22px)
    );
  }

  .service-card__grid {
    clip-path: polygon(
      0 0,
      calc(100% - 22px) 0,
      100% 22px,
      100% 100%,
      22px 100%,
      0 calc(100% - 22px)
    );
  }
}
</style>