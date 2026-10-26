<script setup>
import { onMounted, ref } from "vue";
import { adminAPI } from "@/SIS-Dashboard/api";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";

const items = ref([]);

const isLoading = ref(true);
const isSaving = ref(false);
const isDeleting = ref(false);

const error = ref("");
const successMessage = ref("");

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

const editingId = ref(null);
const editingItem = ref(null);

const selectedFile = ref(null);
const imagePreview = ref("");

const isEditing = ref(false);

const showDeleteDialog = ref(false);
const itemToDelete = ref(null);


const API_BASE = (import.meta.env.VITE_API_BASE_URL || "").replace(
  /\/+$/,
  "",
);

function getImageUrl(src) {
  if (!src) return "";

  if (src.startsWith("blob:")) {
    return src;
  }

  if (src.startsWith("http://") || src.startsWith("https://")) {
    return src;
  }

  const normalizedSrc = src
    .replace(/^\/+/, "")
    .replace(/^storage\/+/, "");

  const origin = /^https?:\/\//i.test(API_BASE)
    ? new URL(API_BASE).origin
    : window.location.origin;

  return `${origin}/storage/${normalizedSrc}`;
}

function emptyItem() {
  return {
    title: "",
    description: "",
    alt: "",
    src: "",
  };
}

function normalizeItem(item) {
  return {
    id: item?.id ?? null,
    src: item?.src ?? "",
    alt: item?.alt ?? "",
    title: item?.title ?? "",
    description: item?.description ?? "",
  };
}

function clearImagePreview() {
  if (imagePreview.value?.startsWith("blob:")) {
    URL.revokeObjectURL(imagePreview.value);
  }

  imagePreview.value = "";
}

function resetForm() {
  clearImagePreview();

  selectedFile.value = null;
  editingId.value = null;
  editingItem.value = null;
  isEditing.value = false;
}

function startCreate() {
  error.value = "";
  successMessage.value = "";

  clearImagePreview();

  editingId.value = "new";
  editingItem.value = emptyItem();

  selectedFile.value = null;
  isEditing.value = true;
}

function startEdit(item) {
  error.value = "";
  successMessage.value = "";

  clearImagePreview();

  editingId.value = item.id;
  editingItem.value = { ...item };

  selectedFile.value = null;
  isEditing.value = true;
}

function cancelEdit() {
  resetForm();
  error.value = "";
}

function handleFileChange(event) {
  const file = event.target.files?.[0];

  if (!file) return;

  if (!file.type.startsWith("image/")) {
    error.value = "Please select a valid image file.";
    event.target.value = "";
    return;
  }

  selectedFile.value = file;

  clearImagePreview();

  imagePreview.value = URL.createObjectURL(file);

  error.value = "";
}

function removeSelectedImage() {
  clearImagePreview();
  selectedFile.value = null;
}

async function loadHero() {
  isLoading.value = true;
  error.value = "";

  try {
    const data = await adminAPI.getHeroSlides();

    items.value = Array.isArray(data)
      ? data.map(normalizeItem)
      : [];
  } catch (err) {
    console.error("Failed to load hero slides:", err);

    error.value =
      err?.message ||
      "Unable to load the hero slides. Please try again.";
  } finally {
    isLoading.value = false;
  }
}

async function saveItem() {
  if (!editingItem.value) return;

  error.value = "";
  successMessage.value = "";

  if (!editingItem.value.title?.trim()) {
    error.value = "Title is required.";
    return;
  }

  if (!editingItem.value.description?.trim()) {
    error.value = "Description is required.";
    return;
  }

  if (!editingItem.value.alt?.trim()) {
    error.value = "Alt text is required.";
    return;
  }

  if (editingId.value === "new" && !selectedFile.value) {
    error.value = "Please select an image.";
    return;
  }

  isSaving.value = true;

  const isCreating = editingId.value === "new";

  try {
    let payload;

if (selectedFile.value) {
  const formData = new FormData();

  formData.append("title", editingItem.value.title.trim());
  formData.append(
    "description",
    editingItem.value.description.trim(),
  );
  formData.append("alt", editingItem.value.alt.trim());
  formData.append("src", selectedFile.value);

  payload = formData;
} else {
  payload = {
    title: editingItem.value.title.trim(),
    description: editingItem.value.description.trim(),
    alt: editingItem.value.alt.trim(),
  };
}

    if (isCreating) {
      await adminAPI.createHeroSlide(payload);
    } else {
      await adminAPI.updateHeroSlide(
        editingId.value,
        payload,
      );
    }

    resetForm();

    await loadHero();

    successMessage.value = isCreating
      ? "Hero slide created successfully."
      : "Hero slide updated successfully.";

    notify(
      isCreating ? "Slide Created" : "Slide Updated",
      successMessage.value,
      "success",
    );
  } catch (err) {
    console.error("Failed to save hero slide:", err);

    error.value =
      err?.message ||
      "Unable to save the hero slide. Please try again.";

    notify("Save Failed", error.value, "error");
  } finally {
    isSaving.value = false;
  }
}

function askDelete(item) {
  error.value = "";
  successMessage.value = "";

  itemToDelete.value = item;
  showDeleteDialog.value = true;
}

async function deleteItem() {
  if (!itemToDelete.value) return;

  error.value = "";
  successMessage.value = "";
  isDeleting.value = true;

  try {
    await adminAPI.deleteHeroSlide(itemToDelete.value.id);

    showDeleteDialog.value = false;
    itemToDelete.value = null;

    await loadHero();

    successMessage.value = "Hero slide deleted successfully.";

    notify("Slide Deleted", successMessage.value, "success");
  } catch (err) {
    console.error("Failed to delete hero slide:", err);

    error.value =
      err?.message ||
      "Unable to delete the hero slide. Please try again.";

    notify("Delete Failed", error.value, "error");
  } finally {
    isDeleting.value = false;
  }
}

function handleImageError(event) {
  event.target.style.display = "none";
}

onMounted(loadHero);
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="Hero Slides"
      subtitle="Manage the homepage hero carousel slides and visual banners."
      category="Hero & Presentation"
      icon="view_carousel"
    >
      <template #actions>
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
          @click="startCreate"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Add Slide
        </button>
      </template>
    </AdminPageHeader>

      <!-- Error -->
      <div
        v-if="error && !isEditing"
        class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4"
      >
        <div class="flex items-start gap-3">
          <span
            class="material-symbols-outlined mt-0.5 text-[20px] text-red-500"
          >
            error
          </span>

          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-red-800">
              Unable to load hero
            </p>

            <p class="mt-1 text-sm text-red-700">
              {{ error }}
            </p>
          </div>

          <button
            type="button"
            class="rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-50"
            @click="loadHero"
          >
            Try Again
          </button>
        </div>
      </div>

      <!-- Success -->
      <div
        v-if="successMessage"
        class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3"
      >
        <span
          class="material-symbols-outlined text-[20px] text-emerald-600"
        >
          check_circle
        </span>

        <p class="text-sm font-medium text-emerald-800">
          {{ successMessage }}
        </p>
      </div>

      <!-- Loading -->
      <div v-if="isLoading" class="space-y-4">
        <div
          v-for="n in 3"
          :key="n"
          class="animate-pulse overflow-hidden rounded-2xl border border-slate-200 bg-white"
        >
          <div class="flex flex-col lg:flex-row">
            <div
              class="h-52 w-full shrink-0 bg-slate-200 lg:h-64 lg:w-72"
            ></div>

            <div class="flex-1 space-y-4 p-5">
              <div class="h-4 w-24 rounded bg-slate-200"></div>

              <div class="h-6 w-1/3 rounded bg-slate-200"></div>

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
          v-if="items.length === 0"
          class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"
        >
          <div
            class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100"
          >
            <span
              class="material-symbols-outlined text-[28px] text-slate-500"
            >
              image
            </span>
          </div>

          <h2 class="text-base font-semibold text-slate-900">
            No hero slides found
          </h2>

          <p
            class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500"
          >
            Add your first homepage hero slide to get started.
          </p>

          <button
            type="button"
            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
            @click="startCreate"
          >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Add Slide
          </button>
        </div>

        <!-- Slides -->
        <article
          v-for="(item, index) in items"
          :key="item.id"
          class="group overflow-hidden rounded-2xl border border-sis-border bg-white shadow-sm transition-all hover:border-sis-accent/30 hover:shadow-md"
        >
          <div class="flex flex-col lg:flex-row">
            <!-- Image -->
            <div
              class="relative h-52 w-full shrink-0 bg-slate-100 lg:h-auto lg:w-72"
            >
              <img
                v-if="item.src"
                :src="getImageUrl(item.src)"
                :alt="item.alt || item.title"
                class="h-full w-full object-cover"
                @error="handleImageError"
              />

              <div
                v-else
                class="flex h-full min-h-52 items-center justify-center"
              >
                <div class="text-center">
                  <span
                    class="material-symbols-outlined text-[36px] text-slate-300"
                  >
                    image
                  </span>

                  <p class="mt-2 text-xs text-slate-400">
                    No image
                  </p>
                </div>
              </div>

              <div
                class="absolute left-3 top-3 rounded-lg bg-slate-900/80 px-2.5 py-1 text-xs font-semibold text-white backdrop-blur"
              >
                Slide {{ index + 1 }}
              </div>
            </div>

            <!-- Content -->
            <div class="flex min-w-0 flex-1 flex-col p-5">
              <div
                class="flex items-start justify-between gap-4"
              >
                <div class="min-w-0">
                  <p
                    class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400"
                  >
                    Hero Slide
                  </p>

                  <h2
                    class="truncate text-lg font-bold text-slate-900"
                  >
                    {{ item.title || "Untitled slide" }}
                  </h2>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                  <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                    @click="startEdit(item)"
                  >
                    <span
                      class="material-symbols-outlined text-[18px]"
                    >
                      edit
                    </span>

                    Edit
                  </button>

                  <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="isDeleting"
                    @click="askDelete(item)"
                  >
                    <span
                      class="material-symbols-outlined text-[18px]"
                    >
                      delete
                    </span>
                  </button>
                </div>
              </div>

              <p
                class="mt-4 line-clamp-3 text-sm leading-6 text-slate-600"
              >
                {{ item.description || "No description provided." }}
              </p>

              <div
                class="mt-5 border-t border-slate-100 pt-4"
              >
                <p class="truncate text-xs text-slate-400">
                  {{ item.src }}
                </p>
              </div>
            </div>
          </div>
        </article>
      </div>

    <!-- Edit / Create Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div
          v-if="isEditing"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
          @click.self="cancelEdit"
        >
          <div
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
          >
            <!-- Header -->
            <div
              class="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-5"
            >
              <div>
                <p
                  class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                >
                  Hero Slide
                </p>

                <h2
                  class="mt-1 text-xl font-bold text-slate-900"
                >
                  {{ editingId === "new" ? "Add Slide" : "Edit Slide" }}
                </h2>
              </div>

              <button
                type="button"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                @click="cancelEdit"
              >
                <span
                  class="material-symbols-outlined text-[22px]"
                >
                  close
                </span>
              </button>
            </div>

            <!-- Form -->
            <form
              class="space-y-5 px-6 py-6"
              @submit.prevent="saveItem"
            >
              <!-- Image -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Image
                </label>

                <label
                  class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-6 py-8 text-center transition hover:border-slate-300 hover:bg-slate-100"
                >
                  <span
                    class="material-symbols-outlined text-[32px] text-slate-400"
                  >
                    cloud_upload
                  </span>

                  <span
                    class="mt-2 text-sm font-semibold text-slate-700"
                  >
                    Choose an image
                  </span>

                  <span class="mt-1 text-xs text-slate-400">
                    Select an image from your computer
                  </span>

                  <input
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleFileChange"
                  />
                </label>

                <!-- Preview -->
                <div
                  v-if="imagePreview || editingItem?.src"
                  class="relative mt-3 overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                >
                  <img
                    :src="
                      imagePreview ||
                      getImageUrl(editingItem?.src)
                    "
                    :alt="
                      editingItem?.alt ||
                      editingItem?.title ||
                      'Hero image'
                    "
                    class="h-56 w-full object-cover"
                  />

                  <button
                    v-if="selectedFile"
                    type="button"
                    class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900/80 text-white backdrop-blur transition hover:bg-slate-900"
                    @click="removeSelectedImage"
                  >
                    <span
                      class="material-symbols-outlined text-[20px]"
                    >
                      close
                    </span>
                  </button>
                </div>

                <p
                  v-if="editingId !== 'new' && !selectedFile"
                  class="mt-2 text-xs text-slate-400"
                >
                  Leave the image unchanged to keep the current image.
                </p>
              </div>

              <!-- Alt -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Alt Text
                </label>

                <input
                  v-model="editingItem.alt"
                  type="text"
                  placeholder="Describe the image"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                />
              </div>

              <!-- Title -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Title
                </label>

                <input
                  v-model="editingItem.title"
                  type="text"
                  placeholder="Enter title"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                />
              </div>

              <!-- Description -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-800"
                >
                  Description
                </label>

                <textarea
                  v-model="editingItem.description"
                  rows="5"
                  placeholder="Enter description"
                  class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                ></textarea>
              </div>

              <!-- Error -->
              <div
                v-if="error"
                class="flex items-start gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3"
              >
                <span
                  class="material-symbols-outlined text-[20px] text-red-500"
                >
                  error
                </span>

                <p class="text-sm leading-5 text-red-700">
                  {{ error }}
                </p>
              </div>

              <!-- Actions -->
              <div
                class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end"
              >
                <button
                  type="button"
                  class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="isSaving"
                  @click="cancelEdit"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                  :disabled="isSaving"
                >
                  <span
                    v-if="isSaving"
                    class="material-symbols-outlined animate-spin text-[18px]"
                  >
                    progress_activity
                  </span>

                  <span>
                    {{ isSaving ? "Saving..." : "Save Changes" }}
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
      v-model="showDeleteDialog"
      title="Delete Hero Slide"
      :message="`Are you sure you want to delete '${itemToDelete?.title || 'this slide'}'? This action cannot be undone.`"
      confirm-text="Delete"
      cancel-text="Cancel"
      :danger="true"
      icon="delete"
      @confirm="deleteItem"
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