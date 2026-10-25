<script setup>
import { ref, computed, onMounted } from "vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import IconPicker from "@/SIS-Dashboard/components/IconPicker.vue";
import SeoFormSection from "@/SIS-Dashboard/components/seo/SeoFormSection.vue";
import { adminAPI, isMock } from "@/SIS-Dashboard/api/index.js";
import { resolveImageUrl } from "@/utils/imageUrl.js";

/* =========================================================
   General State
========================================================= */

const loading = ref(true);
const saving = ref(false);

const projects = ref([]);
const sectors = ref([]);
const services = ref([]);
const failedImages = ref(new Set());

function handleImageError(id) {
    failedImages.value.add(id);
}

const search = ref("");
const sectorFilter = ref("");

const modalOpen = ref(false);
const modalTab = ref("overview"); // 'overview' | 'case_study' | 'seo'
const editingId = ref(null);
const previewUrl = ref(null);

const form = ref(emptyForm());
const galleryPreviews = ref([]);

const showToast = ref(false);
const toast = ref({
    title: "",
    message: "",
    variant: "success",
});

const confirmState = ref({
    open: false,
    project: null,
    title: "",
    message: "",
});

/* =========================================================
   Use Cases Page Settings (Header, Stats, Pillars)
========================================================= */

const pageLoading = ref(true);
const pageSaving = ref(false);

const pageSettings = ref({
    title: "Projects & Case Studies",
    subtitle: "Real-world automation and digital transformation projects delivered across multiple industries using leading technologies.",
});

const stats = ref([
    {
        value: "500+",
        label: "Projects Delivered",
        icon: "inventory_2",
    },
    {
        value: "250+",
        label: "Happy Clients",
        icon: "groups",
    },
    {
        value: "15+",
        label: "Years of Experience",
        icon: "calendar_today",
    },
    {
        value: "12+",
        label: "Countries Served",
        icon: "public",
    },
]);

const pillars = ref([
    {
        title: "Innovation",
        description: "Driving smarter solutions with cutting-edge technologies",
        icon: "diamond",
    },
    {
        title: "Reliability",
        description: "Proven performance and long-term partnership",
        icon: "verified_user",
    },
    {
        title: "Global Reach",
        description: "Delivering value across 12+ countries",
        icon: "public",
    },
    {
        title: "Sustainability",
        description: "Building a smarter and cleaner tomorrow",
        icon: "eco",
    },
]);

/* =========================================================
   Project Form
========================================================= */

function emptyForm() {
    return {
        title: "",
        client_name: "",
        sector_id: "",
        location: "",
        summary: "",
        description: "",
        tags: "",
        gallery_limit: 4,

        service_ids: [],

        // Case Study Details
        scope: [],
        technologies: [],
        results: [],
        metrics: [],

        cover_image: null,
        images: [],
        gallery_files: [],
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

/* =========================================================
   Computed
========================================================= */

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();

    return projects.value.filter((p) => {
        const matchesSearch =
            !q ||
            [p.title, p.client_name, p.location, p.slug]
                .filter(Boolean)
                .some((v) => String(v).toLowerCase().includes(q));

        const matchesSector =
            !sectorFilter.value ||
            String(p.sector?.id ?? p.sector_id) === String(sectorFilter.value);

        return matchesSearch && matchesSector;
    });
});

const sectorName = (id) =>
    sectors.value.find((s) => String(s.id) === String(id))?.name || "";

/* =========================================================
   Toast
========================================================= */

function notify(title, message, variant = "success") {
    toast.value = {
        title,
        message,
        variant,
    };

    showToast.value = true;
}

/* =========================================================
   Load Projects / Sectors / Services
========================================================= */

async function load() {
    loading.value = true;

    try {
        const [p, s, sv] = await Promise.allSettled([
            adminAPI.getProjects({
                per_page: 100,
            }),

            adminAPI.getSectors(),

            adminAPI.getItems("services"),
        ]);

        /* Projects */

        if (p.status === "fulfilled") {
            projects.value = Array.isArray(p.value?.projects)
                ? p.value.projects
                : [];

            if (!projects.value.length) {
                notify(
                    "No projects",
                    "Backend returned no projects.",
                    "error",
                );
            }
        } else {
            notify(
                "Load failed",
                p.reason?.message || "Could not load projects.",
                "error",
            );
        }

        /* Sectors */

        if (s.status === "fulfilled") {
            sectors.value = Array.isArray(s.value?.industries)
                ? s.value.industries
                : Array.isArray(s.value?.sectors)
                    ? s.value.sectors
                    : Array.isArray(s.value)
                        ? s.value
                        : [];
        }

        /* Services */

        if (sv.status === "fulfilled") {
            services.value = Array.isArray(sv.value)
                ? sv.value
                : [];
        }
    } finally {
        loading.value = false;
    }
}

/* =========================================================
   Load Use Cases Header / Stats
/* =========================================================
   Load Use Cases Page Settings (Header, Stats, Pillars)
========================================================= */

async function loadPageSettings() {
    pageLoading.value = true;

    try {
        let res = null;
        try {
            res = await adminAPI.getUseCasesPage();
        } catch {
            const publicRes = await adminAPI.getUseCaseProjects();
            res = publicRes?.header || publicRes;
        }

        const data = res?.data || res;
        if (data?.title) {
            pageSettings.value.title = data.title;
        }
        if (data?.subtitle) {
            pageSettings.value.subtitle = data.subtitle;
        }

        if (Array.isArray(data?.stats) && data.stats.length) {
            stats.value = data.stats.map((s) => ({
                value: s?.value || "",
                label: s?.label || "",
                icon: s?.icon || "",
            }));
        }

        const rawPillars = Array.isArray(data?.pillars) && data.pillars.length
            ? data.pillars
            : (Array.isArray(data?.header?.pillars) ? data.header.pillars : null);

        if (rawPillars) {
            pillars.value = rawPillars.map((p) => ({
                title: p?.title || "",
                description: p?.description || "",
                icon: p?.icon || "",
            }));
        }
    } catch (e) {
        console.error("Failed to load Use Cases page settings:", e);
    } finally {
        pageLoading.value = false;
    }
}

/* =========================================================
   Save Page Settings (Header, Stats, Pillars)
========================================================= */

async function savePageSettings() {
    pageSaving.value = true;

    try {
        const payload = {
            title: pageSettings.value.title.trim(),
            subtitle: pageSettings.value.subtitle.trim(),
            stats: stats.value.map((s) => ({
                value: String(s.value || "").trim(),
                label: String(s.label || "").trim(),
                icon: String(s.icon || "").trim(),
            })),
            pillars: pillars.value.map((p) => ({
                title: String(p.title || "").trim(),
                description: String(p.description || "").trim(),
                icon: String(p.icon || "").trim(),
            })),
            is_active: true,
        };

        await adminAPI.updateUseCasesPage(payload);

        notify(
            "Settings saved",
            "Use Cases page header, statistics, and pillars saved successfully.",
            "success",
        );

        await loadPageSettings();
    } catch (e) {
        console.error("Failed to save Use Cases page settings:", e);
        notify(
            "Save failed",
            e?.message || "Could not save Use Cases settings.",
            "error",
        );
    } finally {
        pageSaving.value = false;
    }
}

function addPillar() {
    pillars.value.push({
        title: "",
        description: "",
        icon: "diamond",
    });
}

function removePillar(index) {
    if (pillars.value.length <= 1) {
        notify("Warning", "At least one pillar is required.", "warning");
        return;
    }
    pillars.value.splice(index, 1);
}

function addStat() {
    if (stats.value.length >= 6) {
        notify("Limit reached", "Maximum 6 statistics allowed.", "warning");
        return;
    }
    stats.value.push({
        value: "",
        label: "",
        icon: "analytics",
    });
}

function removeStat(index) {
    if (stats.value.length <= 1) {
        notify("Warning", "At least one statistic is required.", "warning");
        return;
    }
    stats.value.splice(index, 1);
}

/* =========================================================
   Project Create / Edit
========================================================= */

function healMetrics(rawMetrics) {
    if (!Array.isArray(rawMetrics)) return [];
    const healed = [];
    for (let i = 0; i < rawMetrics.length; i++) {
        const curr = rawMetrics[i] || {};
        const val = String(curr.value || "").trim();
        const lbl = String(curr.label || "").trim();

        if (val && !lbl && rawMetrics[i + 1]) {
            const next = rawMetrics[i + 1] || {};
            const nextVal = String(next.value || "").trim();
            const nextLbl = String(next.label || "").trim();
            if (!nextVal && nextLbl) {
                healed.push({ value: val, label: nextLbl });
                i++;
                continue;
            }
        }

        if (val || lbl) {
            healed.push({ value: val, label: lbl });
        }
    }
    return healed;
}

function openCreate() {
    form.value = emptyForm();
    editingId.value = null;
    previewUrl.value = null;
    galleryPreviews.value = [];
    modalTab.value = "overview";
    modalOpen.value = true;
}

function setProjectForm(p) {
    const rawImages = Array.isArray(p.images)
        ? p.images
              .map((img) =>
                  typeof img === "string" ? img : img?.url || img?.image_path || ""
              )
              .filter(Boolean)
        : [];

    let extractedLimit = 4;
    if (p.gallery_limit && Number(p.gallery_limit) > 0) {
        extractedLimit = Number(p.gallery_limit);
    }

    const rawTagsList = Array.isArray(p.tags)
        ? p.tags
        : typeof p.tags === "string"
            ? p.tags.split(",")
            : [];

    const cleanTags = [];
    for (const t of rawTagsList) {
        const trimmed = String(t).trim();
        if (!trimmed) continue;
        if (trimmed.startsWith("gallery_limit:")) {
            const parsed = parseInt(trimmed.replace("gallery_limit:", "").trim(), 10);
            if (!isNaN(parsed) && parsed > 0 && !p.gallery_limit) {
                extractedLimit = parsed;
            }
        } else {
            cleanTags.push(trimmed);
        }
    }

    form.value = {
        title: p.title || "",
        client_name: p.client_name || "",

        sector_id: String(
            p.sector?.id ??
            p.sector_id ??
            "",
        ),

        location: p.location || "",
        summary: p.summary || "",
        description: p.description || "",

        tags: cleanTags.join(", "),
        gallery_limit: extractedLimit,

        service_ids: Array.isArray(p.services)
            ? p.services.map((s) => s.id)
            : Array.isArray(p.service_ids)
                ? p.service_ids
                : [],

        // Case Study Details
        scope: Array.isArray(p.scope)
            ? [...p.scope]
            : [],

        technologies: Array.isArray(p.technologies)
            ? [...p.technologies]
            : [],

        results: Array.isArray(p.results)
            ? [...p.results]
            : [],

        metrics: Array.isArray(p.metrics)
            ? healMetrics(p.metrics)
            : [],

        cover_image: null,
        images: [...rawImages],
        gallery_files: [],
        seo: {
            meta_title: {
                en: p.seo_raw?.meta_title?.en || (typeof p.seo_raw?.meta_title === "string" ? p.seo_raw.meta_title : (p.title || "")),
                ar: p.seo_raw?.meta_title?.ar || "",
            },
            meta_description: {
                en: p.seo_raw?.meta_description?.en || (typeof p.seo_raw?.meta_description === "string" ? p.seo_raw.meta_description : (p.summary || "")),
                ar: p.seo_raw?.meta_description?.ar || "",
            },
            og_title: {
                en: p.seo_raw?.og_title?.en || (typeof p.seo_raw?.og_title === "string" ? p.seo_raw.og_title : ""),
                ar: p.seo_raw?.og_title?.ar || "",
            },
            og_description: {
                en: p.seo_raw?.og_description?.en || (typeof p.seo_raw?.og_description === "string" ? p.seo_raw.og_description : ""),
                ar: p.seo_raw?.og_description?.ar || "",
            },
            og_image: p.seo_raw?.og_image || (p.cover_image ? resolveImageUrl(p.cover_image) : "") || "",
            og_image_file: null,
            canonical_url: p.seo_raw?.canonical_url || "",
            is_noindex: Boolean(p.seo_raw?.is_noindex),
            is_nofollow: Boolean(p.seo_raw?.is_nofollow),
            schema_type: p.seo_raw?.schema_type || "Service",
        },
    };

    galleryPreviews.value = rawImages.map((url) => ({
        type: "existing",
        url: resolveImageUrl(url),
        rawUrl: url,
    }));

    editingId.value = p.id;
    previewUrl.value = p.cover_image ? resolveImageUrl(p.cover_image) : null;
}

async function openEdit(p) {
    setProjectForm(p);
    modalTab.value = "overview";
    modalOpen.value = true;

    // Fetch fresh project details to ensure gallery images and relations are fully up-to-date
    if (p?.slug || p?.id) {
        try {
            const fresh = await adminAPI.getProject(p.slug || p.id);
            const freshData = fresh?.data || fresh;
            if (freshData && modalOpen.value && editingId.value === p.id) {
                if (!form.value.gallery_files.length && !form.value.cover_image) {
                    setProjectForm(freshData);
                }
            }
        } catch {
            // Silently fall back to initial project data
        }
    }
}

/* =========================================================
   Image
========================================================= */

function onFile(e) {
    const file = e.target.files?.[0];

    if (!file) return;

    form.value.cover_image = isMock
        ? URL.createObjectURL(file)
        : file;

    previewUrl.value = isMock
        ? form.value.cover_image
        : URL.createObjectURL(file);
}

function onGalleryFiles(e) {
    const files = Array.from(e.target.files || []);

    if (!files.length) return;

    form.value.gallery_files.push(...files);

    galleryPreviews.value.push(
        ...files.map((file) => ({
            type: "new",
            file,
            url: window.URL.createObjectURL(file),
        })),
    );

    e.target.value = "";
}

function removeGalleryImage(index) {
    const preview = galleryPreviews.value[index];

    if (!preview) return;

    if (preview.type === "existing") {
        const targetRaw = preview.rawUrl || preview.url;
        const targetResolved = preview.url;
        const imageIndex = form.value.images.findIndex(
            (u) => u === targetRaw || resolveImageUrl(u) === targetResolved
        );

        if (imageIndex !== -1) {
            form.value.images.splice(imageIndex, 1);
        }
    }

    if (preview.type === "new") {
        const fileIndex = form.value.gallery_files.indexOf(preview.file);

        if (fileIndex !== -1) {
            form.value.gallery_files.splice(fileIndex, 1);
        }

        window.URL.revokeObjectURL(preview.url);
    }

    galleryPreviews.value.splice(index, 1);
}

/* =========================================================
   Services
========================================================= */

function toggleService(id) {
    const list = form.value.service_ids;

    const idx = list.indexOf(id);

    if (idx === -1) {
        list.push(id);
    } else {
        list.splice(idx, 1);
    }
}

/* =========================================================
   Case Study Details
========================================================= */

function addScope() {
    form.value.scope.push("");
}

function removeScope(index) {
    form.value.scope.splice(index, 1);
}

function addTechnology() {
    form.value.technologies.push("");
}

function removeTechnology(index) {
    form.value.technologies.splice(index, 1);
}

function addResult() {
    form.value.results.push("");
}

function removeResult(index) {
    form.value.results.splice(index, 1);
}

function addMetric() {
    form.value.metrics.push({
        value: "",
        label: "",
    });
}

function removeMetric(index) {
    form.value.metrics.splice(index, 1);
}

/* =========================================================
   Save Project
========================================================= */

async function save() {
    if (
        !form.value.title.trim() ||
        !form.value.client_name.trim() ||
        !form.value.sector_id
    ) {
        notify(
            "Missing fields",
            "Title, client name and sector are required.",
            "error",
        );

        return;
    }

    if (form.value.summary.trim().length < 5) {
        notify(
            "Summary too short",
            "Please write a summary of at least 5 characters.",
            "error",
        );

        return;
    }

    saving.value = true;

    const limitNum = Math.max(1, Number(form.value.gallery_limit) || 4);
    const userTags = form.value.tags
        .split(",")
        .map((t) => t.trim())
        .filter((t) => t && !t.startsWith("gallery_limit:"));

    const tagsWithLimit = [...userTags, `gallery_limit:${limitNum}`];

    const payload = {
        title: form.value.title.trim(),

        client_name:
            form.value.client_name.trim(),

        sector_id:
            Number(form.value.sector_id),

        location:
            form.value.location.trim(),

        summary:
            form.value.summary.trim(),

        description:
            form.value.description.trim(),

        tags: tagsWithLimit,

        gallery_limit: limitNum,

        service_ids:
            form.value.service_ids,

        // Case Study Details
        scope:
            form.value.scope
                .map((item) => String(item || "").trim())
                .filter(Boolean),

        technologies:
            form.value.technologies
                .map((item) => String(item || "").trim())
                .filter(Boolean),

        results:
            form.value.results
                .map((item) => String(item || "").trim())
                .filter(Boolean),

        metrics:
            form.value.metrics
                .map((metric) => ({
                    value: String(metric.value || "").trim(),
                    label: String(metric.label || "").trim(),
                }))
                .filter(
                    (metric) =>
                        metric.value || metric.label,
                ),
    };

    if (form.value.cover_image) {
        payload.cover_image =
            form.value.cover_image;
    }

    if (editingId.value) {
        // Send kept existing image URLs under 'images' (strings)
        payload.images = (form.value.images || []).filter(
            (img) => typeof img === "string" && img.trim()
        );
        // Send new files separately under 'gallery_images' so PHP files/post arrays never collide
        if (form.value.gallery_files?.length) {
            payload.gallery_images = form.value.gallery_files;
        }
    } else {
        if (form.value.gallery_files?.length) {
            payload.images = form.value.gallery_files;
            payload.gallery_images = form.value.gallery_files;
        }
    }

    if (form.value.seo) {
        payload.seo = form.value.seo;
        if (form.value.seo.og_image_file) {
            payload.seo_og_image = form.value.seo.og_image_file;
        }
    }

    try {
        if (editingId.value) {
            await adminAPI.updateUseCase(
                editingId.value,
                payload,
            );

            notify(
                "Project updated",
                `"${payload.title}" saved.`,
                "success",
            );
        } else {
            const created =
                await adminAPI.createUseCase(payload);

            notify(
                "Project created",
                `"${created?.title || payload.title}" added.`,
                "success",
            );
        }

        modalOpen.value = false;

        await load();
    } catch (e) {
        notify(
            "Save failed",
            e.message || "Could not save project.",
            "error",
        );
    } finally {
        saving.value = false;
    }
}

/* =========================================================
   Delete Project
========================================================= */

function remove(p) {
    confirmState.value = {
        open: true,
        project: p,
        title: "Delete project?",
        message:
            `"${p.title}" will be permanently removed. This cannot be undone.`,
    };
}

async function onDeleteConfirmed() {
    const { project: p } = confirmState.value;

    if (!p) return;

    try {
        await adminAPI.deleteUseCase(p.id);

        notify(
            "Project deleted",
            `"${p.title}" removed.`,
            "success",
        );

        await load();
    } catch (e) {
        notify(
            "Delete failed",
            e.message || "Could not delete project.",
            "error",
        );
    }
}

/* =========================================================
   Initial Load
========================================================= */

onMounted(async () => {
    await Promise.all([
        loadPageSettings(),
        load(),
    ]);
});
</script>

<template>
    <div class="space-y-8">

        <!-- =====================================================
         Use Cases Page Content (Header, Metrics & Pillars)
    ====================================================== -->
        <section class="rounded-2xl border border-sis-border bg-white p-6 shadow-sis dark:border-slate-800 dark:bg-[#071426]">
            <!-- Top Header & Save Button -->
            <div class="flex flex-col gap-4 border-b border-sis-border pb-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sis-accent/10 text-sis-accent">
                        <span class="material-symbols-outlined text-[21px]">
                            auto_stories
                        </span>
                    </div>
                    <div>
                        <h2 class="font-headline-lg text-lg font-bold tracking-tight text-sis-text dark:text-white">
                            Page Content & Core Pillars
                        </h2>
                        <p class="text-xs text-sis-muted dark:text-slate-400">
                            Manage the headline, subtitle, metric counters, and key pillars shown on /use-cases.
                        </p>
                    </div>
                </div>

                <button
                    @click="savePageSettings"
                    :disabled="pageSaving || pageLoading"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sis-light disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': pageSaving }">
                        {{ pageSaving ? 'progress_activity' : 'save' }}
                    </span>
                    {{ pageSaving ? 'Saving...' : 'Save Page Content' }}
                </button>
            </div>

            <!-- Loading State -->
            <div v-if="pageLoading" class="py-12 flex justify-center items-center gap-3 text-sis-muted">
                <span class="material-symbols-outlined animate-spin text-2xl text-sis-accent">progress_activity</span>
                <span class="text-sm font-medium">Loading page settings...</span>
            </div>

            <div v-else class="mt-6 space-y-8">
                <!-- 1. Header Information -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-sis-muted dark:text-slate-400">
                        Header Information
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-sis-text dark:text-slate-300">
                                Page Title
                            </label>
                            <input
                                v-model="pageSettings.title"
                                type="text"
                                placeholder="Projects & Case Studies"
                                class="w-full rounded-xl border border-sis-border bg-slate-50/50 px-3.5 py-2.5 text-sm text-sis-text outline-none transition focus:border-sis-accent focus:bg-white dark:border-slate-700 dark:bg-slate-900/60 dark:text-white"
                            />
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-semibold text-sis-text dark:text-slate-300">
                                Subtitle / Description
                            </label>
                            <textarea
                                v-model="pageSettings.subtitle"
                                rows="2"
                                placeholder="Real-world automation and digital transformation projects..."
                                class="w-full resize-none rounded-xl border border-sis-border bg-slate-50/50 px-3.5 py-2 text-sm text-sis-text outline-none transition focus:border-sis-accent focus:bg-white dark:border-slate-700 dark:bg-slate-900/60 dark:text-white"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Top Statistics Counters -->
                <div class="space-y-4 border-t border-sis-border pt-6 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-sis-muted dark:text-slate-400">
                                Top Stat Counters
                            </h3>
                            <p class="text-xs text-sis-muted">
                                Key metrics displayed in the header badge grid on the public page.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addStat"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-sis-border px-3 py-1.5 text-xs font-medium text-sis-text hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                        >
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            Add Metric
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="(st, idx) in stats"
                            :key="idx"
                            class="relative rounded-xl border border-sis-border bg-slate-50/60 p-4 transition-all hover:border-sis-accent/40 dark:border-slate-800 dark:bg-slate-900/40"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sis-muted uppercase">Stat #{{ idx + 1 }}</span>
                                <button
                                    type="button"
                                    @click="removeStat(idx)"
                                    class="text-slate-400 hover:text-red-500 transition"
                                    title="Remove metric"
                                >
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-sis-muted">Icon</label>
                                    <IconPicker v-model="st.icon" placeholder="Select icon..." />
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-sis-muted">Value (e.g. 500+)</label>
                                    <input
                                        v-model="st.value"
                                        type="text"
                                        placeholder="500+"
                                        class="w-full rounded-lg border border-sis-border bg-white px-3 py-1.5 text-sm text-sis-text outline-none focus:border-sis-accent dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-sis-muted">Label</label>
                                    <input
                                        v-model="st.label"
                                        type="text"
                                        placeholder="Projects Delivered"
                                        class="w-full rounded-lg border border-sis-border bg-white px-3 py-1.5 text-sm text-sis-text outline-none focus:border-sis-accent dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Core Pillars (Value Points) -->
                <div class="space-y-4 border-t border-sis-border pt-6 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold uppercase tracking-wider text-sis-muted dark:text-slate-400">
                                Core Pillars (Value Points)
                            </h3>
                            <p class="text-xs text-sis-muted">
                                Key pillars (Innovation, Reliability, Global Reach, Sustainability) displayed across the page.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="addPillar"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-sis-border px-3 py-1.5 text-xs font-medium text-sis-text hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                        >
                            <span class="material-symbols-outlined text-[16px]">add</span>
                            Add Pillar
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="(pillar, idx) in pillars"
                            :key="idx"
                            class="relative rounded-xl border border-sis-border bg-slate-50/60 p-4 transition-all hover:border-sis-accent/40 dark:border-slate-800 dark:bg-slate-900/40"
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sis-muted uppercase">Pillar #{{ idx + 1 }}</span>
                                <button
                                    type="button"
                                    @click="removePillar(idx)"
                                    class="text-slate-400 hover:text-red-500 transition"
                                    title="Remove pillar"
                                >
                                    <span class="material-symbols-outlined text-[18px]">close</span>
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-sis-muted">Icon</label>
                                    <IconPicker v-model="pillar.icon" placeholder="Select icon..." />
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-sis-muted">Title</label>
                                    <input
                                        v-model="pillar.title"
                                        type="text"
                                        placeholder="e.g. Innovation"
                                        class="w-full rounded-lg border border-sis-border bg-white px-3 py-1.5 text-sm text-sis-text outline-none focus:border-sis-accent dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                    />
                                </div>
                                <div>
                                    <label class="mb-1 block text-[11px] font-medium text-sis-muted">Description</label>
                                    <textarea
                                        v-model="pillar.description"
                                        rows="2"
                                        placeholder="Short description..."
                                        class="w-full resize-none rounded-lg border border-sis-border bg-white px-3 py-1.5 text-xs text-sis-text outline-none focus:border-sis-accent dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- =====================================================
         Projects / Case Studies
    ====================================================== -->

        <section class="space-y-6">

            <!-- Header -->
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>

                    <div class="flex items-center gap-2.5">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-sis-accent/10 text-sis-accent">
                            <span class="material-symbols-outlined text-[21px]">
                                folder
                            </span>
                        </div>

                        <h2 class="font-headline-lg text-xl font-bold tracking-tight text-sis-text">
                            Projects & Case Studies
                        </h2>

                    </div>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-sis-muted">
                        Case studies shown on the website. Create, edit or delete projects.
                    </p>

                </div>

                <button @click="openCreate"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:bg-sis-light hover:shadow-md">
                    <span class="material-symbols-outlined text-[18px]">
                        add
                    </span>

                    New Project
                </button>

            </div>

            <!-- Search / Filters -->
            <div
                class="flex flex-col gap-3 rounded-2xl border border-sis-border bg-white p-3 shadow-sis sm:flex-row sm:items-center">

                <div class="relative min-w-0 w-full flex-1 sm:min-w-[220px]">
                    <span
                        class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-sis-muted">
                        search
                    </span>

                    <input v-model="search" type="text" placeholder="Search by title, client, location…"
                        class="w-full rounded-xl border border-sis-border bg-sis-panel/30 py-2.5 pl-10 pr-10 text-sm text-sis-text transition-all placeholder:text-sis-muted/70 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

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

                <select v-model="sectorFilter"
                    class="rounded-xl border border-sis-border bg-sis-panel/30 px-3 py-2.5 text-sm text-sis-text transition-all focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10">
                    <option value="">
                        All sectors
                    </option>

                    <option v-for="s in sectors" :key="s.id" :value="String(s.id)">
                        {{ s.name }}
                    </option>
                </select>

                <div class="flex shrink-0 items-center gap-2 px-1 text-xs text-sis-muted">
                    <span class="h-1.5 w-1.5 rounded-full bg-sis-accent"></span>

                    {{ filtered.length }} / {{ projects.length }} shown
                </div>

            </div>

            <!-- Loading -->
            <div v-if="loading" class="space-y-3">
                <div v-for="n in 5" :key="n"
                    class="animate-pulse rounded-2xl border border-sis-border bg-white p-4 shadow-sis">
                    <div class="flex items-center gap-4">

                        <div class="h-14 w-20 rounded-lg bg-sis-panel"></div>

                        <div class="flex-1 space-y-2">

                            <div class="h-3.5 w-1/3 rounded bg-sis-panel"></div>

                            <div class="h-3 w-1/4 rounded bg-sis-panel"></div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Projects Table -->
            <div v-else-if="filtered.length"
                class="overflow-hidden rounded-2xl border border-sis-border bg-white shadow-sis">
                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1000px] table-fixed text-left text-sm">

                        <!-- Table Header -->
                        <thead
                            class="border-b border-sis-border bg-sis-panel/50 text-[10px] uppercase tracking-[0.12em] text-sis-muted">
                            <tr>

                                <th class="w-[28%] px-5 py-3.5 font-semibold">
                                    Project
                                </th>

                                <th class="w-[17%] px-5 py-3.5 font-semibold">
                                    Client
                                </th>

                                <th class="w-[14%] px-5 py-3.5 font-semibold">
                                    Sector
                                </th>

                                <th class="w-[15%] px-5 py-3.5 font-semibold">
                                    Location
                                </th>

                                <th class="w-[9%] px-5 py-3.5 text-right font-semibold">
                                    Actions
                                </th>

                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-sis-border/70">

                            <tr v-for="p in filtered" :key="p.id" class="group transition-colors hover:bg-sis-panel/40">

                                <!-- Project -->
                                <td class="px-5 py-4">

                                    <div class="flex min-w-0 items-center gap-3">

                                        <img v-if="p.cover_image && !failedImages.has(p.id)"
                                            :src="resolveImageUrl(p.cover_image)"
                                            :alt="p.title"
                                            @error="handleImageError(p.id)"
                                            class="h-24 w-24 flex-shrink-0 rounded-lg object-contain bg-sis-panel p-1 ring-1 ring-black/5" />

                                        <span v-else
                                            class="flex h-12 w-20 flex-shrink-0 items-center justify-center rounded-lg bg-sis-accent/10 text-xs font-bold text-sis-accent ring-1 ring-sis-accent/10">
                                            {{ (p.title || "UC").slice(0, 2).toUpperCase() }}
                                        </span>

                                        <div class="min-w-0">

                                            <p class="truncate font-semibold text-sis-text" :title="p.title">
                                                {{ p.title }}
                                            </p>

                                            <p class="mt-0.5 truncate text-xs text-sis-muted" :title="p.slug">
                                                /{{ p.slug }}
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <!-- Client -->
                                <td class="px-5 py-4">

                                    <p class="truncate text-sis-text" :title="p.client_name">
                                        {{ p.client_name || "—" }}
                                    </p>

                                </td>

                                <!-- Sector -->
                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex max-w-full items-center rounded-full border border-sis-accent/10 bg-sis-accent/5 px-2.5 py-1 text-[11px] font-semibold text-sis-accent"
                                        :title="p.sector?.name ||
                                            sectorName(p.sector_id) ||
                                            '—'
                                            ">
                                        <span class="truncate">
                                            {{
                                                p.sector?.name ||
                                                sectorName(p.sector_id) ||
                                                "—"
                                            }}
                                        </span>
                                    </span>

                                </td>

                                <!-- Location -->
                                <td class="px-5 py-4">

                                    <p class="truncate text-sis-text" :title="p.location">
                                        {{ p.location || "—" }}
                                    </p>

                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-1.5">

                                        <button @click="openEdit(p)" title="Edit"
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-transparent bg-blue-100 text-sis-accent transition-all">
                                            <span class="material-symbols-outlined !text-2xl">
                                                edit
                                            </span>
                                        </button>

                                        <button @click="remove(p)" title="Delete"
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md border border-transparent bg-red-100 text-red-600 transition-all">
                                            <span class="material-symbols-outlined !text-2xl">
                                                delete
                                            </span>
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>
            </div>

            <!-- Empty State -->
            <div v-else
                class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-sis-border bg-white px-6 py-14 text-center shadow-sis">

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sis-panel text-sis-muted/60">
                    <span class="material-symbols-outlined text-[28px]">
                        folder_off
                    </span>
                </div>

                <p class="mt-4 text-sm font-semibold text-sis-text">
                    No projects found
                </p>

                <p class="mt-1 max-w-sm text-xs leading-5 text-sis-muted">
                    No projects match your current search or sector filter.
                </p>

                <button @click="openCreate"
                    class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-sis-accent transition-colors hover:text-sis-light">
                    <span class="material-symbols-outlined text-[17px]">
                        add
                    </span>

                    Create project
                </button>

            </div>

        </section>

        <!-- =====================================================
         Create / Edit Project Modal
    ====================================================== -->

        <Teleport to="body">

            <div v-if="modalOpen" class="fixed inset-0 z-[999] flex items-center justify-center p-4">

                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="modalOpen = false"></div>

                <!-- Modal -->
                <div
                    class="relative flex max-h-[92vh] w-full max-w-5xl xl:max-w-6xl flex-col overflow-hidden rounded-3xl border border-sis-border bg-white shadow-[0_24px_80px_rgba(0,0,0,0.18)]">

                    <!-- Modal Header -->
                    <div
                        class="sticky top-0 z-20 border-b border-sis-border bg-white/95 px-6 pt-4 pb-3 backdrop-blur">

                        <div class="flex items-center justify-between pb-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-sis-accent">
                                    Project / Case Study
                                </p>

                                <h3 class="mt-0.5 font-headline-md text-lg font-bold text-sis-text">
                                    {{
                                        editingId
                                            ? "Edit Project"
                                            : "New Project"
                                    }}
                                </h3>
                            </div>

                            <button @click="modalOpen = false"
                                class="flex h-9 w-9 items-center justify-center rounded-xl border border-sis-border text-sis-muted transition-all hover:bg-sis-panel hover:text-sis-text">
                                <span class="material-symbols-outlined text-[20px]">
                                    close
                                </span>
                            </button>
                        </div>

                        <!-- Modal Tabs Navigation -->
                        <div class="flex flex-wrap items-center gap-1.5 border-t border-slate-100 pt-2.5">
                            <button
                                type="button"
                                @click="modalTab = 'overview'"
                                class="flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition cursor-pointer"
                                :class="modalTab === 'overview' ? 'bg-sis-accent text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">info</span>
                                Project Overview
                            </button>

                            <button
                                type="button"
                                @click="modalTab = 'case_study'"
                                class="flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition cursor-pointer"
                                :class="modalTab === 'case_study' ? 'bg-sis-accent text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">article</span>
                                Case Study Deep Dive
                            </button>

                            <button
                                type="button"
                                @click="modalTab = 'seo'"
                                class="flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition cursor-pointer"
                                :class="modalTab === 'seo' ? 'bg-sis-accent text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                            >
                                <span class="material-symbols-outlined text-[16px]">travel_explore</span>
                                Google SEO & Social Media
                            </button>
                        </div>

                    </div>

                    <!-- Modal Body -->
                    <div class="flex-1 overflow-y-auto px-6 py-6">

                        <!-- TAB 1: PROJECT OVERVIEW -->
                        <div v-show="modalTab === 'overview'" class="grid gap-4 sm:grid-cols-2">

                            <!-- Title -->
                            <div class="sm:col-span-2">

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Title *
                                </label>

                                <input v-model="form.title" type="text" placeholder="e.g. Smart Factory Automation"
                                    class="w-full rounded-xl border border-sis-border bg-sis-panel/20 px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                            </div>

                            <!-- Client -->
                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Client name *
                                </label>

                                <input v-model="form.client_name" type="text" placeholder="e.g. Gulf Steel Co."
                                    class="w-full rounded-xl border border-sis-border bg-sis-panel/20 px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                            </div>

                            <!-- Sector -->
                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Sector *
                                </label>

                                <select v-model="form.sector_id"
                                    class="w-full rounded-xl border border-sis-border bg-sis-panel/20 px-3.5 py-2.5 text-sm text-sis-text transition-all focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10">

                                    <option value="">
                                        Select a sector…
                                    </option>

                                    <option v-for="s in sectors" :key="s.id" :value="String(s.id)">
                                        {{ s.name }}
                                    </option>

                                </select>

                            </div>

                            <!-- Location -->
                            <div class="sm:col-span-2">

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Location
                                </label>

                                <input v-model="form.location" type="text" placeholder="e.g. Riyadh, Saudi Arabia"
                                    class="w-full rounded-xl border border-sis-border bg-sis-panel/20 px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                            </div>

                            <!-- Summary -->
                            <div class="sm:col-span-2">

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Summary *
                                </label>

                                <textarea v-model="form.summary" rows="2"
                                    placeholder="One or two sentences shown in the project card."
                                    class="w-full resize-none rounded-xl border border-sis-border bg-sis-panel/20 px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10"></textarea>

                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Description
                                </label>

                                <textarea v-model="form.description" rows="4" placeholder="Full case-study description."
                                    class="w-full resize-none rounded-xl border border-sis-border bg-sis-panel/20 px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10"></textarea>

                            </div>

                            <!-- Cover Image -->
                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Cover image
                                </label>

                                <div class="flex items-center gap-3">

                                    <img v-if="previewUrl" :src="resolveImageUrl(previewUrl)"
                                        class="h-14 w-24 flex-shrink-0 rounded-lg object-cover ring-1 ring-black/5" />

                                    <span v-else
                                        class="flex h-14 w-24 flex-shrink-0 items-center justify-center rounded-lg bg-sis-panel text-xs text-sis-muted">
                                        no image
                                    </span>

                                    <label
                                        class="cursor-pointer rounded-xl border border-dashed border-sis-border bg-sis-panel/20 px-3 py-2.5 text-sm text-sis-muted transition-all hover:border-sis-accent hover:bg-sis-accent/5 hover:text-sis-text">
                                        {{
                                            form.cover_image
                                                ? "Replace"
                                                : "Upload"
                                        }}

                                        <input type="file" accept="image/*" class="hidden" @change="onFile" />
                                    </label>

                                </div>

                            </div>

                            <!-- Tags -->
                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Tags
                                </label>

                                <input v-model="form.tags" type="text" placeholder="comma, separated, tags"
                                    class="w-full rounded-xl border border-sis-border bg-sis-panel/20 px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                            </div>

                            <!-- Services -->
                            <div class="sm:col-span-2">

                                <label class="mb-2 block text-xs font-semibold uppercase tracking-wider text-sis-muted">
                                    Services
                                </label>

                                <div class="grid gap-2 sm:grid-cols-2">

                                    <label v-for="sv in services" :key="sv.id"
                                        class="flex cursor-pointer items-center gap-2 rounded-xl border px-3.5 py-2.5 text-sm transition-all"
                                        :class="form.service_ids.includes(sv.id)
                                            ? 'border-sis-accent bg-sis-accent/5 text-sis-text shadow-sm'
                                            : 'border-sis-border bg-white text-sis-muted hover:border-sis-accent/40 hover:bg-sis-panel/40'
                                            ">

                                        <input type="checkbox" :checked="form.service_ids.includes(sv.id)
                                            " class="accent-sis-accent" @change="toggleService(sv.id)" />

                                        {{ sv.title }}

                                    </label>

                                </div>

                            </div>

                        </div>

                        <!-- TAB 2: CASE STUDY DETAILS -->
                        <div v-show="modalTab === 'case_study'" class="space-y-6">

                            <!-- Scope of Work -->
                            <div class="rounded-2xl border border-sis-border bg-sis-panel/10 p-5">

                                <div class="mb-3 flex items-center justify-between">

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-wider text-sis-text">
                                            Scope of Work
                                        </label>

                                        <p class="mt-0.5 text-xs text-sis-muted">
                                            Main tasks or engineering deliverables completed in this project.
                                        </p>
                                    </div>

                                    <button type="button" @click="addScope"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-sis-border bg-white px-3 py-1.5 text-xs font-semibold text-sis-accent transition-all hover:border-sis-accent/40 hover:bg-sis-accent/5">
                                        <span class="material-symbols-outlined text-[16px]">
                                            add
                                        </span>

                                        Add Item
                                    </button>

                                </div>

                                <div v-if="form.scope.length" class="space-y-2">

                                    <div v-for="(item, index) in form.scope" :key="index"
                                        class="flex items-center gap-2">

                                        <input v-model="form.scope[index]" type="text"
                                            :placeholder="`Scope item ${index + 1}`"
                                            class="min-w-0 flex-1 rounded-xl border border-sis-border bg-white px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                                        <button type="button" @click="removeScope(index)"
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-sis-border text-sis-muted transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                            title="Remove">
                                            <span class="material-symbols-outlined text-[18px]">
                                                delete
                                            </span>
                                        </button>

                                    </div>

                                </div>

                                <div v-else
                                    class="rounded-xl border border-dashed border-sis-border bg-white/60 px-4 py-3 text-xs text-sis-muted">
                                    No scope items added yet. Click "+ Add Item" above.
                                </div>

                            </div>

                            <!-- Technologies -->
                            <div class="rounded-2xl border border-sis-border bg-sis-panel/10 p-5">

                                <div class="mb-3 flex items-center justify-between">

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-wider text-sis-text">
                                            Technologies & Hardware
                                        </label>

                                        <p class="mt-0.5 text-xs text-sis-muted">
                                            PLCs, SCADA platforms, sensors, or edge software utilized.
                                        </p>
                                    </div>

                                    <button type="button" @click="addTechnology"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-sis-border bg-white px-3 py-1.5 text-xs font-semibold text-sis-accent transition-all hover:border-sis-accent/40 hover:bg-sis-accent/5">
                                        <span class="material-symbols-outlined text-[16px]">
                                            add
                                        </span>

                                        Add Item
                                    </button>

                                </div>

                                <div v-if="form.technologies.length" class="space-y-2">

                                    <div v-for="(item, index) in form.technologies" :key="index"
                                        class="flex items-center gap-2">

                                        <input v-model="form.technologies[index]" type="text"
                                            :placeholder="`Technology ${index + 1}`"
                                            class="min-w-0 flex-1 rounded-xl border border-sis-border bg-white px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                                        <button type="button" @click="removeTechnology(index)"
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-sis-border text-sis-muted transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                            title="Remove">
                                            <span class="material-symbols-outlined text-[18px]">
                                                delete
                                            </span>
                                        </button>

                                    </div>

                                </div>

                                <div v-else
                                    class="rounded-xl border border-dashed border-sis-border bg-white/60 px-4 py-3 text-xs text-sis-muted">
                                    No technologies added yet. Click "+ Add Item" above.
                                </div>

                            </div>

                            <!-- Results -->
                            <div class="rounded-2xl border border-sis-border bg-sis-panel/10 p-5">

                                <div class="mb-3 flex items-center justify-between">

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-wider text-sis-text">
                                            Key Results & Outcomes
                                        </label>

                                        <p class="mt-0.5 text-xs text-sis-muted">
                                            Key operational outcomes, uptime gains, or safety improvements achieved.
                                        </p>
                                    </div>

                                    <button type="button" @click="addResult"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-sis-border bg-white px-3 py-1.5 text-xs font-semibold text-sis-accent transition-all hover:border-sis-accent/40 hover:bg-sis-accent/5">
                                        <span class="material-symbols-outlined text-[16px]">
                                            add
                                        </span>

                                        Add Item
                                    </button>

                                </div>

                                <div v-if="form.results.length" class="space-y-2">

                                    <div v-for="(item, index) in form.results" :key="index"
                                        class="flex items-center gap-2">

                                        <input v-model="form.results[index]" type="text"
                                            :placeholder="`Result ${index + 1}`"
                                            class="min-w-0 flex-1 rounded-xl border border-sis-border bg-white px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                                        <button type="button" @click="removeResult(index)"
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-sis-border text-sis-muted transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                            title="Remove">
                                            <span class="material-symbols-outlined text-[18px]">
                                                delete
                                            </span>
                                        </button>

                                    </div>

                                </div>

                                <div v-else
                                    class="rounded-xl border border-dashed border-sis-border bg-white/60 px-4 py-3 text-xs text-sis-muted">
                                    No results added yet. Click "+ Add Item" above.
                                </div>

                            </div>

                            <!-- Metrics -->
                            <div class="rounded-2xl border border-sis-border bg-sis-panel/10 p-5">

                                <div class="mb-3 flex items-center justify-between">

                                    <div>
                                        <label
                                            class="block text-xs font-bold uppercase tracking-wider text-sis-text">
                                            Key Metrics & KPIs
                                        </label>

                                        <p class="mt-0.5 text-xs text-sis-muted">
                                            Measurable stats highlighting project success (e.g. 30% Energy Reduction).
                                        </p>
                                    </div>

                                    <button type="button" @click="addMetric"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-sis-border bg-white px-3 py-1.5 text-xs font-semibold text-sis-accent transition-all hover:border-sis-accent/40 hover:bg-sis-accent/5">
                                        <span class="material-symbols-outlined text-[16px]">
                                            add
                                        </span>

                                        Add Metric
                                    </button>

                                </div>

                                <div v-if="form.metrics.length" class="space-y-2">

                                    <div v-for="(metric, index) in form.metrics" :key="index"
                                        class="grid grid-cols-[1fr_1.5fr_auto] gap-2">

                                        <input v-model="metric.value" type="text" placeholder="30%"
                                            class="min-w-0 rounded-xl border border-sis-border bg-white px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                                        <input v-model="metric.label" type="text" placeholder="Energy Reduction"
                                            class="min-w-0 rounded-xl border border-sis-border bg-white px-3.5 py-2.5 text-sm text-sis-text transition-all placeholder:text-sis-muted/60 focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />

                                        <button type="button" @click="removeMetric(index)"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-sis-border text-sis-muted transition-all hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                            title="Remove">
                                            <span class="material-symbols-outlined text-[18px]">
                                                delete
                                            </span>
                                        </button>

                                    </div>

                                </div>

                                <div v-else
                                    class="rounded-xl border border-dashed border-sis-border bg-white/60 px-4 py-3 text-xs text-sis-muted">
                                    No metrics added yet. Click "+ Add Metric" above.
                                </div>

                            </div>

                            <!-- Project Gallery -->
                            <div class="rounded-2xl border border-sis-border bg-sis-panel/10 p-5">

                                <label
                                    class="mb-1 block text-xs font-bold uppercase tracking-wider text-sis-text">
                                    Project Photo Gallery
                                </label>
                                <p class="mb-4 text-xs text-sis-muted">
                                    High-resolution field deployment and plant installation images.
                                </p>

                                <div class="rounded-2xl border border-dashed border-sis-border bg-white p-4">

                                    <div v-if="galleryPreviews.length"
                                        class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                        <div v-for="(image, index) in galleryPreviews"
                                            :key="`${image.type}-${index}-${image.url}`"
                                            class="group relative aspect-square overflow-hidden rounded-xl border border-sis-border bg-white">
                                            <img :src="resolveImageUrl(image.url)" :alt="`Project image ${index + 1}`"
                                                class="h-full w-full object-cover" />

                                            <button type="button" @click="removeGalleryImage(index)"
                                                class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/60 text-white opacity-0 transition group-hover:opacity-100"
                                                title="Remove image">
                                                <span class="material-symbols-outlined text-[16px]">
                                                    close
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <label
                                        class="flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-sis-border px-4 py-6 text-center transition-all hover:border-sis-accent hover:bg-sis-accent/5">

                                        <span class="material-symbols-outlined text-[28px] text-sis-accent">
                                            photo_library
                                        </span>

                                        <span class="mt-2 text-sm font-semibold text-sis-text">
                                            Add project images
                                        </span>

                                        <span class="mt-1 text-xs text-sis-muted">
                                            You can select multiple images at once
                                        </span>

                                        <input type="file" accept="image/*" multiple class="hidden"
                                            @change="onGalleryFiles" />

                                    </label>

                                    <!-- Visible Gallery Limit -->
                                    <div
                                        class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border border-sis-border bg-sis-panel/20 p-3.5">
                                        <div>
                                            <label class="block text-xs font-bold text-sis-text">
                                                Visible Images in Case Study
                                            </label>
                                            <p class="mt-0.5 text-[11px] text-sis-muted">
                                                Number of images shown in modal before "Want to see more?" card appears.
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input v-model.number="form.gallery_limit" type="number" min="1" max="50"
                                                class="w-20 rounded-lg border border-sis-border bg-white px-3 py-1.5 text-center text-sm font-semibold text-sis-text transition-all focus:border-sis-accent focus:bg-white focus:outline-none focus:ring-2 focus:ring-sis-accent/10" />
                                            <span class="text-xs text-sis-muted">images</span>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- TAB 3: SEO & SOCIAL METADATA -->
                        <div v-show="modalTab === 'seo'" class="space-y-4">
                            <SeoFormSection
                                v-model="form.seo"
                                :title="form.title ? `${form.title} SEO` : 'Project SEO'"
                                :fallback-title="form.title"
                                :fallback-description="form.summary"
                                :fallback-image="previewUrl"
                                :page-path="`projects/${editingId || ''}`"
                            />
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div
                        class="sticky bottom-0 flex items-center justify-end gap-3 border-t border-sis-border bg-white/95 px-6 py-4 backdrop-blur">

                        <button @click="modalOpen = false"
                            class="rounded-xl border border-sis-border bg-white px-4 py-2.5 text-sm font-medium text-sis-muted transition-all hover:bg-sis-panel hover:text-sis-text">
                            Cancel
                        </button>

                        <button @click="save" :disabled="saving"
                            class="flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-sis-light hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50">

                            <span v-if="saving" class="material-symbols-outlined animate-spin text-[18px]">
                                progress_activity
                            </span>

                            <span v-else class="material-symbols-outlined text-[18px]">
                                save
                            </span>

                            {{
                                saving
                                    ? "Saving…"
                                    : editingId
                                        ? "Save Changes"
                                        : "Create Project"
                            }}

                        </button>

                    </div>

                </div>

            </div>

        </Teleport>

        <!-- =====================================================
         Toast
    ====================================================== -->

        <ToastNotification v-if="showToast" :title="toast.title" :message="toast.message" :variant="toast.variant"
            @close="showToast = false" />

        <!-- =====================================================
         Confirm Dialog
    ====================================================== -->

        <ConfirmDialog v-model="confirmState.open" :title="confirmState.title" :message="confirmState.message"
            confirm-text="Delete" danger @confirm="onDeleteConfirmed" />

    </div>
</template>
