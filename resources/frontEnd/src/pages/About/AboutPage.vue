<template>
  <main
    class="bg-[#070d1a] text-[#eaf1fb] overflow-x-hidden relative"
  >
    <!-- Global Decorative Background Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
      <div
        class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[120px]"
      ></div>
      <div
        class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-secondary/5 rounded-full blur-[120px]"
      ></div>
    </div>

    <!-- Section Components -->
    <AboutHero />
    <AboutMissionVision />
    <AboutDomains />
    <TimelineSection />
    <AboutITOT />
    <AboutCoreValues />
  </main>
</template>

<script setup>
import AboutHero from "@/components/about/AboutHero.vue";
import AboutMissionVision from "@/components/about/AboutMissionVision.vue";
import AboutITOT from "@/components/about/AboutITOT.vue";
import AboutDomains from "@/components/about/AboutDomains.vue";
import AboutCoreValues from "@/components/about/AboutCoreValues.vue";
import TimelineSection from "@/components/timeline/TimelineSection.vue";
import { useSeo } from "@/composables/useSeo.js";
import { buildBreadcrumbsSchema } from "@/utils/seo.js";

useSeo({
  title: "About Us | SiS",
  description:
    "Discover SiS - our mission, engineering pillars, industrial automation heritage, and leadership bridging operational technology with enterprise digital solutions.",
  canonical: "/about",
  breadcrumbs: [
    { name: "Home", path: "/" },
    { name: "About Us", path: "/about" },
  ],
}, "about");
</script>
