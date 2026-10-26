<template>
  <div class="grid grid-cols-1 gap-[18px] md:grid-cols-2 lg:grid-cols-3">
    <template v-if="loading">
      <div
        v-for="i in skeletonCount"
        :key="`skeleton-${i}`"
        class="block overflow-hidden rounded-xl border border-[var(--border-40)] dark:border-[rgba(126,165,224,0.14)]"
        style="background: var(--bg-card-2)"
      >
        <div
          class="relative aspect-[4/3] border-b border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)]"
        >
          <Skeleton class="h-full w-full rounded-none" />
        </div>
        <div class="p-5">
          <Skeleton class="mb-2 h-5 w-3/4" />
          <Skeleton class="mb-1 h-3 w-1/3" />
          <Skeleton class="mb-1 h-4 w-full" />
          <Skeleton class="h-4 w-5/6" />
        </div>
      </div>
    </template>

    <div
      v-else-if="samples.length === 0"
      class="col-span-full rounded-xl border border-dashed border-[var(--border-40)] px-6 py-12 text-center"
      style="background: var(--bg-card-2)"
    >
      <span class="material-symbols-outlined text-sis-muted dark:text-[#7688a6]" style="font-size: 32px">
        dashboards
      </span>
      <h3 class="mt-3 text-base font-bold text-sis-text dark:text-[#eaf1fb]">
        No work samples available
      </h3>
      <p class="mx-auto mt-1 max-w-md text-sm text-sis-muted dark:text-[#aebfd8]">
        Please check back soon for new dashboards and project showcases.
      </p>
    </div>

    <a
      v-for="(sample, index) in samples"
      :key="sample.slug"
      :href="sampleUrl(sample)"
      target="_blank"
      rel="noopener noreferrer"
      class="group block overflow-hidden rounded-xl border border-[var(--border-40)] dark:border-[rgba(126,165,224,0.14)] hover:border-[var(--bg-accent-50)] dark:hover:border-[rgba(55,182,255,0.5)] hover:shadow-xl hover:shadow-[var(--shadow-accent-8)] dark:hover:shadow-[var(--shadow-blue-8)] hover:-translate-y-1 transition-all duration-300"
      style="background: var(--bg-card-2)"
      data-reveal
      :style="{ transitionDelay: `${index * 0.06}s` }"
      @click.prevent="handleClick(sample)"
      :aria-label="`${sample.title} work sample`"
    >
      <div
        class="relative aspect-[4/3] overflow-hidden border-b border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)]"
      >
        <img
          :src="sampleImage(sample)"
          :data-sample="sample.id ?? sample.slug"
          :alt="sample.title"
          class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
          loading="lazy"
          @error="onImageError"
        />
        <span
          class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-black/55 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-white backdrop-blur-sm"
        >
          <span class="material-symbols-outlined" style="font-size: 12px">{{
            sample.free ? "lock_open" : "lock"
          }}</span>
          {{ sample.badge || (sample.free ? "Free access" : "Demo access") }}
        </span>
        <div
          v-if="!sample.free"
          class="absolute inset-0 flex items-center justify-center bg-sis-panel/20 dark:bg-dark-bg/20 backdrop-blur-md transition-opacity duration-300"
        >
          <span
            class="inline-flex items-center gap-2 rounded-full bg-sis-accent dark:bg-[var(--accent-blue)] text-white dark:text-[#04121f] px-4 py-2 text-xs font-bold shadow-lg"
          >
            View work sample
            <span class="material-symbols-outlined" style="font-size: 16px"
              >open_in_new</span
            >
          </span>
        </div>
      </div>

      <div class="p-5">
        <div class="flex items-start justify-between gap-3">
          <h3
            class="text-lg font-bold text-sis-text dark:text-[#eaf1fb] leading-snug"
          >
            {{ sample.title }}
          </h3>
          <span
            class="material-symbols-outlined flex-shrink-0 text-sis-muted dark:text-[#7688a6] transition-all duration-300 group-hover:translate-x-1 group-hover:text-sis-accent dark:group-hover:text-[var(--accent-blue)]"
            style="font-size: 18px"
            >arrow_forward</span
          >
        </div>
        <p
          class="mt-1 text-xs font-bold uppercase tracking-[0.14em] text-sis-accent dark:text-[var(--accent-blue)]"
        >
          {{ sample.industry }}
        </p>
        <p
          class="mt-3 text-sm text-sis-muted dark:text-[#aebfd8] leading-relaxed"
        >
          {{ sample.blurb }}
        </p>
      </div>
    </a>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useAccessStore } from "@/stores/useAccessStore";
import AccessModal from "@/components/Home/work-samples/AccessModal.vue";
import Skeleton from "@/components/common/Skeleton.vue";

defineProps({
  samples: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  skeletonCount: { type: Number, default: 3 },
});

const store = useAccessStore();
const modalOpen = ref(false);
const pendingSample = ref(null);
const fallbackImg = ref({});

const imageBase = `${import.meta.env.BASE_URL}images/work-samples-images/`;

const SLUG_ALIASES = { "food-beverage-oee": "fmb-oee" };

const LOCAL_DEMO_SLUGS = new Set([
  "water-scada",
  "fmb-oee",
  "oil-gas-telemetry",
  "manufacturing-mes",
  "automotive-line",
  "pharma-batch",
  "infrastructure-bms",
  "energy-grid",
  "data-center-dcim",
]);

function localSlug(sample) {
  return SLUG_ALIASES[sample.slug] || sample.slug;
}

function localImage(sample) {
  return `${imageBase}${localSlug(sample)}.png`;
}

function sampleImage(sample) {
  const key = sample.id ?? sample.slug;
  if (fallbackImg.value[key]) return localImage(sample) || "";
  if (sample.imageUrl) return sample.imageUrl;
  return localImage(sample) || `${imageBase}${sample.image}` || "";
}

function onImageError(event) {
  const key = event.currentTarget?.dataset?.sample;
  if (key) fallbackImg.value[key] = true;
}

function sampleUrl(sample) {
  if (LOCAL_DEMO_SLUGS.has(localSlug(sample))) {
    return `${import.meta.env.BASE_URL}work-samples/${localSlug(sample)}/index.html`;
  }
  const url = (sample.demoUrl || "").trim();
  const local = `${import.meta.env.BASE_URL}work-samples/${sample.slug}/index.html`;
  if (!url) return local;
  try {
    const absolute =
      typeof url === "string" && !/^https?:\/\//i.test(url)
        ? ""
        : new URL(url).host;
    const apiHost = (import.meta.env.VITE_API_BASE_URL || "")
      .replace(/^https?:\/\//, "")
      .split("/")[0];
    const isRelative = !/^https?:\/\//i.test(url);
    const isApiHosted = !isRelative && absolute === apiHost;
    if (isRelative || isApiHosted) return local;
    return url;
  } catch {
    return url;
  }
}

function handleClick(sample) {
  window.open(sampleUrl(sample), "_blank", "noopener");
}

function handleUnlocked() {
  modalOpen.value = false;
  if (pendingSample.value) {
    window.open(sampleUrl(pendingSample.value), "_blank", "noopener");
    pendingSample.value = null;
  }
}
</script>
