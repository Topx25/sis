<template>
  <div class="mx-auto max-w-7xl space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="Clients"
      subtitle="Manage the client logos, names, and display ordering on the website."
      category="Social Proof & Trust"
      icon="business"
    >
      <template #actions>
        <button
          v-if="canCreate"
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
          @click="openCreate"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Add Client
        </button>
      </template>
    </AdminPageHeader>

    <!-- Section Header Editor -->
    <div
      class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
    >
      <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900">
            Section Header (Trusted by Industry Leaders)
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

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <!-- Subtitle / Badge -->
        <div>
          <label class="mb-1.5 block text-xs font-semibold text-slate-700">
            Subtitle / Badge
          </label>
          <input
            v-model="sectionForm.subtitle"
            type="text"
            placeholder="e.g. Our Clients"
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
            placeholder="e.g. Trusted by"
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
            placeholder="e.g. Industry Leaders"
            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
          />
        </div>
      </div>

      <!-- Description / Suffix -->
      <div class="mt-4">
        <label class="mb-1.5 block text-xs font-semibold text-slate-700">
          Description / Suffix
        </label>
        <input
          v-model="sectionForm.description"
          type="text"
          placeholder="e.g. Partnering with leading organizations across the MENA region to deliver industrial excellence."
          class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
        />
      </div>
    </div>

    <!-- Search & Filter Bar -->
    <div
      v-if="!loading && clients.length > 0"
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
          placeholder="Search clients..."
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
          {{ filteredItems.length }}
        </span>
        {{ filteredItems.length === 1 ? "client" : "clients" }}
      </div>
    </div>

    <!-- Loading -->
    <div
      v-if="loading"
      class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
    >
      <div
        v-for="i in 6"
        :key="i"
        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center gap-4">
          <Skeleton class="h-20 w-20 rounded-xl" />

          <div class="flex-1">
            <Skeleton class="mb-2 h-5 w-2/3" />
            <Skeleton class="h-4 w-1/3" />
          </div>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div
      v-else-if="!filteredItems.length"
      class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"
    >
      <div
        class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-full bg-slate-100 text-slate-400"
      >
        <UiIcon name="business" />
      </div>

      <h2 class="text-lg font-semibold text-slate-800">
        No clients found
      </h2>

      <p class="mt-1 text-sm text-slate-500">
        {{
          search
            ? "Try changing your search."
            : "Add your first client to get started."
        }}
      </p>
    </div>

    <!-- Clients -->
    <div
      v-else
      class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"
    >
      <article
        v-for="client in filteredItems"
        :key="client.id"
        class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-sis-accent/30 hover:shadow-md"
      >
        <div class="flex items-start gap-4">
          <!-- Logo -->
          <div
            class="flex h-20 w-20 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
          >
            <img
              v-if="client.logo_url || client.src"
              :src="resolveImageUrl(client.logo_url || client.src)"
              :alt="client.name"
              class="h-full w-full object-contain p-2"
            />

            <UiIcon
              v-else
              name="business"
              class="text-slate-300 [&_svg]:h-7 [&_svg]:w-7"
            />
          </div>

          <!-- Info -->
          <div class="min-w-0 flex-1">
            <h2
              class="truncate font-['Plus_Jakarta_Sans',Inter,sans-serif] text-base font-bold text-slate-900"
            >
              {{ client.name }}
            </h2>

            <p class="mt-1 text-xs text-slate-500">
              Order: {{ client.sort_order ?? "—" }}
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div
          class="mt-5 flex items-center justify-end gap-4 border-t border-slate-100 pt-4"
        >
          <button
            v-if="canUpdate"
            type="button"
            class="text-sm font-semibold text-slate-600 transition hover:text-sis-accent"
            @click="openEdit(client)"
          >
            Edit
          </button>

          <button
            v-if="canDelete"
            type="button"
            class="text-sm font-semibold text-red-500 transition hover:text-red-600"
            @click="openDelete(client)"
          >
            Delete
          </button>
        </div>
      </article>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <transition name="modal-fade">
        <div
          v-if="modalOpen"
          class="fixed inset-0 z-[9999] flex items-center justify-center bg-[#020b13]/70 px-4 py-6 backdrop-blur-sm"
          @click.self="closeModal"
        >
          <div
            class="max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
          >
            <!-- Modal Header -->
            <div
              class="flex items-center justify-between border-b border-slate-200 px-6 py-5"
            >
              <div>
                <h2
                  class="text-lg font-bold text-slate-900"
                >
                  {{ editingItem ? "Edit Client" : "Add Client" }}
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                  {{
                    editingItem
                      ? "Update this client's information."
                      : "Add a new client to the website."
                  }}
                </p>
              </div>

              <button
                type="button"
                class="grid h-9 w-9 place-items-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                @click="closeModal"
              >
                <UiIcon name="close" />
              </button>
            </div>

            <!-- Form -->
            <form
              class="space-y-5 px-6 py-6"
              @submit.prevent="save"
            >
              <!-- Name -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Name
                </label>

                <input
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="Client name"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/10"
                />
              </div>

              <!-- Logo -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Logo
                </label>

                <div
                  class="overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4"
                >
                  <div
                    v-if="imagePreview"
                    class="mb-4 flex items-center gap-4"
                  >
                    <img
                      :src="imagePreview"
                      :alt="form.name || 'Client logo'"
                      class="h-24 w-24 rounded-xl border border-slate-200 bg-white object-contain p-2"
                    />

                    <button
                      type="button"
                      class="text-sm font-semibold text-red-500 hover:text-red-600"
                      @click="removeImage"
                    >
                      Remove image
                    </button>
                  </div>

                  <input
                    ref="fileInput"
                    type="file"
                    accept="image/*"
                    class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-lg file:border-0 file:bg-sis-accent file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:opacity-90"
                    @change="handleImageChange"
                  />

                  <p class="mt-2 text-xs text-slate-400">
                    Choose an image for this client.
                  </p>
                </div>
              </div>

              <!-- Sort order -->
              <div>
                <label
                  class="mb-2 block text-sm font-semibold text-slate-700"
                >
                  Sort Order
                </label>

                <input
                  v-model.number="form.sort_order"
                  type="number"
                  min="0"
                  placeholder="1"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/10"
                />
              </div>

              <!-- Error -->
              <div
                v-if="errorMessage"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600"
              >
                {{ errorMessage }}
              </div>

              <!-- Actions -->
              <div
                class="flex justify-end gap-3 border-t border-slate-100 pt-5"
              >
                <button
                  type="button"
                  class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                  @click="closeModal"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  :disabled="saving"
                  class="rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
                >
                  {{ saving ? "Saving..." : editingItem ? "Save Changes" : "Add Client" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>
    </Teleport>

    <!-- Confirm Delete -->
    <ConfirmDialog
      v-model="deleteDialogOpen"
      title="Delete Client"
      :message="`Are you sure you want to delete ${itemToDelete?.name || 'this client'}?`"
      confirm-text="Delete"
      cancel-text="Cancel"
      :danger="true"
      icon="delete"
      @confirm="deleteClient"
    />

    <!-- Toast -->
    <ToastNotification
      v-if="showToastNotification"
      :title="toast.title"
      :message="toast.message"
      :variant="toast.variant"
      @close="showToastNotification = false"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from "vue";

import UiIcon from "@/components/common/UiIcon.vue";
import Skeleton from "@/components/common/Skeleton.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";

import { adminAPI } from "@/SIS-Dashboard/api";

const clients = ref([]);
const loading = ref(true);
const saving = ref(false);
const search = ref("");

const sectionLoading = ref(false);
const sectionSaving = ref(false);
const sectionId = ref(1);

const sectionForm = reactive({
  subtitle: "Our Clients",
  title: "Trusted by",
  title_highlight: "Industry Leaders",
  description: "Partnering with leading organizations across the MENA region to deliver industrial excellence.",
});

async function loadSectionHeader() {
  sectionLoading.value = true;
  try {
    const res = await adminAPI.getSection("clients");
    const data =
      res?.section ||
      res?.clients ||
      (Array.isArray(res)
        ? res.find((c) => c.title || c.subtitle || c.description) || res[0]
        : res);
    if (
      data &&
      (data.subtitle !== undefined ||
        data.title !== undefined ||
        data.title_highlight !== undefined ||
        data.description !== undefined)
    ) {
      sectionId.value = data.id || 1;
      sectionForm.subtitle = data.subtitle || "";
      sectionForm.title = data.title || "";
      sectionForm.title_highlight = data.title_highlight !== undefined ? data.title_highlight : "";
      sectionForm.description = data.description || "";
    }
  } catch (err) {
    console.warn("Could not load clients section header:", err);
  } finally {
    sectionLoading.value = false;
  }
}

async function saveSectionHeader() {
  sectionSaving.value = true;
  try {
    await adminAPI.updateSection("clients", sectionId.value || 1, {
      subtitle: sectionForm.subtitle,
      title: sectionForm.title,
      title_highlight: sectionForm.title_highlight,
      description: sectionForm.description,
    });
    showToast("Section header updated successfully.", "success");
  } catch (err) {
    console.error("Failed to update section header:", err);
    showToast(err?.message || "Failed to update section header.", "error");
  } finally {
    sectionSaving.value = false;
  }
}

const modalOpen = ref(false);
const editingItem = ref(null);

const deleteDialogOpen = ref(false);
const itemToDelete = ref(null);

const fileInput = ref(null);
const imageFile = ref(null);
const imagePreview = ref("");

const errorMessage = ref("");

const showToastNotification = ref(false);
const toast = ref({
  title: "Success",
  message: "",
  variant: "success",
});

const canCreate = true;
const canUpdate = true;
const canDelete = true;

const form = ref({
  name: "",
  src: null,
  sort_order: null,
});

const filteredItems = computed(() => {
  const query = search.value.trim().toLowerCase();

  const sorted = [...clients.value].sort(
    (a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0),
  );

  if (!query) return sorted;

  return sorted.filter((client) =>
    client.name?.toLowerCase().includes(query),
  );
});

function showToast(message, type = "success") {
  showToastNotification.value = false;
  setTimeout(() => {
    toast.value = {
      title: type === "error" ? "Error" : "Success",
      message,
      variant: type === "error" ? "error" : "success",
    };
    showToastNotification.value = true;
  }, 10);
}

function resetForm() {
  form.value = {
    name: "",
    src: null,
    sort_order: null,
  };

  imageFile.value = null;
  imagePreview.value = "";
  errorMessage.value = "";

  if (fileInput.value) {
    fileInput.value.value = "";
  }
}

function openCreate() {
  editingItem.value = null;
  resetForm();
  modalOpen.value = true;
}

function openEdit(client) {
  editingItem.value = client;
  errorMessage.value = "";

  form.value = {
    name: client.name || "",
    src: null,
    sort_order: client.sort_order ?? null,
  };

  imageFile.value = null;
  imagePreview.value = resolveImageUrl(
    client.logo_url || client.src || "",
  );

  modalOpen.value = true;
}

function closeModal() {
  modalOpen.value = false;
  editingItem.value = null;
  resetForm();
}

function handleImageChange(event) {
  const file = event.target.files?.[0];

  if (!file) return;

  imageFile.value = file;
  form.value.src = file;

  imagePreview.value = URL.createObjectURL(file);
}

function removeImage() {
  imageFile.value = null;
  form.value.src = null;
  imagePreview.value = "";

  if (fileInput.value) {
    fileInput.value.value = "";
  }
}

function resolveImageUrl(url) {
  if (!url) return "";

  if (url.startsWith("http://") || url.startsWith("https://")) {
    return url;
  }

  return url;
}

async function loadClients() {
  loading.value = true;
  try {
    const result = await adminAPI.getItems("clients");

    const items = Array.isArray(result)
      ? result
      : Array.isArray(result?.clients)
        ? result.clients
        : [];

    clients.value = items;

    const section =
      result?.section ||
      items.find((c) => c.subtitle || c.title || c.description);
    if (section) {
      if (section.id) sectionId.value = section.id;
      if (section.subtitle !== undefined)
        sectionForm.subtitle = section.subtitle || "";
      if (section.title !== undefined)
        sectionForm.title = section.title || "";
      if (section.title_highlight !== undefined)
        sectionForm.title_highlight = section.title_highlight || "";
      if (section.description !== undefined)
        sectionForm.description = section.description || "";
    }
  } catch (error) {
    console.error("Failed to load clients:", error);

    clients.value = [];

    showToast(
      error?.message || "Failed to load clients.",
      "error",
    );
  } finally {
    loading.value = false;
  }
}

async function save() {
  errorMessage.value = "";

  if (!form.value.name.trim()) {
    errorMessage.value = "Client name is required.";
    return;
  }

  saving.value = true;

  try {
    const payload = {
      name: form.value.name.trim(),
      sort_order: form.value.sort_order ?? null,
    };

    // Send src only when a new image was selected.
    if (imageFile.value) {
      payload.src = imageFile.value;
    }

    if (editingItem.value) {
      await adminAPI.updateItem(
        "clients",
        editingItem.value.id,
        payload,
      );

      showToast("Client updated successfully.");
    } else {
      if (!imageFile.value) {
        errorMessage.value = "Please select a client logo.";
        saving.value = false;
        return;
      }

      await adminAPI.createItem("clients", {
        ...payload,
        src: imageFile.value,
      });

      showToast("Client created successfully.");
    }

    closeModal();
    await loadClients();
  } catch (error) {
    console.error("Failed to save client:", error);

    errorMessage.value =
      error?.message || "Failed to save client.";

    showToast(
      error?.message || "Failed to save client.",
      "error",
    );
  } finally {
    saving.value = false;
  }
}

function openDelete(client) {
  itemToDelete.value = client;
  deleteDialogOpen.value = true;
}

async function deleteClient() {
  if (!itemToDelete.value) return;

  const client = itemToDelete.value;

  try {
    await adminAPI.deleteItem("clients", client.id);

    showToast("Client deleted successfully.");

    itemToDelete.value = null;
    await loadClients();
  } catch (error) {
    console.error("Failed to delete client:", error);

    showToast(
      error?.message || "Failed to delete client.",
      "error",
    );
  }
}

onMounted(() => {
  loadClients();
  loadSectionHeader();
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 180ms ease;
}

.modal-fade-enter-active > div,
.modal-fade-leave-active > div {
  transition:
    transform 180ms ease,
    opacity 180ms ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.modal-fade-enter-from > div,
.modal-fade-leave-to > div {
  opacity: 0;
  transform: translateY(8px) scale(0.98);
}
</style>