<template>
  <ProjectsSection />
</template>

<script setup>
import ProjectsSection from '@/components/projects/ProjectsSection.vue';
import { useSeo } from "@/composables/useSeo.js";
import { buildBreadcrumbsSchema } from "@/utils/seo.js";

useSeo({
  title: "Projects & Case Studies | SiS",
  description:
    "Proven industrial automation, SCADA, and OT projects engineered for mission-critical operations across key industrial sectors.",
  canonical: "/projects",
  breadcrumbs: [
    { name: "Home", path: "/" },
    { name: "Projects", path: "/projects" },
  ],
}, "projects");
</script>
