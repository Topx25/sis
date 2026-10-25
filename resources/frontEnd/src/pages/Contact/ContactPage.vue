<template>
  <ContactSection />
</template>

<script setup>
import ContactSection from '@/components/Contact/ContactSection.vue';
import { useSeo } from "@/composables/useSeo.js";
import { resolveOrigin } from "@/utils/seo.js";

useSeo({
  title: "Contact Us | SiS",
  description:
    "Contact SiS engineering and sales for consultations, industrial automation inquiries, and mission-critical technical support.",
  canonical: "/contact",
  structuredData: {
    "@context": "https://schema.org",
    "@type": "ContactPage",
    name: "Contact SiS Engineering & Sales",
    url: `${resolveOrigin() || "https://sis.iptvpro.org"}/contact`,
    mainEntity: {
      "@id": `${resolveOrigin() || "https://sis.iptvpro.org"}/#organization`,
    },
  },
}, "contact");
</script>
