<script setup>
import { computed, onMounted, ref } from "vue";

import ToastNotification from "@/components/global/ToastNotification.vue";
import IconPicker from "@/SIS-Dashboard/components/IconPicker.vue";
import ImageInput from "@/SIS-Dashboard/components/collections/ImageInput.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import SeoFormSection from "@/SIS-Dashboard/components/seo/SeoFormSection.vue";
import { adminAPI } from "@/SIS-Dashboard/api/index.js";
import { resolveImageUrl } from "@/utils/imageUrl.js";

const showToast = ref(false);

const toast = ref({
  title: "",
  message: "",
  variant: "success",
});

function notify(title, message, variant = "success") {
  showToast.value = false;
  setTimeout(() => {
    toast.value = {
      title,
      message,
      variant,
    };
    showToast.value = true;
  }, 10);
}

const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);

const industries = ref([]);
const search = ref("");
const page = ref(1);

const perPage = 6;

const modalOpen = ref(false);
const modalTab = ref("content"); // 'content' | 'seo'
const editingId = ref(null);
const isCreateMode = computed(() => editingId.value === null);
const slugManuallyEdited = ref(false);
const showDeleteDialog = ref(false);
const industryToDelete = ref(null);

const form = ref({
  name: "",
  slug: "",
  icon: "",
  description: "",
  sort_order: 0,
  is_active: true,
  image: null,
  relevant_programs: [],
  typical_scope: [],
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
    schema_type: "CollectionPage",
  },
});

const filtered = computed(() => {
  const query = search.value.trim().toLowerCase();

  if (!query) {
    return industries.value;
  }

  return industries.value.filter((industry) => {
    return [
      industry.name,
      industry.slug,
      industry.description,
    ].some((value) =>
      String(value ?? "")
        .toLowerCase()
        .includes(query),
    );
  });
});

const pages = computed(() =>
  Math.max(1, Math.ceil(filtered.value.length / perPage)),
);

const paginated = computed(() => {
  const start = (page.value - 1) * perPage;

  return filtered.value.slice(start, start + perPage);
});

const loadIndustries = async () => {
  loading.value = true;

  try {
    const response = await adminAPI.getIndustries();

    const rawList = Array.isArray(response?.industries)
      ? response.industries
      : Array.isArray(response?.data?.industries)
        ? response.data.industries
        : Array.isArray(response)
          ? response
          : [];

    industries.value = rawList;

    page.value = 1;
  } catch (error) {
    console.error("Failed to load industries:", error);

    industries.value = [];

    notify(
      "Unable to load industries",
      error?.message || "Please try again in a moment.",
      "error",
    );
  } finally {
    loading.value = false;
  }
};

function parseArrayField(val) {
  if (!val) return [];
  if (Array.isArray(val)) return [...val];
  if (typeof val === "string") {
    try {
      const parsed = JSON.parse(val);
      if (Array.isArray(parsed)) return parsed;
    } catch {
      return val.split(",").map((s) => s.trim()).filter(Boolean);
    }
  }
  return [];
}

function resetForm() {
  form.value = {
    name: "",
    slug: "",
    icon: "",
    description: "",
    sort_order: 0,
    is_active: true,
    image: null,
    relevant_programs: [],
    typical_scope: [],
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
      schema_type: "CollectionPage",
    },
  };
}

function openCreate() {
  editingId.value = null;
  slugManuallyEdited.value = false;
  modalTab.value = "content";
  resetForm();
  modalOpen.value = true;
}

function onNameInput() {
  if (isCreateMode.value && !slugManuallyEdited.value) {
    form.value.slug = (form.value.name || "")
      .toLowerCase()
      .trim()
      .replace(/[^\w\s-]/g, "")
      .replace(/[\s_-]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }
}

function openEdit(industry) {
  editingId.value = industry.id;
  slugManuallyEdited.value = false;

  const rawSeo = industry.seo_raw || industry.seo || {};

  form.value = {
    name: industry.name ?? "",
    slug: industry.slug ?? "",
    icon: industry.icon ?? "",
    description: industry.description ?? "",
    sort_order: industry.sort_order ?? 0,
    is_active: Boolean(industry.is_active),
    image: industry.image_url || industry.image || "",
    relevant_programs: parseArrayField(industry.relevant_programs),
    typical_scope: parseArrayField(industry.typical_scope),
    seo: {
      meta_title: {
        en: rawSeo.meta_title?.en || (typeof rawSeo.meta_title === "string" ? rawSeo.meta_title : (industry.name ?? "")),
        ar: rawSeo.meta_title?.ar || "",
      },
      meta_description: {
        en: rawSeo.meta_description?.en || (typeof rawSeo.meta_description === "string" ? rawSeo.meta_description : (industry.description ?? "")),
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
      og_image: rawSeo.og_image || industry.image_url || industry.image || "",
      og_image_file: null,
      canonical_url: rawSeo.canonical_url || "",
      is_noindex: Boolean(rawSeo.is_noindex),
      is_nofollow: Boolean(rawSeo.is_nofollow),
      schema_type: rawSeo.schema_type || "CollectionPage",
    },
  };

  modalTab.value = "content";
  modalOpen.value = true;
}

function addRelevantProgram() {
  form.value.relevant_programs.push("");
}

function removeRelevantProgram(index) {
  form.value.relevant_programs.splice(index, 1);
}

function addTypicalScope() {
  form.value.typical_scope.push("");
}

function removeTypicalScope(index) {
  form.value.typical_scope.splice(index, 1);
}

function closeModal(force = false) {
  if (saving.value && force !== true) return;

  modalTab.value = "content";
  modalOpen.value = false;
  editingId.value = null;
  slugManuallyEdited.value = false;
  resetForm();
}

function askDelete(industry = null) {
  industryToDelete.value =
    industry || (editingId.value ? { id: editingId.value, name: form.value.name } : null);
  if (industryToDelete.value) {
    showDeleteDialog.value = true;
  }
}

async function confirmDelete() {
  if (!industryToDelete.value?.id) return;

  const id = industryToDelete.value.id;
  const targetName = industryToDelete.value.name || "Industry";
  showDeleteDialog.value = false;
  deleting.value = true;

  try {
    await adminAPI.deleteIndustry(id);

    if (modalOpen.value && editingId.value === id) {
      closeModal(true);
    }

    await loadIndustries();
    notify("Industry deleted", `"${targetName}" was successfully deleted.`, "success");
  } catch (error) {
    console.error("Failed to delete industry:", error);
    let errorMsg = error?.message || "Please try again in a moment.";
    if (error?.errors && typeof error.errors === "object") {
      const details = Object.values(error.errors).flat().filter(Boolean).join(" ");
      if (details) errorMsg = details;
    }
    notify("Unable to delete industry", errorMsg, "error");
  } finally {
    deleting.value = false;
    industryToDelete.value = null;
  }
}

async function save() {
  if (!form.value.name?.trim()) {
    notify("Validation Error", "Industry name is required.", "error");
    return;
  }

  saving.value = true;

  try {
    const cleanedPrograms = (form.value.relevant_programs || [])
      .map((item) => (typeof item === "string" ? item.trim() : item))
      .filter(Boolean);

    const cleanedScope = (form.value.typical_scope || [])
      .map((item) => (typeof item === "string" ? item.trim() : item))
      .filter(Boolean);

    const payload = {
      name: form.value.name.trim(),
      slug: form.value.slug?.trim() || undefined,
      icon: form.value.icon || "",
      description: form.value.description || "",
      sort_order: form.value.sort_order ?? 0,
      is_active: form.value.is_active,
      relevant_programs: cleanedPrograms,
      typical_scope: cleanedScope,
    };

    if (form.value.image instanceof File) {
      payload.image = form.value.image;
    }

    if (form.value.seo) {
      payload.seo = form.value.seo;
      if (form.value.seo.og_image_file) {
        payload.seo_og_image = form.value.seo.og_image_file;
      }
    }

    if (editingId.value !== null) {
      await adminAPI.updateIndustry(
        editingId.value,
        payload,
      );

      notify(
        "Industry updated",
        `"${payload.name}" has been updated successfully.`,
        "success",
      );
    } else {
      await adminAPI.createIndustry(payload);

      notify(
        "Industry created",
        `"${payload.name}" has been created successfully.`,
        "success",
      );
    }

    await loadIndustries();
    closeModal(true);
  } catch (error) {
    console.error("Failed to save industry:", error);

    let errorMsg = error?.message || "Please try again in a moment.";
    if (error?.errors && typeof error.errors === "object") {
      const details = Object.values(error.errors).flat().filter(Boolean).join(" ");
      if (details) errorMsg = details;
    }

    notify(
      editingId.value !== null ? "Unable to update industry" : "Unable to create industry",
      errorMsg,
      "error",
    );
  } finally {
    saving.value = false;
  }
}

function getMaterialIcon(icon) {
  return icon || "factory";
}

function handleSearch() {
  page.value = 1;
}

onMounted(loadIndustries);
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="Industries"
      subtitle="Manage the industries served by SiS and maintain their descriptions, icons, images, and visibility."
      category="Industries & Sectors"
      icon="factory"
    >
      <template #actions>
        <div class="flex items-center gap-3">
          <button
            type="button"
            :disabled="loading"
            class="inline-flex items-center gap-1.5 rounded-xl border border-sis-border bg-white px-3.5 py-2.5 text-sm font-semibold text-sis-text shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
            @click="loadIndustries"
          >
            <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': loading }">sync</span>
            Refresh
          </button>

          <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
            @click="openCreate"
          >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Add Industry
          </button>
        </div>
      </template>
    </AdminPageHeader>

    <!-- Search & Filter Bar -->
    <div
      v-if="!loading && industries.length > 0"
      class="flex flex-col gap-3 rounded-2xl border border-sis-border bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="relative w-full sm:max-w-md">
        <span
          class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400"
        >
          search
        </span>

        <input
          v-model="search"
          type="search"
          placeholder="Search industries..."
          class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-10 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/15"
          @input="handleSearch"
        />

        <button
          v-if="search"
          type="button"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
          aria-label="Clear search"
          @click="search = ''; handleSearch()"
        >
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </div>

      <div class="flex items-center gap-2 text-sm text-slate-500">
        <span class="font-semibold text-sis-text">
          {{ filtered.length }}
        </span>
        {{ filtered.length === 1 ? "industry" : "industries" }}
      </div>
    </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-4">
        <div
          v-for="n in 4"
          :key="n"
          class="animate-pulse rounded-2xl border border-slate-200 bg-white p-5"
        >
          <div class="flex gap-5">
            <div class="h-11 w-11 shrink-0 rounded-xl bg-slate-200"></div>

            <div class="flex-1 space-y-3">
              <div class="h-5 w-1/3 rounded bg-slate-200"></div>

              <div class="h-4 w-full rounded bg-slate-200"></div>

              <div class="h-4 w-2/3 rounded bg-slate-200"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div v-else class="space-y-4">

        <!-- Empty -->
        <div
          v-if="paginated.length === 0"
          class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"
        >
          <div
            class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100"
          >
            <span
              class="material-symbols-outlined text-[28px] text-slate-500"
            >
              factory
            </span>
          </div>

          <h2 class="text-base font-semibold text-slate-900">
            No industries found
          </h2>

          <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
            {{
              search
                ? "There are no industries matching your search."
                : "There are no industries available yet."
            }}
          </p>

          <button
            v-if="!search"
            type="button"
            class="mx-auto mt-5 inline-flex items-center gap-1.5 rounded-xl bg-sis-accent px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
            @click="openCreate"
          >
            <span class="material-symbols-outlined text-[16px]">add</span>
            Add Industry
          </button>
        </div>

        <!-- Industries -->
        <template v-else>
          <article
            v-for="(industry, index) in paginated"
            :key="industry.id"
            class="group rounded-2xl border border-sis-border bg-white p-5 shadow-sm transition-all hover:border-sis-accent/30 hover:shadow-md"
          >
            <div class="flex items-start gap-4">

              <!-- Thumbnail or Icon -->
              <div
                v-if="industry.image_url || industry.image"
                class="h-14 w-20 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-inner"
              >
                <img
                  :src="resolveImageUrl(industry.image_url || industry.image)"
                  :alt="industry.name"
                  class="h-full w-full object-cover"
                />
              </div>
              <div
                v-else
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-blue-50/80 text-sis-accent"
              >
                <span
                  class="material-symbols-outlined text-[22px]"
                >
                  {{ getMaterialIcon(industry.icon) }}
                </span>
              </div>

              <!-- Content -->
              <div class="min-w-0 flex-1">
                <div
                  class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                >
                  <div class="min-w-0">
                    <div class="mb-1 flex flex-wrap items-center gap-2">
                      <p
                        class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                      >
                        SiS Industry
                      </p>

                      <span
                        class="rounded-full px-2 py-0.5 text-[11px] font-semibold"
                        :class="
                          industry.is_active
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'bg-slate-100 text-slate-500'
                        "
                      >
                        {{ industry.is_active ? "Active" : "Inactive" }}
                      </span>
                    </div>

                    <h2 class="text-lg font-bold text-slate-900">
                      {{ industry.name || "Untitled industry" }}
                    </h2>
                  </div>

                  <!-- Actions -->
                  <div class="flex items-center gap-1.5">
                    <button
                      type="button"
                      class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                      @click="openEdit(industry)"
                    >
                      <span class="material-symbols-outlined text-[18px]">
                        edit
                      </span>
                      Edit
                    </button>

                    <button
                      type="button"
                      class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                      title="Delete Industry"
                      @click="askDelete(industry)"
                    >
                      <span class="material-symbols-outlined text-[18px]">
                        delete
                      </span>
                    </button>
                  </div>
                </div>

                <!-- Description -->
                <p
                  v-if="industry.description"
                  class="mt-3 max-w-4xl text-sm leading-6 text-slate-600"
                >
                  {{ industry.description }}
                </p>

                <p
                  v-else
                  class="mt-3 text-sm italic text-slate-400"
                >
                  No description provided.
                </p>

                <!-- Meta -->
                <div
                  class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-slate-100 pt-3"
                >
                  <div
                    v-if="industry.slug"
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      link
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      {{ industry.slug }}
                    </span>
                  </div>

                  <div
                    v-if="industry.icon"
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      star
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      {{ industry.icon }}
                    </span>
                  </div>

                  <div
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      work
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      {{ industry.projects_count ?? 0 }}
                      {{
                        industry.projects_count === 1
                          ? "project"
                          : "projects"
                      }}
                    </span>
                  </div>

                  <div
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      sort
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      Order {{ industry.sort_order ?? index }}
                    </span>
                  </div>

                  <div
                    v-if="industry.relevant_programs?.length"
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      domain_verification
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      {{ industry.relevant_programs.length }} {{ industry.relevant_programs.length === 1 ? 'program' : 'programs' }}
                    </span>
                  </div>

                  <div
                    v-if="industry.typical_scope?.length"
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      checklist
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      {{ industry.typical_scope.length }} {{ industry.typical_scope.length === 1 ? 'scope item' : 'scope items' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </template>
      </div>

      <!-- Pagination -->
      <div
        v-if="!loading && pages > 1"
        class="mt-6 flex items-center justify-between"
      >
        <p class="text-sm text-slate-500">
          Page
          <span class="font-semibold text-slate-900">
            {{ page }}
          </span>
          of
          <span class="font-semibold text-slate-900">
            {{ pages }}
          </span>
        </p>

        <div class="flex items-center gap-2">
          <button
            type="button"
            :disabled="page <= 1"
            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
            @click="page--"
          >
            <span class="material-symbols-outlined text-[18px]">
              chevron_left
            </span>
          </button>

          <button
            type="button"
            :disabled="page >= pages"
            class="inline-flex h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
            @click="page++"
          >
            <span class="material-symbols-outlined text-[18px]">
              chevron_right
            </span>
          </button>
        </div>
      </div>

    <!-- Edit Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div
          v-if="modalOpen"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
          @click.self="closeModal"
        >
          <div
            class="flex max-h-[92vh] w-full max-w-5xl xl:max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
          >
            <!-- Header -->
            <div
              class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 pt-5 pb-4"
            >
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                >
                  SiS Industry
                </p>

                <h2 class="mt-1 text-xl font-bold text-slate-900">
                  {{ editingId ? "Edit Industry" : "Add Industry" }}
                </h2>
              </div>

              <button
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                @click="closeModal"
              >
                <span class="material-symbols-outlined text-[22px]">
                  close
                </span>
              </button>
            </div>

            <!-- Modal Tabs Navigation -->
            <div class="flex flex-wrap items-center gap-1.5 border-b border-slate-200 bg-slate-50/75 px-6 py-2.5">
              <button
                type="button"
                @click="modalTab = 'content'"
                class="flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition cursor-pointer"
                :class="modalTab === 'content' ? 'bg-sis-accent text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-slate-900'"
              >
                <span class="material-symbols-outlined text-[16px]">domain</span>
                Industry Details
              </button>

              <button
                type="button"
                @click="modalTab = 'seo'"
                class="flex items-center gap-1.5 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition cursor-pointer"
                :class="modalTab === 'seo' ? 'bg-sis-accent text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-slate-900'"
              >
                <span class="material-symbols-outlined text-[16px]">travel_explore</span>
                Google SEO & Social Media
              </button>
            </div>

            <!-- Body -->
            <form
              class="flex-1 overflow-y-auto px-6 py-6"
              @submit.prevent="save"
            >
              <!-- TAB 1: INDUSTRY CONTENT -->
              <div v-show="modalTab === 'content'" class="space-y-5">
                <!-- Name & Slug Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Name -->
                  <div>
                    <label
                      class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                      Name *
                    </label>

                    <input
                      v-model="form.name"
                      type="text"
                      placeholder="Enter industry name"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                      @input="onNameInput"
                    />
                  </div>

                  <!-- Slug -->
                  <div>
                    <label
                      class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                      Slug *
                    </label>

                    <input
                      v-model="form.slug"
                      type="text"
                      placeholder="Enter slug"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                      @input="slugManuallyEdited = true"
                    />
                  </div>
                </div>

                <!-- Icon & Cover Image Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Icon -->
                  <div>
                    <label
                      class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                      Icon
                    </label>

                    <IconPicker
                      v-model="form.icon"
                      placeholder="Select industry icon..."
                    />
                  </div>

                  <!-- Cover Image -->
                  <div>
                    <ImageInput
                      v-model="form.image"
                      label="Industry Cover Image"
                    />
                  </div>
                </div>

                <!-- Description -->
                <div>
                  <label
                    class="mb-2 block text-sm font-semibold text-slate-800"
                  >
                    Description
                  </label>

                  <textarea
                    v-model="form.description"
                    rows="4"
                    placeholder="Enter industry description"
                    class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                  ></textarea>
                </div>

                <!-- Relevant Programs & Typical Scope Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                  <!-- Relevant Programs -->
                  <div class="space-y-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <label class="block text-sm font-semibold text-slate-800">
                          Relevant Programs
                        </label>
                        <p class="text-xs text-slate-500">
                          Key client deployments or facilities.
                        </p>
                      </div>

                      <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.98]"
                        @click="addRelevantProgram"
                      >
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Add Program
                      </button>
                    </div>

                    <div
                      v-if="form.relevant_programs.length === 0"
                      class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-center text-xs text-slate-400"
                    >
                      No relevant programs added yet.
                    </div>

                    <div v-else class="space-y-2">
                      <div
                        v-for="(prog, idx) in form.relevant_programs"
                        :key="idx"
                        class="flex items-center gap-2"
                      >
                        <span class="w-5 text-right font-mono text-xs font-semibold text-slate-400">
                          {{ idx + 1 }}.
                        </span>
                        <input
                          v-model="form.relevant_programs[idx]"
                          type="text"
                          placeholder="e.g. Béjaïa RO Plant, SWCC, Saudi Aramco, etc."
                          class="flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                        />
                        <button
                          type="button"
                          class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                          title="Remove program"
                          @click="removeRelevantProgram(idx)"
                        >
                          <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Typical Scope -->
                  <div class="space-y-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <label class="block text-sm font-semibold text-slate-800">
                          Typical Scope
                        </label>
                        <p class="text-xs text-slate-500">
                          Core engineering & automation delivered.
                        </p>
                      </div>

                      <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.98]"
                        @click="addTypicalScope"
                      >
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Add Scope Item
                      </button>
                    </div>

                    <div
                      v-if="form.typical_scope.length === 0"
                      class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-center text-xs text-slate-400"
                    >
                      No typical scope items added yet.
                    </div>

                    <div v-else class="space-y-2">
                      <div
                        v-for="(scopeItem, idx) in form.typical_scope"
                        :key="idx"
                        class="flex items-center gap-2"
                      >
                        <span class="w-5 text-right font-mono text-xs font-semibold text-slate-400">
                          {{ idx + 1 }}.
                        </span>
                        <input
                          v-model="form.typical_scope[idx]"
                          type="text"
                          placeholder="e.g. PLC & SCADA redundancy, Safety systems"
                          class="flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                        />
                        <button
                          type="button"
                          class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-600"
                          title="Remove scope item"
                          @click="removeTypicalScope(idx)"
                        >
                          <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Sort Order & Active Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                  <!-- Sort Order -->
                  <div>
                    <label
                      class="mb-2 block text-sm font-semibold text-slate-800"
                    >
                      Sort Order
                    </label>

                    <input
                      v-model.number="form.sort_order"
                      type="number"
                      min="0"
                      class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                    />
                  </div>

                  <!-- Active -->
                  <div
                    class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
                  >
                    <div>
                      <p class="text-sm font-semibold text-slate-800">
                        Active Status
                      </p>

                      <p class="mt-0.5 text-xs text-slate-500">
                        Show this industry on the public website.
                      </p>
                    </div>

                    <button
                      type="button"
                      class="relative h-6 w-11 rounded-full transition"
                      :class="
                        form.is_active
                          ? 'bg-slate-900'
                          : 'bg-slate-300'
                      "
                      @click="form.is_active = !form.is_active"
                    >
                      <span
                        class="absolute top-1 h-4 w-4 rounded-full bg-white shadow-sm transition"
                        :class="
                          form.is_active
                            ? 'left-6'
                            : 'left-1'
                        "
                      ></span>
                    </button>
                  </div>
                </div>
              </div>

              <!-- TAB 2: SEO & SOCIAL METADATA -->
              <div v-show="modalTab === 'seo'" class="space-y-4">
                <SeoFormSection
                  v-model="form.seo"
                  :title="form.name ? `${typeof form.name === 'object' ? (form.name.en || form.name.ar) : form.name} SEO` : 'Industry SEO'"
                  :fallback-title="form.name"
                  :fallback-description="form.description"
                  :fallback-image="typeof form.image === 'string' ? form.image : ''"
                  :page-path="`industries/${form.slug || ''}`"
                />
              </div>

              <!-- Actions Footer (Pinned inside or at bottom of modal) -->
              <div
                class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 mt-6 sm:flex-row sm:items-center sm:justify-between"
              >
                <div>
                  <button
                    v-if="editingId"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-red-200 bg-white px-4 py-2.5 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="saving || deleting"
                    @click="askDelete({ id: editingId, name: form.name })"
                  >
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    {{ deleting ? "Deleting..." : "Delete Industry" }}
                  </button>
                </div>

                <div class="flex items-center justify-end gap-3">
                  <button
                    type="button"
                    class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="saving"
                    @click="closeModal"
                  >
                    Cancel
                  </button>

                  <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60 cursor-pointer"
                    :disabled="saving"
                  >
                    <span
                      v-if="saving"
                      class="material-symbols-outlined animate-spin text-[18px]"
                    >
                      progress_activity
                    </span>

                    <span>
                      {{ saving ? (editingId ? "Saving..." : "Creating...") : (editingId ? "Save Changes" : "Create Industry") }}
                    </span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Confirm Delete Dialog -->
    <ConfirmDialog
      v-model="showDeleteDialog"
      title="Delete Industry"
      :message="`Are you sure you want to delete '${industryToDelete?.name || 'this industry'}'? This action cannot be undone.`"
      confirm-text="Delete"
      cancel-text="Cancel"
      :danger="true"
      icon="delete"
      @confirm="confirmDelete"
      @cancel="showDeleteDialog = false"
    />

    <!-- Toast -->
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
  transition: opacity 0.2s ease;
}

.modal-enter-active > div,
.modal-leave-active > div {
  transition:
    transform 0.2s ease,
    opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div,
.modal-leave-to > div {
  opacity: 0;
  transform: translateY(10px) scale(0.98);
}
</style>
