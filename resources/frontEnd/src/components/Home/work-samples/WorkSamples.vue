<template>
  <section
    id="work-samples"
    class="relative border-y border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] py-[92px]"
    style="background: var(--bg-section-1)"
  >
    <div class="mx-auto max-w-[1140px] px-7">
      <div class="mb-[46px] max-w-[64ch]" data-reveal>
        <span
          class="inline-flex items-center gap-[11px] font-mono text-[12px] font-semibold uppercase tracking-[0.26em] text-[var(--accent-blue)]"
        >
          <span class="block h-px w-6 bg-gradient-to-r from-[var(--accent-blue)] to-transparent"></span>
          Live dashboards
        </span>
        <h2
          class="mt-4 text-[clamp(27px,3.5vw,42px)] font-extrabold leading-[1.1] -tracking-[0.025em] text-sis-text dark:text-white text-balance"
        >
          Work Samples
        </h2>
        <p class="mt-[14px] max-w-[56ch] text-[15px] text-sis-muted dark:text-[#aebfd8]">
          Animated simulations of client dashboards we've engineered — preview the
          quality of the control-room views we ship with every deployment.
        </p>
      </div>

      <WorkSamplesGrid :samples="samples" :loading="!workSamplesLoaded" />

      <div class="mt-[46px] text-center" data-reveal>
        <router-link
          :to="{ name: 'services' }"
          class="inline-flex items-center gap-3 min-h-[52px] px-[26px] rounded-xl text-[0.87rem] font-[750] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] bg-sis-panel dark:bg-[#0e1728] text-sis-text dark:text-[#eaf1fb] hover:border-sis-accent dark:hover:border-[var(--accent-blue)] hover:text-sis-accent dark:hover:text-[var(--accent-blue)] hover:-translate-y-0.5 transition-all duration-200"
        >
          Explore our services
          <UiIcon name="arrow" :size="17" />
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from "vue";
import { storeToRefs } from "pinia";
import { useSiteStore } from "@/stores/useSiteStore";
import WorkSamplesGrid from "@/components/Home/work-samples/WorkSamplesGrid.vue";
import UiIcon from "@/components/common/UiIcon.vue";

const siteStore = useSiteStore();
const { workSamples, workSamplesLoaded } = storeToRefs(siteStore);

const samples = computed(() =>
  workSamplesLoaded.value ? workSamples.value.slice(0, 3) : []
);
</script>
