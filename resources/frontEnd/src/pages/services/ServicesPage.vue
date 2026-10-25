<template>
  <section
    id="services-page"
    class="relative border-y border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] py-32"
    style="background: var(--bg-section-1)"
  >
    <div class="mx-auto max-w-[1140px] px-7">
      <div class="mb-[46px] max-w-[64ch]" data-reveal>
        <span
          class="inline-flex items-center gap-[11px] font-mono text-[12px] font-semibold uppercase tracking-[0.26em] text-[var(--accent-blue)]"
        >
          <span class="block h-px w-6 bg-gradient-to-r from-[var(--accent-blue)] to-transparent"></span>
          Work samples
        </span>
        <h2
          class="mt-4 text-[clamp(27px,3.5vw,42px)] font-extrabold leading-[1.1] -tracking-[0.025em] text-sis-text dark:text-white text-balance"
        >
          Services & Dashboards
        </h2>
        <p class="mt-[14px] max-w-[56ch] text-[15px] text-sis-muted dark:text-[#aebfd8]">
          Every sample below is a live, animated simulation of the dashboards we
          ship with our deployments. Explore the full set — the first two are open
          to all, the rest unlock with access credentials.
        </p>
      </div>

      <WorkSamplesGrid :samples="samples" :loading="!workSamplesLoaded" :skeleton-count="6" />
    </div>
  </section>
</template>

<script setup>
import { storeToRefs } from "pinia";
import { useSiteStore } from "@/stores/useSiteStore";
import WorkSamplesGrid from "@/components/Home/work-samples/WorkSamplesGrid.vue";

const siteStore = useSiteStore();
const { workSamples, workSamplesLoaded } = storeToRefs(siteStore);

const samples = workSamples;

import { useSeo } from "@/composables/useSeo.js";
import { buildBreadcrumbsSchema } from "@/utils/seo.js";

useSeo({
  title: "Services & Solutions | SiS",
  description:
    "Explore turnkey industrial engineering and automation services delivered by SiS, including DCS migrations, cybersecurity, and systems integration.",
  canonical: "/services",
  breadcrumbs: [
    { name: "Home", path: "/" },
    { name: "Services & Solutions", path: "/services" },
  ],
}, "services");
</script>
