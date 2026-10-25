<script setup>
import { computed, onMounted, reactive, ref } from "vue";
import { adminAPI } from "@/SIS-Dashboard/api/index.js";

import ToastNotification from "@/components/global/ToastNotification.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import IconPicker from "@/SIS-Dashboard/components/IconPicker.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";

import { COLLECTION_SCHEMA } from "@/SIS-Dashboard/config/collectionSchema.js";
import { useCollectionManager } from "@/SIS-Dashboard/composables/useCollectionManager.js";

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

const schema = computed(() => COLLECTION_SCHEMA.services);

const resource = computed(() => "services");

const {
  loading,
  saving,
  items,
  search,
  page,
  pages,
  filtered,
  paginated,

  modalOpen,
  editingId,
  form,

  confirmState,

  canCreate,
  canUpdate,
  canDelete,

  getMaterialIcon,

  openCreate,
  openEdit,
  closeModal,

  save,
  askRemove,
  closeConfirm,
  onDeleteConfirmed,
} = useCollectionManager(
  resource,
  schema,
  notify,
);

const sectionLoading = ref(false);
const sectionSaving = ref(false);
const sectionId = ref(1);

const sectionForm = reactive({
  subtitle: "Services & Capabilities",
  title: "One platform,",
  title_highlight: "every",
  description: "industrial capability",
});

async function loadSectionHeader() {
  sectionLoading.value = true;
  try {
    const res = await adminAPI.getSection("services");
    const data = res?.section || res;
    if (data && (data.subtitle !== undefined || data.title !== undefined || data.title_highlight !== undefined || data.description !== undefined)) {
      sectionId.value = data.id || 1;
      sectionForm.subtitle = data.subtitle || "";
      sectionForm.title = data.title || "";
      sectionForm.title_highlight = data.title_highlight !== undefined ? data.title_highlight : "";
      sectionForm.description = data.description || "";
    }
  } catch (err) {
    console.warn("Could not load services section header:", err);
  } finally {
    sectionLoading.value = false;
  }
}

async function saveSectionHeader() {
  sectionSaving.value = true;
  try {
    await adminAPI.updateSection("services", sectionId.value || 1, {
      subtitle: sectionForm.subtitle,
      title: sectionForm.title,
      title_highlight: sectionForm.title_highlight,
      description: sectionForm.description,
    });
    notify("Success", "Section header updated successfully.", "success");
  } catch (err) {
    console.error("Failed to update section header:", err);
    notify("Error", err?.message || "Failed to update section header.", "error");
  } finally {
    sectionSaving.value = false;
  }
}

onMounted(() => {
  loadSectionHeader();
});
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="Services"
      subtitle="Manage the services and engineering capabilities offered by SiS."
      category="Services & Capabilities"
      icon="build"
    >
      <template #actions>
        <button
          v-if="canCreate"
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
          @click="openCreate"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Add Service
        </button>
      </template>
    </AdminPageHeader>

      <!-- Section Header Editor -->
      <div
        class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
      >
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-base font-bold text-slate-900">
              Section Header (One platform, every industrial capability)
            </h2>
            <p class="text-xs text-slate-500">
              Customize the section's intro badge, main title, highlighted keyword, and description.
            </p>
          </div>

          <button
            type="button"
            :disabled="sectionSaving"
            class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-50"
            @click="saveSectionHeader"
          >
            <span class="material-symbols-outlined text-[16px]">
              save
            </span>
            {{ sectionSaving ? "Saving..." : "Save Header" }}
          </button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <!-- Subtitle / Badge -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
              Subtitle / Badge
            </label>
            <input
              v-model="sectionForm.subtitle"
              type="text"
              placeholder="e.g. Services & Capabilities"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            />
          </div>

          <!-- Title Part 1 -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
              Title
            </label>
            <input
              v-model="sectionForm.title"
              type="text"
              placeholder="e.g. One platform,"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            />
          </div>

          <!-- Highlight Word -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
              Highlight Word
            </label>
            <input
              v-model="sectionForm.title_highlight"
              type="text"
              placeholder="e.g. every"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:blue-500"
            />
          </div>

          <!-- Title Description -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
              Description / Suffix
            </label>
            <input
              v-model="sectionForm.description"
              type="text"
              placeholder="e.g. industrial capability"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            />
          </div>
        </div>
      </div>

      <!-- Error -->
      <div
        v-if="!loading && items.length === 0 && !filtered.length"
        class="mb-6"
      >
        <!-- Intentionally handled by empty state below -->
      </div>

    <!-- Search & Filter Bar -->
    <div
      v-if="!loading && items.length > 0"
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
          placeholder="Search services..."
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
        <span class="font-semibold text-sis-text">
          {{ filtered.length }}
        </span>
        {{ filtered.length === 1 ? "service" : "services" }}
      </div>
    </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-4">
        <div
          v-for="n in 3"
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
              engineering
            </span>
          </div>

          <h2 class="text-base font-semibold text-slate-900">
            No services found
          </h2>

          <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
            {{
              search
                ? "There are no services matching your search."
                : "There are no services available yet."
            }}
          </p>

          <button
            v-if="canCreate && !search"
            type="button"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
            @click="openCreate"
          >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Add Service
          </button>
        </div>

        <!-- Services -->
        <template v-else>
          <article
            v-for="(service, index) in paginated"
            :key="service.id"
            class="group rounded-2xl border border-sis-border bg-white p-5 shadow-sm transition-all hover:border-sis-accent/30 hover:shadow-md"
          >
            <div class="flex items-start gap-4">

              <!-- Number / Icon -->
              <div
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-blue-100 bg-blue-50/80 text-sis-accent"
              >
                <span
                  v-if="service.icon"
                  class="material-symbols-outlined text-[22px]"
                >
                  {{ getMaterialIcon(service.icon) }}
                </span>

                <span
                  v-else
                  class="text-sm font-bold text-sis-accent"
                >
                  {{ String(index + 1).padStart(2, "0") }}
                </span>
              </div>

              <!-- Content -->
              <div class="min-w-0 flex-1">
                <div
                  class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                >
                  <div class="min-w-0">
                    <p
                      class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400"
                    >
                      SiS Service
                    </p>

                    <h2
                      class="text-lg font-bold text-slate-900"
                    >
                      {{ service.title || "Untitled service" }}
                    </h2>

                    <p
                      v-if="service.headline"
                      class="mt-0.5 text-xs font-medium text-blue-600"
                    >
                      {{ service.headline }}
                    </p>
                  </div>

                  <!-- Actions -->
                  <div
                    v-if="canUpdate || canDelete"
                    class="flex shrink-0 items-center gap-2"
                  >
                    <!-- Edit -->
                    <button
                      v-if="canUpdate"
                      type="button"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                      @click="openEdit(service)"
                    >
                      <span class="material-symbols-outlined text-[18px]">
                        edit
                      </span>

                      Edit
                    </button>

                    <!-- Delete -->
                    <button
                      v-if="canDelete"
                      type="button"
                      class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                      @click="askRemove(service)"
                    >
                      <span class="material-symbols-outlined text-[18px]">
                        delete
                      </span>

                      Delete
                    </button>
                  </div>
                </div>

                <!-- Summary -->
                <p
                  v-if="service.summary"
                  class="mt-3 max-w-4xl text-sm leading-6 text-slate-600"
                >
                  {{ service.summary }}
                </p>

                <!-- Description fallback -->
                <p
                  v-else-if="service.description"
                  class="mt-3 max-w-4xl text-sm leading-6 text-slate-600"
                >
                  {{ service.description }}
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
                    v-if="service.slug"
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      link
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      {{ service.slug }}
                    </span>
                  </div>

                  <div
                    v-if="service.icon"
                    class="flex items-center gap-1.5"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-slate-400"
                    >
                      star
                    </span>

                    <span class="text-xs font-medium text-slate-500">
                      {{ service.icon }}
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

    <!-- Create / Edit Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div
          v-if="modalOpen"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
          @click.self="closeModal"
        >
          <div
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
          >
            <!-- Modal Header -->
            <div
              class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5"
            >
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                >
                  SiS Service
                </p>

                <h2 class="mt-1 text-xl font-bold text-slate-900">
                  {{
                    editingId !== null
                      ? "Edit Service"
                      : "Add Service"
                  }}
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

            <!-- Modal Body -->
            <form
              class="space-y-5 px-6 py-6"
              @submit.prevent="save"
            >
              <!-- Title -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Title
                </label>

                <input
                  v-model="form.title"
                  type="text"
                  placeholder="Enter service title"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                />
              </div>

              <!-- Headline / Subtitle -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Headline / Subtitle
                </label>

                <input
                  v-model="form.headline"
                  type="text"
                  placeholder="e.g. One platform, every industrial capability"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                />
              </div>

              <!-- Slug -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Slug
                </label>

                <input
                  v-model="form.slug"
                  type="text"
                  placeholder="Enter slug"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                />

                <p class="mt-1.5 text-xs text-slate-400">
                  Leave empty to generate it from the title.
                </p>
              </div>

              <!-- Icon -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Icon
                </label>

                <IconPicker
                  v-model="form.icon"
                  placeholder="Select service icon..."
                />
              </div>

              <!-- Summary -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Summary
                </label>

                <textarea
                  v-model="form.summary"
                  rows="3"
                  placeholder="Enter a short summary"
                  class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                ></textarea>
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
                  rows="6"
                  placeholder="Enter service description"
                  class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                ></textarea>
              </div>

              <!-- Actions -->
              <div
                class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end"
              >
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
                  class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                  :disabled="saving"
                >
                  <span
                    v-if="saving"
                    class="material-symbols-outlined animate-spin text-[18px]"
                  >
                    progress_activity
                  </span>

                  <span>
                    {{ saving ? "Saving..." : "Save Changes" }}
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete Confirmation -->
    <ConfirmDialog
      v-model="confirmState.open"
      :title="confirmState.title"
      :message="confirmState.message"
      :confirm-text="confirmState.confirmText || 'Delete'"
      :cancel-text="confirmState.cancelText || 'Cancel'"
      :danger="confirmState.danger ?? true"
      :icon="confirmState.icon || 'delete'"
      @confirm="onDeleteConfirmed"
      @cancel="closeConfirm"
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