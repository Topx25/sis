<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import ToastNotification from "@/components/global/ToastNotification.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import { adminAPI } from "@/SIS-Dashboard/api/index.js";

const route = useRoute();
const workSampleId = computed(() => Number(route.params.id));

const loading = ref(true);
const saving = ref(false);
const credentials = ref([]);
const workSample = ref(null);
const modalOpen = ref(false);
const form = ref({ username: "", password: "" });
const generateMode = ref(false);
const showToast = ref(false);
const toast = ref({ title: "", message: "", variant: "success" });
const copyFeedback = ref("");
const confirmState = ref({ open: false, credential: null, title: "", message: "" });

const InputCls =
  "w-full rounded-lg border border-sis-border bg-white px-3 py-2 text-sm text-sis-text placeholder:text-sis-muted/70 focus:border-sis-accent focus:outline-none focus:ring-1 focus:ring-sis-accent/30";

// Filter credentials by expiry status
const activeCredentials = computed(() =>
  credentials.value.filter((c) => c.isActive && c.daysUntilExpiry > 0),
);

const expiringCredentials = computed(() =>
  credentials.value.filter(
    (c) => c.isActive && c.daysUntilExpiry <= 2 && c.daysUntilExpiry > 0,
  ),
);

const expiredCredentials = computed(() =>
  credentials.value.filter((c) => c.daysUntilExpiry === 0 || !c.isActive),
);

function notify(title, message, variant) {
  toast.value = { title, message, variant };
  showToast.value = true;
}

function getStatusBadgeClass(credential) {
  if (!credential.isActive) return "bg-red-50 text-red-600";
  if (credential.daysUntilExpiry === 0) return "bg-red-50 text-red-600";
  if (credential.daysUntilExpiry <= 2) return "bg-yellow-50 text-yellow-600";
  return "bg-green-50 text-green-600";
}

function getStatusText(credential) {
  if (!credential.isActive) return "Inactive";
  if (credential.daysUntilExpiry === 0) return "Expired";
  if (credential.daysUntilExpiry === 1) return "Expires tomorrow";
  return `${credential.daysUntilExpiry} days left`;
}

async function load() {
  loading.value = true;
  try {
    // Get the work sample details first
    const workSamples = await adminAPI.getItems("workSamples");
    workSample.value = workSamples.find((w) => w.id === workSampleId.value);

    // Get credentials for this work sample
    const creds = await adminAPI.getWorkSampleCredentials(workSampleId.value);
    credentials.value = creds;
  } catch (e) {
    notify("Load failed", e.message || "Could not load credentials.", "error");
  } finally {
    loading.value = false;
  }
}

async function generateCredential() {
  try {
    const generated = await adminAPI.generateRandomCredential();
    form.value.username = generated.username;
    form.value.password = generated.password;
    generateMode.value = true;
    notify(
      "Generated",
      "Username and password generated successfully.",
      "success",
    );
  } catch (e) {
    notify(
      "Generation failed",
      e.message || "Could not generate credential.",
      "error",
    );
  }
}

function openCreate() {
  form.value = { username: "", password: "" };
  generateMode.value = false;
  modalOpen.value = true;
}

async function save() {
  if (!form.value.username?.trim() || !form.value.password?.trim()) {
    notify(
      "Missing fields",
      "Both username and password are required.",
      "error",
    );
    return;
  }

  saving.value = true;
  try {
    await adminAPI.createWorkSampleCredential(workSampleId.value, {
      username: form.value.username.trim(),
      password: form.value.password.trim(),
    });
    notify(
      "Credential created",
      "New demo access created successfully.",
      "success",
    );
    modalOpen.value = false;
    await load();
  } catch (e) {
    notify("Save failed", e.message, "error");
  } finally {
    saving.value = false;
  }
}

async function toggleActive(credential) {
  try {
    await adminAPI.updateWorkSampleCredential(credential.id, {
      isActive: !credential.isActive,
    });
    credential.isActive = !credential.isActive;
    notify(
      credential.isActive ? "Activated" : "Deactivated",
      `Credential ${credential.isActive ? "activated" : "deactivated"}.`,
      "success",
    );
  } catch (e) {
    notify("Update failed", e.message, "error");
  }
}

async function deleteCredential(credential) {
  confirmState.value = {
    open: true,
    credential,
    title: "Delete credential?",
    message: `"${credential.username}" will be permanently removed. This cannot be undone.`,
  };
}

async function onDeleteConfirmed() {
  const { credential } = confirmState.value;
  try {
    await adminAPI.deleteWorkSampleCredential(credential.id);
    notify(
      "Credential deleted",
      `"${credential.username}" removed.`,
      "success",
    );
    await load();
  } catch (e) {
    notify("Delete failed", e.message, "error");
  }
}

function copyToClipboard(text, field) {
  navigator.clipboard.writeText(text);
  copyFeedback.value = field;
  setTimeout(() => {
    copyFeedback.value = "";
  }, 1500);
}

onMounted(load);
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="Demo Access Credentials"
      :subtitle="workSample ? `Managing credentials for ${workSample.title}` : 'Manage demo user accounts with 7-day expiration'"
      category="Security & Access"
      icon="lock"
    >
      <template #actions>
        <button
          type="button"
          @click="openCreate"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98]"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          New Credential
        </button>
      </template>
    </AdminPageHeader>

    <!-- Stats Cards -->
    <div v-if="!loading" class="grid gap-3 sm:grid-cols-3">
      <div class="rounded-lg border border-sis-border bg-white p-4">
        <div class="flex items-center justify-between">
          <div>
            <p
              class="text-xs font-semibold uppercase tracking-wider text-sis-muted"
            >
              Active
            </p>
            <p class="mt-1 text-2xl font-bold text-green-600">
              {{ activeCredentials.length }}
            </p>
          </div>
          <span class="material-symbols-outlined text-3xl text-green-200"
            >check_circle</span
          >
        </div>
      </div>
      <div class="rounded-lg border border-sis-border bg-white p-4">
        <div class="flex items-center justify-between">
          <div>
            <p
              class="text-xs font-semibold uppercase tracking-wider text-sis-muted"
            >
              Expiring Soon
            </p>
            <p class="mt-1 text-2xl font-bold text-yellow-600">
              {{ expiringCredentials.length }}
            </p>
          </div>
          <span class="material-symbols-outlined text-3xl text-yellow-200"
            >schedule</span
          >
        </div>
      </div>
      <div class="rounded-lg border border-sis-border bg-white p-4">
        <div class="flex items-center justify-between">
          <div>
            <p
              class="text-xs font-semibold uppercase tracking-wider text-sis-muted"
            >
              Expired
            </p>
            <p class="mt-1 text-2xl font-bold text-red-600">
              {{ expiredCredentials.length }}
            </p>
          </div>
          <span class="material-symbols-outlined text-3xl text-red-200"
            >lock_clock</span
          >
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-3">
      <div
        v-for="n in 3"
        :key="n"
        class="animate-pulse rounded-xl border border-sis-border bg-white p-4 shadow-sis"
      >
        <div class="flex items-center gap-4">
          <div class="h-12 w-12 rounded-lg bg-sis-panel"></div>
          <div class="flex-1 space-y-2">
            <div class="h-3.5 w-1/3 rounded bg-sis-panel"></div>
            <div class="h-3 w-1/4 rounded bg-sis-panel"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Credentials List -->
    <div v-else-if="credentials.length" class="space-y-4">
      <!-- Active Credentials -->
      <div v-if="activeCredentials.length">
        <h3 class="mb-3 text-sm font-semibold text-sis-text">
          Active Credentials
        </h3>
        <div class="space-y-3">
          <div
            v-for="cred in activeCredentials"
            :key="cred.id"
            class="rounded-lg border border-sis-border bg-white p-4 shadow-sm transition-all hover:shadow-md"
          >
            <div
              class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
            >
              <div class="flex-1">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >check_circle</span
                    >
                  </div>
                  <div>
                    <p class="font-semibold text-sis-text">
                      {{ cred.username }}
                    </p>
                    <p class="text-xs text-sis-muted">
                      Created
                      {{ new Date(cred.createdAt).toLocaleDateString() }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span
                  :class="[
                    'rounded-full px-3 py-1 text-xs font-medium',
                    getStatusBadgeClass(cred),
                  ]"
                >
                  {{ getStatusText(cred) }}
                </span>
              </div>
            </div>

            <!-- Password Display -->
            <div class="mt-4 rounded-lg bg-sis-panel/30 p-3">
              <p
                class="text-xs font-semibold uppercase tracking-wider text-sis-muted mb-2"
              >
                Password
              </p>
              <div class="flex items-center gap-2">
                <code class="flex-1 font-mono text-sm text-sis-text">{{
                  cred.password
                }}</code>
                <button
                  @click="copyToClipboard(cred.password, `pass_${cred.id}`)"
                  class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-sis-muted transition-colors hover:bg-white hover:text-sis-accent"
                  :title="
                    copyFeedback === `pass_${cred.id}`
                      ? 'Copied!'
                      : 'Copy password'
                  "
                >
                  <span class="material-symbols-outlined text-[18px]">
                    {{
                      copyFeedback === `pass_${cred.id}`
                        ? "check"
                        : "content_copy"
                    }}
                  </span>
                </button>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-4 flex gap-2">
              <button
                @click="toggleActive(cred)"
                class="flex items-center gap-2 rounded-lg border border-sis-border px-3 py-2 text-sm font-medium text-sis-muted transition-colors hover:bg-sis-panel hover:text-sis-text"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >toggle_on</span
                >
                Deactivate
              </button>
              <button
                @click="deleteCredential(cred)"
                class="flex items-center gap-2 rounded-lg text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >delete</span
                >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Expiring Soon Section -->
      <div v-if="expiringCredentials.length">
        <h3 class="mb-3 text-sm font-semibold text-sis-text">Expiring Soon</h3>
        <div class="space-y-3">
          <div
            v-for="cred in expiringCredentials"
            :key="cred.id"
            class="rounded-lg border border-yellow-200 bg-yellow-50 p-4 shadow-sm transition-all hover:shadow-md"
          >
            <div
              class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
            >
              <div class="flex-1">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >schedule</span
                    >
                  </div>
                  <div>
                    <p class="font-semibold text-yellow-900">
                      {{ cred.username }}
                    </p>
                    <p class="text-xs text-yellow-700">
                      Created
                      {{ new Date(cred.createdAt).toLocaleDateString() }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span
                  class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-600"
                >
                  {{ getStatusText(cred) }}
                </span>
              </div>
            </div>

            <div class="mt-4 rounded-lg bg-white/50 p-3">
              <p
                class="text-xs font-semibold uppercase tracking-wider text-yellow-700 mb-2"
              >
                Password
              </p>
              <div class="flex items-center gap-2">
                <code class="flex-1 font-mono text-sm text-yellow-900">{{
                  cred.password
                }}</code>
                <button
                  @click="copyToClipboard(cred.password, `pass_${cred.id}`)"
                  class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-yellow-600 transition-colors hover:bg-white hover:text-yellow-700"
                  :title="
                    copyFeedback === `pass_${cred.id}`
                      ? 'Copied!'
                      : 'Copy password'
                  "
                >
                  <span class="material-symbols-outlined text-[18px]">
                    {{
                      copyFeedback === `pass_${cred.id}`
                        ? "check"
                        : "content_copy"
                    }}
                  </span>
                </button>
              </div>
            </div>

            <div class="mt-4 flex gap-2">
              <button
                @click="toggleActive(cred)"
                class="flex items-center gap-2 rounded-lg border border-yellow-200 px-3 py-2 text-sm font-medium text-yellow-600 transition-colors hover:bg-yellow-100"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >toggle_on</span
                >
                Deactivate
              </button>
              <button
                @click="deleteCredential(cred)"
                class="flex items-center gap-2 rounded-lg text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >delete</span
                >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Expired/Inactive Section -->
      <div v-if="expiredCredentials.length">
        <h3 class="mb-3 text-sm font-semibold text-sis-muted">
          Expired / Inactive
        </h3>
        <div class="space-y-3 opacity-60">
          <div
            v-for="cred in expiredCredentials"
            :key="cred.id"
            class="rounded-lg border border-sis-border bg-sis-panel/30 p-4 shadow-sm"
          >
            <div
              class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
            >
              <div class="flex-1">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >lock_clock</span
                    >
                  </div>
                  <div>
                    <p class="font-semibold text-sis-text line-through">
                      {{ cred.username }}
                    </p>
                    <p class="text-xs text-sis-muted">
                      Expired
                      {{ new Date(cred.expiresAt).toLocaleDateString() }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span
                  class="rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-600"
                >
                  {{ getStatusText(cred) }}
                </span>
              </div>
            </div>

            <div class="mt-4 flex gap-2">
              <button
                v-if="!cred.isActive"
                @click="toggleActive(cred)"
                class="flex items-center gap-2 rounded-lg border border-sis-border px-3 py-2 text-sm font-medium text-sis-muted transition-colors hover:bg-sis-panel hover:text-sis-text"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >toggle_off</span
                >
                Reactivate
              </button>
              <button
                @click="deleteCredential(cred)"
                class="flex items-center gap-2 rounded-lg text-sm font-medium text-red-600 transition-colors hover:bg-red-50"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >delete</span
                >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="rounded-xl border border-dashed border-sis-border bg-white p-10 text-center"
    >
      <span class="material-symbols-outlined text-[36px] text-sis-muted/50"
        >lock_open</span
      >
      <p class="mt-2 text-sm text-sis-muted">No credentials created yet.</p>
      <button
        @click="openCreate"
        class="mt-3 text-sm font-semibold text-sis-accent hover:underline"
      >
        Create the first credential
      </button>
    </div>

    <!-- Create Credential Modal -->
    <Teleport to="body">
      <div
        v-if="modalOpen"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4"
      >
        <div
          class="absolute inset-0 bg-black/40 backdrop-blur-sm"
          @click="modalOpen = false"
        ></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
          <div
            class="flex items-center justify-between border-b border-sis-border px-6 py-4"
          >
            <h3 class="font-headline-md text-lg font-semibold text-sis-text">
              New Demo Credential
            </h3>
            <button
              @click="modalOpen = false"
              class="flex h-8 w-8 items-center justify-center rounded-full text-sis-muted transition-colors hover:bg-sis-panel hover:text-sis-text"
            >
              <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
          </div>

          <div class="space-y-4 px-6 py-6">
            <!-- Auto Generate Option -->
            <button
              @click="generateCredential"
              class="w-full rounded-lg border border-sis-accent/30 bg-sis-accent/5 px-4 py-3 text-sm font-semibold text-sis-accent transition-colors hover:bg-sis-accent/10"
            >
              <span
                class="material-symbols-outlined mr-2 text-[18px] align-middle"
                >auto_awesome</span
              >
              Auto-Generate Username & Password
            </button>

            <div class="flex items-center gap-3">
              <div class="flex-1 border-t border-sis-border"></div>
              <span class="text-xs text-sis-muted">or enter manually</span>
              <div class="flex-1 border-t border-sis-border"></div>
            </div>

            <!-- Username Input -->
            <div>
              <label
                class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted"
              >
                Username *
              </label>
              <input
                v-model="form.username"
                type="text"
                placeholder="e.g., demo_sales_1"
                :class="InputCls"
              />
            </div>

            <!-- Password Input -->
            <div>
              <label
                class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-sis-muted"
              >
                Password *
              </label>
              <input
                v-model="form.password"
                type="text"
                placeholder="e.g., SecurePass123!"
                :class="InputCls"
              />
              <p class="mt-1 text-xs text-sis-muted">
                This credential will auto-expire in 7 days.
              </p>
            </div>
          </div>

          <div
            class="flex items-center justify-end gap-3 border-t border-sis-border px-6 py-4"
          >
            <button
              @click="modalOpen = false"
              class="rounded-lg border border-sis-border px-4 py-2 text-sm font-medium text-sis-muted transition-colors hover:text-sis-text"
            >
              Cancel
            </button>
            <button
              @click="save"
              :disabled="saving"
              class="flex items-center gap-2 rounded-lg bg-sis-accent px-5 py-2 text-sm font-semibold text-white transition-colors hover:bg-sis-light disabled:opacity-50"
            >
              <span
                v-if="saving"
                class="material-symbols-outlined animate-spin text-[18px]"
                >progress_activity</span
              >
              <span v-else class="material-symbols-outlined text-[18px]"
                >add</span
              >
              {{ saving ? "Creating…" : "Create Credential" }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <ToastNotification
      v-if="showToast"
      :title="toast.title"
      :message="toast.message"
      :variant="toast.variant"
      @close="showToast = false"
    />
    <ConfirmDialog
      v-model="confirmState.open"
      :title="confirmState.title"
      :message="confirmState.message"
      confirm-text="Delete"
      danger
      @confirm="onDeleteConfirmed"
    />
  </div>
</template>
