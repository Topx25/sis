<template>
  <div class="mx-auto max-w-7xl space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="Why SiS"
      subtitle="Manage the Why SiS section content and feature differentiators."
      category="Value Proposition"
      icon="psychology"
    >
      <template #actions>
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-sis-border bg-white px-4 py-2.5 text-sm font-semibold text-sis-text shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
          :disabled="isLoading"
          @click="loadSection"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': isLoading }">sync</span>
          Refresh
        </button>
      </template>
    </AdminPageHeader>

        <!-- Loading -->
        <div
          v-if="isLoading"
          class="space-y-6"
        >
          <div
            class="h-80 animate-pulse rounded-2xl bg-slate-200 dark:bg-slate-800"
          />

          <div
            class="h-64 animate-pulse rounded-2xl bg-slate-200 dark:bg-slate-800"
          />
        </div>

        <!-- Error -->
        <div
          v-else-if="error"
          class="rounded-2xl border border-red-200 bg-red-50 p-6 dark:border-red-900/40 dark:bg-red-950/20"
        >
          <h2 class="font-semibold text-red-700 dark:text-red-400">
            Unable to load Why SiS
          </h2>

          <p class="mt-2 text-sm text-red-600 dark:text-red-300">
            {{ error }}
          </p>

          <button
            type="button"
            class="mt-4 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
            @click="loadSection"
          >
            Try Again
          </button>
        </div>

        <!-- Content -->
        <div
          v-else
          class="space-y-8"
        >
          <!-- ==================== SECTION ==================== -->
          <section
            class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#071426]"
          >
            <div
              class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800"
            >
              <div>
                <h2 class="text-lg font-bold">
                  Section Content
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                  Manage the main Why SiS section content in both languages.
                </p>
              </div>

              <button
                type="button"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="sectionSaving || !section"
                @click="saveSection"
              >
                {{ sectionSaving ? "Saving..." : "Save Section" }}
              </button>
            </div>

            <div class="p-5">
              <div
                v-if="!section"
                class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
              >
                No section data available.
              </div>

              <div
                v-else
                class="grid gap-6 lg:grid-cols-2"
              >
                <!-- Left Title -->
                <div
                  class="rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-[#0a192d]"
                >
                  <div class="mb-4">
                    <h3 class="font-semibold">
                      Left Title
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                      Main heading displayed on the left side.
                    </p>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                      >
                        English
                      </label>

                      <input
                        v-model="sectionForm.left_title.en"
                        type="text"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-[#071426] dark:text-white"
                        placeholder="Enter English title"
                      />
                    </div>

        
                  </div>
                </div>

                <!-- Right Subtitle -->
                <div
                  class="rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-[#0a192d]"
                >
                  <div class="mb-4">
                    <h3 class="font-semibold">
                      Right Subtitle
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                      Small subtitle above the main right title.
                    </p>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                      >
                        English
                      </label>

                      <input
                        v-model="sectionForm.right_subtitle.en"
                        type="text"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-[#071426] dark:text-white"
                        placeholder="Enter English subtitle"
                      />
                    </div>

                  
                  </div>
                </div>

                <!-- Right Title -->
                <div
                  class="rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-[#0a192d]"
                >
                  <div class="mb-4">
                    <h3 class="font-semibold">
                      Right Title
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                      Main text displayed on the right side.
                    </p>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                      >
                        English
                      </label>

                      <textarea
                        v-model="sectionForm.right_title.en"
                        rows="4"
                        class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-[#071426] dark:text-white"
                        placeholder="Enter English title"
                      />
                    </div>

                  
                  </div>
                </div>

                <!-- Right Description -->
                <div
                  class="rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-[#0a192d]"
                >
                  <div class="mb-4">
                    <h3 class="font-semibold">
                      Right Description
                    </h3>

                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                      Supporting description below the right title.
                    </p>
                  </div>

                  <div class="space-y-4">
                    <div>
                      <label
                        class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                      >
                        English
                      </label>

                      <textarea
                        v-model="sectionForm.right_description.en"
                        rows="4"
                        class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-[#071426] dark:text-white"
                        placeholder="Enter English description"
                      />
                    </div>

          
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- ==================== FEATURES ==================== -->
          <section
            class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#071426]"
          >
            <div
              class="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800"
            >
              <div>
                <h2 class="text-lg font-bold">
                  Features
                </h2>

                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                  Manage the features displayed inside the Why SiS section.
                </p>
              </div>

              <button
                type="button"
                class="inline-flex items-center justify-center rounded-xl border border-blue-600 px-5 py-2.5 text-sm font-semibold text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-950/20"
                @click="startCreate"
              >
                + Add Feature
              </button>
            </div>

            <div class="p-5">
              <div
                v-if="!items.length"
                class="rounded-xl border border-dashed border-slate-300 p-10 text-center dark:border-slate-700"
              >
                <p class="text-sm text-slate-500 dark:text-slate-400">
                  No features found.
                </p>
              </div>

              <div
                v-else
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
              >
                <article
                  v-for="item in items"
                  :key="item.id"
                  class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 transition hover:border-blue-300 hover:shadow-md dark:border-slate-800 dark:bg-[#0a192d] dark:hover:border-blue-800"
                >
                  <div class="flex items-start justify-between gap-4">
                    <div
                      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400"
                    >
                      ✓
                    </div>

                    <div class="flex items-center gap-2">
                      <button
                        type="button"
                        class="rounded-lg px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 dark:text-blue-400 dark:hover:bg-blue-950/30"
                        @click="startEdit(item)"
                      >
                        Edit
                      </button>

                      <button
                        type="button"
                        class="rounded-lg px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-950/30"
                        @click="askDelete(item)"
                      >
                        Delete
                      </button>
                    </div>
                  </div>

                  <h3 class="mt-5 text-base font-bold">
                    {{ item.title || "Untitled Feature" }}
                  </h3>

                  <p
                    class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400"
                  >
                    {{ item.description || "No description available." }}
                  </p>
                </article>
              </div>
            </div>
          </section>
        </div>

    <!-- ==================== FEATURE MODAL ==================== -->
    <teleport to="body">
      <transition name="modal-fade">
        <div
          v-if="showModal"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 px-4 py-6 backdrop-blur-sm"
          @click.self="closeModal"
        >
          <div
            class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white shadow-2xl dark:border-slate-800 dark:bg-[#071426]"
          >
            <div
              class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-800"
            >
              <div>
                <h2 class="text-lg font-bold">
                  {{ editingId === "new" ? "Add Feature" : "Edit Feature" }}
                </h2>

                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                  Update the feature content.
                </p>
              </div>

              <button
                type="button"
                class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:hover:bg-slate-800 dark:hover:text-white"
                @click="closeModal"
              >
                ✕
              </button>
            </div>

            <form
              class="space-y-5 p-5"
              @submit.prevent="saveItem"
            >
              <div>
                <label
                  class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                >
                  Title
                </label>

                <input
                  v-model="form.title"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-[#0a192d] dark:text-white"
                  placeholder="Feature title"
                />
              </div>

              <div>
                <label
                  class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300"
                >
                  Description
                </label>

                <textarea
                  v-model="form.description"
                  rows="5"
                  class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-[#0a192d] dark:text-white"
                  placeholder="Feature description"
                />
              </div>

              <div
                class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end"
              >
                <button
                  type="button"
                  class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                  @click="closeModal"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="saving"
                >
                  {{ saving ? "Saving..." : "Save Feature" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>
    </teleport>

    <!-- ==================== DELETE MODAL ==================== -->
    <ConfirmDialog
      :model-value="!!itemToDelete"
      title="Delete Feature"
      message="Are you sure you want to delete this feature? This action cannot be undone."
      confirm-text="Delete"
      cancel-text="Cancel"
      :danger="true"
      icon="delete"
      @confirm="confirmDelete"
      @cancel="itemToDelete = null"
      @update:model-value="(val) => { if (!val) itemToDelete = null }"
    />

    <!-- Toast Notification -->
    <ToastNotification
      v-if="showToast"
      :title="toast.title"
      :message="toast.message"
      :variant="toast.variant"
      @close="showToast = false"
    />
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import { adminAPI } from "@/SIS-Dashboard/api";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";

const showToast = ref(false);
const toast = ref({
  title: "",
  message: "",
  variant: "success",
});

function notify(title, message, variant = "success") {
  toast.value = {
    title,
    message,
    variant,
  };
  showToast.value = true;
}

const isLoading = ref(false);
const error = ref("");

const section = ref(null);
const sectionSaving = ref(false);

const items = ref([]);

const showModal = ref(false);
const saving = ref(false);
const deleting = ref(false);

const editingId = ref(null);
const itemToDelete = ref(null);

const sectionForm = reactive({
  left_title: {
    en: "",
    ar: "",
  },

  right_subtitle: {
    en: "",
    ar: "",
  },

  right_title: {
    en: "",
    ar: "",
  },

  right_description: {
    en: "",
    ar: "",
  },
});

const form = reactive({
  title: "",
  description: "",
});

function normalizeWhySiSItem(item) {
  return {
    id: item?.id ?? null,
    title: item?.title ?? "",
    description: item?.description ?? "",
  };
}

function normalizeItems(data) {
  if (!Array.isArray(data)) return [];

  return data.map(normalizeWhySiSItem);
}

function resetSectionForm(data = null) {
  sectionForm.left_title.en = data?.left_title?.en ?? "";
  sectionForm.left_title.ar = data?.left_title?.ar ?? "";

  sectionForm.right_subtitle.en = data?.right_subtitle?.en ?? "";
  sectionForm.right_subtitle.ar = data?.right_subtitle?.ar ?? "";

  sectionForm.right_title.en = data?.right_title?.en ?? "";
  sectionForm.right_title.ar = data?.right_title?.ar ?? "";

  sectionForm.right_description.en =
    data?.right_description?.en ?? "";

  sectionForm.right_description.ar =
    data?.right_description?.ar ?? "";
}

function resetForm() {
  form.title = "";
  form.description = "";
}

async function loadSection() {
  isLoading.value = true;
  error.value = "";

  try {
    const data = await adminAPI.getSection("additional");

    section.value = data?.section ?? null;

    resetSectionForm(section.value);

    items.value = normalizeItems(data?.features);
  } catch (err) {
    console.error("Failed to load Why SiS:", err);

    error.value =
      err?.message ||
      "Something went wrong while loading the Why SiS section.";
  } finally {
    isLoading.value = false;
  }
}

async function saveSection() {
  if (!section.value?.id) {
    error.value = "Why SiS section ID is missing.";
    return;
  }

  sectionSaving.value = true;
  error.value = "";

  try {
    const payload = {
      left_title: {
        en: sectionForm.left_title.en,
        ar: sectionForm.left_title.ar,
      },

      right_subtitle: {
        en: sectionForm.right_subtitle.en,
        ar: sectionForm.right_subtitle.ar,
      },

      right_title: {
        en: sectionForm.right_title.en,
        ar: sectionForm.right_title.ar,
      },

      right_description: {
        en: sectionForm.right_description.en,
        ar: sectionForm.right_description.ar,
      },
    };

    await adminAPI.updateSection(
      "additional",
      section.value.id,
      payload,
    );

    /*
     * Don't use the PUT response to update the form.
     * The PUT response may have a different structure.
     *
     * Re-fetch the complete Why SiS data instead.
     */
    await loadSection();

    notify(
      "Section updated",
      "Why SiS section has been saved successfully.",
      "success",
    );
  } catch (err) {
    console.error("Failed to update Why SiS section:", err);

    notify(
      "Save failed",
      err?.message || "Something went wrong while updating the Why SiS section.",
      "error",
    );
  } finally {
    sectionSaving.value = false;
  }
}

function startEdit(item) {
  editingId.value = item.id;

  form.title = item.title;
  form.description = item.description;

  showModal.value = true;
}

function startCreate() {
  editingId.value = "new";

  resetForm();

  showModal.value = true;
}

function closeModal(force = false) {
  if (saving.value && force !== true) return;

  showModal.value = false;
  editingId.value = null;

  resetForm();
}

async function saveItem() {
  if (!form.title.trim()) {
    notify("Validation error", "Please enter a feature title.", "error");
    return;
  }

  if (!form.description.trim()) {
    notify("Validation error", "Please enter a feature description.", "error");
    return;
  }

  saving.value = true;

  try {
    const payload = {
      title: form.title.trim(),
      description: form.description.trim(),
    };

    /*
     * Create is supported through the generic admin API.
     */
    if (editingId.value === "new") {
      const created = await adminAPI.createItem(
        "home-why-sis",
        payload,
      );

      /*
       * If the backend does not expose create for this resource,
       * this will fail and the error will be shown.
       */
      if (created) {
        items.value.push(normalizeWhySiSItem(created));
      }
    } else {
      const updated = await adminAPI.updateItem(
        "home-why-sis",
        editingId.value,
        payload,
      );

      const index = items.value.findIndex(
        (item) => item.id === editingId.value,
      );

      if (index !== -1) {
        items.value[index] = normalizeWhySiSItem(
          updated || {
            ...items.value[index],
            ...payload,
          },
        );
      }
    }

    closeModal(true);

    /*
     * Reload from backend to guarantee that the dashboard
     * reflects the actual saved data.
     */
    await loadSection();

    notify(
      "Feature saved",
      editingId.value === "new"
        ? "New feature has been created."
        : "Feature has been updated successfully.",
      "success",
    );
  } catch (err) {
    console.error("Failed to save Why SiS feature:", err);

    notify(
      "Save failed",
      err?.message || "Something went wrong while saving the feature.",
      "error",
    );
  } finally {
    saving.value = false;
  }
}

function askDelete(item) {
  itemToDelete.value = item;
}

async function confirmDelete() {
  if (!itemToDelete.value?.id) return;

  deleting.value = true;

  try {
    await adminAPI.deleteWhySiS(itemToDelete.value.id);

    itemToDelete.value = null;

    await loadSection();

    notify("Feature deleted", "The feature has been removed.", "success");
  } catch (err) {
    console.error("Failed to delete Why SiS feature:", err);

    notify(
      "Delete failed",
      err?.message || "Something went wrong while deleting the feature.",
      "error",
    );
  } finally {
    deleting.value = false;
  }
}

onMounted(() => {
  loadSection();
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
