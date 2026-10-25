<template>
  <partnerEcosystem />
</template>

<script setup>
import partnerEcosystem from '@/components/partnerEcosystem/partnerEcosystem.vue';
import { useSeo } from "@/composables/useSeo.js";
import { buildBreadcrumbsSchema } from "@/utils/seo.js";

useSeo({
  title: "Partner Ecosystem | SiS",
  description:
    "Explore the global industrial automation and technology leaders partnering with Superior Integrated Solutions.",
  canonical: "/partner-ecosystem",
  breadcrumbs: [
    { name: "Home", path: "/" },
    { name: "Partner Ecosystem", path: "/partner-ecosystem" },
  ],
}, "partner-ecosystem");
</script>
