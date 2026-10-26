<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({}),
  },
  title: {
    type: String,
    default: "",
  },
  formTitle: {
    type: String,
    default: "",
  },
  fallbackTitle: {
    type: [String, Object],
    default: "",
  },
  fallbackDescription: {
    type: [String, Object],
    default: "",
  },
  fallbackImage: {
    type: String,
    default: "",
  },
  pagePath: {
    type: String,
    default: "",
  },
  defaultDomain: {
    type: String,
    default: "",
  },
});

const emit = defineEmits(["update:modelValue", "imageChange"]);

const currentLang = ref("en");
const activeSubTab = ref("serp"); // 'serp' | 'social' | 'technical'
const showPreview = ref(true);
const previewDevice = ref("desktop");
const fileInput = ref(null);
const localImagePreview = ref("");

// Display Form Title for intuitive identification
const displayFormTitle = computed(() => {
  if (props.formTitle) return props.formTitle;
  if (props.title) return props.title;
  if (resolvedFallbackTitle.value) return `SEO: ${resolvedFallbackTitle.value}`;
  if (props.pagePath) return `SEO: ${props.pagePath}`;
  return "Search Engine & Social Media Settings";
});

// Internal mutable copy synced with modelValue
const internalSeo = ref(normalizeSeo(props.modelValue));

function normalizeSeo(val) {
  const src = val || {};
  return {
    meta_title: {
      en: typeof src.meta_title === "object" ? src.meta_title?.en || "" : (typeof src.meta_title === "string" ? src.meta_title : ""),
      ar: typeof src.meta_title === "object" ? src.meta_title?.ar || "" : "",
    },
    meta_description: {
      en: typeof src.meta_description === "object" ? src.meta_description?.en || "" : (typeof src.meta_description === "string" ? src.meta_description : ""),
      ar: typeof src.meta_description === "object" ? src.meta_description?.ar || "" : "",
    },
    meta_keywords: {
      en: typeof src.meta_keywords === "object" ? src.meta_keywords?.en || "" : (typeof src.meta_keywords === "string" ? src.meta_keywords : ""),
      ar: typeof src.meta_keywords === "object" ? src.meta_keywords?.ar || "" : "",
    },
    og_title: {
      en: typeof src.og_title === "object" ? src.og_title?.en || "" : (typeof src.og_title === "string" ? src.og_title : ""),
      ar: typeof src.og_title === "object" ? src.og_title?.ar || "" : "",
    },
    og_description: {
      en: typeof src.og_description === "object" ? src.og_description?.en || "" : (typeof src.og_description === "string" ? src.og_description : ""),
      ar: typeof src.og_description === "object" ? src.og_description?.ar || "" : "",
    },
    og_image: src.og_image || "",
    og_image_file: src.og_image_file || null,
    canonical_url: src.canonical_url || "",
    is_noindex: Boolean(src.is_noindex),
    is_nofollow: Boolean(src.is_nofollow),
    schema_type: src.schema_type || "Service",
  };
}

watch(
  () => props.modelValue,
  (newVal) => {
    internalSeo.value = normalizeSeo(newVal);
  },
  { deep: true },
);

function updateField() {
  emit("update:modelValue", { ...internalSeo.value });
}

// Helpers for resolved fallbacks based on active language
const resolvedFallbackTitle = computed(() => {
  if (typeof props.fallbackTitle === "object" && props.fallbackTitle) {
    return props.fallbackTitle[currentLang.value] || props.fallbackTitle.en || "";
  }
  return String(props.fallbackTitle || "");
});

const resolvedFallbackDesc = computed(() => {
  if (typeof props.fallbackDescription === "object" && props.fallbackDescription) {
    return props.fallbackDescription[currentLang.value] || props.fallbackDescription.en || "";
  }
  return String(props.fallbackDescription || "");
});

// Character Guidance Computeds
const currentTitleLength = computed(() => {
  return (internalSeo.value.meta_title[currentLang.value] || "").length;
});

const titleGuidanceClass = computed(() => {
  const len = currentTitleLength.value;
  if (len === 0) return "text-slate-400";
  if (len >= 45 && len <= 60) return "text-emerald-600 font-semibold";
  if (len > 60) return "text-amber-600 font-semibold";
  return "text-slate-500";
});

const currentDescLength = computed(() => {
  return (internalSeo.value.meta_description[currentLang.value] || "").length;
});

const descGuidanceClass = computed(() => {
  const len = currentDescLength.value;
  if (len === 0) return "text-slate-400";
  if (len >= 120 && len <= 160) return "text-emerald-600 font-semibold";
  if (len > 160) return "text-amber-600 font-semibold";
  return "text-slate-500";
});

// SEO Health & Optimization Score (0 - 100)
const seoHealth = computed(() => {
  let score = 0;
  const tLen = currentTitleLength.value;
  const dLen = currentDescLength.value;
  const kLen = (internalSeo.value.meta_keywords?.[currentLang.value] || "").trim().length;

  if (tLen > 0) {
    score += 30;
    if (tLen >= 45 && tLen <= 60) score += 10;
    else if (tLen < 45) score += 5;
  } else if (resolvedFallbackTitle.value) {
    score += 20; // Has fallback
  }

  if (dLen > 0) {
    score += 25;
    if (dLen >= 120 && dLen <= 160) score += 10;
    else if (dLen >= 60) score += 5;
  } else if (resolvedFallbackDesc.value) {
    score += 15; // Has fallback
  }

  // Keywords (+10 score)
  if (kLen > 0) {
    score += 10;
  }

  if (localImagePreview.value || internalSeo.value.og_image || props.fallbackImage) {
    score += 10;
  }

  if (internalSeo.value.schema_type) {
    score += 5;
  }

  return Math.min(100, score);
});

const seoHealthStatus = computed(() => {
  const s = seoHealth.value;
  if (s >= 85) return { label: "Excellent", color: "text-emerald-700 bg-emerald-50 border-emerald-200", icon: "check_circle" };
  if (s >= 60) return { label: "Good - Ready", color: "text-blue-700 bg-blue-50 border-blue-200", icon: "thumb_up" };
  return { label: "Needs Info", color: "text-amber-700 bg-amber-50 border-amber-200", icon: "priority_high" };
});

// Auto-fill from section content
function autoFillFromContent() {
  if (resolvedFallbackTitle.value) {
    internalSeo.value.meta_title[currentLang.value] = resolvedFallbackTitle.value;
  }
  if (resolvedFallbackDesc.value) {
    const clean = resolvedFallbackDesc.value.replace(/<[^>]+>/g, "").trim();
    internalSeo.value.meta_description[currentLang.value] = clean.slice(0, 160);
  }
  updateField();
}

// Real-time Preview Computeds
const previewTitle = computed(() => {
  const manual = internalSeo.value.meta_title[currentLang.value]?.trim();
  if (manual) {
    return manual.includes("SiS") ? manual : `${manual} | SiS - Superior Integrated Solutions`;
  }
  const fallback = resolvedFallbackTitle.value?.trim();
  if (fallback) {
    return fallback.includes("SiS") ? fallback : `${fallback} | SiS - Superior Integrated Solutions`;
  }
  return "SiS - Superior Integrated Solutions";
});

const previewDesc = computed(() => {
  const manual = internalSeo.value.meta_description[currentLang.value]?.trim();
  if (manual) return manual;
  const fallback = resolvedFallbackDesc.value?.trim();
  if (fallback) return fallback.replace(/<[^>]+>/g, "").slice(0, 160);
  return "Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and AI.";
});

const previewSocialTitle = computed(() => {
  const custom = internalSeo.value.og_title[currentLang.value]?.trim();
  if (custom) return custom;
  return previewTitle.value;
});

const previewSocialDesc = computed(() => {
  const custom = internalSeo.value.og_description[currentLang.value]?.trim();
  if (custom) return custom;
  return previewDesc.value;
});

const previewImage = computed(() => {
  if (localImagePreview.value) return localImagePreview.value;
  if (internalSeo.value.og_image) {
    if (typeof internalSeo.value.og_image === "string") {
      return internalSeo.value.og_image.startsWith("http")
        ? internalSeo.value.og_image
        : `https://${props.defaultDomain}${internalSeo.value.og_image.startsWith("/") ? "" : "/"}${internalSeo.value.og_image}`;
    }
  }
  if (props.fallbackImage) {
    return props.fallbackImage.startsWith("http")
      ? props.fallbackImage
      : `https://${props.defaultDomain}${props.fallbackImage.startsWith("/") ? "" : "/"}${props.fallbackImage}`;
  }
  return `https://${props.defaultDomain}/sis-logo.png`;
});

const previewBreadcrumb = computed(() => {
  const path = (props.pagePath || "/").replace(/^\/+/, "");
  if (!path) return props.defaultDomain;
  return `${props.defaultDomain} > ${path.split("/").join(" > ")}`;
});

// Image Upload Handler
function handleImagePick(e) {
  const file = e.target.files?.[0];
  if (!file) return;

  internalSeo.value.og_image_file = file;
  localImagePreview.value = URL.createObjectURL(file);
  emit("imageChange", file);
  updateField();
}

function clearImage() {
  internalSeo.value.og_image = "";
  internalSeo.value.og_image_file = null;
  localImagePreview.value = "";
  if (fileInput.value) {
    fileInput.value.value = "";
  }
  emit("imageChange", null);
  updateField();
}

function resetToDefaults() {
  internalSeo.value.meta_title = { en: "", ar: "" };
  internalSeo.value.meta_description = { en: "", ar: "" };
  internalSeo.value.meta_keywords = { en: "", ar: "" };
  internalSeo.value.og_title = { en: "", ar: "" };
  internalSeo.value.og_description = { en: "", ar: "" };
  internalSeo.value.og_image = "";
  internalSeo.value.og_image_file = null;
  internalSeo.value.canonical_url = "";
  internalSeo.value.is_noindex = false;
  internalSeo.value.is_nofollow = false;
  internalSeo.value.schema_type = "Service";
  localImagePreview.value = "";
  if (fileInput.value) fileInput.value.value = "";
  updateField();
}
</script>

<template>
  <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm transition-all">
    <!-- Header with Gradient Branding & Health Score -->
    <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-[#0e1e38] to-[#12284c] px-6 py-5 text-white">
      <!-- Ambient light effect -->
      <div class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-blue-500/10 blur-3xl"></div>

      <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3.5">
          <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-500/20 text-blue-300 ring-1 ring-white/10 backdrop-blur-md">
            <span class="material-symbols-outlined text-[24px]">travel_explore</span>
          </div>
          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-base font-bold text-white tracking-tight">
                {{ displayFormTitle }}
              </h3>
              <span class="rounded-full bg-cyan-400/20 px-2 py-0.5 text-[10px] font-bold text-cyan-200 ring-1 ring-cyan-400/30 uppercase tracking-wider">
                Google & Social Media
              </span>
            </div>
            <p class="text-xs text-slate-300/80 mt-0.5">
              Control how this page appears on Google search and when shared on WhatsApp, LinkedIn, and Facebook.
            </p>
          </div>
        </div>

        <!-- Health Meter & Quick Tools -->
        <div class="flex flex-wrap items-center gap-2">
          <!-- Live Health Badge -->
          <div class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold backdrop-blur-md" :class="seoHealthStatus.color">
            <span class="material-symbols-outlined text-[16px]">{{ seoHealthStatus.icon }}</span>
            <span>SEO Score: {{ seoHealth }}% ({{ seoHealthStatus.label }})</span>
          </div>

          <!-- Auto-fill Button -->
          <button
            type="button"
            @click="autoFillFromContent"
            class="inline-flex items-center gap-1.5 rounded-xl border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white hover:bg-white/20 transition-all cursor-pointer backdrop-blur-sm"
            title="Automatically copy page title and summary into SEO fields"
          >
            <span class="material-symbols-outlined text-[16px] text-cyan-300">auto_fix_high</span>
            <span>Auto-Fill from Page</span>
          </button>

          <!-- Reset Defaults -->
          <button
            type="button"
            @click="resetToDefaults"
            class="inline-flex items-center gap-1.5 rounded-xl border border-white/10 bg-white/5 px-2.5 py-1.5 text-xs font-medium text-slate-300 hover:bg-white/15 hover:text-white transition-all cursor-pointer"
            title="Clear manual entries and use automatic defaults"
          >
            <span class="material-symbols-outlined text-[15px]">restart_alt</span>
            <span class="hidden sm:inline">Reset</span>
          </button>

          <!-- Toggle Preview -->
          <button
            type="button"
            @click="showPreview = !showPreview"
            class="inline-flex items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-xs font-semibold transition-all cursor-pointer"
            :class="showPreview ? 'bg-cyan-500/20 text-cyan-200 ring-1 ring-cyan-400/40' : 'bg-white/10 text-slate-300 hover:bg-white/20'"
          >
            <span class="material-symbols-outlined text-[15px]">{{ showPreview ? 'visibility_off' : 'visibility' }}</span>
            <span class="hidden sm:inline">{{ showPreview ? 'Hide Preview' : 'Show Preview' }}</span>
          </button>
        </div>
      </div>

      <!-- Category Sub-Tabs with simple friendly titles -->
      <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-white/10 pt-3 text-xs">
        <button
          type="button"
          @click="activeSubTab = 'serp'"
          class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-semibold transition-all cursor-pointer"
          :class="activeSubTab === 'serp' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
        >
          <span class="material-symbols-outlined text-[15px] text-emerald-600">search</span>
          1. Google Search
        </button>

        <button
          type="button"
          @click="activeSubTab = 'social'"
          class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-semibold transition-all cursor-pointer"
          :class="activeSubTab === 'social' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
        >
          <span class="material-symbols-outlined text-[15px] text-blue-500">share</span>
          2. Social Media Sharing (WhatsApp, LinkedIn)
        </button>

        <button
          type="button"
          @click="activeSubTab = 'technical'"
          class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 font-semibold transition-all cursor-pointer"
          :class="activeSubTab === 'technical' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
        >
          <span class="material-symbols-outlined text-[15px] text-purple-400">tune</span>
          3. Privacy & Page Settings
        </button>

        <!-- Language Switcher -->
        <div class="ml-auto flex items-center gap-1.5 rounded-xl bg-black/30 p-1 ring-1 ring-white/10">
          <button
            type="button"
            @click="currentLang = 'en'"
            class="flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-bold transition-all cursor-pointer"
            :class="currentLang === 'en' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
          >
            <span>🇬🇧</span> EN
          </button>
          <button
            type="button"
            @click="currentLang = 'ar'"
            class="flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-bold transition-all cursor-pointer"
            :class="currentLang === 'ar' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
          >
            <span>🇸🇦</span> AR
          </button>
        </div>
      </div>
    </div>

    <!-- Tab 1: Google Search Appearance -->
    <div v-show="activeSubTab === 'serp'" class="p-6 space-y-6">
      <!-- Section Title -->
      <div class="border-b border-slate-100 pb-3">
        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
          <span class="material-symbols-outlined text-emerald-600 text-[20px]">manage_search</span>
          Google Search Appearance ({{ currentLang === 'ar' ? 'اللغة العربية' : 'English' }})
        </h4>
        <p class="text-xs text-slate-500 mt-0.5">
          Enter the title, description, and keywords that search engines use to display this page.
        </p>
      </div>

      <!-- SEO Title -->
      <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
          <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
              <span>Page Title in Google</span>
              <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[10px] font-bold text-slate-600 uppercase">{{ currentLang }}</span>
            </label>
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">
              <span class="material-symbols-outlined text-[12px]">grade</span>
              Main Title
            </span>
          </div>

          <span class="text-xs" :class="titleGuidanceClass">
            {{ currentTitleLength }} / 60 characters
            <span v-if="currentTitleLength >= 45 && currentTitleLength <= 60" class="text-[10px] ml-1 font-semibold text-emerald-600">✓ Ideal Google Length</span>
            <span v-else-if="currentTitleLength > 60" class="text-[10px] ml-1 font-semibold text-amber-600">⚠ Too long (Google will truncate)</span>
          </span>
        </div>

        <input
          v-model="internalSeo.meta_title[currentLang]"
          @input="updateField"
          type="text"
          maxlength="120"
          :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
          :placeholder="resolvedFallbackTitle ? `${resolvedFallbackTitle} | SiS` : 'e.g. Industrial Automation & Control Systems | SiS'"
          class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
        />

        <div class="mt-2.5 flex items-start gap-2 rounded-lg bg-blue-50/70 p-2.5 text-xs text-blue-900 leading-relaxed">
          <span class="material-symbols-outlined text-[16px] text-blue-600 shrink-0 mt-0.5">info</span>
          <div>
            <strong>Help:</strong> This is the large blue clickable headline shown in Google search results. Including your main service or product name here helps customers find you easily.
          </div>
        </div>
      </div>

      <!-- Meta Description -->
      <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
          <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
              <span>Page Description in Google</span>
              <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[10px] font-bold text-slate-600 uppercase">{{ currentLang }}</span>
            </label>
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700 ring-1 ring-blue-200">
              <span class="material-symbols-outlined text-[12px]">ads_click</span>
              Summary Text
            </span>
          </div>

          <span class="text-xs" :class="descGuidanceClass">
            {{ currentDescLength }} / 160 characters
            <span v-if="currentDescLength >= 120 && currentDescLength <= 160" class="text-[10px] ml-1 font-semibold text-emerald-600">✓ Ideal Summary Length</span>
            <span v-else-if="currentDescLength > 160" class="text-[10px] ml-1 font-semibold text-amber-600">⚠ Too long (Google will truncate)</span>
          </span>
        </div>

        <textarea
          v-model="internalSeo.meta_description[currentLang]"
          @input="updateField"
          rows="3"
          maxlength="255"
          :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
          :placeholder="resolvedFallbackDesc ? resolvedFallbackDesc.slice(0, 150) : 'Write a short, engaging summary explaining what this page offers to encourage people to click...'"
          class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all resize-y"
        ></textarea>

        <!-- Progress bar for 160 chars -->
        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
          <div
            class="h-full transition-all duration-300"
            :class="[
              currentDescLength === 0 ? 'bg-transparent' : '',
              currentDescLength > 0 && currentDescLength < 120 ? 'bg-blue-400' : '',
              currentDescLength >= 120 && currentDescLength <= 160 ? 'bg-emerald-500' : '',
              currentDescLength > 160 ? 'bg-amber-500' : '',
            ]"
            :style="{ width: `${Math.min(100, (currentDescLength / 160) * 100)}%` }"
          ></div>
        </div>

        <div class="mt-2.5 flex items-start gap-2 rounded-lg bg-blue-50/70 p-2.5 text-xs text-blue-900 leading-relaxed">
          <span class="material-symbols-outlined text-[16px] text-blue-600 shrink-0 mt-0.5">info</span>
          <div>
            <strong>Help:</strong> This summary appears directly beneath your title in Google search results. A clear summary convinces searchers to visit your website.
          </div>
        </div>
      </div>

      <!-- Meta Keywords (الكلمات المفتاحية) -->
      <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:border-slate-200">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
          <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
              <span>Keywords & Search Topics (Meta Keywords)</span>
              <span class="rounded bg-slate-200 px-1.5 py-0.2 text-[10px] font-bold text-slate-600 uppercase">{{ currentLang }}</span>
            </label>
            <span class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2 py-0.5 text-[10px] font-bold text-purple-700 ring-1 ring-purple-200">
              <span class="material-symbols-outlined text-[12px]">tag</span>
              Keywords
            </span>
          </div>

          <span class="text-xs text-slate-400">
            Separate words with commas
          </span>
        </div>

        <input
          v-model="internalSeo.meta_keywords[currentLang]"
          @input="updateField"
          type="text"
          maxlength="255"
          :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
          :placeholder="currentLang === 'ar' ? 'مثال: أتمتة صناعية، أنظمة سكادا، تحكم آلي، تكامل الأنظمة' : 'e.g. industrial automation, scada systems, plc control, ot cybersecurity, dcs migration'"
          class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all"
        />

        <div class="mt-2.5 flex items-start gap-2 rounded-lg bg-purple-50/70 p-2.5 text-xs text-purple-900 leading-relaxed">
          <span class="material-symbols-outlined text-[16px] text-purple-600 shrink-0 mt-0.5">info</span>
          <div>
            <strong>Help:</strong> Enter 4 to 8 relevant keywords or search terms that represent this page, separated by commas. Search engines and catalogs use these terms to categorize your business topics.
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 2: Social Media Cards (WhatsApp, LinkedIn, Facebook) -->
    <div v-show="activeSubTab === 'social'" class="p-6 space-y-6">
      <!-- Section Title -->
      <div class="border-b border-slate-100 pb-3">
        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
          <span class="material-symbols-outlined text-blue-600 text-[20px]">share</span>
          Social Media Sharing (WhatsApp, LinkedIn, Facebook, X)
        </h4>
        <p class="text-xs text-slate-500 mt-0.5">
          Choose the image, headline, and summary that appear when people share this link in chats or social media.
        </p>
      </div>

      <!-- Social Image Upload & Management -->
      <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:border-slate-200">
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-slate-800">Social Preview Image</label>
            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 ring-1 ring-indigo-200">
              <span class="material-symbols-outlined text-[12px]">image</span>
              WhatsApp & LinkedIn
            </span>
          </div>
          <span class="rounded bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-600">Recommended: 1200 × 630 px</span>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
          <!-- Thumbnail -->
          <div class="relative h-24 w-44 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-900 shadow-sm flex items-center justify-center">
            <img
              v-if="localImagePreview || internalSeo.og_image"
              :src="localImagePreview || internalSeo.og_image"
              alt="OG Preview"
              class="h-full w-full object-cover"
            />
            <div v-else-if="fallbackImage" class="relative h-full w-full">
              <img :src="fallbackImage" alt="Hero Fallback" class="h-full w-full object-cover opacity-70" />
              <div class="absolute inset-0 bg-black/40 flex items-center justify-center text-[10px] text-white font-semibold">
                Using Page Image
              </div>
            </div>
            <span v-else class="material-symbols-outlined text-slate-500 text-3xl">image</span>
          </div>

          <!-- Controls -->
          <div class="flex-1 space-y-2">
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
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-bold text-slate-800 shadow-sm hover:bg-slate-50 transition-colors cursor-pointer"
              >
                <span class="material-symbols-outlined text-[16px] text-blue-600">upload</span>
                Upload Image
              </button>
              <button
                v-if="localImagePreview || internalSeo.og_image"
                type="button"
                @click="clearImage"
                class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition-colors cursor-pointer"
              >
                <span class="material-symbols-outlined text-[15px]">delete</span>
                Remove Custom Image
              </button>
            </div>
            <p class="text-[11px] text-slate-500">
              {{ localImagePreview ? '✓ New image selected. Remember to save changes.' : (internalSeo.og_image ? 'Custom image active.' : 'Currently using the page main photo.') }}
            </p>
          </div>
        </div>

        <!-- Explainer -->
        <div class="mt-3 flex items-start gap-2 rounded-lg bg-indigo-50/70 p-2.5 text-xs text-indigo-900 leading-relaxed">
          <span class="material-symbols-outlined text-[16px] text-indigo-600 shrink-0 mt-0.5">info</span>
          <div>
            <strong>Tip:</strong> Links shared with an attractive picture receive up to <strong>3 times more clicks</strong> on WhatsApp, LinkedIn, and social networks compared to links with empty previews.
          </div>
        </div>
      </div>

      <!-- Optional Social Title & Description Overrides -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
            <span>Custom Social Title</span>
            <span class="text-[10px] text-slate-400 font-normal">Optional</span>
          </label>
          <input
            v-model="internalSeo.og_title[currentLang]"
            @input="updateField"
            type="text"
            :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
            :placeholder="previewTitle"
            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
          />
          <p class="mt-1 text-[11px] text-slate-400">Leave empty to use the Google Title above.</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-800 mb-1 flex items-center justify-between">
            <span>Custom Social Summary</span>
            <span class="text-[10px] text-slate-400 font-normal">Optional</span>
          </label>
          <input
            v-model="internalSeo.og_description[currentLang]"
            @input="updateField"
            type="text"
            :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
            :placeholder="previewDesc"
            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
          />
          <p class="mt-1 text-[11px] text-slate-400">Leave empty to use the Google Description above.</p>
        </div>
      </div>
    </div>

    <!-- Tab 3: Privacy & Page Settings -->
    <div v-show="activeSubTab === 'technical'" class="p-6 space-y-6">
      <!-- Section Title -->
      <div class="border-b border-slate-100 pb-3">
        <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
          <span class="material-symbols-outlined text-purple-600 text-[20px]">tune</span>
          Search Privacy & Page Link Settings
        </h4>
        <p class="text-xs text-slate-500 mt-0.5">
          Manage page privacy, content category, and canonical web address.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Content Topic / Type -->
        <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
          <div class="flex items-center gap-2 mb-2">
            <label class="text-xs font-bold text-slate-800">Page Content Type</label>
            <span class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2 py-0.5 text-[10px] font-bold text-purple-700 ring-1 ring-purple-200">
              <span class="material-symbols-outlined text-[12px]">category</span>
              Google Topic
            </span>
          </div>

          <select
            v-model="internalSeo.schema_type"
            @change="updateField"
            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-semibold text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
          >
            <option value="Service">Industrial Solution / Service Page</option>
            <option value="Article">Project Case Study / Article</option>
            <option value="CollectionPage">Category / Directory List</option>
            <option value="WebPage">Standard Web Page</option>
          </select>

          <p class="mt-2 text-xs text-slate-500 leading-relaxed">
            <strong>Tip:</strong> Tells Google's search algorithms what kind of content this page contains so it can display appropriate rich results.
          </p>
        </div>

        <!-- Canonical URL -->
        <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
          <div class="flex items-center gap-2 mb-2">
            <label class="text-xs font-bold text-slate-800">Original Page Link (Canonical Link)</label>
            <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-2 py-0.5 text-[10px] font-bold text-slate-700">
              Original URL
            </span>
          </div>

          <input
            v-model="internalSeo.canonical_url"
            @input="updateField"
            type="url"
            :placeholder="`https://${defaultDomain}${(pagePath || '/').startsWith('/') ? '' : '/'}${pagePath || ''}`"
            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10"
          />

          <p class="mt-2 text-xs text-slate-500 leading-relaxed">
            <strong>Tip:</strong> Automatically set to this page's web address. Only fill this if this page is a duplicate copy of another web address.
          </p>
        </div>
      </div>

      <!-- Visibility Options -->
      <div class="rounded-xl border border-slate-200/90 bg-white p-4">
        <h4 class="text-xs font-bold text-slate-900 mb-2 flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px] text-slate-700">visibility</span>
          <span>Search Engine Visibility</span>
        </h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <label class="flex items-start gap-3 rounded-xl border border-slate-200/80 p-3 hover:bg-slate-50/80 cursor-pointer transition-colors" :class="internalSeo.is_noindex ? 'bg-rose-50/60 border-rose-200' : ''">
            <input
              type="checkbox"
              v-model="internalSeo.is_noindex"
              @change="updateField"
              class="mt-1 h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
            />
            <div class="text-xs">
              <span class="font-bold text-slate-900 flex items-center gap-1.5">
                Hide this page from search results (<code class="text-[11px] text-rose-600 font-mono">noindex</code>)
                <span v-if="internalSeo.is_noindex" class="rounded bg-rose-100 px-1.5 py-0.2 text-[10px] font-bold text-rose-700">HIDDEN</span>
              </span>
              <p class="text-slate-500 text-[11px] mt-0.5">Check this box if this page is a private page, draft, or internal test that should not appear on Google.</p>
            </div>
          </label>

          <label class="flex items-start gap-3 rounded-xl border border-slate-200/80 p-3 hover:bg-slate-50/80 cursor-pointer transition-colors" :class="internalSeo.is_nofollow ? 'bg-amber-50/60 border-amber-200' : ''">
            <input
              type="checkbox"
              v-model="internalSeo.is_nofollow"
              @change="updateField"
              class="mt-1 h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500"
            />
            <div class="text-xs">
              <span class="font-bold text-slate-900 flex items-center gap-1.5">
                Do not follow links on this page (<code class="text-[11px] text-amber-600 font-mono">nofollow</code>)
              </span>
              <p class="text-slate-500 text-[11px] mt-0.5">Tells search engines not to crawl any outbound links placed on this page.</p>
            </div>
          </label>
        </div>
      </div>
    </div>

    <!-- Live Real-Time Simulator Accordion -->
    <div v-if="showPreview" class="border-t border-slate-200/80 bg-slate-50/60 p-6">
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-[18px] text-blue-600">visibility</span>
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">
            Live Preview (How this page looks when found or shared)
          </h4>
        </div>

        <div class="inline-flex rounded-lg bg-slate-200/70 p-0.5 text-xs font-semibold">
          <button
            type="button"
            @click="previewDevice = 'desktop'"
            class="rounded-md px-2.5 py-1 transition-all cursor-pointer"
            :class="previewDevice === 'desktop' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'"
          >
            Desktop Search
          </button>
          <button
            type="button"
            @click="previewDevice = 'mobile'"
            class="rounded-md px-2.5 py-1 transition-all cursor-pointer"
            :class="previewDevice === 'mobile' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'"
          >
            Mobile Search
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- 1. Google SERP Simulator -->
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mb-3 uppercase tracking-wider">
            <span class="flex items-center gap-1.5 text-emerald-700 font-bold">
              <span class="material-symbols-outlined text-[14px]">search</span>
              Google Search Result Preview
            </span>
            <span class="text-slate-400 font-normal">Live Preview</span>
          </div>

          <div
            class="rounded-lg bg-white p-3 border border-slate-100 transition-all font-sans"
            :class="previewDevice === 'mobile' ? 'max-w-xs mx-auto border-dashed border-blue-200' : ''"
          >
            <!-- Google Breadcrumb -->
            <div class="flex items-center gap-2 mb-1.5">
              <div class="h-5 w-5 rounded-full bg-blue-600 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                S
              </div>
              <div class="truncate text-xs text-slate-800">
                <span class="font-medium text-slate-900 block leading-tight">Superior Integrated Solutions</span>
                <span class="text-[11px] text-slate-400 truncate block">{{ previewBreadcrumb }}</span>
              </div>
            </div>

            <!-- Title -->
            <h5 class="text-base text-[#1a0dab] hover:underline cursor-pointer font-medium leading-snug line-clamp-2">
              {{ previewTitle }}
            </h5>

            <!-- Meta Description -->
            <p class="mt-1 text-xs text-[#4d5156] leading-relaxed line-clamp-3">
              {{ previewDesc }}
            </p>
          </div>
        </div>

        <!-- 2. Social Card (LinkedIn / Twitter / WhatsApp) -->
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mb-3 uppercase tracking-wider">
            <span class="flex items-center gap-1.5 text-blue-700 font-bold">
              <span class="material-symbols-outlined text-[14px]">share</span>
              Social Media Preview (WhatsApp, LinkedIn, Facebook)
            </span>
            <span class="text-slate-400 font-normal">Live Preview</span>
          </div>

          <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-900 shadow-sm">
            <div class="relative aspect-[1.91/1] w-full bg-slate-900 overflow-hidden flex items-center justify-center">
              <img
                :src="previewImage"
                alt="Social Card Preview"
                class="h-full w-full object-cover"
              />
              <div class="absolute bottom-2 right-2 rounded bg-black/70 px-2 py-0.5 text-[10px] font-mono text-white/90 backdrop-blur-sm">
                1200 × 630
              </div>
            </div>

            <div class="p-3.5 bg-white border-t border-slate-100">
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">
                {{ defaultDomain.toUpperCase() }}
              </span>
              <h6 class="text-xs font-bold text-slate-900 leading-snug line-clamp-1">
                {{ previewSocialTitle }}
              </h6>
              <p class="mt-0.5 text-[11px] text-slate-500 leading-normal line-clamp-2">
                {{ previewSocialDesc }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
