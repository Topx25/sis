<template>
  <section
  class="relative overflow-hidden px-6 py-24 font-['Plus_Jakarta_Sans','Manrope','Inter',sans-serif] text-white
         bg-[linear-gradient(105deg,#064985_0%,#0b4f91_25%,#303575_50%,#713064_75%,#b3134d_100%)]"
>
    <!-- Blue glow -->
    <div
      class="pointer-events-none absolute rounded-full blur-[80px]"
      :class="'w-96 h-96 -top-24 -right-24 bg-[rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.09)] max-md:w-[300px] max-md:h-[300px] max-md:-top-32 max-md:-right-24 max-[480px]:w-[180px] max-[480px]:h-[180px] max-[480px]:-top-20 max-[480px]:-right-16'"
      aria-hidden="true"
    ></div>

    <!-- Teal glow -->
    <div
      class="pointer-events-none absolute rounded-full blur-[80px]"
      :class="'w-64 h-64 -bottom-16 -left-16 bg-[rgba(var(--accent-teal-r),var(--accent-teal-g),var(--accent-teal-b),0.06)] max-md:w-[300px] max-md:h-[300px] max-md:-bottom-32 max-md:-left-20 max-[480px]:w-[180px] max-[480px]:h-[180px] max-[480px]:-bottom-20 max-[480px]:-left-12'"
      aria-hidden="true"
    ></div>

    <div class="relative z-10 mx-auto w-full max-w-[1600px]">
      <!-- Heading -->
      <div
        class="mb-16 flex flex-col items-center gap-4 text-center"
        data-reveal
      >
        <span
          class="mb-1 block text-[0.7rem] font-bold uppercase tracking-[0.3em] text-[var(--accent-blue)]"
        >
          {{ sectionData.subtitle || 'Our Clients' }}
        </span>

        <h2
          class="m-0 font-['Plus_Jakarta_Sans','Inter',sans-serif] text-[clamp(2.2rem,5vw,3.5rem)] font-bold leading-tight -tracking-[0.02em] text-white dark:text-[#eaf1fb]"
        >
          {{ sectionData.title || 'Trusted by' }}
          <em
            v-if="sectionData.title_highlight"
            class="not-italic font-bold text-white dark:text-dark-accent-teal ml-2"
          >
            {{ sectionData.title_highlight }}
          </em>
        </h2>

        <p
          v-if="sectionData.description"
          class="m-0 max-w-[36rem] font-sans text-base leading-relaxed text-gray-300 dark:text-dark-text-muted"
        >
          {{ sectionData.description }}
        </p>
      </div>

      <!-- Clients Carousel -->
      <div
        class="clients-marquee relative w-full min-w-0 overflow-hidden rounded-2xl border border-[rgba(126,165,224,0.18)] bg-[rgba(255,255,255,0.25)] px-6 py-5 shadow-[0_8px_40px_rgba(20,70,120,0.06)] backdrop-blur-md dark:border-[rgba(126,165,224,0.14)] dark:bg-[rgba(126,165,224,0.06)] dark:shadow-none max-md:px-4 max-md:py-4 max-[480px]:px-3"
        data-reveal
      >
        <Swiper
          :modules="[Autoplay]"
          :slides-per-view="'auto'"
          :space-between="36"
          :loop="true"
          :speed="5000"
          :autoplay="{
            delay: 0,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
          }"
          :allow-touch-move="true"
          :grab-cursor="true"
          class="client-carousel"
        >
          <SwiperSlide
            v-for="(client, i) in clients"
            :key="client.id || i"
            class="!w-auto"
          >
            <div
              class="group flex flex-col items-center gap-3 py-1 transition-transform duration-300 hover:-translate-y-1"
            >
              <!-- Card Box: Fixed uniform dimensions for any uploaded image -->
              <div
                class="flex h-36 w-48 items-center justify-center rounded-2xl border border-white/40 bg-white p-4 shadow-sm transition-all duration-300 group-hover:border-white/80 group-hover:shadow-lg dark:border-white/20 dark:bg-white max-md:h-28 max-md:w-36 max-md:p-3 max-[480px]:h-24 max-[480px]:w-28 max-[480px]:p-2"
              >
                <img
                  :src="resolveClientLogo(client)"
                  :alt="client.name || 'Client logo'"
                  class="max-h-full max-w-full h-auto w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                  loading="lazy"
                  @load="carouselReady = true"
                />
              </div>

              <span
                class="whitespace-nowrap text-center font-sans text-sm font-semibold leading-tight text-white/95 drop-shadow-sm transition-colors duration-200 group-hover:text-white max-[480px]:text-xs"
              >
                {{ client.name }}
              </span>
            </div>
          </SwiperSlide>
        </Swiper>

        <!-- Loading -->
        <div
          v-show="isLoading"
          class="absolute inset-0 z-10 flex items-center gap-8 bg-[rgba(255,255,255,0.45)] px-6 py-5 backdrop-blur-sm dark:bg-[rgba(126,165,224,0.06)] max-md:px-4 max-[480px]:px-3"
        >
          <div
            v-for="i in 6"
            :key="i"
            class="flex flex-shrink-0 flex-col items-center gap-3"
          >
            <Skeleton
              class="h-36 w-48 rounded-2xl max-md:h-28 max-md:w-36 max-[480px]:h-24 max-[480px]:w-28"
            />
            <Skeleton class="h-4 w-20 rounded-full" />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay } from "swiper/modules";
import "swiper/css";

import Skeleton from "@/components/common/Skeleton.vue";
import { api } from "../../api/index.js";
import { normalizeClients } from "../../api/normalizers.js";
import { resolveImageUrl } from "@/utils/imageUrl.js";

const clients = ref([]);
const isLoading = ref(true);
const carouselReady = ref(false);

const sectionData = ref({
  subtitle: "Our Clients",
  title: "Trusted by",
  title_highlight: "Industry Leaders",
  description: "Partnering with leading organizations across the MENA region to deliver industrial excellence.",
});

function resolveClientLogo(client) {
  const raw = client?.src || client?.logo_url || "";
  if (!raw) return "";
  return resolveImageUrl(raw);
}

onMounted(async () => {
  try {
    const response = await api.getClients();
    const list = Array.isArray(response)
      ? response
      : Array.isArray(response?.clients)
        ? response.clients
        : Array.isArray(response?.data)
          ? response.data
          : [];

    if (list.length > 0) {
      clients.value = list;
      const headerItem =
        response?.section ||
        list.find((c) => c.title || c.subtitle || c.description) ||
        list[0];
      if (headerItem && (headerItem.subtitle || headerItem.title || headerItem.description)) {
        sectionData.value = {
          subtitle: headerItem.subtitle ?? sectionData.value.subtitle,
          title: headerItem.title ?? sectionData.value.title,
          title_highlight: headerItem.title_highlight ?? sectionData.value.title_highlight,
          description: headerItem.description ?? sectionData.value.description,
        };
      }
    } else {
      clients.value = normalizeClients();
    }
  } catch (error) {
    console.error("Failed to fetch clients:", error);
    clients.value = normalizeClients();
  } finally {
    isLoading.value = false;
  }
});
</script>
