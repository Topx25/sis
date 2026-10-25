<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import { adminApi } from "../../api/adminApi";
import ImageInput from "@/SIS-Dashboard/components/collections/ImageInput.vue";
import IconPicker from "@/SIS-Dashboard/components/IconPicker.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import SeoFormSection from "@/SIS-Dashboard/components/seo/SeoFormSection.vue";
import { resolveImageUrl } from "@/utils/imageUrl.js";

/* =========================================================
   Static Catalog Definition
========================================================= */
const defaultSolutionPages = [
    { slug: "digital-solutions", name: "Digital Solutions", icon: "devices" },
    { slug: "engineering-services", name: "Engineering Services", icon: "engineering" },
    { slug: "general-services", name: "General Services", icon: "school" },
    { slug: "industrial-ai", name: "Industrial AI", icon: "psychology" },
    { slug: "industrial-automation", name: "Industrial Automation", icon: "precision_manufacturing" },
    { slug: "infrastructure-solutions", name: "Infrastructure Solutions", icon: "apartment" },
    { slug: "operations-maintenance", name: "Operations & Maintenance", icon: "build" },
    { slug: "ot-cybersecurity", name: "OT Cybersecurity", icon: "security" },
    { slug: "predictive-maintenance", name: "Predictive Maintenance", icon: "monitoring" },
    { slug: "process-automation", name: "Process Automation", icon: "account_tree" },
    { slug: "smart-manufacturing", name: "Smart Manufacturing", icon: "smart_toy" },
    { slug: "training-consulting", name: "Training & Consulting", icon: "school" },
];

/* =========================================================
   Overview State
========================================================= */
const solutionPagesList = ref(
    defaultSolutionPages.map((item) => ({
        ...item,
        title: item.name,
        subtitle: "",
        hero_image: "",
        hero_image_url: "",
        badgesCount: 0,
        servicesCount: 0,
        stepsCount: 0,
        is_active: true,
        loaded: false,
        raw: null,
    })),
);

const overviewLoading = ref(true);
const search = ref("");

/* =========================================================
   Edit Modal State
========================================================= */
const modalOpen = ref(false);
const isCreateMode = ref(false);
const slugManuallyEdited = ref(false);
const editingSlug = ref("");
const editingName = ref("");
const editingIcon = ref("hub");
const modalLoading = ref(false);
const saving = ref(false);
const deleting = ref(false);
const modalError = ref("");
const activeTab = ref("general");

const form = ref(emptyPageForm());

function openCreate() {
    isCreateMode.value = true;
    slugManuallyEdited.value = false;
    editingSlug.value = "";
    editingName.value = "New Solution Page";
    editingIcon.value = "hub";
    activeTab.value = "general";
    modalError.value = "";
    form.value = emptyPageForm();
    modalLoading.value = false;
    modalOpen.value = true;
}

function onTitleInput() {
    if (isCreateMode.value && !slugManuallyEdited.value) {
        form.value.slug = (form.value.title || "")
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, "")
            .replace(/[\s_-]+/g, "-")
            .replace(/^-+|-+$/g, "");
    }
}

/* =========================================================
   Delete Confirmation Dialog State
========================================================= */
const showDeleteDialog = ref(false);
const pageToDelete = ref(null);

/* =========================================================
   Toast Notification State
========================================================= */
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

/* =========================================================
   Form Normalization
========================================================= */
function emptyPageForm() {
    return {
        slug: "",
        title: "",
        icon: "hub",
        description: "",
        subtitle: "",
        hero_image: null,
        hero_image_url: "",
        is_active: true,
        badges: [],
        services: [],
        approach_title: "",
        approach_steps: [],
        why_us_title: "",
        why_us_points: [],
        industries_title: "",
        industries: [],
        cta: {
            headline: "",
            subheadline: "",
            button_text: "",
            button_link: "/contact",
        },
        seo: {
            meta_title: { en: "", ar: "" },
            meta_description: { en: "", ar: "" },
            og_title: { en: "", ar: "" },
            og_description: { en: "", ar: "" },
            og_image: "",
            og_image_file: null,
            canonical_url: "",
            is_noindex: false,
            is_nofollow: false,
            schema_type: "Service",
        },
    };
}

function normalizePageData(raw, fallbackSlug = "", fallbackName = "") {
    if (!raw) return emptyPageForm();

    const targetSlug = raw.slug || fallbackSlug;
    const def = defaultSolutionPages.find((p) => p.slug === targetSlug);
    const iconVal = raw.icon || def?.icon || editingIcon.value || "hub";
    const descVal = raw.description || raw.subtitle || "";

    const rawSeo = raw.seo_raw || raw.seo || {};

    return {
        slug: targetSlug,
        title: raw.title || fallbackName || "",
        icon: iconVal,
        description: descVal,
        subtitle: raw.subtitle || descVal,
        hero_image: raw.hero_image || null,
        hero_image_url: raw.hero_image_url || (typeof raw.hero_image === "string" ? raw.hero_image : ""),
        is_active: raw.is_active !== undefined ? Boolean(raw.is_active) : true,
        badges: Array.isArray(raw.badges)
            ? raw.badges.map((b) => ({
                  title: b?.title || "",
                  description: b?.description || "",
                  icon: b?.icon || "",
              }))
            : [],
        services: Array.isArray(raw.services)
            ? raw.services.map((s) => ({
                  title: s?.title || "",
                  description: s?.description || "",
                  icon: s?.icon || "",
                  focus: s?.focus || "",
                  approach: s?.approach || "",
                  outcome: s?.outcome || "",
              }))
            : [],
        approach_title: raw.approach_title || "",
        approach_steps: Array.isArray(raw.approach_steps)
            ? raw.approach_steps.map((st, i) => ({
                  number: st?.number || String(i + 1).padStart(2, "0"),
                  title: st?.title || "",
                  icon: st?.icon || "",
              }))
            : [],
        why_us_title: raw.why_us_title || "",
        why_us_points: Array.isArray(raw.why_us_points)
            ? raw.why_us_points.map((p) => {
                  if (typeof p === "string") {
                      return { text: p, icon: "check" };
                  }
                  return {
                      text: p?.text || p?.title || p?.point || "",
                      icon: p?.icon || "check",
                  };
              })
            : [],
        industries_title: raw.industries_title || "",
        industries: Array.isArray(raw.industries)
            ? raw.industries.map((ind) => ({
                  title: ind?.title || "",
                  icon: ind?.icon || "",
              }))
            : [],
        cta: {
            headline: raw.cta?.headline || raw.cta_headline || "",
            subheadline: raw.cta?.subheadline || raw.cta_subheadline || "",
            button_text: raw.cta?.button_text || raw.cta_button_text || "",
            button_link: raw.cta?.button_link || raw.cta_button_link || "/contact",
        },
        seo: {
            meta_title: {
                en: rawSeo.meta_title?.en || (typeof rawSeo.meta_title === "string" ? rawSeo.meta_title : (raw.title || fallbackName || "")),
                ar: rawSeo.meta_title?.ar || "",
            },
            meta_description: {
                en: rawSeo.meta_description?.en || (typeof rawSeo.meta_description === "string" ? rawSeo.meta_description : (descVal || "")),
                ar: rawSeo.meta_description?.ar || "",
            },
            og_title: {
                en: rawSeo.og_title?.en || (typeof rawSeo.og_title === "string" ? rawSeo.og_title : ""),
                ar: rawSeo.og_title?.ar || "",
            },
            og_description: {
                en: rawSeo.og_description?.en || (typeof rawSeo.og_description === "string" ? rawSeo.og_description : ""),
                ar: rawSeo.og_description?.ar || "",
            },
            og_image: rawSeo.og_image || raw.hero_image_url || (typeof raw.hero_image === "string" ? raw.hero_image : "") || "",
            og_image_file: null,
            canonical_url: rawSeo.canonical_url || "",
            is_noindex: Boolean(rawSeo.is_noindex),
            is_nofollow: Boolean(rawSeo.is_nofollow),
            schema_type: rawSeo.schema_type || "Service",
        },
    };
}

/* =========================================================
   Load All Pages for Overview Grid
========================================================= */
async function loadAllPages() {
    overviewLoading.value = true;

    try {
        let loadedFromApi = false;
        try {
            const pages = await adminApi.getSolutionPages();
            if (Array.isArray(pages) && pages.length > 0) {
                solutionPagesList.value = pages.map((item) => {
                    const def = defaultSolutionPages.find((p) => p.slug === item.slug);
                    return {
                        id: item.id,
                        slug: item.slug,
                        name: item.title || def?.name || item.slug,
                        icon: item.icon || def?.icon || "hub",
                        title: item.title || def?.name || item.slug,
                        subtitle: item.subtitle || item.description || "",
                        hero_image: item.hero_image || "",
                        hero_image_url: item.hero_image_url || (typeof item.hero_image === "string" ? item.hero_image : ""),
                        badgesCount: Array.isArray(item.badges) ? item.badges.length : 0,
                        servicesCount: Array.isArray(item.services) ? item.services.length : 0,
                        stepsCount: Array.isArray(item.approach_steps) ? item.approach_steps.length : 0,
                        is_active: item.is_active !== undefined ? Boolean(item.is_active) : true,
                        loaded: true,
                        raw: item,
                    };
                });
                loadedFromApi = true;
            }
        } catch (err) {
            console.warn("Could not load solution pages list from admin API, attempting fallback:", err);
        }

        if (!loadedFromApi) {
            const promises = defaultSolutionPages.map(async (def) => {
                try {
                    const res = await adminApi.getSolutionPage(def.slug);
                    if (res) {
                        return {
                            slug: def.slug,
                            name: def.name,
                            icon: def.icon,
                            title: res.title || def.name,
                            subtitle: res.subtitle || "",
                            hero_image: res.hero_image || "",
                            hero_image_url: res.hero_image_url || (typeof res.hero_image === "string" ? res.hero_image : ""),
                            badgesCount: Array.isArray(res.badges) ? res.badges.length : 0,
                            servicesCount: Array.isArray(res.services) ? res.services.length : 0,
                            stepsCount: Array.isArray(res.approach_steps) ? res.approach_steps.length : 0,
                            is_active: res.is_active !== undefined ? Boolean(res.is_active) : true,
                            loaded: true,
                            raw: res,
                        };
                    }
                } catch (err) {
                    console.warn(`Could not load page for ${def.slug}:`, err);
                }

                return {
                    ...def,
                    title: def.name,
                    subtitle: "",
                    hero_image: "",
                    hero_image_url: "",
                    badgesCount: 0,
                    servicesCount: 0,
                    stepsCount: 0,
                    is_active: true,
                    loaded: false,
                    raw: null,
                };
            });

            const results = await Promise.all(promises);
            solutionPagesList.value = results;
        }
    } catch (error) {
        console.error("Failed to load solution pages overview:", error);
        notify("Error", "Failed to load solution pages overview.", "error");
    } finally {
        overviewLoading.value = false;
    }
}

/* =========================================================
   Filtered Cards
========================================================= */
const filteredPages = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return solutionPagesList.value;

    return solutionPagesList.value.filter((p) => {
        return (
            p.name?.toLowerCase().includes(q) ||
            p.slug?.toLowerCase().includes(q) ||
            p.title?.toLowerCase().includes(q) ||
            p.subtitle?.toLowerCase().includes(q)
        );
    });
});

/* =========================================================
   Open Edit Modal
========================================================= */
async function openEdit(pageItem) {
    isCreateMode.value = false;
    slugManuallyEdited.value = false;
    editingSlug.value = pageItem.slug;
    editingName.value = pageItem.name || pageItem.title || pageItem.slug;
    editingIcon.value = pageItem.icon || "hub";
    activeTab.value = "general";
    modalError.value = "";
    modalOpen.value = true;

    // Use already loaded data immediately for quick rendering
    if (pageItem.raw) {
        form.value = normalizePageData(pageItem.raw, pageItem.slug, pageItem.name);
    } else {
        form.value = emptyPageForm();
        form.value.slug = pageItem.slug;
        form.value.title = pageItem.name;
    }

    modalLoading.value = true;
    try {
        const fresh = await adminApi.getSolutionPage(pageItem.slug);
        if (fresh) {
            form.value = normalizePageData(fresh, pageItem.slug, pageItem.name);
            // Sync overview item
            const idx = solutionPagesList.value.findIndex((p) => p.slug === pageItem.slug);
            if (idx !== -1) {
                solutionPagesList.value[idx].raw = fresh;
                solutionPagesList.value[idx].loaded = true;
            }
        }
    } catch (err) {
        console.error("Failed to fetch fresh solution page data:", err);
    } finally {
        modalLoading.value = false;
    }
}

function closeModal(force = false) {
    if (saving.value && force !== true) return;
    modalOpen.value = false;
    modalError.value = "";
}

/* =========================================================
   Image Value Resolver for ImageInput
========================================================= */
function isBlobOrFile(val) {
    if (!val) return false;
    if (typeof Blob !== "undefined" && val instanceof Blob) return true;
    if (typeof File !== "undefined" && val instanceof File) return true;
    return typeof val === "object" && typeof val.size === "number" && typeof val.type === "string";
}

function getHeroImageValue() {
    if (!form.value) return "";
    if (isBlobOrFile(form.value.hero_image)) {
        return form.value.hero_image;
    }
    if (form.value.hero_image === "") {
        return "";
    }
    return form.value.hero_image_url || form.value.hero_image || "";
}

function onHeroImageUpdate(val) {
    form.value.hero_image = val;
    if (!val) {
        form.value.hero_image_url = "";
    }
}

/* =========================================================
   Save Solution Page (Handles JSON vs FormData)
========================================================= */
async function savePage() {
    if (!form.value) return;

    saving.value = true;
    modalError.value = "";

    try {
        if (!form.value.title?.trim()) {
            modalError.value = "Page Title is required.";
            activeTab.value = "general";
            notify("Validation Error", modalError.value, "error");
            saving.value = false;
            return;
        }

        const targetSlug = (form.value.slug || editingSlug.value || "").trim().toLowerCase();
        if (!targetSlug) {
            modalError.value = "Page Slug is required.";
            activeTab.value = "general";
            notify("Validation Error", modalError.value, "error");
            saving.value = false;
            return;
        }

        const defaultPage = defaultSolutionPages.find((p) => p.slug === targetSlug);
        const targetIcon = form.value.icon || editingIcon.value || defaultPage?.icon || "hub";
        const targetTitle = form.value.title?.trim() || editingName.value || targetSlug;
        const targetDescription = form.value.description?.trim() || form.value.subtitle?.trim() || targetTitle;
        const targetSubtitle = form.value.subtitle?.trim() || targetDescription;

        const hasNewImage = isBlobOrFile(form.value.hero_image);

        const cleanedServices = (form.value.services || []).map((s) => ({
            title: s?.title || "",
            description: s?.description || "",
            icon: s?.icon || "",
            focus: s?.focus || "",
            approach: s?.approach || "",
            outcome: s?.outcome || "",
        }));

        const cleanedWhyUsPoints = (form.value.why_us_points || []).map((p) => {
            if (typeof p === "string") {
                return { text: p, icon: "check" };
            }
            return {
                text: p?.text || p?.title || p?.point || "",
                icon: p?.icon || "check",
            };
        });

        let payload;

        const hasNewSeoImage = Boolean(form.value.seo?.og_image_file);

        if (hasNewImage || hasNewSeoImage) {
            const formData = new FormData();
            if (hasNewImage) {
                formData.append("hero_image", form.value.hero_image);
            }
            if (hasNewSeoImage) {
                formData.append("seo_og_image", form.value.seo.og_image_file);
            }
            if (form.value.seo) {
                formData.append("seo", JSON.stringify(form.value.seo));
            }
            formData.append("slug", targetSlug);
            formData.append("icon", targetIcon);
            formData.append("description", targetDescription);
            formData.append("title", targetTitle);
            formData.append("name", targetTitle);
            formData.append("subtitle", targetSubtitle);
            formData.append("badges", JSON.stringify(form.value.badges || []));
            formData.append("services", JSON.stringify(cleanedServices));
            formData.append("approach_title", form.value.approach_title || "");
            formData.append("approach_steps", JSON.stringify(form.value.approach_steps || []));
            formData.append("why_us_title", form.value.why_us_title || "");
            formData.append("why_us_points", JSON.stringify(cleanedWhyUsPoints));
            formData.append("industries_title", form.value.industries_title || "");
            formData.append("industries", JSON.stringify(form.value.industries || []));
            formData.append("cta_headline", form.value.cta?.headline || "");
            formData.append("cta_subheadline", form.value.cta?.subheadline || "");
            formData.append("cta_button_text", form.value.cta?.button_text || "");
            formData.append("cta_button_link", form.value.cta?.button_link || "/contact");
            formData.append("is_active", form.value.is_active ? "1" : "0");

            payload = formData;
        } else {
            payload = {
                slug: targetSlug,
                icon: targetIcon,
                description: targetDescription,
                title: targetTitle,
                name: targetTitle,
                subtitle: targetSubtitle,
                badges: form.value.badges || [],
                services: cleanedServices,
                approach_title: form.value.approach_title || "",
                approach_steps: form.value.approach_steps || [],
                why_us_title: form.value.why_us_title || "",
                why_us_points: cleanedWhyUsPoints,
                industries_title: form.value.industries_title || "",
                industries: form.value.industries || [],
                cta: {
                    headline: form.value.cta?.headline || "",
                    subheadline: form.value.cta?.subheadline || "",
                    button_text: form.value.cta?.button_text || "",
                    button_link: form.value.cta?.button_link || "/contact",
                },
                cta_headline: form.value.cta?.headline || "",
                cta_subheadline: form.value.cta?.subheadline || "",
                cta_button_text: form.value.cta?.button_text || "",
                cta_button_link: form.value.cta?.button_link || "/contact",
                is_active: Boolean(form.value.is_active),
                seo: form.value.seo || undefined,
            };
        }

        let response;
        if (isCreateMode.value) {
            response = await adminApi.createSolutionPage(payload);
            notify("Created Successfully", `"${targetTitle}" has been created.`, "success");
        } else {
            response = await adminApi.updateSolutionPage(editingSlug.value, payload);
            notify("Saved Successfully", `"${targetTitle}" has been updated.`, "success");
        }

        // Update form state with returned persisted hero_image and hero_image_url
        if (response) {
            form.value.hero_image = response.hero_image || null;
            form.value.hero_image_url = response.hero_image_url || (typeof response.hero_image === "string" ? response.hero_image : "");
            if (response.icon) form.value.icon = response.icon;
            if (response.description) form.value.description = response.description;
        }

        await loadAllPages();
        closeModal(true);
    } catch (error) {
        console.error("Failed to save solution page:", error);
        let errorMsg = error?.message || "Failed to save solution page. Please try again.";
        if (error?.errors && typeof error.errors === "object") {
            const details = Object.values(error.errors).flat().filter(Boolean).join(" ");
            if (details) errorMsg = details;
        }
        modalError.value = errorMsg;
        notify(isCreateMode.value ? "Create Failed" : "Save Failed", modalError.value, "error");
    } finally {
        saving.value = false;
    }
}

/* =========================================================
   Delete Handlers
========================================================= */
function askDelete(pageItem = null) {
    pageToDelete.value = pageItem || { slug: editingSlug.value, name: editingName.value };
    showDeleteDialog.value = true;
}

async function confirmDelete() {
    if (!pageToDelete.value?.slug) return;

    const slug = pageToDelete.value.slug;
    const pageName = pageToDelete.value.name || pageToDelete.value.title || slug;
    showDeleteDialog.value = false;
    deleting.value = true;

    try {
        await adminApi.deleteSolutionPage(slug);

        // If currently editing this page in modal, close modal
        if (modalOpen.value && editingSlug.value === slug) {
            closeModal(true);
        }

        // Re-sync all pages from backend
        await loadAllPages();

        notify("Page Deleted", `"${pageName}" has been deleted.`, "success");
    } catch (error) {
        console.error("Failed to delete solution page:", error);
        notify("Delete Failed", error?.message || "Failed to delete solution page.", "error");
        deleting.value = false;
        pageToDelete.value = null;
    }
}

/* =========================================================
   Dynamic Item Helpers
========================================================= */
function addBadge() {
    form.value.badges.push({ title: "", description: "", icon: "" });
}
function removeBadge(index) {
    form.value.badges.splice(index, 1);
}

function addService() {
    form.value.services.push({
        title: "",
        description: "",
        icon: "",
        focus: "",
        approach: "",
        outcome: "",
    });
}
function removeService(index) {
    form.value.services.splice(index, 1);
}

function addApproachStep() {
    form.value.approach_steps.push({
        number: String(form.value.approach_steps.length + 1).padStart(2, "0"),
        title: "",
        icon: "",
    });
}
function removeApproachStep(index) {
    form.value.approach_steps.splice(index, 1);
}

function addWhyPoint() {
    form.value.why_us_points.push({ text: "", icon: "check" });
}
function removeWhyPoint(index) {
    form.value.why_us_points.splice(index, 1);
}

function addIndustry() {
    form.value.industries.push({ title: "", icon: "" });
}
function removeIndustry(index) {
    form.value.industries.splice(index, 1);
}

/* =========================================================
   Keyboard Shortcuts (Escape closes modal)
========================================================= */
function handleKeydown(e) {
    if (e.key === "Escape" && modalOpen.value && !saving.value) {
        closeModal();
    }
}

onMounted(() => {
    loadAllPages();
    window.addEventListener("keydown", handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener("keydown", handleKeydown);
});
</script>

<template>
    <div class="mx-auto max-w-7xl space-y-6 pb-12">
        <!-- Header -->
        <AdminPageHeader
            title="Services & Solutions"
            subtitle="Manage comprehensive solution pages, value badges, process steps, and offerings."
            category="Solutions & Capabilities"
            icon="hub"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="loadAllPages"
                        :disabled="overviewLoading"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-sis-border bg-white px-3.5 py-2.5 text-sm font-semibold text-sis-text shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': overviewLoading }">sync</span>
                        Refresh
                    </button>

                    <button
                        type="button"
                        @click="openCreate"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
                    >
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Add Page
                    </button>
                </div>
            </template>
        </AdminPageHeader>

        <!-- Search & Metrics Toolbar -->
        <div class="flex flex-col gap-3 rounded-2xl border border-sis-border bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-md">
                <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">
                    search
                </span>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search solution pages by name or slug..."
                    class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-10 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/15"
                />
                <button
                    v-if="search"
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                    aria-label="Clear search"
                    @click="search = ''"
                >
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            <div class="flex items-center gap-2 text-sm text-slate-500">
                <span class="font-semibold text-slate-800">{{ filteredPages.length }}</span>
                <span>{{ filteredPages.length === 1 ? 'solution page' : 'solution pages' }}</span>
            </div>
        </div>

        <!-- Overview Cards Grid: Loading Skeletons -->
        <div v-if="overviewLoading" class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="n in 6"
                :key="n"
                class="animate-pulse rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="mb-4 flex items-center justify-between">
                    <div class="h-10 w-10 rounded-xl bg-slate-200"></div>
                    <div class="h-5 w-16 rounded-full bg-slate-200"></div>
                </div>
                <div class="mb-3 h-32 w-full rounded-xl bg-slate-200"></div>
                <div class="mb-2 h-5 w-3/4 rounded bg-slate-200"></div>
                <div class="mb-4 h-4 w-1/2 rounded bg-slate-100"></div>
                <div class="flex gap-2 border-t border-slate-100 pt-4">
                    <div class="h-9 flex-1 rounded-xl bg-slate-200"></div>
                    <div class="h-9 w-9 rounded-xl bg-slate-100"></div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-else-if="filteredPages.length === 0"
            class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm"
        >
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                <span class="material-symbols-outlined text-[28px]">search_off</span>
            </div>
            <h3 class="text-base font-semibold text-slate-900">No solution pages found</h3>
            <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">
                No solution pages match your search "{{ search }}". Try a different query.
            </p>
            <button
                type="button"
                @click="search = ''"
                class="mt-4 inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Clear Search
            </button>
        </div>

        <!-- Overview Cards Grid: Loaded Content -->
        <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            <article
                v-for="pageItem in filteredPages"
                :key="pageItem.slug"
                class="group flex flex-col justify-between rounded-2xl border border-sis-border bg-white p-5 shadow-sm transition-all duration-200 hover:border-sis-accent/40 hover:shadow-md"
            >
                <div>
                    <!-- Top Bar: Icon + Status -->
                    <div class="mb-3.5 flex items-center justify-between gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-blue-50/80 text-sis-accent transition-colors group-hover:bg-blue-600 group-hover:text-white">
                            <span class="material-symbols-outlined text-[22px]">{{ pageItem.icon || 'hub' }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="pageItem.is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200'"
                            >
                                <span class="h-1.5 w-1.5 rounded-full" :class="pageItem.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                {{ pageItem.is_active ? 'Active' : 'Draft' }}
                            </span>
                        </div>
                    </div>

                    <!-- Hero Image Thumbnail -->
                    <div class="relative mb-3.5 h-32 w-full overflow-hidden rounded-xl border border-slate-100 bg-slate-50">
                        <img
                            v-if="pageItem.hero_image_url || pageItem.hero_image"
                            :src="resolveImageUrl(pageItem.hero_image_url || pageItem.hero_image)"
                            :alt="pageItem.title"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                        />
                        <div v-else class="flex h-full w-full flex-col items-center justify-center gap-1.5 text-slate-400">
                            <span class="material-symbols-outlined text-[26px]">image</span>
                            <span class="text-xs font-medium">No hero image uploaded</span>
                        </div>
                    </div>

                    <!-- Title & Slug -->
                    <div class="mb-2">
                        <h3 class="text-base font-bold text-slate-900 transition-colors group-hover:text-sis-accent">
                            {{ pageItem.title || pageItem.name }}
                        </h3>
                        <p class="font-mono text-xs text-slate-400">
                            /{{ pageItem.slug }}
                        </p>
                    </div>

                    <!-- Subtitle / Excerpt -->
                    <p class="line-clamp-2 min-h-[2.5rem] text-xs leading-5 text-slate-500">
                        {{ pageItem.subtitle || 'No description provided yet.' }}
                    </p>

                    <!-- Feature Badges -->
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                            <span class="material-symbols-outlined text-[14px] text-sis-accent">verified</span>
                            {{ pageItem.badgesCount }} {{ pageItem.badgesCount === 1 ? 'Badge' : 'Badges' }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                            <span class="material-symbols-outlined text-[14px] text-blue-500">tune</span>
                            {{ pageItem.servicesCount }} {{ pageItem.servicesCount === 1 ? 'Service' : 'Services' }}
                        </span>
                        <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                            <span class="material-symbols-outlined text-[14px] text-amber-500">route</span>
                            {{ pageItem.stepsCount }} {{ pageItem.stepsCount === 1 ? 'Step' : 'Steps' }}
                        </span>
                    </div>
                </div>

                <!-- Footer Card Actions -->
                <div class="mt-5 flex items-center gap-2 border-t border-slate-100 pt-4">
                    <button
                        type="button"
                        @click="openEdit(pageItem)"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-sis-accent active:scale-[0.98]"
                    >
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        Edit Page
                    </button>

                    <button
                        type="button"
                        @click="askDelete(pageItem)"
                        title="Delete Solution Page"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                    >
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </article>
        </div>

        <!-- =========================================================
             EDIT MODAL DIALOG
        ========================================================= -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="modalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-3 backdrop-blur-sm sm:p-5"
                    @click.self="closeModal"
                >
                    <div
                        class="flex max-h-[92vh] w-full max-w-5xl xl:max-w-6xl flex-col rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
                        role="dialog"
                        aria-modal="true"
                    >
                        <!-- Modal Header -->
                        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-blue-50 text-sis-accent">
                                    <span class="material-symbols-outlined text-[20px]">{{ form.icon || editingIcon || 'hub' }}</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-lg font-bold text-slate-900">
                                            {{ isCreateMode ? 'Add Solution Page' : 'Edit Solution Page' }}
                                        </h2>
                                        <span v-if="!isCreateMode" class="rounded-md bg-slate-100 px-2 py-0.5 font-mono text-xs font-semibold text-slate-600">
                                            {{ editingSlug }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        {{ isCreateMode ? 'Create a new solution page and mega-menu entry' : editingName }}
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="closeModal"
                                :disabled="saving"
                                class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 disabled:opacity-50"
                                aria-label="Close modal"
                            >
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <!-- Modal Tabs Navigation -->
                        <div class="flex flex-wrap items-center gap-1 border-b border-slate-200 bg-slate-50/75 px-6 py-2">
                            <button
                                type="button"
                                @click="activeTab = 'general'"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                :class="activeTab === 'general' ? 'bg-white text-sis-accent shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/60 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">info</span>
                                General & Hero
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'badges'"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                :class="activeTab === 'badges' ? 'bg-white text-sis-accent shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/60 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">verified</span>
                                Badges ({{ form.badges.length }})
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'services'"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                :class="activeTab === 'services' ? 'bg-white text-sis-accent shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/60 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">tune</span>
                                Services ({{ form.services.length }})
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'process'"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                :class="activeTab === 'process' ? 'bg-white text-sis-accent shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/60 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">route</span>
                                Process ({{ form.approach_steps.length }})
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'why_industries'"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                :class="activeTab === 'why_industries' ? 'bg-white text-sis-accent shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/60 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">domain</span>
                                Why SIS & Industries
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'cta'"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                :class="activeTab === 'cta' ? 'bg-white text-sis-accent shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/60 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">call_to_action</span>
                                CTA
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'seo'"
                                class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                                :class="activeTab === 'seo' ? 'bg-white text-sis-accent shadow-sm ring-1 ring-slate-200' : 'text-slate-600 hover:bg-white/60 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">travel_explore</span>
                                Google SEO & Social Media
                            </button>
                        </div>

                        <!-- Modal Error Banner -->
                        <div v-if="modalError" class="mx-6 mt-4 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-medium text-red-700">
                            <span class="material-symbols-outlined text-[18px]">error</span>
                            <span>{{ modalError }}</span>
                        </div>

                        <!-- Modal Scrollable Content -->
                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            <!-- Loading Indicator inside modal -->
                            <div v-if="modalLoading" class="flex min-h-[300px] flex-col items-center justify-center gap-3">
                                <div class="h-9 w-9 animate-spin rounded-full border-4 border-slate-200 border-t-sis-accent"></div>
                                <p class="text-xs font-medium text-slate-500">Loading page details...</p>
                            </div>

                            <form v-else @submit.prevent="savePage" class="space-y-6">
                                <!-- TAB 1: General & Hero -->
                                <div v-show="activeTab === 'general'" class="space-y-5">
                                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">Page Visibility</p>
                                            <p class="text-xs text-slate-500">Enable to display this solution page on the public website and mega menu.</p>
                                        </div>
                                        <button
                                            type="button"
                                            class="relative h-6 w-11 rounded-full transition"
                                            :class="form.is_active ? 'bg-slate-900' : 'bg-slate-300'"
                                            @click="form.is_active = !form.is_active"
                                        >
                                            <span
                                                class="absolute top-1 h-4 w-4 rounded-full bg-white shadow-sm transition"
                                                :class="form.is_active ? 'left-6' : 'left-1'"
                                            ></span>
                                        </button>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-800">
                                                Page Title <span class="text-red-500">*</span>
                                            </label>
                                            <input
                                                v-model="form.title"
                                                @input="onTitleInput"
                                                type="text"
                                                placeholder="e.g. Industrial Automation"
                                                required
                                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/15"
                                            />
                                        </div>

                                        <div>
                                            <label class="mb-2 block text-sm font-semibold text-slate-800">
                                                Page Slug <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 font-mono text-xs text-slate-400">/</span>
                                                <input
                                                    v-model="form.slug"
                                                    @input="slugManuallyEdited = true"
                                                    :disabled="!isCreateMode"
                                                    type="text"
                                                    placeholder="e.g. industrial-automation"
                                                    required
                                                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-7 pr-4 font-mono text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/15 disabled:bg-slate-100 disabled:text-slate-500"
                                                />
                                            </div>
                                            <p v-if="!isCreateMode" class="mt-1 text-[11px] text-slate-400">
                                                Slug is preserved for existing canonical routes.
                                            </p>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-800">
                                            Page Icon <span class="text-red-500">*</span>
                                        </label>
                                        <IconPicker v-model="form.icon" placeholder="Select solution page icon..." />
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-semibold text-slate-800">
                                            Subtitle / Overview <span class="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            v-model="form.subtitle"
                                            rows="3"
                                            placeholder="Enter brief description or tagline displayed in the mega menu and overview"
                                            class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/15"
                                        ></textarea>
                                    </div>

                                    <div>
                                        <ImageInput
                                            :model-value="getHeroImageValue()"
                                            @update:model-value="onHeroImageUpdate"
                                            label="Hero Image"
                                        />
                                    </div>
                                </div>

                                <!-- TAB 2: Value Badges -->
                                <div v-show="activeTab === 'badges'" class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900">Value Badges</h3>
                                            <p class="text-xs text-slate-500">Key statistics or differentiators displayed on the solution page.</p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="addBadge"
                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            Add Badge
                                        </button>
                                    </div>

                                    <div v-if="form.badges.length === 0" class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-400">
                                        No badges added yet. Click "+ Add Badge" to highlight key metrics.
                                    </div>

                                    <div
                                        v-for="(badge, index) in form.badges"
                                        :key="index"
                                        class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 transition hover:border-slate-300"
                                    >
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-700">Badge {{ index + 1 }}</span>
                                            <button
                                                type="button"
                                                @click="removeBadge(index)"
                                                class="text-xs font-semibold text-red-600 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>

                                        <div class="grid gap-3 md:grid-cols-3">
                                            <input
                                                v-model="badge.title"
                                                type="text"
                                                placeholder="Title (e.g. 99.9% Uptime)"
                                                class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                            />
                                            <input
                                                v-model="badge.description"
                                                type="text"
                                                placeholder="Description"
                                                class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                            />
                                            <IconPicker v-model="badge.icon" placeholder="Badge icon" />
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 3: Services -->
                                <div v-show="activeTab === 'services'" class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900">Capabilities & Services</h3>
                                            <p class="text-xs text-slate-500">Service offerings specific to this solution domain.</p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="addService"
                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            Add Service
                                        </button>
                                    </div>

                                    <div v-if="form.services.length === 0" class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-400">
                                        No services added yet. Click "+ Add Service" to add capabilities.
                                    </div>

                                    <div
                                        v-for="(service, index) in form.services"
                                        :key="index"
                                        class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 transition hover:border-slate-300"
                                    >
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-700">Service {{ index + 1 }}</span>
                                            <button
                                                type="button"
                                                @click="removeService(index)"
                                                class="text-xs font-semibold text-red-600 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>

                                            <div class="grid gap-3">
                                                <div class="grid gap-3 md:grid-cols-2">
                                                    <input
                                                        v-model="service.title"
                                                        type="text"
                                                        placeholder="Service title"
                                                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                                    />
                                                    <IconPicker v-model="service.icon" placeholder="Service icon" />
                                                </div>
                                                <textarea
                                                    v-model="service.description"
                                                    rows="2"
                                                    placeholder="Service description"
                                                    class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                                ></textarea>

                                                <div class="grid gap-3 md:grid-cols-3">
                                                    <div>
                                                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">Focus</label>
                                                        <input
                                                            v-model="service.focus"
                                                            type="text"
                                                            placeholder="e.g. Expert delivery"
                                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">Approach</label>
                                                        <input
                                                            v-model="service.approach"
                                                            type="text"
                                                            placeholder="e.g. Practical guidance"
                                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                                        />
                                                    </div>
                                                    <div>
                                                        <label class="mb-1 block text-[11px] font-semibold text-slate-600">Outcome</label>
                                                        <input
                                                            v-model="service.outcome"
                                                            type="text"
                                                            placeholder="e.g. Long-term value"
                                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <!-- TAB 4: Process -->
                                <div v-show="activeTab === 'process'" class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-sm font-bold text-slate-900">Process & Methodology</h3>
                                            <p class="text-xs text-slate-500">Step-by-step approach taken for this solution area.</p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="addApproachStep"
                                            class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            Add Step
                                        </button>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Section Title</label>
                                        <input
                                            v-model="form.approach_title"
                                            type="text"
                                            placeholder="Process section title (e.g. Our Structured Implementation)"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                        />
                                    </div>

                                    <div
                                        v-for="(step, index) in form.approach_steps"
                                        :key="index"
                                        class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 transition hover:border-slate-300"
                                    >
                                        <div class="mb-3 flex items-center justify-between">
                                            <span class="text-xs font-bold text-slate-700">Step {{ index + 1 }}</span>
                                            <button
                                                type="button"
                                                @click="removeApproachStep(index)"
                                                class="text-xs font-semibold text-red-600 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>

                                        <div class="grid gap-3 md:grid-cols-3">
                                            <input
                                                v-model="step.number"
                                                type="text"
                                                placeholder="Number (e.g. 01)"
                                                class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                            />
                                            <input
                                                v-model="step.title"
                                                type="text"
                                                placeholder="Step title"
                                                class="rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                            />
                                            <IconPicker v-model="step.icon" placeholder="Step icon" />
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 5: Why SIS & Industries -->
                                <div v-show="activeTab === 'why_industries'" class="space-y-6">
                                    <!-- Why SIS Section -->
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-sm font-bold text-slate-900">Why Choose SIS</h3>
                                                <p class="text-xs text-slate-500">Key reasons and value propositions for this solution.</p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="addWhyPoint"
                                                class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                            >
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                                Add Point
                                            </button>
                                        </div>

                                        <input
                                            v-model="form.why_us_title"
                                            type="text"
                                            placeholder="Why SIS section title"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                        />

                                        <div class="space-y-2.5">
                                            <div
                                                v-for="(point, index) in form.why_us_points"
                                                :key="index"
                                                class="flex items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50/80 p-2.5 transition hover:border-slate-300"
                                            >
                                                <div class="w-48 flex-shrink-0">
                                                    <IconPicker v-model="point.icon" placeholder="Icon" />
                                                </div>
                                                <input
                                                    v-model="point.text"
                                                    type="text"
                                                    :placeholder="`Reason / Point ${index + 1}`"
                                                    class="flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                                />
                                                <button
                                                    type="button"
                                                    @click="removeWhyPoint(index)"
                                                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl border border-red-200 text-red-500 hover:bg-red-50 transition"
                                                    title="Remove point"
                                                >
                                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Industries Served -->
                                    <div class="space-y-3 border-t border-slate-200 pt-5">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="text-sm font-bold text-slate-900">Industries Served</h3>
                                                <p class="text-xs text-slate-500">Target sectors where this solution is deployed.</p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="addIndustry"
                                                class="inline-flex items-center gap-1 rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800"
                                            >
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                                Add Industry
                                            </button>
                                        </div>

                                        <input
                                            v-model="form.industries_title"
                                            type="text"
                                            placeholder="Industries section title"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                        />

                                        <div class="grid gap-3 md:grid-cols-2">
                                            <div
                                                v-for="(industry, index) in form.industries"
                                                :key="index"
                                                class="rounded-xl border border-slate-200 bg-slate-50/80 p-3"
                                            >
                                                <div class="mb-2 flex items-center justify-between">
                                                    <span class="text-xs font-bold text-slate-700">Industry {{ index + 1 }}</span>
                                                    <button
                                                        type="button"
                                                        @click="removeIndustry(index)"
                                                        class="text-xs font-semibold text-red-600 hover:text-red-700"
                                                    >
                                                        Remove
                                                    </button>
                                                </div>

                                                <div class="space-y-2">
                                                    <input
                                                        v-model="industry.title"
                                                        type="text"
                                                        placeholder="Industry title"
                                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                                    />
                                                    <IconPicker v-model="industry.icon" placeholder="Industry icon" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 6: Call To Action (CTA) -->
                                <div v-show="activeTab === 'cta'" class="space-y-4">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Call To Action (CTA)</h3>
                                        <p class="text-xs text-slate-500">Configure the lead generation banner at the bottom of the page.</p>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Headline</label>
                                        <input
                                            v-model="form.cta.headline"
                                            type="text"
                                            placeholder="Ready to accelerate your operations?"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                        />
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Subheadline</label>
                                        <textarea
                                            v-model="form.cta.subheadline"
                                            rows="2"
                                            placeholder="Contact our specialist team today for tailored engineering solutions."
                                            class="w-full resize-y rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                        ></textarea>
                                    </div>

                                    <div class="grid gap-3 md:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">Button Text</label>
                                            <input
                                                v-model="form.cta.button_text"
                                                type="text"
                                                placeholder="Request Consultation"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                            />
                                        </div>

                                        <div>
                                            <label class="mb-1.5 block text-xs font-semibold text-slate-700">Button Link</label>
                                            <input
                                                v-model="form.cta.button_link"
                                                type="text"
                                                placeholder="/contact"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- TAB 7: SEO & SOCIAL METADATA -->
                                <div v-show="activeTab === 'seo'" class="space-y-4">
                                    <SeoFormSection
                                        v-model="form.seo"
                                        :title="form.title ? `${typeof form.title === 'object' ? (form.title.en || form.title.ar) : form.title} SEO` : (editingName ? `${editingName} SEO` : 'Solution SEO')"
                                        :fallback-title="form.title || editingName"
                                        :fallback-description="form.description || form.subtitle"
                                        :fallback-image="form.hero_image_url || form.hero_image"
                                        :page-path="`solution-pages/${form.slug || editingSlug}`"
                                    />
                                </div>
                            </form>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50/50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <button
                                    v-if="!isCreateMode"
                                    type="button"
                                    @click="askDelete({ slug: editingSlug, name: editingName })"
                                    :disabled="saving || deleting"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    {{ deleting ? 'Deleting...' : 'Delete Page' }}
                                </button>
                            </div>

                            <div class="flex items-center justify-end gap-3">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    :disabled="saving"
                                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="button"
                                    @click="savePage"
                                    :disabled="saving || modalLoading"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-6 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <span v-if="saving" class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
                                    <span>{{ saving ? (isCreateMode ? 'Creating...' : 'Saving Changes...') : (isCreateMode ? 'Create Page' : 'Save Changes') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            v-model="showDeleteDialog"
            title="Delete Solution Page"
            :message="`Are you sure you want to delete '${pageToDelete?.name || 'this solution page'}'? This action cannot be undone.`"
            confirm-text="Delete"
            cancel-text="Cancel"
            :danger="true"
            icon="delete"
            @confirm="confirmDelete"
            @cancel="showDeleteDialog = false"
        />

        <!-- Global Toast Notification -->
        <ToastNotification
            v-if="showToast"
            :title="toast.title"
            :message="toast.message"
            :variant="toast.variant"
            @close="showToast = false"
        />
    </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>