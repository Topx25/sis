<script setup>
import { computed, onMounted, ref } from "vue";
import { adminApi } from "../../api/adminApi.js";
import SeoFormSection from "../../components/seo/SeoFormSection.vue";

const mainTab = ref("global"); // 'global' | 'pages'
const loading = ref(true);
const saving = ref(false);
const currentLang = ref("en");
const activeCategory = ref("identity"); // 'identity' | 'description' | 'social' | 'webmasters' | 'organization' | 'safety'
const fileInput = ref(null);
const localImagePreview = ref("");
const previewDevice = ref("desktop");

// Global SEO Categories Configuration (Merged into 3 Logical Workspaces)
const globalCategories = [
  {
    id: "identity",
    title: "1. Website Identity & Search Content",
    subtitle: "Official domain, brand name, description, & keywords",
    icon: "domain",
    badge: "Core SEO",
    activeIconClass: "bg-blue-600 text-white shadow-sm shadow-blue-500/30",
  },
  {
    id: "social_company",
    title: "2. Social Media & Company Profile",
    subtitle: "Default share image (OG banner), contact info, & social channels",
    icon: "share",
    badge: "Social & Brand",
    activeIconClass: "bg-indigo-600 text-white shadow-sm shadow-indigo-500/30",
  },
  {
    id: "verification_shield",
    title: "3. Search Verification & Site Shield",
    subtitle: "Google & Bing ownership tags, search visibility, & offline mode",
    icon: "verified_user",
    badge: "Verification & Shield",
    activeIconClass: "bg-emerald-600 text-white shadow-sm shadow-emerald-500/30",
  },
];

const activeCategoryMeta = computed(() => {
  return globalCategories.find((c) => c.id === activeCategory.value) || globalCategories[0];
});

// Core Pages SEO State
const pagesList = ref([]);
const loadingPages = ref(false);
const selectedPageKey = ref("home");
const savingPage = ref(false);
const pageSearch = ref("");

// Toast Notification State
const showToast = ref(false);
const toast = ref({
  title: "",
  message: "",
  variant: "success",
});

function notify(title, message, variant = "success") {
  showToast.value = false;
  setTimeout(() => {
    toast.value = { title, message, variant };
    showToast.value = true;
  }, 10);
}

// Form State
const form = ref({
  canonical_base_url: "https://sis.iptvpro.org",
  site_name: {
    en: "SiS - Superior Integrated Solutions",
    ar: "حلول متكاملة متقدمة (SiS)",
  },
  title_suffix: {
    en: " | SiS - Superior Integrated Solutions",
    ar: " | حلول متكاملة متقدمة",
  },
  default_meta_description: {
    en: "Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and AI.",
    ar: "تقدم سوبيريور للحلول المتكاملة (SiS) حلول أتمتة صناعية وأنظمة تحكم رقمية متقدمة في منطقة الشرق الأوسط.",
  },
  default_meta_keywords: {
    en: "industrial automation, OT, IT, SCADA, PLC, DCS, IIoT, digital transformation, MENA, Egypt",
    ar: "أتمتة صناعية, تحول رقمي, أنظمة تحكم, سكادا, إنترنت الأشياء الصناعي, مصر, الشرق الأوسط",
  },
  default_noindex: false,
  google_site_verification: "",
  bing_site_verification: "",
  default_og_image: "",
  default_og_image_file: null,
  remove_default_og_image: false,
  organization_legal_name: "Superior Integrated Solutions S.A.E.",
  organization_phone: "+20 1007 568 651",
  organization_email: "sales@sis-holding.com",
  social_links: [
    "https://www.linkedin.com/company/superior-for-integrated-solutions",
  ],
});

// New Social Link Input
const newSocialLink = ref("");

function addSocialLink() {
  const link = newSocialLink.value.trim();
  if (link && !form.value.social_links.includes(link)) {
    form.value.social_links.push(link);
    newSocialLink.value = "";
  }
}

function removeSocialLink(index) {
  form.value.social_links.splice(index, 1);
}

// Load Settings from API
async function loadSettings() {
  loading.value = true;
  try {
    const data = await adminApi.getSeoSettings();
    if (data) {
      form.value.canonical_base_url = data.canonical_base_url || "https://sis.iptvpro.org";
      const siteNameObj = typeof data.site_name === "object" && data.site_name ? data.site_name : null;
      const titleSuffixObj = typeof data.title_suffix === "object" && data.title_suffix ? data.title_suffix : null;
      const descObj = typeof data.default_meta_description === "object" && data.default_meta_description ? data.default_meta_description : null;

      form.value.site_name = {
        en: siteNameObj?.en || data.site_name_translations?.en || (typeof data.site_name === "string" ? data.site_name : "") || "SiS - Superior Integrated Solutions",
        ar: siteNameObj?.ar || data.site_name_translations?.ar || "",
      };
      form.value.title_suffix = {
        en: titleSuffixObj?.en || data.title_suffix_translations?.en || (typeof data.title_suffix === "string" ? data.title_suffix : "") || " | SiS",
        ar: titleSuffixObj?.ar || data.title_suffix_translations?.ar || "",
      };
      form.value.default_meta_description = {
        en: descObj?.en || data.default_meta_description_translations?.en || (typeof data.default_meta_description === "string" ? data.default_meta_description : "") || "",
        ar: descObj?.ar || data.default_meta_description_translations?.ar || "",
      };
      const keywordsObj = typeof data.default_meta_keywords === "object" && data.default_meta_keywords ? data.default_meta_keywords : null;
      form.value.default_meta_keywords = {
        en: keywordsObj?.en || data.default_meta_keywords_translations?.en || (typeof data.default_meta_keywords === "string" ? data.default_meta_keywords : "") || "",
        ar: keywordsObj?.ar || data.default_meta_keywords_translations?.ar || "",
      };
      form.value.default_noindex = Boolean(data.default_noindex);
      form.value.google_site_verification = data.google_site_verification || "";
      form.value.bing_site_verification = data.bing_site_verification || "";
      form.value.default_og_image = data.default_og_image || "";
      form.value.default_og_image_file = null;
      form.value.remove_default_og_image = false;
      form.value.organization_legal_name = data.organization_legal_name || "Superior Integrated Solutions S.A.E.";
      form.value.organization_phone = data.organization_phone || "+20 1007 568 651";
      form.value.organization_email = data.organization_email || "sales@sis-holding.com";
      form.value.social_links = Array.isArray(data.social_links) ? [...data.social_links] : [];
    }
  } catch (err) {
    console.error("Failed to load SEO settings:", err);
    notify("Warning", "Could not fetch current SEO settings. Using system defaults.", "error");
  } finally {
    loading.value = false;
  }
}

async function loadPagesSeo() {
  loadingPages.value = true;
  try {
    const data = await adminApi.getPagesSeo();
    if (Array.isArray(data)) {
      pagesList.value = data;
      if (!selectedPageKey.value && data.length > 0) {
        selectedPageKey.value = data[0].key;
      }
    }
  } catch (err) {
    console.error("Failed to load pages SEO:", err);
    notify("Error", "Could not fetch core pages SEO registry.", "error");
  } finally {
    loadingPages.value = false;
  }
}

const activePage = computed(() => {
  return pagesList.value.find((p) => p.key === selectedPageKey.value) || pagesList.value[0] || null;
});

const filteredPages = computed(() => {
  const q = pageSearch.value.trim().toLowerCase();
  if (!q) return pagesList.value;
  return pagesList.value.filter(
    (p) =>
      p.name.toLowerCase().includes(q) ||
      p.path.toLowerCase().includes(q) ||
      p.key.toLowerCase().includes(q)
  );
});

async function saveCurrentPageSeo() {
  if (!activePage.value) return;
  savingPage.value = true;
  try {
    const updated = await adminApi.updatePageSeo(activePage.value.key, activePage.value.seo);
    if (updated) {
      notify("Success", `SEO metadata for "${activePage.value.name}" saved successfully.`, "success");
      if (updated.seo) {
        activePage.value.seo = updated.seo;
      }
    }
  } catch (err) {
    console.error("Failed to save page SEO:", err);
    notify("Save Error", err.message || "Failed to update page SEO.", "error");
  } finally {
    savingPage.value = false;
  }
}

onMounted(() => {
  loadSettings();
  loadPagesSeo();
});

// Image handlers
function handleImagePick(e) {
  const file = e.target.files?.[0];
  if (!file) return;

  form.value.default_og_image_file = file;
  form.value.remove_default_og_image = false;
  localImagePreview.value = URL.createObjectURL(file);
}

function clearOgImage() {
  form.value.default_og_image = "";
  form.value.default_og_image_file = null;
  form.value.remove_default_og_image = true;
  localImagePreview.value = "";
  if (fileInput.value) fileInput.value.value = "";
}

// Character Guidance Computeds
const descLength = computed(() => {
  return (form.value.default_meta_description[currentLang.value] || "").length;
});

const descGuidanceClass = computed(() => {
  const len = descLength.value;
  if (len === 0) return "text-slate-400";
  if (len >= 120 && len <= 160) return "text-emerald-600 font-semibold";
  if (len > 160) return "text-amber-600 font-semibold";
  return "text-slate-500";
});

// SERP & Social Previews
const previewTitle = computed(() => {
  const name = form.value.site_name[currentLang.value] || "SiS - Superior Integrated Solutions";
  const suffix = form.value.title_suffix[currentLang.value] || "";
  return `${name}${name.includes("SiS") ? "" : suffix}`;
});

const previewDesc = computed(() => {
  return form.value.default_meta_description[currentLang.value] || "Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and AI.";
});

const previewImage = computed(() => {
  if (localImagePreview.value) return localImagePreview.value;
  if (form.value.default_og_image) {
    return form.value.default_og_image.startsWith("http")
      ? form.value.default_og_image
      : `${form.value.canonical_base_url.replace(/\/+$/, "")}${form.value.default_og_image.startsWith("/") ? "" : "/"}${form.value.default_og_image}`;
  }
  return `${form.value.canonical_base_url.replace(/\/+$/, "")}/sis-logo.png`;
});

// Save Handler
async function saveSettings() {
  saving.value = true;
  try {
    const hasNewImage = Boolean(form.value.default_og_image_file);

    let payload;
    if (hasNewImage || form.value.remove_default_og_image) {
      const fd = new FormData();
      fd.append("canonical_base_url", form.value.canonical_base_url);
      fd.append("site_name", JSON.stringify(form.value.site_name));
      fd.append("title_suffix", JSON.stringify(form.value.title_suffix));
      fd.append("default_meta_description", JSON.stringify(form.value.default_meta_description));
      fd.append("default_meta_keywords", JSON.stringify(form.value.default_meta_keywords));
      fd.append("default_noindex", form.value.default_noindex ? "1" : "0");
      fd.append("google_site_verification", form.value.google_site_verification || "");
      fd.append("bing_site_verification", form.value.bing_site_verification || "");
      fd.append("organization_legal_name", form.value.organization_legal_name || "");
      fd.append("organization_phone", form.value.organization_phone || "");
      fd.append("organization_email", form.value.organization_email || "");
      fd.append("social_links", JSON.stringify(form.value.social_links));

      if (hasNewImage) {
        fd.append("default_og_image", form.value.default_og_image_file);
      }
      if (form.value.remove_default_og_image) {
        fd.append("remove_default_og_image", "1");
      }
      payload = fd;
    } else {
      payload = {
        canonical_base_url: form.value.canonical_base_url,
        site_name: form.value.site_name,
        title_suffix: form.value.title_suffix,
        default_meta_description: form.value.default_meta_description,
        default_meta_keywords: form.value.default_meta_keywords,
        default_noindex: form.value.default_noindex,
        google_site_verification: form.value.google_site_verification,
        bing_site_verification: form.value.bing_site_verification,
        default_og_image: form.value.default_og_image,
        organization_legal_name: form.value.organization_legal_name,
        organization_phone: form.value.organization_phone,
        organization_email: form.value.organization_email,
        social_links: form.value.social_links,
      };
    }

    const updated = await adminApi.updateSeoSettings(payload);
    if (updated) {
      notify("Success", "Global SEO settings updated and cache invalidated successfully.", "success");
      await loadSettings();
    }
  } catch (err) {
    console.error("Failed to save SEO settings:", err);
    notify("Save Error", err.message || "Failed to update SEO settings.", "error");
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <div class="space-y-8 pb-20">
    <!-- Executive Ambient Hero Header -->
    <section class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-[#0B1E3D] via-[#102B59] to-[#08152B] p-7 text-white shadow-[0_20px_60px_-15px_rgba(11,30,61,0.3)] sm:p-10">
      <!-- Ambient Lighting Spotlights -->
      <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -right-20 -top-24 h-80 w-80 rounded-full bg-blue-500/20 blur-[100px]"></div>
        <div class="absolute -bottom-32 left-1/4 h-72 w-72 rounded-full bg-cyan-400/15 blur-[90px]"></div>
        <div class="absolute right-12 top-10 h-44 w-44 rounded-full border border-white/[0.04]"></div>
      </div>

      <div class="relative flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <!-- Platform Badge -->
          <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-blue-400/25 bg-blue-500/10 px-3.5 py-1.5 backdrop-blur-md">
            <span class="h-2 w-2 rounded-full bg-cyan-300 shadow-[0_0_8px_rgba(103,232,249,0.9)] animate-pulse"></span>
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-blue-200">
              SiS · Website Search & Social Media Control
            </p>
          </div>

          <!-- Headline -->
          <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white">
            Website Search Engine &
            <span class="bg-gradient-to-r from-blue-300 via-sky-200 to-indigo-200 bg-clip-text text-transparent">
              Social Sharing Setup
            </span>
          </h2>

          <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300/85 sm:text-base">
            Control how your website appears when people find you on Google or share your links on WhatsApp, LinkedIn, and Facebook.
          </p>

          <!-- Live Telemetry Badges -->
          <div class="mt-6 flex flex-wrap items-center gap-3 text-xs">
            <!-- Canonical Badge -->
            <div class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.06] px-3.5 py-2 backdrop-blur-md">
              <span class="material-symbols-outlined text-[17px] text-cyan-300">verified</span>
              <span>Website URL: <strong class="text-white">{{ form.canonical_base_url }}</strong></span>
            </div>

            <!-- Sitemap Link -->
            <a
              :href="`${form.canonical_base_url}/sitemap.xml`"
              target="_blank"
              class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/[0.06] px-3.5 py-2 text-slate-200 hover:bg-white/15 hover:text-white transition-all backdrop-blur-md"
              title="Inspect live production XML sitemap"
            >
              <span class="material-symbols-outlined text-[17px] text-emerald-400">lan</span>
              <span>Sitemap: <strong>/sitemap.xml</strong></span>
              <span class="material-symbols-outlined text-[14px]">open_in_new</span>
            </a>

            <!-- Indexing State Badge -->
            <div
              class="flex items-center gap-2 rounded-xl border px-3.5 py-2 backdrop-blur-md font-semibold"
              :class="form.default_noindex ? 'border-amber-400/30 bg-amber-500/15 text-amber-200' : 'border-emerald-400/30 bg-emerald-500/15 text-emerald-200'"
            >
              <span class="h-2 w-2 rounded-full" :class="form.default_noindex ? 'bg-amber-400' : 'bg-emerald-400'"></span>
              <span>{{ form.default_noindex ? 'Website Hidden (noindex)' : 'Publicly Indexed on Google' }}</span>
            </div>
          </div>
        </div>

        <!-- Top Action Button -->
        <div class="flex items-center gap-3">
          <button
            v-if="mainTab === 'global'"
            type="button"
            @click="saveSettings"
            :disabled="saving || loading"
            class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-3.5 text-sm font-bold text-white shadow-[0_10px_25px_-5px_rgba(59,130,246,0.5)] hover:from-blue-600 hover:to-indigo-700 disabled:opacity-50 transition-all cursor-pointer ring-1 ring-white/20 active:scale-95"
          >
            <span v-if="saving" class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            <span v-else class="material-symbols-outlined text-[20px]">save</span>
            {{ saving ? "Saving Settings..." : "Save Global SEO" }}
          </button>

          <button
            v-else-if="activePage"
            type="button"
            @click="saveCurrentPageSeo"
            :disabled="savingPage || loadingPages"
            class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-[0_10px_25px_-5px_rgba(16,185,129,0.5)] hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 transition-all cursor-pointer ring-1 ring-white/20 active:scale-95"
          >
            <span v-if="savingPage" class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span>
            <span v-else class="material-symbols-outlined text-[20px]">save</span>
            {{ savingPage ? "Saving..." : `Save ${activePage.name} SEO` }}
          </button>
        </div>
      </div>
    </section>

    <!-- Top Workspace Selector: Global Brand SEO vs Core Pages SEO -->
    <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
      <button
        type="button"
        @click="mainTab = 'global'"
        class="flex flex-1 items-center justify-center gap-2.5 rounded-xl py-3 px-4 text-sm font-bold transition-all cursor-pointer"
        :class="mainTab === 'global' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
      >
        <span class="material-symbols-outlined text-[20px]">globe</span>
        Website-Wide SEO Settings
      </button>

      <button
        type="button"
        @click="mainTab = 'pages'"
        class="flex flex-1 items-center justify-center gap-2.5 rounded-xl py-3 px-4 text-sm font-bold transition-all cursor-pointer"
        :class="mainTab === 'pages' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
      >
        <span class="material-symbols-outlined text-[20px]">auto_stories</span>
        Page-by-Page SEO (7 Core Pages)
        <span
          class="rounded-full px-2 py-0.5 text-[11px] font-semibold"
          :class="mainTab === 'pages' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700'"
        >
          {{ pagesList.length || 7 }} Pages
        </span>
      </button>
    </div>

    <!-- Tab 2: Core Pages SEO Workspace -->
    <div v-if="mainTab === 'pages'" class="space-y-6">
      <!-- Loading Pages State -->
      <div v-if="loadingPages" class="rounded-3xl border border-slate-200 bg-white p-16 text-center shadow-sm">
        <span class="material-symbols-outlined animate-spin text-5xl text-blue-600 mb-3">progress_activity</span>
        <p class="text-base font-bold text-slate-800">Loading Core Pages Registry...</p>
        <p class="text-xs text-slate-400 mt-1">Connecting to page-level SEO overrides</p>
      </div>

      <!-- Core Pages Layout: Sidebar List + Editor -->
      <div v-else class="flex flex-col lg:flex-row gap-6 items-start">
        <!-- Left: Static Pages Selector Sidebar -->
        <div class="w-full lg:w-80 shrink-0 space-y-4">
          <!-- Filter Card -->
          <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-blue-600">view_list</span>
                Core Website Pages
              </span>
              <span class="text-[11px] font-semibold text-slate-400">
                {{ filteredPages.length }} of {{ pagesList.length }}
              </span>
            </div>

            <!-- Search Filter -->
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-2.5 text-[17px] text-slate-400">search</span>
              <input
                v-model="pageSearch"
                type="text"
                placeholder="Search page name or address..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50 pl-9 pr-3 py-2 text-xs focus:bg-white focus:border-blue-500 focus:outline-none transition-all"
              />
            </div>
          </div>

          <!-- Page Cards List -->
          <div class="space-y-2">
            <div
              v-for="page in filteredPages"
              :key="page.key"
              @click="selectedPageKey = page.key"
              class="group relative rounded-2xl border p-4 transition-all cursor-pointer"
              :class="selectedPageKey === page.key ? 'border-blue-500 bg-blue-50/60 shadow-md ring-1 ring-blue-500' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/70 shadow-sm'"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2">
                    <h4 class="text-sm font-bold text-slate-900 truncate group-hover:text-blue-600 transition-colors">
                      {{ page.name }}
                    </h4>
                    <span
                      v-if="page.seo?.meta_title?.en || page.seo?.meta_description?.en"
                      class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                      title="Custom SEO active"
                    ></span>
                  </div>
                  <div class="mt-1 flex items-center gap-1.5 text-[11px] font-mono text-slate-500">
                    <span class="material-symbols-outlined text-[13px] text-slate-400">link</span>
                    <span class="truncate">{{ page.path }}</span>
                  </div>
                </div>

                <!-- Status Badge -->
                <span
                  class="rounded-md px-1.5 py-0.5 text-[10px] font-mono font-medium"
                  :class="selectedPageKey === page.key ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'"
                >
                  {{ page.seo?.schema_type || 'WebPage' }}
                </span>
              </div>

              <!-- Status subtext -->
              <div class="mt-2.5 flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                <span class="flex items-center gap-1 text-[10px]">
                  <span
                    class="h-1.5 w-1.5 rounded-full"
                    :class="page.seo?.is_noindex ? 'bg-amber-400' : 'bg-emerald-400'"
                  ></span>
                  {{ page.seo?.is_noindex ? 'Hidden from Search' : 'Indexed on Google' }}
                </span>
                <span class="text-[10px] text-blue-600 font-semibold group-hover:underline flex items-center gap-0.5">
                  Edit SEO
                  <span class="material-symbols-outlined text-[13px]">chevron_right</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Page SEO Editor Body -->
        <div class="flex-1 min-w-0 space-y-4">
          <div v-if="activePage" class="space-y-4">
            <!-- Active Page Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
              <div>
                <div class="flex items-center gap-2">
                  <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <span class="material-symbols-outlined text-[18px]">edit_document</span>
                  </span>
                  <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                      {{ activePage.name }} SEO Settings
                      <span class="rounded-lg bg-blue-50 px-2 py-0.5 text-xs font-mono font-semibold text-blue-700">
                        {{ activePage.path }}
                      </span>
                    </h3>
                    <p class="text-xs text-slate-400">
                      Configure title, description, keywords, and social preview for the {{ activePage.name }} page.
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2.5">
                <!-- Open Live Route Link -->
                <a
                  :href="`${form.canonical_base_url}${activePage.path === '/' ? '' : activePage.path}`"
                  target="_blank"
                  class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-all cursor-pointer"
                  title="View live public route in new tab"
                >
                  <span class="material-symbols-outlined text-[15px]">visibility</span>
                  View Live Page
                  <span class="material-symbols-outlined text-[12px]">open_in_new</span>
                </a>

                <!-- Save Page SEO Button -->
                <button
                  type="button"
                  @click="saveCurrentPageSeo"
                  :disabled="savingPage"
                  class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 transition-all cursor-pointer"
                >
                  <span v-if="savingPage" class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
                  <span v-else class="material-symbols-outlined text-[16px]">save</span>
                  {{ savingPage ? "Saving..." : "Save Page SEO" }}
                </button>
              </div>
            </div>

            <!-- Reused Executive SeoFormSection Component -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
              <SeoFormSection
                v-model="activePage.seo"
                :title="`${activePage.name} SEO`"
                :fallback-title="activePage.fallback_title"
                :fallback-description="activePage.fallback_description"
                :fallback-image="activePage.seo?.og_image || form.default_og_image"
                :page-path="activePage.path"
                :default-domain="form.canonical_base_url"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 1: Global Brand SEO Workspace -->
    <div v-else-if="loading" class="rounded-3xl border border-slate-200 bg-white p-16 text-center shadow-sm">
      <span class="material-symbols-outlined animate-spin text-5xl text-blue-600 mb-3">progress_activity</span>
      <p class="text-base font-bold text-slate-800">Loading Global SEO Registry...</p>
      <p class="text-xs text-slate-400 mt-1">Connecting to singleton configuration and bilingual dictionaries</p>
    </div>

    <!-- Main Workspace (Tab 1: Global Brand SEO) -->
    <div v-else-if="mainTab === 'global'" class="flex flex-col lg:flex-row gap-6 items-start">
      <!-- Left Column: Settings Categories Navigation Sidebar -->
      <div class="w-full lg:w-80 shrink-0 space-y-4 lg:sticky lg:top-6">
        <!-- Categories Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm space-y-1.5">
          <div class="flex items-center justify-between px-3 py-2 border-b border-slate-100">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
              <span class="material-symbols-outlined text-[16px] text-blue-600">tune</span>
              Settings Categories
            </span>
            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500">
              3 Sections
            </span>
          </div>

          <!-- Category Buttons -->
          <button
            v-for="cat in globalCategories"
            :key="cat.id"
            type="button"
            @click="activeCategory = cat.id"
            class="group flex w-full items-start gap-3 rounded-xl p-2.5 text-left transition-all cursor-pointer border"
            :class="activeCategory === cat.id
              ? 'border-blue-500/40 bg-blue-50/70 text-blue-950 shadow-sm ring-1 ring-blue-500/20'
              : 'border-transparent bg-white text-slate-700 hover:border-slate-200 hover:bg-slate-50'"
          >
            <div
              class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-all"
              :class="activeCategory === cat.id ? cat.activeIconClass : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200/70'"
            >
              <span class="material-symbols-outlined text-[18px]">{{ cat.icon }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between gap-1">
                <span class="text-xs font-bold truncate" :class="activeCategory === cat.id ? 'text-blue-900 font-extrabold' : 'text-slate-900'">
                  {{ cat.title }}
                </span>
                <span v-if="cat.id === 'verification_shield' && form.default_noindex" class="h-2 w-2 rounded-full bg-rose-500 animate-pulse shrink-0" title="Website is hidden from search"></span>
              </div>
              <p class="text-[11px] leading-tight truncate mt-0.5" :class="activeCategory === cat.id ? 'text-blue-700/80 font-medium' : 'text-slate-500'">
                {{ cat.subtitle }}
              </p>
            </div>
            <span
              class="material-symbols-outlined text-[16px] transition-transform self-center"
              :class="activeCategory === cat.id ? 'text-blue-600 translate-x-0.5' : 'text-slate-300 opacity-0 group-hover:opacity-100'"
            >
              chevron_right
            </span>
          </button>
        </div>

        <!-- Global Status Quick Info Card -->
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2.5 text-xs">
          <div class="flex items-center justify-between font-bold text-slate-700">
            <span class="flex items-center gap-1.5">
              <span class="material-symbols-outlined text-[15px] text-emerald-600">verified_user</span>
              Active Profile
            </span>
            <span
              class="rounded-full px-2 py-0.5 text-[10px] font-bold"
              :class="form.default_noindex ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
            >
              {{ form.default_noindex ? 'Offline / Hidden' : 'Public & Indexed' }}
            </span>
          </div>

          <div class="space-y-1.5 text-slate-500 text-[11px]">
            <div class="flex items-center justify-between">
              <span>Main Web Address:</span>
              <span class="font-mono text-slate-800 truncate max-w-[150px]">{{ form.canonical_base_url.replace(/^https?:\/\//, '') }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span>Sitemap:</span>
              <a :href="`${form.canonical_base_url}/sitemap.xml`" target="_blank" class="font-mono text-emerald-700 font-semibold hover:underline inline-flex items-center gap-0.5">
                /sitemap.xml
                <span class="material-symbols-outlined text-[11px]">open_in_new</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Active Form Section & Live Previews -->
      <div class="flex-1 min-w-0 space-y-6">
        <!-- Top Toolbar with Category Breadcrumb, Language Switcher, and Quick Save -->
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm">
          <div class="flex items-center gap-3">
            <div
              class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-white shadow-sm"
              :class="activeCategoryMeta.activeIconClass"
            >
              <span class="material-symbols-outlined text-[20px]">{{ activeCategoryMeta.icon }}</span>
            </div>
            <div>
              <h3 class="text-sm font-bold text-slate-900 leading-tight">{{ activeCategoryMeta.title }}</h3>
              <p class="text-xs text-slate-500 leading-tight">{{ activeCategoryMeta.subtitle }}</p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <!-- Language Studio Switcher -->
            <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1">
              <button
                type="button"
                @click="currentLang = 'en'"
                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold transition-all cursor-pointer"
                :class="currentLang === 'en' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
              >
                <span>🇬🇧</span> English
              </button>
              <button
                type="button"
                @click="currentLang = 'ar'"
                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold transition-all cursor-pointer"
                :class="currentLang === 'ar' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
              >
                <span>🇸🇦</span> العربية
              </button>
            </div>

            <!-- Quick Save Button -->
            <button
              type="button"
              @click="saveSettings"
              :disabled="saving || loading"
              class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 transition-all cursor-pointer active:scale-95"
            >
              <span v-if="saving" class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
              <span v-else class="material-symbols-outlined text-[16px]">save</span>
              {{ saving ? "Saving..." : "Save Settings" }}
            </button>
          </div>
        </div>

      <!-- SECTION 1 (MERGED): Website Identity, Title, Description, & Keywords -->
      <div v-show="activeCategory === 'identity'" class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm space-y-6">
        <!-- Section Header -->
        <div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
            <span class="material-symbols-outlined text-[24px]">public</span>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Website Identity, Description & Keywords</h3>
            <p class="text-xs text-slate-500">Official web address, brand name, default search snippet, and fallback keywords for Google & Bing.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Field 1: Canonical Base URL -->
          <div class="md:col-span-2 rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <span>Official Website Web Address (URL)</span>
                <span class="text-rose-500">*</span>
              </label>
              <!-- Benefit Badge -->
              <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                <span class="material-symbols-outlined text-[13px]">shield</span>
                Main Website Link
              </span>
            </div>

            <input
              v-model="form.canonical_base_url"
              type="url"
              required
              placeholder="https://sis.iptvpro.org"
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-mono text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            />

            <!-- Benefit Callout -->
            <div class="rounded-xl bg-blue-50/80 p-3 text-xs text-blue-900 leading-relaxed flex items-start gap-2.5">
              <span class="material-symbols-outlined text-[18px] text-blue-600 shrink-0 mt-0.5">info</span>
              <div>
                <strong>Why this matters:</strong> The primary URL of your site (e.g. <code>{{ form.canonical_base_url }}</code>). Tells Google where your official website lives and automatically generates links in your <code>/sitemap.xml</code> and social shares.
              </div>
            </div>
          </div>

          <!-- Field 2: Site Brand Name -->
          <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <span>Company / Brand Name</span>
                <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[10px] font-bold text-slate-700 uppercase">{{ currentLang }}</span>
              </label>
              <!-- Benefit Badge -->
              <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 ring-1 ring-indigo-200">
                <span class="material-symbols-outlined text-[13px]">badge</span>
                Brand Name
              </span>
            </div>

            <input
              v-model="form.site_name[currentLang]"
              type="text"
              :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
              placeholder="SiS - Superior Integrated Solutions"
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            />

            <!-- Benefit Callout -->
            <div class="rounded-xl bg-indigo-50/80 p-3 text-xs text-indigo-900 leading-relaxed flex items-start gap-2">
              <span class="material-symbols-outlined text-[16px] text-indigo-600 shrink-0 mt-0.5">help</span>
              <div>
                <strong>Why this matters:</strong> Main company name shown in Google search results and browser title bars in both English and Arabic.
              </div>
            </div>
          </div>

          <!-- Field 3: Title Brand Suffix -->
          <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <span>Brand Suffix for Page Titles</span>
                <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[10px] font-bold text-slate-700 uppercase">{{ currentLang }}</span>
              </label>
              <!-- Benefit Badge -->
              <span class="inline-flex items-center gap-1 rounded-full bg-cyan-50 px-2.5 py-0.5 text-[11px] font-bold text-cyan-800 ring-1 ring-cyan-200">
                <span class="material-symbols-outlined text-[13px]">auto_awesome</span>
                Appended to Titles
              </span>
            </div>

            <input
              v-model="form.title_suffix[currentLang]"
              type="text"
              :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
              placeholder=" | SiS - Superior Integrated Solutions"
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            />

            <!-- Benefit Callout -->
            <div class="rounded-xl bg-cyan-50/80 p-3 text-xs text-cyan-950 leading-relaxed flex items-start gap-2">
              <span class="material-symbols-outlined text-[16px] text-cyan-700 shrink-0 mt-0.5">help</span>
              <div>
                <strong>Why this matters:</strong> Automatically added to the end of all page titles (e.g. <code>Services {{ form.title_suffix[currentLang] }}</code>).
              </div>
            </div>
          </div>

          <!-- Field 4: Default Website Description -->
          <div class="md:col-span-2 rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                  <span>Default Website Description</span>
                  <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[10px] font-bold text-slate-700 uppercase">{{ currentLang }}</span>
                </label>
                <!-- Benefit Badge -->
                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                  <span class="material-symbols-outlined text-[13px]">ads_click</span>
                  Backup Summary
                </span>
              </div>

              <span class="text-xs" :class="descGuidanceClass">
                {{ descLength }} / 160 characters
                <span v-if="descLength >= 120 && descLength <= 160" class="text-[10px] ml-1 font-semibold text-emerald-600">✓ Ideal Google Length</span>
                <span v-else-if="descLength > 160" class="text-[10px] ml-1 font-semibold text-amber-600">⚠ Too long for Google</span>
              </span>
            </div>

            <textarea
              v-model="form.default_meta_description[currentLang]"
              rows="3"
              maxlength="300"
              :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
              placeholder="A concise, compelling overview of your company..."
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 resize-y"
            ></textarea>

            <!-- Character Progress Bar -->
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
              <div
                class="h-full transition-all duration-300"
                :class="[
                  descLength === 0 ? 'bg-transparent' : '',
                  descLength > 0 && descLength < 120 ? 'bg-blue-400' : '',
                  descLength >= 120 && descLength <= 160 ? 'bg-emerald-500' : '',
                  descLength > 160 ? 'bg-amber-500' : '',
                ]"
                :style="{ width: `${Math.min(100, (descLength / 160) * 100)}%` }"
              ></div>
            </div>

            <!-- Benefit Callout -->
            <div class="rounded-xl bg-blue-50/80 p-3.5 text-xs text-blue-900 leading-relaxed flex items-start gap-2.5">
              <span class="material-symbols-outlined text-[18px] text-blue-600 shrink-0 mt-0.5">info</span>
              <div>
                <strong>Why this matters:</strong> Backup summary shown on Google when a page doesn't have its own custom description.
              </div>
            </div>
          </div>

          <!-- Field 5: Default Search Keywords (Meta Keywords) -->
          <div class="md:col-span-2 rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                  <span>Default Search Keywords (Meta Keywords)</span>
                  <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[10px] font-bold text-slate-700 uppercase">{{ currentLang }}</span>
                </label>
                <!-- Benefit Badge -->
                <span class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2.5 py-0.5 text-[11px] font-bold text-purple-700 ring-1 ring-purple-200">
                  <span class="material-symbols-outlined text-[13px]">tag</span>
                  Fallback Keywords
                </span>
              </div>

              <span class="text-xs text-slate-400 font-medium">
                Comma-separated keywords
              </span>
            </div>

            <input
              v-model="form.default_meta_keywords[currentLang]"
              type="text"
              :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
              placeholder="e.g. industrial automation, SCADA, PLC, IoT, digital solutions, Egypt, MENA"
              class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            />

            <!-- Benefit Callout -->
            <div class="rounded-xl bg-purple-50/80 p-3.5 text-xs text-purple-950 leading-relaxed flex items-start gap-2.5">
              <span class="material-symbols-outlined text-[18px] text-purple-600 shrink-0 mt-0.5">info</span>
              <div>
                <strong>Why this matters:</strong> These global fallback keywords are automatically added to pages across your website whenever a specific page does not have its own individual keywords configured. Enter 5 to 10 key search terms separated by commas in both English and Arabic.
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SECTION 2 (MERGED): Social Media Share Card & Company Profile -->
      <div v-show="activeCategory === 'social_company'" class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm space-y-6">
        <!-- Section Header -->
        <div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
            <span class="material-symbols-outlined text-[24px]">share</span>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Social Media & Company Profile</h3>
            <p class="text-xs text-slate-500">Default share image (Open Graph banner), official company contact details, and verified social channels.</p>
          </div>
        </div>

        <!-- 1. Master Social Share Image -->
        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <label class="text-xs font-bold text-slate-800">Master Social Share Image (Open Graph Banner)</label>
              <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-bold text-indigo-700 ring-1 ring-indigo-200">
                <span class="material-symbols-outlined text-[13px]">trending_up</span>
                3x Higher Clicks
              </span>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <!-- 1200x630 Preview Box -->
            <div class="relative h-28 w-52 shrink-0 overflow-hidden rounded-2xl border border-slate-300 bg-slate-900 shadow-md flex items-center justify-center">
              <img
                v-if="localImagePreview || form.default_og_image"
                :src="localImagePreview || form.default_og_image"
                alt="Default OG"
                class="h-full w-full object-cover"
              />
              <span v-else class="material-symbols-outlined text-slate-500 text-4xl">image</span>

              <div class="absolute bottom-2 right-2 rounded-md bg-black/70 px-1.5 py-0.5 text-[10px] font-mono text-white/90 backdrop-blur-sm">
                1200 × 630
              </div>
            </div>

            <!-- Upload Tools -->
            <div class="flex-1 space-y-2.5">
              <input
                ref="fileInput"
                type="file"
                accept="image/png,image/jpeg,image/webp"
                @change="handleImagePick"
                class="hidden"
              />
              <div class="flex flex-wrap items-center gap-2">
                <button
                  type="button"
                  @click="fileInput?.click()"
                  class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-blue-700 transition-colors cursor-pointer"
                >
                  <span class="material-symbols-outlined text-[17px]">upload</span>
                  Upload 1200×630 Image
                </button>

                <button
                  v-if="localImagePreview || form.default_og_image"
                  type="button"
                  @click="clearOgImage"
                  class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-100 transition-colors cursor-pointer"
                >
                  <span class="material-symbols-outlined text-[16px]">delete</span>
                  Remove Image
                </button>
              </div>

              <p class="text-xs text-slate-500">
                Recommended: 1200 × 630 pixels (Aspect ratio 1.91:1), under 2MB (PNG or JPEG).
              </p>
            </div>
          </div>

          <!-- Benefit Callout -->
          <div class="rounded-xl bg-indigo-50/80 p-3.5 text-xs text-indigo-900 leading-relaxed flex items-start gap-2.5">
            <span class="material-symbols-outlined text-[18px] text-indigo-600 shrink-0 mt-0.5">info</span>
            <div>
              <strong>Why this matters:</strong> Displayed when someone shares any link from your website on WhatsApp, LinkedIn, Facebook, or X.
            </div>
          </div>
        </div>

        <!-- 2. Company Information -->
        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-4">
          <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-800">Company Information (Google Knowledge Graph)</label>
            <span class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2.5 py-0.5 text-[11px] font-bold text-purple-700 ring-1 ring-purple-200">
              <span class="material-symbols-outlined text-[13px]">corporate_fare</span>
              Entity Data
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Company Legal Name</label>
              <input
                v-model="form.organization_legal_name"
                type="text"
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Official Phone Number</label>
              <input
                v-model="form.organization_phone"
                type="text"
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Corporate Sales Email</label>
              <input
                v-model="form.organization_email"
                type="email"
                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
              />
            </div>
          </div>
        </div>

        <!-- 3. Social Media Links Manager -->
        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-4">
          <label class="block text-xs font-bold text-slate-800">
            Official Social Media Profiles (LinkedIn, Facebook, YouTube, X)
          </label>

          <div class="space-y-2">
            <div
              v-for="(link, idx) in form.social_links"
              :key="idx"
              class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs"
            >
              <span class="material-symbols-outlined text-slate-400 text-[18px]">public</span>
              <span class="flex-1 font-mono text-slate-800 truncate">{{ link }}</span>
              <button
                type="button"
                @click="removeSocialLink(idx)"
                class="text-rose-500 hover:text-rose-700 p-1 rounded-md transition-colors cursor-pointer"
                title="Remove link"
              >
                <span class="material-symbols-outlined text-[16px]">close</span>
              </button>
            </div>
          </div>

          <!-- Add new link input -->
          <div class="flex items-center gap-2">
            <input
              v-model="newSocialLink"
              type="url"
              placeholder="https://www.linkedin.com/company/superior-for-integrated-solutions"
              @keyup.enter.prevent="addSocialLink"
              class="flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            />
            <button
              type="button"
              @click="addSocialLink"
              class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-black transition-colors cursor-pointer shadow-sm"
            >
              <span class="material-symbols-outlined text-[16px]">add</span>
              Add Social Link
            </button>
          </div>
        </div>
      </div>

      <!-- SECTION 3 (MERGED): Search Verification & Site Shield -->
      <div v-show="activeCategory === 'verification_shield'" class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm space-y-6">
        <!-- Section Header -->
        <div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
            <span class="material-symbols-outlined text-[24px]">verified_user</span>
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Search Verification & Site Shield</h3>
            <p class="text-xs text-slate-500">Google Search Console, Bing Webmaster Tools, and site-wide search indexing visibility control.</p>
          </div>
        </div>

        <!-- 1. Google & Bing Webmaster Verifications -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Google Verification -->
          <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <label class="text-xs font-bold text-slate-800">Google Search Console Verification Tag</label>
              <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                <span class="material-symbols-outlined text-[13px]">query_stats</span>
                Google Search
              </span>
            </div>

            <input
              v-model="form.google_site_verification"
              type="text"
              placeholder="e.g. 4x8Z9jKqLmNoPqRsTuVwXyZ"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            />

            <div class="flex items-center justify-between text-xs pt-1">
              <span class="text-slate-400">Added to <code>&lt;head&gt;</code> automatically</span>
              <a
                href="https://search.google.com/search-console"
                target="_blank"
                class="text-blue-600 hover:text-blue-800 font-bold inline-flex items-center gap-1"
              >
                Open Google Search Console →
              </a>
            </div>
          </div>

          <!-- Bing Verification -->
          <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <label class="text-xs font-bold text-slate-800">Bing Webmaster Tools Verification Tag</label>
              <span class="inline-flex items-center gap-1 rounded-full bg-cyan-50 px-2.5 py-0.5 text-[11px] font-bold text-cyan-800 ring-1 ring-cyan-200">
                <span class="material-symbols-outlined text-[13px]">smart_toy</span>
                Bing & Copilot AI
              </span>
            </div>

            <input
              v-model="form.bing_site_verification"
              type="text"
              placeholder="e.g. 9B8C7D6E5F4A3B2C1D0"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
            />

            <div class="flex items-center justify-between text-xs pt-1">
              <span class="text-slate-400">Added to <code>&lt;head&gt;</code> automatically</span>
              <a
                href="https://www.bing.com/webmasters"
                target="_blank"
                class="text-blue-600 hover:text-blue-800 font-bold inline-flex items-center gap-1"
              >
                Open Bing Webmasters →
              </a>
            </div>
          </div>
        </div>

        <!-- 2. Maintenance Mode / Hide Website Shield -->
        <div
          class="rounded-2xl border p-6 transition-all"
          :class="form.default_noindex ? 'border-rose-300 bg-rose-50/70' : 'border-slate-200 bg-slate-50/60'"
        >
          <label class="flex items-start gap-4 cursor-pointer">
            <input
              type="checkbox"
              v-model="form.default_noindex"
              class="mt-1 h-5 w-5 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
            />
            <div class="space-y-1">
              <span class="text-sm font-bold text-slate-900 flex items-center gap-2">
                Hide entire website from search engines (<code class="font-mono text-xs text-rose-600">noindex, nofollow</code>)
                <span v-if="form.default_noindex" class="rounded-full bg-rose-200 px-2.5 py-0.5 text-[10px] font-extrabold text-rose-800">
                  WEBSITE IS HIDDEN FROM SEARCH
                </span>
              </span>

              <p class="text-xs text-slate-600 leading-relaxed">
                When checked, every page tells Google and Bing not to index the site and hides URLs from the sitemap.
              </p>
            </div>
          </label>

          <!-- Warning Callout -->
          <div class="mt-4 rounded-xl bg-white/80 p-3.5 text-xs text-slate-700 leading-relaxed border border-slate-200/60">
            <strong>Important:</strong> Keep this <strong>UNCHECKED</strong> for your live production website so clients can find you on Google. Only check this box if you are working on a private test version or performing temporary maintenance.
          </div>
        </div>
      </div>

      <!-- Real-Time Live Previews Simulator (Always Visible at Bottom) -->
      <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
          <div class="flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[20px] text-blue-600">visibility</span>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">
              Live Preview (How your Homepage looks on Google & Social Media)
            </h4>
          </div>

          <!-- Device switcher -->
          <div class="inline-flex rounded-xl bg-slate-100 p-1 text-xs font-semibold">
            <button
              type="button"
              @click="previewDevice = 'desktop'"
              class="rounded-lg px-3 py-1 transition-all cursor-pointer"
              :class="previewDevice === 'desktop' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
            >
              Desktop Search
            </button>
            <button
              type="button"
              @click="previewDevice = 'mobile'"
              class="rounded-lg px-3 py-1 transition-all cursor-pointer"
              :class="previewDevice === 'mobile' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
            >
              Mobile Search
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
          <!-- 1. Google Search Simulator -->
          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3 font-sans">
            <span class="text-[11px] font-bold text-emerald-700 tracking-wider uppercase flex items-center gap-1.5">
              <span class="material-symbols-outlined text-[14px]">search</span>
              Google Search Result Preview
            </span>

            <div
              class="rounded-xl border border-slate-100 p-4 transition-all"
              :class="previewDevice === 'mobile' ? 'max-w-xs mx-auto border-dashed border-blue-200' : ''"
            >
              <div class="flex items-center gap-2 mb-1.5">
                <div class="h-5 w-5 rounded-full bg-blue-600 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                  S
                </div>
                <div class="truncate text-xs text-slate-800">
                  <span class="font-medium text-slate-900 block leading-tight">Superior Integrated Solutions</span>
                  <span class="text-[11px] text-slate-400 truncate block">{{ form.canonical_base_url }}</span>
                </div>
              </div>

              <h5 class="text-base text-[#1a0dab] hover:underline cursor-pointer font-medium leading-snug">
                {{ previewTitle }}
              </h5>

              <p class="mt-1 text-xs text-[#4d5156] leading-relaxed line-clamp-3">
                {{ previewDesc }}
              </p>
            </div>
          </div>

          <!-- 2. Social Card (LinkedIn / Twitter / WhatsApp) -->
          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
            <span class="text-[11px] font-bold text-blue-700 tracking-wider uppercase flex items-center gap-1.5">
              <span class="material-symbols-outlined text-[14px]">share</span>
              Social Media Preview (WhatsApp, LinkedIn, Facebook)
            </span>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-900 shadow-sm">
              <div class="aspect-[1.91/1] w-full bg-slate-900 relative flex items-center justify-center overflow-hidden">
                <img :src="previewImage" alt="OG Preview" class="h-full w-full object-cover" />
                <div class="absolute bottom-2 right-2 rounded-md bg-black/70 px-2 py-0.5 text-[10px] font-mono text-white/90 backdrop-blur-sm">
                  1200 × 630
                </div>
              </div>

              <div class="p-3.5 bg-white border-t border-slate-100">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">
                  {{ form.canonical_base_url.replace(/^https?:\/\//, '').toUpperCase() }}
                </span>
                <h6 class="text-xs font-bold text-slate-900 leading-snug truncate">{{ previewTitle }}</h6>
                <p class="mt-0.5 text-[11px] text-slate-500 line-clamp-2">{{ previewDesc }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Toast Notification -->
    <div
      v-if="showToast"
      class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl border bg-white p-4 shadow-2xl transition-all"
      :class="toast.variant === 'error' ? 'border-rose-200 text-rose-800' : 'border-emerald-200 text-emerald-800'"
    >
      <span class="material-symbols-outlined text-2xl">
        {{ toast.variant === 'error' ? 'error' : 'check_circle' }}
      </span>
      <div>
        <h6 class="text-xs font-bold">{{ toast.title }}</h6>
        <p class="text-xs">{{ toast.message }}</p>
      </div>
      <button @click="showToast = false" class="text-slate-400 hover:text-slate-600 ml-2 cursor-pointer">
        <span class="material-symbols-outlined text-[16px]">close</span>
      </button>
    </div>
  </div>
</template>
