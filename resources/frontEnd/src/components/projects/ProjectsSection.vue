<template>
  <section
    class="relative overflow-hidden border-y border-[var(--border-30)] px-4 py-16 dark:border-[rgba(126,165,224,0.14)] sm:px-6 sm:py-24 lg:px-8 lg:py-32"
    style="background: var(--bg-section-2)"
  >
    <div class="relative z-10 mx-auto w-full max-w-[1240px]">
      <div
        class="grid justify-items-center text-center mb-[54px] reveal reveal-fade-up"
      >
        <span
          class="inline-block text-xs font-bold tracking-[0.2em] uppercase text-sis-accent dark:text-[var(--accent-blue)]"
          >Industry knowledge</span
        >
        <h2
          class="text-sis-text dark:text-[#eaf1fb] leading-[1.04] mt-[17px]"
          style="font-size: clamp(2.4rem, 4.5vw, 4.75rem); font-weight: 700"
        >
          Engineered for the realities<span class="hidden sm:inline"><br /></span>
          of critical operations.
        </h2>
        <p class="text-sis-muted dark:text-[#aebfd8] max-w-[650px] mt-5">
          Sector experience shapes every control philosophy, network decision
          and commissioning plan.
        </p>
      </div>

      <div
        class="grid grid-cols-1 overflow-hidden border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] lg:min-h-[530px] lg:grid-cols-[minmax(240px,0.32fr)_minmax(0,0.68fr)]"
        style="background: var(--bg-section-1)"
      >
        <template v-if="loaded">
          <div
            class="lg:border-r border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] flex lg:flex-col overflow-x-auto lg:overflow-visible"
            role="tablist"
            aria-label="Industries"
          >
            <button
              v-for="(industry, index) in industries"
              :key="industry.title"
              type="button"
              role="tab"
              :aria-selected="activeIndustry === index"
              class="grid min-h-[72px] shrink-0 grid-cols-[28px_1fr_18px] items-center gap-3 border-b border-[var(--border-30)] px-5 text-left whitespace-nowrap transition-all duration-200 last:border-b-0 dark:border-[rgba(126,165,224,0.14)] lg:h-full lg:min-h-[82px] lg:shrink lg:whitespace-normal"
              :class="
                activeIndustry === index
                  ? 'bg-sis-panel dark:bg-[#0e1728] text-sis-accent dark:text-[var(--accent-blue)] shadow-[inset_3px_0_0] shadow-sis-accent dark:shadow-[var(--accent-blue)]'
                  : 'text-sis-muted dark:text-[#aebfd8] hover:bg-sis-panel dark:hover:bg-[#0e1728] hover:text-sis-accent dark:hover:text-[var(--accent-blue)]'
              "
              @click="activeIndustry = index"
            >
              <UiIcon :name="industry.icon" :size="19" class="flex-shrink-0" />
              <span class="text-[0.73rem] font-[730] min-w-0">{{
                industry.title || industry.name
              }}</span>
              <UiIcon
                name="arrow"
                :size="16"
                class="flex-shrink-0 transition-all duration-200"
                :class="
                  activeIndustry === index
                    ? 'opacity-100 translate-x-0'
                    : 'opacity-0 -translate-x-1'
                "
              />
            </button>
          </div>
        </template>
        <template v-else>
          <div
            class="lg:border-r border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] flex lg:flex-col overflow-x-auto lg:overflow-visible"
          >
            <div
              v-for="i in 5"
              :key="i"
              class="flex items-center gap-3 px-5 min-h-[82px] border-b border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] last:border-b-0"
            >
              <Skeleton class="w-[19px] h-[19px] rounded flex-shrink-0" />
              <Skeleton class="h-3 w-24 rounded-full" />
            </div>
          </div>
        </template>

        <template v-if="loaded">
          <div
            class="grid grid-cols-1 lg:grid-cols-[minmax(0,0.55fr)_minmax(300px,0.45fr)] min-w-0"
            role="tabpanel"
          >
            <div class="p-6 sm:p-8 lg:p-[48px_38px]">
              <span
                class="inline-block text-xs font-bold tracking-[0.2em] uppercase text-sis-accent dark:text-[var(--accent-blue)]"
                >{{ activeIndustryData.kicker || 'Expertise' }}</span
              >
              <h3
                class="text-sis-text dark:text-[#eaf1fb] mt-[15px] leading-[1.04]"
                style="font-size: clamp(1.8rem, 3vw, 3rem); font-weight: 700"
              >
                {{ activeIndustryData.title || activeIndustryData.name }}
              </h3>
              <p
                class="text-sis-muted dark:text-[#aebfd8] max-w-[540px] mt-5 leading-[1.82]"
              >
                {{ activeIndustryData.text || activeIndustryData.description }}
              </p>
              <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-[28px] mt-[37px] pt-[27px] border-t border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)]"
              >
                <div class="grid content-start gap-2">
                  <small
                    class="block text-[0.58rem] font-[800] tracking-[0.1em] uppercase text-sis-accent dark:text-[var(--accent-blue)] mb-[5px]"
                    >Relevant programs</small
                  >
                  <span
                    v-for="(client, cIdx) in (activeIndustryData.relevant_programs?.length ? activeIndustryData.relevant_programs : (activeIndustryData.clients || []))"
                    :key="cIdx"
                    class="relative pl-[11px] text-[0.68rem] text-sis-muted dark:text-[#aebfd8] before:absolute before:top-[0.67em] before:left-0 before:w-[4px] before:h-[4px] before:rounded-full before:bg-sis-accent dark:before:bg-[var(--accent-blue)]"
                    >{{ typeof client === 'object' ? (client.title || client.name || client.value || '') : client }}</span
                  >
                  <span
                    v-if="!activeIndustryData.relevant_programs?.length && !activeIndustryData.clients?.length"
                    class="text-[0.68rem] italic text-sis-muted/70 dark:text-[#aebfd8]/70"
                  >
                    Customized per operational requirements.
                  </span>
                </div>
                <div class="grid content-start gap-2">
                  <small
                    class="block text-[0.58rem] font-[800] tracking-[0.1em] uppercase text-sis-accent dark:text-[var(--accent-blue)] mb-[5px]"
                    >Typical scope</small
                  >
                  <span
                    v-for="(item, sIdx) in (activeIndustryData.typical_scope?.length ? activeIndustryData.typical_scope : (activeIndustryData.scope || []))"
                    :key="sIdx"
                    class="relative pl-[11px] text-[0.68rem] text-sis-muted dark:text-[#aebfd8] before:absolute before:top-[0.67em] before:left-0 before:w-[4px] before:h-[4px] before:rounded-full before:bg-sis-accent dark:before:bg-[var(--accent-blue)]"
                    >{{ typeof item === 'object' ? (item.title || item.name || item.value || '') : item }}</span
                  >
                  <span
                    v-if="!activeIndustryData.typical_scope?.length && !activeIndustryData.scope?.length"
                    class="text-[0.68rem] italic text-sis-muted/70 dark:text-[#aebfd8]/70"
                  >
                    Tailored engineering and automation scope.
                  </span>
                </div>
              </div>
            </div>
            <div
              class="relative min-h-[340px] lg:min-h-[530px] overflow-hidden"
            >
              <div
                class="absolute inset-0 z-10"
                style="
                  background: linear-gradient(
                    180deg,
                    transparent 50%,
                    rgba(3, 24, 37, 0.64)
                  );
                "
              ></div>
              <img
                :src="currentImage"
                :alt="`${activeIndustryData.title} facility`"
                class="w-full h-full absolute inset-0 object-cover transition-transform duration-700 hover:scale-105"
                loading="lazy"
              />
              <span
                class="absolute z-20 bottom-[22px] left-[24px] flex items-center gap-[8px] w-auto whitespace-nowrap text-white text-[0.7rem] font-[750]"
              >
                <UiIcon :name="activeIndustryData.icon" :size="16" />
                {{ activeIndustryData.title }}
              </span>
            </div>
          </div>
        </template>
        <template v-else>
          <div
            class="grid grid-cols-1 lg:grid-cols-[minmax(0,0.55fr)_minmax(300px,0.45fr)] min-w-0"
          >
            <div class="space-y-5 p-6 sm:p-8 lg:p-[48px_38px]">
              <Skeleton class="h-3 w-24 rounded-full" />
              <Skeleton class="h-10 w-64 rounded-lg" />
              <Skeleton class="h-5 w-full rounded-full" />
              <Skeleton class="h-5 w-11/12 rounded-full" />
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-[28px] pt-[27px]">
                <div class="space-y-2">
                  <Skeleton class="h-3 w-28 rounded-full mb-[5px]" />
                  <Skeleton class="h-3 w-20 rounded-full" />
                  <Skeleton class="h-3 w-24 rounded-full" />
                  <Skeleton class="h-3 w-16 rounded-full" />
                </div>
                <div class="space-y-2">
                  <Skeleton class="h-3 w-24 rounded-full mb-[5px]" />
                  <Skeleton class="h-3 w-28 rounded-full" />
                  <Skeleton class="h-3 w-20 rounded-full" />
                  <Skeleton class="h-3 w-24 rounded-full" />
                </div>
              </div>
            </div>
            <div class="relative min-h-[340px] lg:min-h-[530px]">
              <Skeleton class="absolute inset-0 h-full w-full rounded-none" />
            </div>
          </div>
        </template>
      </div>

      
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from "vue";
import { storeToRefs } from "pinia";
import { useSiteStore } from "@/stores/useSiteStore";
import UiIcon from "@/components/common/UiIcon.vue";
import Skeleton from "@/components/common/Skeleton.vue";

const siteStore = useSiteStore();
const { industries, projects, loaded, projectsLoaded } = storeToRefs(siteStore);

const activeIndustry = ref(0);
const activeProjectSector = ref("All");
const showAllProjects = ref(false);

const activeIndustryData = computed(() => {
  if (!industries.value || !industries.value.length) return {};
  return industries.value[activeIndustry.value];
});

const projectSectors = computed(() => {
  if (!projects.value) return ["All"];
  return ["All", ...new Set(projects.value.map((p) => p.sector))];
});

const filteredProjects = computed(() => {
  if (!projects.value) return [];
  const selected =
    activeProjectSector.value === "All"
      ? projects.value
      : projects.value.filter((p) => p.sector === activeProjectSector.value);

  if (activeProjectSector.value === "All" && !showAllProjects.value) {
    return selected.slice(0, 6);
  }
  return selected;
});

function selectProjectSector(sector) {
  activeProjectSector.value = sector;
  if (sector !== "All") showAllProjects.value = true;
}

import { resolveImageUrl } from "@/utils/imageUrl";

const industryImages = {
  "water-waste-water": `${import.meta.env.BASE_URL}images/industry-water.webp`,
  "food-beverage-fmcg": `${import.meta.env.BASE_URL}images/industry-food.webp`,
  "chemical-industries": `${import.meta.env.BASE_URL}images/industry-chemical.webp`,
  "oil-gas": `${import.meta.env.BASE_URL}images/industry-oil-gas.webp`,
  "heavy-industries": `${import.meta.env.BASE_URL}images/industry-heavy.webp`,
  "Water & Waste Water": `${import.meta.env.BASE_URL}images/industry-water.webp`,
  "Food, Beverage & FMCG": `${import.meta.env.BASE_URL}images/industry-food.webp`,
  "Chemical Industries": `${import.meta.env.BASE_URL}images/industry-chemical.webp`,
  "Oil & Gas": `${import.meta.env.BASE_URL}images/industry-oil-gas.webp`,
  "Heavy Industries": `${import.meta.env.BASE_URL}images/industry-heavy.webp`,
};

const logoUrl = (filename) =>
  `${import.meta.env.BASE_URL}images/ourClints/${filename}`;

const isHttpUrl = (value) =>
  typeof value === "string" && /^https?:\/\//i.test(value);

const logoSource = (logo) =>
  isHttpUrl(logo) || logo.startsWith("/") ? logo : logoUrl(logo);

const currentImage = computed(() => {
  const data = activeIndustryData.value;
  if (!data) return `${import.meta.env.BASE_URL}images/industry-heavy.webp`;

  const rawImg = data.image_url || data.image;
  if (rawImg && typeof rawImg === "string" && !rawImg.includes("undefined")) {
    return resolveImageUrl(rawImg);
  }

  if (data.slug && industryImages[data.slug]) {
    return industryImages[data.slug];
  }
  if (data.slug) {
    const slugKey = Object.keys(industryImages).find((k) => data.slug.startsWith(k) || k.startsWith(data.slug));
    if (slugKey) return industryImages[slugKey];
  }

  if (data.title && industryImages[data.title]) {
    return industryImages[data.title];
  }
  if (data.title) {
    const lower = data.title.toLowerCase();
    if (lower.includes("oil") || lower.includes("gas")) return industryImages["oil-gas"];
    if (lower.includes("water")) return industryImages["water-waste-water"];
    if (lower.includes("chem")) return industryImages["chemical-industries"];
    if (lower.includes("food") || lower.includes("beverage") || lower.includes("fmcg")) return industryImages["food-beverage-fmcg"];
    if (lower.includes("heavy")) return industryImages["heavy-industries"];
  }

  return `${import.meta.env.BASE_URL}images/industry-heavy.webp`;
});
</script>
