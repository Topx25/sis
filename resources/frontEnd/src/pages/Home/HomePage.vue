<template>
  <HeroSection />
  <AdditionalSection />
  <ServicesSection />
  <ClientsSection />
  <PartnersSection />
</template>

<script setup>
import HeroSection from "@/components/Home/HeroSection.vue";
import AdditionalSection from "@/components/Home/additionalSection.vue";
import ServicesSection from "@/components/Home/ServicesSection.vue";
import ClientsSection from "@/components/Home/ClientsSection.vue";
import PartnersSection from "@/components/Home/PartnersSection.vue";
import { useSeo } from "@/composables/useSeo.js";
import { buildOrganizationSchema } from "@/utils/seo.js";

useSeo({
  title: "SiS - Superior Integrated Solutions",
  description:
    "Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and AI.",
  canonical: "/",
  structuredData: buildOrganizationSchema(),
}, "home");
</script>
