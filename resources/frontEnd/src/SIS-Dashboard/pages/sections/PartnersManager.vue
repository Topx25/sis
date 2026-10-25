<script setup>
import { computed, onMounted, ref } from "vue";
import { adminAPI } from "@/SIS-Dashboard/api";
import IconPicker from "@/SIS-Dashboard/components/IconPicker.vue";

const partners = ref([]);
const loading = ref(true);
const saving = ref(false);

const search = ref("");

const modalOpen = ref(false);
const editingItem = ref(null);

const deleteDialogOpen = ref(false);
const deletingItem = ref(null);

const formError = ref("");
const headerError = ref("");

const imageFile = ref(null);
const imagePreview = ref("");

const toast = ref({
    visible: false,
    message: "",
    type: "success",
});

const canCreate = true;
const canUpdate = true;
const canDelete = true;

/* =========================================================
   Trusted Partners Header
========================================================= */

const emptyPartnerHeader = () => ({
    title: "",
    subtitle: "",
    icon: "handshake",
    home_subtitle: "Our Partners",
    home_title: "Building stronger",
    home_title_highlight: "ecosystems",
    home_description: "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
    value_badges: [
        {
            title: "",
            description: "",
            icon: "",
        },
        {
            title: "",
            description: "",
            icon: "",
        },
        {
            title: "",
            description: "",
            icon: "",
        },
        {
            title: "",
            description: "",
            icon: "",
        },
    ],
});

const partnerHeader = ref(emptyPartnerHeader());

const headerModalOpen = ref(false);
const savingHeader = ref(false);
const headerForm = ref(emptyPartnerHeader());

/* =========================================================
   Home Page Section Header (Featured Partners)
========================================================= */

const homeHeaderModalOpen = ref(false);
const savingHomeHeader = ref(false);
const homeHeaderError = ref("");
const homeHeaderForm = ref({
    home_subtitle: "",
    home_title: "",
    home_title_highlight: "",
    home_description: "",
});

function openHomeHeaderModal() {
    if (!canUpdate) return;

    homeHeaderError.value = "";
    homeHeaderForm.value = {
        home_subtitle: partnerHeader.value.home_subtitle || "Our Partners",
        home_title: partnerHeader.value.home_title || "Building stronger",
        home_title_highlight: partnerHeader.value.home_title_highlight || "ecosystems",
        home_description: partnerHeader.value.home_description || "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
    };

    homeHeaderModalOpen.value = true;
}

function closeHomeHeaderModal() {
    if (savingHomeHeader.value) return;

    homeHeaderModalOpen.value = false;
    homeHeaderError.value = "";
}

async function saveHomeHeader() {
    homeHeaderError.value = "";

    if (!homeHeaderForm.value.home_title.trim()) {
        homeHeaderError.value = "Home section title is required.";
        return;
    }

    savingHomeHeader.value = true;

    try {
        const payload = {
            title: partnerHeader.value.title || "Our Trusted Partners",
            subtitle: partnerHeader.value.subtitle || "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
            icon: partnerHeader.value.icon || "handshake",
            value_badges: partnerHeader.value.value_badges || [],
            home_subtitle: homeHeaderForm.value.home_subtitle.trim(),
            home_title: homeHeaderForm.value.home_title.trim(),
            home_title_highlight: homeHeaderForm.value.home_title_highlight.trim(),
            home_description: homeHeaderForm.value.home_description.trim(),
        };

        const response = await adminAPI.updateTrustedPartnersPage(payload);

        const updatedHeader =
            response?.header ||
            response?.data?.header ||
            (response?.data && !Array.isArray(response.data) && response.data.title ? response.data : null) ||
            (response && !Array.isArray(response) && response.title ? response : null);

        setPartnerHeader(updatedHeader || payload);

        homeHeaderModalOpen.value = false;

        showToast("Home section header updated successfully.", "success");
    } catch (error) {
        console.error("Failed to update home section header:", error);

        homeHeaderError.value =
            error?.message || "Something went wrong while updating the home header.";

        showToast(
            error?.message || "Failed to update home section header.",
            "error",
        );
    } finally {
        savingHomeHeader.value = false;
    }
}

function extractText(val) {
    if (val === null || val === undefined) return "";
    if (typeof val === "string") return val;
    if (typeof val === "number") return String(val);
    if (typeof val === "object") {
        return val.en || val.ar || Object.values(val)[0] || "";
    }
    return String(val);
}

function setPartnerHeader(header) {
    if (!header) {
        partnerHeader.value = emptyPartnerHeader();
        return;
    }

    let badges = header?.value_badges;
    if (typeof badges === "string") {
        try {
            badges = JSON.parse(badges);
        } catch {
            badges = [];
        }
    }
    if (!Array.isArray(badges)) {
        badges = [];
    }

    partnerHeader.value = {
        id: header?.id || 1,
        title: extractText(header?.title),
        subtitle: extractText(header?.subtitle),
        icon: extractText(header?.icon) || "handshake",
        is_active: header?.is_active !== undefined ? Boolean(header.is_active) : true,
        home_subtitle: extractText(header?.home_subtitle) || "Our Partners",
        home_title: extractText(header?.home_title) || "Building stronger",
        home_title_highlight: extractText(header?.home_title_highlight) || "ecosystems",
        home_description: extractText(header?.home_description) || "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
        value_badges: Array.from({ length: 4 }, (_, index) => {
            const badge = badges[index] || {};

            return {
                title: extractText(badge.title),
                description: extractText(badge.description),
                icon: extractText(badge.icon) || "",
            };
        }),
    };
}

function openHeaderModal() {
    if (!canUpdate) return;

    headerError.value = "";

    // Create an independent copy so Cancel does not modify preview data.
    headerForm.value = {
        title: partnerHeader.value.title || "Our Trusted Partners",
        subtitle: partnerHeader.value.subtitle || "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
        icon: partnerHeader.value.icon || "handshake",
        is_active: partnerHeader.value.is_active !== undefined ? partnerHeader.value.is_active : true,
        home_subtitle: partnerHeader.value.home_subtitle || "Our Partners",
        home_title: partnerHeader.value.home_title || "Building stronger",
        home_title_highlight: partnerHeader.value.home_title_highlight || "ecosystems",
        home_description: partnerHeader.value.home_description || "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
        value_badges: partnerHeader.value.value_badges.map((badge, index) => ({
            title: badge.title || "",
            description: badge.description || "",
            icon: badge.icon || "",
        })),
    };

    headerModalOpen.value = true;
}

function closeHeaderModal() {
    if (savingHeader.value) return;

    headerModalOpen.value = false;
    headerError.value = "";
}

function buildHeaderPayload() {
    return {
        title: headerForm.value.title.trim(),
        subtitle: headerForm.value.subtitle.trim(),
        icon: headerForm.value.icon.trim(),
        home_subtitle: (headerForm.value.home_subtitle || "").trim(),
        home_title: (headerForm.value.home_title || "").trim(),
        home_title_highlight: (headerForm.value.home_title_highlight || "").trim(),
        home_description: (headerForm.value.home_description || "").trim(),
        value_badges: headerForm.value.value_badges.map((badge) => ({
            title: badge.title.trim(),
            description: badge.description.trim(),
            icon: badge.icon.trim(),
        })),
    };
}

async function saveHeader() {
    headerError.value = "";

    if (!headerForm.value.title.trim()) {
        headerError.value = "Header title is required.";
        return;
    }

    if (!headerForm.value.subtitle.trim()) {
        headerError.value = "Header subtitle is required.";
        return;
    }

    if (!headerForm.value.icon.trim()) {
        headerError.value = "Header icon is required.";
        return;
    }

    const hasInvalidBadge = headerForm.value.value_badges.some(
        (badge) =>
            !badge.title.trim() ||
            !badge.description.trim() ||
            !badge.icon.trim(),
    );

    if (hasInvalidBadge) {
        headerError.value =
            "All value badges must have a title, description, and icon.";
        return;
    }

    savingHeader.value = true;

    try {
        const payload = buildHeaderPayload();

        const response = await adminAPI.updateTrustedPartnersPage(payload);

        const updatedHeader =
            response?.header ||
            response?.data?.header ||
            (response?.data && !Array.isArray(response.data) && response.data.title ? response.data : null) ||
            (response && !Array.isArray(response) && response.title ? response : null);

        setPartnerHeader(updatedHeader || payload);

        headerModalOpen.value = false;

        showToast("Trusted Partners header updated successfully.", "success");
    } catch (error) {
        console.error("Failed to update Trusted Partners header:", error);

        headerError.value =
            error?.message || "Something went wrong while updating the header.";

        showToast(
            error?.message || "Failed to update Trusted Partners header.",
            "error",
        );
    } finally {
        savingHeader.value = false;
    }
}

/* =========================================================
   Partners
========================================================= */

const emptyForm = () => ({
    name: "",
    type: "technology_partner",
    website_url: "",
    tagline: "",
    description: "",
    glow: "#00d4ff",
    solutions_link: "/services",
    sort_order: 0,
    is_featured: false,
    src: null,
});

const form = ref(emptyForm());

const filteredPartners = computed(() => {
    const query = search.value.trim().toLowerCase();

    const items = [...partners.value];

    if (!query) {
        return items.sort(
            (a, b) => Number(a.sort_order ?? 0) - Number(b.sort_order ?? 0),
        );
    }

    return items
        .filter((partner) => {
            return [
                partner.name,
                partner.type,
                partner.website_url,
                partner.tagline,
                partner.description,
            ]
                .filter(Boolean)
                .some((value) => String(value).toLowerCase().includes(query));
        })
        .sort((a, b) => Number(a.sort_order ?? 0) - Number(b.sort_order ?? 0));
});

const featuredPartners = computed(() =>
    filteredPartners.value.filter((partner) => Boolean(partner.is_featured)),
);

const ecosystemPartners = computed(() =>
    filteredPartners.value.filter((partner) => !Boolean(partner.is_featured)),
);

function showToast(message, type = "success") {
    toast.value = {
        visible: true,
        message,
        type,
    };

    window.setTimeout(() => {
        toast.value.visible = false;
    }, 3000);
}

function resolveImageUrl(url) {
    if (!url) return "";
    return String(url);
}

function resetForm() {
    form.value = emptyForm();
    imageFile.value = null;
    imagePreview.value = "";
    formError.value = "";
}

function openCreateModal() {
    if (!canCreate) return;

    editingItem.value = null;
    resetForm();
    modalOpen.value = true;
}

function openEditModal(partner) {
    if (!canUpdate) return;

    editingItem.value = partner;

    form.value = {
        name: partner.name || "",
        type: partner.type || "technology_partner",
        website_url: partner.website_url || "",
        tagline: partner.tagline || "",
        description: partner.description || "",
        glow: partner.glow || "#00d4ff",
        solutions_link: partner.solutions_link || "/services",
        sort_order: Number(partner.sort_order ?? 0),
        is_featured: Boolean(partner.is_featured),
        src: null,
    };

    imageFile.value = null;
    imagePreview.value = resolveImageUrl(partner.logo_url);
    formError.value = "";

    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    editingItem.value = null;
    resetForm();
}

function handleImageChange(event) {
    const file = event.target.files?.[0];

    if (!file) {
        imageFile.value = null;
        return;
    }

    imageFile.value = file;

    if (imagePreview.value?.startsWith("blob:")) {
        URL.revokeObjectURL(imagePreview.value);
    }

    imagePreview.value = URL.createObjectURL(file);
}

function removeSelectedImage() {
    imageFile.value = null;

    if (imagePreview.value?.startsWith("blob:")) {
        URL.revokeObjectURL(imagePreview.value);
    }

    imagePreview.value = editingItem.value
        ? resolveImageUrl(editingItem.value.logo_url)
        : "";
}

function buildPayload() {
    const payload = {
        name: form.value.name,
        type: form.value.type,
        website_url: form.value.website_url || null,
        tagline: form.value.tagline || null,
        description: form.value.description || null,
        glow: form.value.glow || null,
        solutions_link: form.value.solutions_link || null,
        sort_order: Number(form.value.sort_order ?? 0),
        is_featured: Boolean(form.value.is_featured),
    };

    if (imageFile.value) {
        payload.src = imageFile.value;
    }

    return payload;
}

async function loadPartners() {
    loading.value = true;

    try {
        const response = await adminAPI.getPartners();

        let headerData =
            response?.header ||
            response?.data?.header ||
            (response?.data && !Array.isArray(response.data) && response.data.title ? response.data : null) ||
            (response && !Array.isArray(response) && response.title ? response : null);

        // Fallback: If headerData was not found in getPartners, try dedicated admin page endpoint if available
        if (!headerData?.title && typeof adminAPI.getTrustedPartnersPage === "function") {
            try {
                const pageRes = await adminAPI.getTrustedPartnersPage();
                if (pageRes?.title || pageRes?.header?.title || pageRes?.data?.title) {
                    headerData = pageRes?.header || pageRes?.data || pageRes;
                }
            } catch (pageErr) {
                console.warn("Could not fetch dedicated trusted partners page:", pageErr);
            }
        }

        setPartnerHeader(headerData);

        const featured = Array.isArray(response?.featured_partners)
            ? response.featured_partners
            : Array.isArray(response?.data?.featured_partners)
              ? response.data.featured_partners
              : [];

        const ecosystem = Array.isArray(response?.partners)
            ? response.partners
            : Array.isArray(response?.data?.partners)
              ? response.data.partners
              : Array.isArray(response)
                ? response
                : [];

        const allPartners = [...featured, ...ecosystem];

        partners.value = Array.from(
            new Map(
                allPartners.map((partner) => [partner.id, partner]),
            ).values(),
        );
    } catch (error) {
        console.error("Failed to load partners:", error);

        partners.value = [];

        setPartnerHeader(null);

        showToast(error?.message || "Failed to load partners.", "error");
    } finally {
        loading.value = false;
    }
}

async function save() {
    formError.value = "";

    if (!form.value.name.trim()) {
        formError.value = "Partner name is required.";
        return;
    }

    if (!editingItem.value && !imageFile.value) {
        formError.value = "Partner logo is required.";
        return;
    }

    saving.value = true;

    try {
        const payload = buildPayload();

        if (editingItem.value) {
            await adminAPI.updateItem(
                "partners",
                editingItem.value.id,
                payload,
            );

            showToast("Partner updated successfully.", "success");
        } else {
            await adminAPI.createItem("partner-ecosystem", payload);

            showToast("Partner created successfully.", "success");
        }

        closeModal();
        await loadPartners();
    } catch (error) {
        console.error("Failed to save partner:", error);

        formError.value =
            error?.message || "Something went wrong while saving the partner.";

        showToast(error?.message || "Failed to save partner.", "error");
    } finally {
        saving.value = false;
    }
}

function askDelete(partner) {
    if (!canDelete) return;

    deletingItem.value = partner;
    deleteDialogOpen.value = true;
}

function cancelDelete() {
    deleteDialogOpen.value = false;
    deletingItem.value = null;
}

async function confirmDelete() {
    if (!deletingItem.value || !canDelete) return;

    const partner = deletingItem.value;

    try {
        await adminAPI.deleteItem("partners", partner.id);

        showToast("Partner deleted successfully.", "success");

        cancelDelete();
        await loadPartners();
    } catch (error) {
        console.error("Failed to delete partner:", error);

        showToast(error?.message || "Failed to delete partner.", "error");
    }
}

onMounted(() => {
    loadPartners();
});
</script>

<template>
    <div class="space-y-8">
        <!-- Trusted Partners -->
        <section
            class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <!-- Section Header -->
            <div
                class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800"
            >
                <div>
                    <h2
                        class="text-xl font-bold text-slate-900 dark:text-white"
                    >
                        Trusted Partners
                    </h2>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Manage the heading and value badges displayed on the
                        Trusted Partners page.
                    </p>
                </div>

                <button
                    v-if="canUpdate"
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-blue-700 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
                    @click="openHeaderModal"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        edit
                    </span>

                    Edit
                </button>
            </div>

            <!-- Preview -->
            <div class="p-6">
                <div class="flex flex-col gap-8">
                    <!-- Title / Subtitle -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                        >
                            <span class="material-symbols-outlined text-[28px]">
                                {{ partnerHeader.icon || "handshake" }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <h3
                                class="text-xl font-bold text-slate-900 dark:text-white"
                            >
                                {{
                                    partnerHeader.title ||
                                    "Our Trusted Partners"
                                }}
                            </h3>

                            <p
                                class="mt-2 max-w-3xl text-sm leading-6 text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    partnerHeader.subtitle ||
                                    "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients."
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Value Badges -->
                    <div
                        class="grid grid-cols-1 gap-4 border-t border-slate-100 pt-6 sm:grid-cols-2 xl:grid-cols-4 dark:border-slate-800"
                    >
                        <div
                            v-for="(badge, index) in partnerHeader.value_badges"
                            :key="index"
                            class="flex items-start gap-3"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                            >
                                <span
                                    class="material-symbols-outlined text-[21px]"
                                >
                                    {{ badge.icon || "star" }}
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p
                                    class="text-sm font-bold text-slate-900 dark:text-white"
                                >
                                    {{ badge.title || `Value ${index + 1}` }}
                                </p>

                                <p
                                    class="mt-1 text-xs leading-5 text-slate-500 dark:text-slate-400"
                                >
                                    {{
                                        badge.description ||
                                        "Partner value proposition."
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Page Header -->
        <section
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div
                class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                        >
                            <span class="material-symbols-outlined">
                                handshake
                            </span>
                        </div>

                        <div>
                            <h1
                                class="text-2xl font-bold text-slate-900 dark:text-white"
                            >
                                Partners
                            </h1>

                            <p
                                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                            >
                                Manage trusted partners, featured partners, and
                                the partner ecosystem.
                            </p>
                        </div>
                    </div>
                </div>

                <button
                    v-if="canCreate"
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                    @click="openCreateModal"
                >
                    <span class="material-symbols-outlined text-[20px]">
                        add
                    </span>

                    Add Partner
                </button>
            </div>
        </section>

        <!-- Toolbar -->
        <section
            class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div
                class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
            >
                <div class="relative w-full md:max-w-md">
                    <span
                        class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400"
                    >
                        search
                    </span>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search partners..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-10 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:bg-slate-800"
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

                <div class="text-sm text-slate-500 dark:text-slate-400">
                    {{ filteredPartners.length }}
                    {{ filteredPartners.length === 1 ? "partner" : "partners" }}
                </div>
            </div>
        </section>

        <!-- Loading -->
        <section
            v-if="loading"
            class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900"
        >
            <div
                class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-blue-600 dark:border-slate-700 dark:border-t-blue-400"
            ></div>

            <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                Loading partners...
            </p>
        </section>

        <template v-else>
            <!-- Featured Partners -->
            <section class="space-y-4">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span
                                class="material-symbols-outlined text-blue-600 dark:text-blue-400"
                            >
                                star
                            </span>

                            <h2
                                class="text-xl font-bold text-slate-900 dark:text-white"
                            >
                                Featured Partners
                            </h2>

                            <span
                                class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                            >
                                {{ featuredPartners.length }}
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Partners and section header featured on the public Home page.
                        </p>
                    </div>

                    <button
                        v-if="canUpdate"
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-blue-700 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
                        @click="openHomeHeaderModal"
                    >
                        <span class="material-symbols-outlined text-[18px]">
                            edit_note
                        </span>

                        Edit Home Section Header
                    </button>
                </div>

                <!-- Home Page Section Header Preview Card -->
                <div
                    class="rounded-2xl border border-slate-200 bg-gradient-to-r from-blue-50/40 via-white to-slate-50/30 p-6 shadow-sm dark:border-slate-800 dark:from-slate-900 dark:via-slate-900/90 dark:to-slate-900/40"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                                <span class="material-symbols-outlined text-[15px]">home</span>
                                {{ partnerHeader.home_subtitle || 'Our Partners' }}
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 dark:text-white sm:text-2xl">
                                {{ partnerHeader.home_title || 'Building stronger' }}
                                <span
                                    v-if="partnerHeader.home_title_highlight"
                                    class="text-blue-600 dark:text-teal-400 font-semibold"
                                >
                                    {{ partnerHeader.home_title_highlight }}
                                </span>
                            </h3>

                            <p class="max-w-3xl text-sm leading-relaxed text-slate-600 dark:text-slate-300">
                                {{
                                    partnerHeader.home_description ||
                                    'We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.'
                                }}
                            </p>
                        </div>

                        <button
                            v-if="canUpdate"
                            type="button"
                            class="inline-flex shrink-0 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-blue-500/10 dark:hover:text-blue-400"
                            @click="openHomeHeaderModal"
                        >
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            Edit Header
                        </button>
                    </div>
                </div>

                <div
                    v-if="featuredPartners.length"
                    class="grid gap-4 xl:grid-cols-2"
                >
                    <article
                        v-for="partner in featuredPartners"
                        :key="`featured-${partner.id}`"
                        class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-200 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-blue-900"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700"
                            >
                                <img
                                    v-if="partner.logo_url"
                                    :src="resolveImageUrl(partner.logo_url)"
                                    :alt="partner.name"
                                    class="h-full w-full object-contain p-2"
                                />

                                <span
                                    v-else
                                    class="material-symbols-outlined text-2xl text-slate-400"
                                >
                                    image
                                </span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3
                                        class="truncate text-base font-bold text-slate-900 dark:text-white"
                                    >
                                        {{ partner.name }}
                                    </h3>

                                    <span
                                        class="rounded-full bg-blue-50 px-2 py-1 text-[11px] font-semibold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                                    >
                                        Featured
                                    </span>
                                </div>

                                <p
                                    class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-400"
                                >
                                    {{ partner.type }}
                                </p>

                                <p
                                    v-if="partner.description"
                                    class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-300"
                                >
                                    {{ partner.description }}
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    <span
                                        class="inline-flex items-center gap-1.5"
                                    >
                                        <span
                                            class="material-symbols-outlined text-[16px]"
                                        >
                                            format_list_numbered
                                        </span>

                                        Order:
                                        {{ partner.sort_order ?? 0 }}
                                    </span>

                                    <a
                                        v-if="partner.website_url"
                                        :href="partner.website_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-blue-600 hover:underline dark:text-blue-400"
                                    >
                                        <span
                                            class="material-symbols-outlined text-[16px]"
                                        >
                                            open_in_new
                                        </span>

                                        Website
                                    </a>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <button
                                    v-if="canUpdate"
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-blue-400"
                                    title="Edit"
                                    @click="openEditModal(partner)"
                                >
                                    <span
                                        class="material-symbols-outlined text-[19px]"
                                    >
                                        edit
                                    </span>
                                </button>

                                <button
                                    v-if="canDelete"
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-red-50 hover:text-red-600 dark:text-slate-400 dark:hover:bg-red-500/10 dark:hover:text-red-400"
                                    title="Delete"
                                    @click="askDelete(partner)"
                                >
                                    <span
                                        class="material-symbols-outlined text-[19px]"
                                    >
                                        delete
                                    </span>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-700 dark:bg-slate-900"
                >
                    <span
                        class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600"
                    >
                        star
                    </span>

                    <p
                        class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-300"
                    >
                        No featured partners found.
                    </p>
                </div>
            </section>

            <!-- Partner Ecosystem -->
            <section class="space-y-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="material-symbols-outlined text-slate-600 dark:text-slate-300"
                        >
                            hub
                        </span>

                        <h2
                            class="text-xl font-bold text-slate-900 dark:text-white"
                        >
                            Partner Ecosystem
                        </h2>

                        <span
                            class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300"
                        >
                            {{ ecosystemPartners.length }}
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Other technology partners and ecosystem networks.
                    </p>
                </div>

                <div
                    v-if="ecosystemPartners.length"
                    class="grid gap-4 xl:grid-cols-2"
                >
                    <article
                        v-for="partner in ecosystemPartners"
                        :key="`ecosystem-${partner.id}`"
                        class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-slate-300 hover:shadow-md dark:border-slate-800 dark:bg-slate-900 dark:hover:border-slate-700"
                    >
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700"
                            >
                                <img
                                    v-if="partner.logo_url"
                                    :src="resolveImageUrl(partner.logo_url)"
                                    :alt="partner.name"
                                    class="h-full w-full object-contain p-2"
                                />

                                <span
                                    v-else
                                    class="material-symbols-outlined text-2xl text-slate-400"
                                >
                                    image
                                </span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3
                                    class="truncate text-base font-bold text-slate-900 dark:text-white"
                                >
                                    {{ partner.name }}
                                </h3>

                                <p
                                    class="mt-1 text-xs font-medium uppercase tracking-wide text-slate-400"
                                >
                                    {{ partner.type }}
                                </p>

                                <p
                                    v-if="partner.description"
                                    class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-300"
                                >
                                    {{ partner.description }}
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    <span
                                        class="inline-flex items-center gap-1.5"
                                    >
                                        <span
                                            class="material-symbols-outlined text-[16px]"
                                        >
                                            format_list_numbered
                                        </span>

                                        Order:
                                        {{ partner.sort_order ?? 0 }}
                                    </span>

                                    <a
                                        v-if="partner.website_url"
                                        :href="partner.website_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 text-blue-600 hover:underline dark:text-blue-400"
                                    >
                                        <span
                                            class="material-symbols-outlined text-[16px]"
                                        >
                                            open_in_new
                                        </span>

                                        Website
                                    </a>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <button
                                    v-if="canUpdate"
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-blue-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-blue-400"
                                    title="Edit"
                                    @click="openEditModal(partner)"
                                >
                                    <span
                                        class="material-symbols-outlined text-[19px]"
                                    >
                                        edit
                                    </span>
                                </button>

                                <button
                                    v-if="canDelete"
                                    type="button"
                                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-red-50 hover:text-red-600 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-red-400"
                                    title="Delete"
                                    @click="askDelete(partner)"
                                >
                                    <span
                                        class="material-symbols-outlined text-[19px]"
                                    >
                                        delete
                                    </span>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <div
                    v-else
                    class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-700 dark:bg-slate-900"
                >
                    <span
                        class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600"
                    >
                        hub
                    </span>

                    <p
                        class="mt-3 text-sm font-medium text-slate-600 dark:text-slate-300"
                    >
                        No ecosystem partners found.
                    </p>
                </div>
            </section>
        </template>

        <!-- =====================================================
         Trusted Partners Header Edit Modal
    ====================================================== -->
        <Teleport to="body">
            <div
                v-if="headerModalOpen"
                class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click.self="closeHeaderModal"
            >
                <div
                    class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900"
                >
                    <!-- Modal Header -->
                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-blue-600 dark:text-blue-400"
                                >
                                    edit
                                </span>

                                <h2
                                    class="text-lg font-bold text-slate-900 dark:text-white"
                                >
                                    Edit Trusted Partners
                                </h2>
                            </div>

                            <p
                                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                            >
                                Update the heading and four value badges
                                displayed on the public page.
                            </p>
                        </div>

                        <button
                            type="button"
                            :disabled="savingHeader"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="closeHeaderModal"
                        >
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form
                        class="overflow-y-auto p-6"
                        @submit.prevent="saveHeader"
                    >
                        <div
                            v-if="headerError"
                            class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-500/10 dark:text-red-400"
                        >
                            {{ headerError }}
                        </div>

                        <!-- Main Header -->
                        <div class="space-y-5">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Title
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="headerForm.title"
                                    type="text"
                                    placeholder="Our Trusted Partners"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Subtitle
                                    <span class="text-red-500">*</span>
                                </label>

                                <textarea
                                    v-model="headerForm.subtitle"
                                    rows="3"
                                    placeholder="We collaborate with world-class technology leaders..."
                                    class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                ></textarea>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Header Icon
                                    <span class="text-red-500">*</span>
                                </label>

                                <IconPicker
                                    v-model="headerForm.icon"
                                    placeholder="Select header icon..."
                                />
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="mt-8">
                            <div class="mb-4">
                                <h3
                                    class="text-base font-bold text-slate-900 dark:text-white"
                                >
                                    Value Badges
                                </h3>

                                <p
                                    class="mt-1 text-xs text-slate-500 dark:text-slate-400"
                                >
                                    The four badges are fixed. You can edit
                                    their content, but cannot add or remove
                                    them.
                                </p>
                            </div>

                            <div class="grid gap-5 lg:grid-cols-2">
                                <article
                                    v-for="(
                                        badge, index
                                    ) in headerForm.value_badges"
                                    :key="index"
                                    class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800/50"
                                >
                                    <div
                                        class="mb-5 flex items-center justify-between"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-xs font-bold text-blue-600 dark:bg-blue-500/10 dark:text-blue-400"
                                            >
                                                {{ index + 1 }}
                                            </span>

                                            <span
                                                class="text-sm font-bold text-slate-800 dark:text-slate-100"
                                            >
                                                Badge {{ index + 1 }}
                                            </span>
                                        </div>

                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-blue-600 shadow-sm dark:bg-slate-900 dark:text-blue-400"
                                        >
                                            <span
                                                class="material-symbols-outlined text-[20px]"
                                            >
                                                {{ badge.icon || "star" }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <!-- Title -->
                                        <div>
                                            <label
                                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                                            >
                                                Title
                                            </label>

                                            <input
                                                v-model="badge.title"
                                                type="text"
                                                placeholder="Strong Alliances"
                                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                            />
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label
                                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                                            >
                                                Description
                                            </label>

                                            <textarea
                                                v-model="badge.description"
                                                rows="3"
                                                placeholder="Strategic partnerships with global technology leaders."
                                                class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                                            ></textarea>
                                        </div>

                                        <!-- Icon -->
                                        <div>
                                            <label
                                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400"
                                            >
                                                Icon
                                            </label>

                                            <IconPicker
                                                v-model="badge.icon"
                                                placeholder="Select badge icon..."
                                            />
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div
                            class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end dark:border-slate-800"
                        >
                            <button
                                type="button"
                                :disabled="savingHeader"
                                class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                @click="closeHeaderModal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                :disabled="savingHeader"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span
                                    v-if="savingHeader"
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                ></span>

                                <span
                                    v-else
                                    class="material-symbols-outlined text-[19px]"
                                >
                                    save
                                </span>

                                {{
                                    savingHeader ? "Saving..." : "Save Changes"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- =====================================================
         Home Page Partners Section Header Modal
        ====================================================== -->
        <Teleport to="body">
            <div
                v-if="homeHeaderModalOpen"
                class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click.self="closeHomeHeaderModal"
            >
                <div
                    class="flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900"
                >
                    <!-- Modal Header -->
                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="material-symbols-outlined text-blue-600 dark:text-blue-400"
                                >
                                    home
                                </span>

                                <h2
                                    class="text-lg font-bold text-slate-900 dark:text-white"
                                >
                                    Edit Home Page Partners Header
                                </h2>
                            </div>

                            <p
                                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                            >
                                Update the title, highlight word, and paragraph displayed on the Home page.
                            </p>
                        </div>

                        <button
                            type="button"
                            :disabled="savingHomeHeader"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                            @click="closeHomeHeaderModal"
                        >
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form
                        class="overflow-y-auto p-6 space-y-5"
                        @submit.prevent="saveHomeHeader"
                    >
                        <div
                            v-if="homeHeaderError"
                            class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-500/10 dark:text-red-400"
                        >
                            {{ homeHeaderError }}
                        </div>

                        <!-- Subtitle / Badge -->
                        <div>
                            <label
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                            >
                                Section Subtitle / Badge
                            </label>

                            <input
                                v-model="homeHeaderForm.home_subtitle"
                                type="text"
                                placeholder="Our Partners"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            />

                            <p class="mt-1 text-xs text-slate-400">
                                The small uppercase badge displayed above the heading (e.g. "OUR PARTNERS").
                            </p>
                        </div>

                        <!-- Title and Highlight -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Main Title
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="homeHeaderForm.home_title"
                                    type="text"
                                    placeholder="Building stronger"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Title Highlight Word
                                </label>

                                <input
                                    v-model="homeHeaderForm.home_title_highlight"
                                    type="text"
                                    placeholder="ecosystems"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />

                                <p class="mt-1 text-xs text-slate-400">
                                    Styled with the prominent teal glow effect on the Home page.
                                </p>
                            </div>
                        </div>

                        <!-- Live Heading Preview -->
                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 dark:border-slate-800 dark:bg-slate-800/40"
                        >
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Live Heading Preview
                            </span>

                            <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">
                                {{ homeHeaderForm.home_title || "Building stronger" }}
                                <span class="text-blue-600 dark:text-teal-400">
                                    {{ homeHeaderForm.home_title_highlight || "ecosystems" }}
                                </span>
                            </div>
                        </div>

                        <!-- Description Paragraph -->
                        <div>
                            <label
                                class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                            >
                                Description Paragraph
                            </label>

                            <textarea
                                v-model="homeHeaderForm.home_description"
                                rows="3"
                                placeholder="We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients."
                                class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            ></textarea>
                        </div>

                        <!-- Modal Actions -->
                        <div
                            class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end dark:border-slate-800"
                        >
                            <button
                                type="button"
                                :disabled="savingHomeHeader"
                                class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                                @click="closeHomeHeaderModal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                :disabled="savingHomeHeader"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span
                                    v-if="savingHomeHeader"
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                ></span>

                                <span
                                    v-else
                                    class="material-symbols-outlined text-[19px]"
                                >
                                    save
                                </span>

                                {{
                                    savingHomeHeader
                                        ? "Saving..."
                                        : "Save Changes"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Partner Modal -->
        <Teleport to="body">
            <div
                v-if="modalOpen"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/60 px-4 py-6 backdrop-blur-sm"
                @click.self="closeModal"
            >
                <div
                    class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900"
                >
                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-6 py-5 dark:border-slate-800"
                    >
                        <div>
                            <h2
                                class="text-lg font-bold text-slate-900 dark:text-white"
                            >
                                {{
                                    editingItem ? "Edit Partner" : "Add Partner"
                                }}
                            </h2>

                            <p
                                class="mt-1 text-sm text-slate-500 dark:text-slate-400"
                            >
                                {{
                                    editingItem
                                        ? "Update partner information."
                                        : "Add a new partner to the ecosystem."
                                }}
                            </p>
                        </div>

                        <button
                            type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-800"
                            @click="closeModal"
                        >
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </button>
                    </div>

                    <form class="overflow-y-auto p-6" @submit.prevent="save">
                        <div
                            v-if="formError"
                            class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-500/10 dark:text-red-400"
                        >
                            {{ formError }}
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Partner Name
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="e.g. Siemens"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Type
                                </label>

                                <select
                                    v-model="form.type"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                >
                                    <option value="technology_partner">
                                        Technology Partner
                                    </option>

                                    <option value="ecosystem_network">
                                        Ecosystem Network
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Website URL
                                </label>

                                <input
                                    v-model="form.website_url"
                                    type="url"
                                    placeholder="https://example.com"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Tagline
                                </label>

                                <input
                                    v-model="form.tagline"
                                    type="text"
                                    placeholder="Partner tagline"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Description
                                </label>

                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Describe the partner..."
                                    class="w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                ></textarea>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Solutions Link
                                </label>

                                <input
                                    v-model="form.solutions_link"
                                    type="text"
                                    placeholder="/services"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Sort Order
                                </label>

                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Glow Color
                                </label>

                                <div class="flex gap-2">
                                    <input
                                        v-model="form.glow"
                                        type="color"
                                        class="h-12 w-14 cursor-pointer rounded-xl border border-slate-200 bg-white p-1 dark:border-slate-700 dark:bg-slate-800"
                                    />

                                    <input
                                        v-model="form.glow"
                                        type="text"
                                        placeholder="#00d4ff"
                                        class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                    />
                                </div>
                            </div>

                            <div
                                class="flex items-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-700 dark:bg-slate-800/50"
                            >
                                <label
                                    class="flex cursor-pointer items-start gap-3"
                                >
                                    <input
                                        v-model="form.is_featured"
                                        type="checkbox"
                                        class="mt-1 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-600"
                                    />

                                    <span>
                                        <span
                                            class="block text-sm font-semibold text-slate-800 dark:text-slate-100"
                                        >
                                            Featured Partner
                                        </span>

                                        <span
                                            class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400"
                                        >
                                            Featured partners are displayed on
                                            the Home page.
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    Partner Logo
                                    <span
                                        v-if="!editingItem"
                                        class="text-red-500"
                                    >
                                        *
                                    </span>
                                </label>

                                <div
                                    class="grid gap-4 md:grid-cols-[1fr_160px]"
                                >
                                    <label
                                        class="flex min-h-[150px] cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-5 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:border-slate-700 dark:bg-slate-800/50 dark:hover:border-blue-500 dark:hover:bg-blue-500/5"
                                    >
                                        <span
                                            class="material-symbols-outlined text-3xl text-slate-400"
                                        >
                                            upload
                                        </span>

                                        <span
                                            class="mt-2 text-sm font-semibold text-slate-700 dark:text-slate-200"
                                        >
                                            Choose logo
                                        </span>

                                        <span
                                            class="mt-1 text-xs text-slate-400"
                                        >
                                            PNG, JPG, WEBP or SVG
                                        </span>

                                        <input
                                            type="file"
                                            accept="image/*"
                                            class="hidden"
                                            @change="handleImageChange"
                                        />
                                    </label>

                                    <div
                                        class="relative flex min-h-[150px] items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800"
                                    >
                                        <img
                                            v-if="imagePreview"
                                            :src="imagePreview"
                                            :alt="form.name || 'Partner logo'"
                                            class="max-h-[130px] max-w-[130px] object-contain p-3"
                                        />

                                        <div
                                            v-else
                                            class="text-center text-slate-400"
                                        >
                                            <span
                                                class="material-symbols-outlined text-3xl"
                                            >
                                                image
                                            </span>

                                            <p class="mt-1 text-xs">No logo</p>
                                        </div>

                                        <button
                                            v-if="imageFile"
                                            type="button"
                                            class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                                            title="Remove selected image"
                                            @click="removeSelectedImage"
                                        >
                                            <span
                                                class="material-symbols-outlined text-[18px]"
                                            >
                                                close
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end dark:border-slate-800"
                        >
                            <button
                                type="button"
                                class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                                @click="closeModal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                :disabled="saving"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <span
                                    v-if="saving"
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                ></span>

                                <span
                                    v-else
                                    class="material-symbols-outlined text-[19px]"
                                >
                                    save
                                </span>

                                {{ saving ? "Saving..." : "Save Partner" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Delete Dialog -->
        <Teleport to="body">
            <div
                v-if="deleteDialogOpen"
                class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-950/60 px-4 backdrop-blur-sm"
                @click.self="cancelDelete"
            >
                <div
                    class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900"
                >
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400"
                        >
                            <span class="material-symbols-outlined">
                                delete
                            </span>
                        </div>

                        <div>
                            <h2
                                class="text-lg font-bold text-slate-900 dark:text-white"
                            >
                                Delete Partner
                            </h2>

                            <p
                                class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400"
                            >
                                Are you sure you want to delete
                                <span
                                    class="font-semibold text-slate-700 dark:text-slate-200"
                                >
                                    {{ deletingItem?.name }}
                                </span>
                                ? This action cannot be undone.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            @click="cancelDelete"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white hover:bg-red-700"
                            @click="confirmDelete"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- Toast -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="translate-y-2 opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-2 opacity-0"
            >
                <div
                    v-if="toast.visible"
                    class="fixed bottom-6 right-6 z-[220] flex max-w-sm items-center gap-3 rounded-xl border px-4 py-3 shadow-xl"
                    :class="
                        toast.type === 'error'
                            ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-900/50 dark:bg-red-500/10 dark:text-red-400'
                            : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-500/10 dark:text-emerald-400'
                    "
                >
                    <span class="material-symbols-outlined text-[20px]">
                        {{ toast.type === "error" ? "error" : "check_circle" }}
                    </span>

                    <span class="text-sm font-medium">
                        {{ toast.message }}
                    </span>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
