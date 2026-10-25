<template>
  <footer
    class="bg-sis-panel dark:bg-[#0b1326] border-t border-sis-border dark:border-white/10"
  >
    <div class="mx-auto w-full max-w-[1600px] px-4 py-12 sm:px-6 lg:px-10 xl:px-12 2xl:px-16">
      <!-- Top row: brand + nav links -->
      <div
        class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8 mb-10"
      >
        <!-- Brand -->
        <div>
          <img
            :src="logoUrl"
            alt="SiS Logo"
            class="mb-3 h-16 w-auto brightness-100 contrast-125 sm:h-20 dark:brightness-100 dark:contrast-100 object-contain"
          />
          <p
            class="mt-2 max-w-md text-sm leading-relaxed text-sis-muted dark:text-white/60"
          >
            {{ companyDescription }}
          </p>
        </div>

        <!-- Nav links -->
        <nav
          class="flex flex-wrap gap-x-8 gap-y-3 text-sm font-medium text-[var(--text-80)] dark:text-white/80"
        >
          <a
            v-for="link in navigationLinks"
            :key="link.id || link.label"
            :href="link.url"
            :target="link.target || '_self'"
            @click="handleNavClick($event, link)"
            class="hover:text-sis-accent transition-colors duration-200 cursor-pointer"
          >
            {{ link.label }}
          </a>
        </nav>
      </div>

      <!-- Divider -->
      <div
        class="border-t border-sis-border dark:border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3"
      >
        <span class="text-xs text-[var(--text-muted-60)] dark:text-white/40">
          {{ copyrightText }}
        </span>

        <!-- Social & Contact Icons -->
        <div class="flex flex-wrap items-center gap-3">
          <a
            v-for="link in contactLinks"
            :key="link.id || link.url"
            :href="link.url"
            :target="link.target || '_blank'"
            rel="noopener noreferrer"
            :aria-label="link.label || 'Social Link'"
            :title="link.label || 'Social Link'"
            class="flex h-11 w-11 items-center justify-center rounded-full border border-sis-border transition-all duration-200 hover:scale-105 dark:border-white/10 dark:text-white/60"
            :class="getIconColorClasses(link)"
          >
            <!-- LinkedIn -->
            <svg
              v-if="matchesIcon(link, 'linkedin')"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"
              />
            </svg>

            <!-- WhatsApp -->
            <svg
              v-else-if="matchesIcon(link, 'whatsapp')"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
              />
            </svg>

            <!-- Twitter / X -->
            <svg
              v-else-if="matchesIcon(link, 'twitter') || matchesIcon(link, 'x')"
              class="h-5 w-5"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"
              />
            </svg>

            <!-- Facebook -->
            <svg
              v-else-if="matchesIcon(link, 'facebook')"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"
              />
            </svg>

            <!-- Instagram -->
            <svg
              v-else-if="matchesIcon(link, 'instagram')"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"
              />
            </svg>

            <!-- YouTube -->
            <svg
              v-else-if="matchesIcon(link, 'youtube')"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"
              />
            </svg>

            <!-- GitHub -->
            <svg
              v-else-if="matchesIcon(link, 'github')"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
              />
            </svg>

            <!-- Telegram -->
            <svg
              v-else-if="matchesIcon(link, 'telegram')"
              class="h-6 w-6"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.75-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"
              />
            </svg>

            <!-- Phone -->
            <svg
              v-else-if="matchesIcon(link, 'phone')"
              class="h-5 w-5"
              fill="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                d="M6.62 10.79a15.46 15.46 0 006.59 6.59l2.2-2.2c.28-.28.67-.37 1.02-.25 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1C10.06 21 3 13.94 3 5c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
              />
            </svg>

            <!-- Email / Mail -->
            <svg
              v-else-if="matchesIcon(link, 'mail') || matchesIcon(link, 'email')"
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>

            <!-- Fallback Globe / Link -->
            <svg
              v-else
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <circle cx="12" cy="12" r="10" />
              <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { scrollToSection } from "@/utils/scrollTo.js";
import { useSiteStore } from "@/stores/useSiteStore.js";

const router = useRouter();
const route = useRoute();
const siteStore = useSiteStore();

const defaultLogoUrl = `${import.meta.env.BASE_URL}images/sis-panel-img.jpeg`;

// Fallbacks matching project default requirements
const defaultNavLinks = [
  { id: 'def-1', label: "Home", url: "#hero", target: "_self" },
  { id: 'def-2', label: "About", url: "#about", target: "_self" },
  { id: 'def-3', label: "Services", url: "#services", target: "_self" },
  { id: 'def-4', label: "Contact", url: "#contact", target: "_self" },
];

const defaultContactLinks = [
  {
    id: 'def-c1',
    type: "social",
    label: "LinkedIn",
    url: "https://www.linkedin.com/company/superior-for-integrated-solutions/",
    icon: "linkedin",
    target: "_blank",
  },
  {
    id: 'def-c2',
    type: "whatsapp",
    label: "WhatsApp",
    url: "https://wa.me/201007568651",
    icon: "whatsapp",
    target: "_blank",
  },
];

onMounted(() => {
  if (!siteStore.footerLoaded) {
    siteStore.loadFooter();
  }
});

const logoUrl = computed(() => {
  return (
    siteStore.footer?.company?.logo ||
    siteStore.footer?.company?.logo_path ||
    defaultLogoUrl
  );
});

const companyDescription = computed(() => {
  return (
    siteStore.footer?.company?.description ||
    "Bridging OT and IT across the MENA region with industrial automation and IIoT excellence."
  );
});

const copyrightText = computed(() => {
  const c = siteStore.footer?.copyright;
  if (!c) {
    return "© 2026 Superior Integrated Solutions. All rights reserved.";
  }
  if (c.formatted) {
    return c.formatted;
  }
  if (c.text) {
    const year = c.dynamic_year ? (c.year || new Date().getFullYear()) : (c.year || "");
    return year ? `© ${year} ${c.text}` : c.text;
  }
  return "© 2026 Superior Integrated Solutions. All rights reserved.";
});

const navigationLinks = computed(() => {
  if (siteStore.footer?.navigation_links && siteStore.footer.navigation_links.length > 0) {
    return siteStore.footer.navigation_links.filter(l => l.is_active !== false);
  }
  return defaultNavLinks;
});

const contactLinks = computed(() => {
  if (siteStore.footer?.contact_links && siteStore.footer.contact_links.length > 0) {
    const active = siteStore.footer.contact_links.filter(l => l.is_active !== false);
    if (active.length > 0) return active;
  }
  return defaultContactLinks;
});

function matchesIcon(link, keyword) {
  const iconStr = String(link.icon || link.type || "").toLowerCase();
  const labelStr = String(link.label || "").toLowerCase();
  const urlStr = String(link.url || "").toLowerCase();
  const target = keyword.toLowerCase();

  return (
    iconStr.includes(target) ||
    labelStr.includes(target) ||
    urlStr.includes(target)
  );
}

function getIconColorClasses(link) {
  if (matchesIcon(link, "linkedin")) {
    return "text-[#0A66C2] hover:border-[#0A66C2] hover:text-[#0A66C2]";
  }
  if (matchesIcon(link, "whatsapp")) {
    return "text-[#25D366] hover:border-[#25D366] hover:text-[#25D366]";
  }
  if (matchesIcon(link, "twitter") || matchesIcon(link, "x")) {
    return "text-slate-800 dark:text-white/80 hover:border-slate-800 hover:text-black dark:hover:border-white dark:hover:text-white";
  }
  if (matchesIcon(link, "facebook")) {
    return "text-[#1877F2] hover:border-[#1877F2] hover:text-[#1877F2]";
  }
  if (matchesIcon(link, "instagram")) {
    return "text-[#E4405F] hover:border-[#E4405F] hover:text-[#E4405F]";
  }
  if (matchesIcon(link, "youtube")) {
    return "text-[#FF0000] hover:border-[#FF0000] hover:text-[#FF0000]";
  }
  if (matchesIcon(link, "github")) {
    return "text-slate-700 dark:text-white/70 hover:border-slate-900 hover:text-black dark:hover:border-white dark:hover:text-white";
  }
  if (matchesIcon(link, "telegram")) {
    return "text-[#229ED9] hover:border-[#229ED9] hover:text-[#229ED9]";
  }
  if (matchesIcon(link, "phone")) {
    return "text-sis-accent hover:border-sis-accent hover:text-sis-accent";
  }
  if (matchesIcon(link, "email") || matchesIcon(link, "mail")) {
    return "text-[#EA4335] hover:border-[#EA4335] hover:text-[#EA4335]";
  }
  return "text-[var(--text-80)] hover:border-sis-accent hover:text-sis-accent dark:hover:border-white/40 dark:hover:text-white";
}

function handleNavClick(e, link) {
  const url = link.url || "";

  // Check if it's an in-page section link
  const inPageMatch = url.match(/^#?([a-zA-Z0-9_\-]+)$/) || url.match(/^\/([a-zA-Z0-9_\-]+)$/);
  const knownSections = ["hero", "about", "services", "contact", "partners", "use-cases", "clients"];

  if (inPageMatch) {
    const rawTarget = inPageMatch[1].toLowerCase();
    const sectionTarget = rawTarget === "home" ? "hero" : rawTarget;
    if (knownSections.includes(sectionTarget)) {
      e.preventDefault();
      scrollToSection(sectionTarget, router, route);
      return;
    }
  }

  // If internal route starting with /
  if (url.startsWith("/") && link.target !== "_blank") {
    e.preventDefault();
    router.push(url);
  }
}
</script>
