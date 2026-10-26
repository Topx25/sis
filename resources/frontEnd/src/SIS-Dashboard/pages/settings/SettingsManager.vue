<script setup>
import { computed, onMounted, ref } from "vue";
import { adminApi } from "@/SIS-Dashboard/api/adminApi.js";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";

/* ---------------- Toast ---------------- */
const showToast = ref(false);
const toast = ref({ title: "", message: "", variant: "success" });
function notify(title, message, variant = "success") {
  toast.value = { title, message, variant };
  showToast.value = true;
}

/* ---------------- Current user / role ---------------- */
const currentUser = ref(null);
const isAdmin = computed(() => currentUser.value?.role === "admin");

/* ---------------- Tabs ---------------- */
const allTabs = [
  { id: "protection", label: "Protection", icon: "shield", adminOnly: true },
  { id: "account", label: "My Account", icon: "person", adminOnly: false },
  { id: "users", label: "Users", icon: "group", adminOnly: true },
  { id: "email", label: "Email", icon: "mail", adminOnly: true },
  { id: "seo", label: "SEO", icon: "travel_explore", adminOnly: true },
];
const tabs = computed(() =>
  allTabs.filter((t) => !t.adminOnly || isAdmin.value),
);
const activeTab = ref("account");

/* ================= Protection (Turnstile) ================= */
const turnstile = ref({ enabled: false, site_key: "", has_secret: false });
const turnstileSecretInput = ref("");
const savingTurnstile = ref(false);

async function loadTurnstile() {
  try {
    const data = await adminApi.getTurnstileSettings();
    turnstile.value = {
      enabled: !!data?.enabled,
      site_key: data?.site_key || "",
      has_secret: !!data?.has_secret,
    };
    turnstileSecretInput.value = "";
  } catch (err) {
    notify("Error", err.message || "Could not load protection settings.", "error");
  }
}

async function saveTurnstile() {
  savingTurnstile.value = true;
  try {
    const payload = {
      enabled: turnstile.value.enabled,
      site_key: turnstile.value.site_key || "",
    };
    // Only send the secret when the admin typed a new one.
    if (turnstileSecretInput.value.trim() !== "") {
      payload.secret_key = turnstileSecretInput.value.trim();
    }
    const data = await adminApi.updateTurnstileSettings(payload);
    turnstile.value = {
      enabled: !!data?.enabled,
      site_key: data?.site_key || "",
      has_secret: !!data?.has_secret,
    };
    turnstileSecretInput.value = "";
    notify("Saved", "Turnstile protection settings saved.", "success");
  } catch (err) {
    notify("Save failed", err.message || "Could not save protection settings.", "error");
  } finally {
    savingTurnstile.value = false;
  }
}

/* ================= Account (own profile) ================= */
const profile = ref({ name: "", email: "" });
const profilePw = ref({ current_password: "", password: "" });
const savingProfile = ref(false);

async function loadProfile() {
  try {
    const data = await adminApi.me();
    currentUser.value = data;
    profile.value = { name: data?.name || "", email: data?.email || "" };
    if (!isAdmin.value) activeTab.value = "account";
  } catch (err) {
    notify("Error", err.message || "Could not load your account.", "error");
  }
}

async function saveProfile() {
  savingProfile.value = true;
  try {
    const payload = { name: profile.value.name, email: profile.value.email };
    if (profilePw.value.password.trim() !== "") {
      payload.current_password = profilePw.value.current_password;
      payload.password = profilePw.value.password;
    }
    const data = await adminApi.updateProfile(payload);
    currentUser.value = { ...currentUser.value, ...data };
    profilePw.value = { current_password: "", password: "" };
    notify("Saved", "Your account was updated.", "success");
  } catch (err) {
    notify("Save failed", err.message || "Could not update your account.", "error");
  } finally {
    savingProfile.value = false;
  }
}

/* ================= Users ================= */
const users = ref([]);
const loadingUsers = ref(false);
const userForm = ref(null); // null = closed; object = editing/creating
const savingUser = ref(false);
const showDeleteUser = ref(false);
const userToDelete = ref(null);

async function loadUsers() {
  loadingUsers.value = true;
  try {
    users.value = (await adminApi.getUsers()) || [];
  } catch (err) {
    notify("Error", err.message || "Could not load users.", "error");
  } finally {
    loadingUsers.value = false;
  }
}

function startCreateUser() {
  userForm.value = { id: null, name: "", email: "", role: "editor", password: "" };
}
function startEditUser(u) {
  userForm.value = { id: u.id, name: u.name, email: u.email, role: u.role, password: "" };
}
function cancelUserForm() {
  userForm.value = null;
}

async function saveUser() {
  if (!userForm.value) return;
  savingUser.value = true;
  try {
    const f = userForm.value;
    const payload = { name: f.name, email: f.email, role: f.role };
    if (f.id) {
      if (f.password.trim() !== "") payload.password = f.password;
      await adminApi.updateUser(f.id, payload);
      notify("Saved", "User updated.", "success");
    } else {
      payload.password = f.password;
      await adminApi.createUser(payload);
      notify("Created", "User added.", "success");
    }
    userForm.value = null;
    await loadUsers();
  } catch (err) {
    notify("Save failed", err.message || "Could not save the user.", "error");
  } finally {
    savingUser.value = false;
  }
}

function askDeleteUser(u) {
  userToDelete.value = u;
  showDeleteUser.value = true;
}
async function deleteUser() {
  if (!userToDelete.value) return;
  try {
    await adminApi.deleteUser(userToDelete.value.id);
    notify("Deleted", "User removed.", "success");
    await loadUsers();
  } catch (err) {
    notify("Delete failed", err.message || "Could not delete the user.", "error");
  } finally {
    showDeleteUser.value = false;
    userToDelete.value = null;
  }
}

/* ================= Email (SMTP) ================= */
const mail = ref({
  enabled: false,
  mailer: "smtp",
  host: "",
  port: "587",
  username: "",
  encryption: "tls",
  from_address: "",
  from_name: "",
  to_address: "",
  has_password: false,
});
const mailPwInput = ref("");
const savingMail = ref(false);
const testTo = ref("");
const sendingTest = ref(false);

async function loadMail() {
  try {
    const data = await adminApi.getMailSettings();
    mail.value = {
      enabled: !!data?.enabled,
      mailer: data?.mailer || "smtp",
      host: data?.host || "",
      port: data?.port || "587",
      username: data?.username || "",
      encryption: data?.encryption || "tls",
      from_address: data?.from_address || "",
      from_name: data?.from_name || "",
      to_address: data?.to_address || "",
      has_password: !!data?.has_password,
    };
    mailPwInput.value = "";
  } catch (err) {
    notify("Error", err.message || "Could not load email settings.", "error");
  }
}

async function saveMail() {
  savingMail.value = true;
  try {
    const payload = {
      enabled: mail.value.enabled,
      mailer: mail.value.mailer,
      host: mail.value.host,
      port: mail.value.port ? Number(mail.value.port) : null,
      username: mail.value.username,
      encryption: mail.value.encryption,
      from_address: mail.value.from_address,
      from_name: mail.value.from_name,
      to_address: mail.value.to_address,
    };
    if (mailPwInput.value.trim() !== "") payload.password = mailPwInput.value.trim();
    const data = await adminApi.updateMailSettings(payload);
    mail.value.has_password = !!data?.has_password;
    mailPwInput.value = "";
    notify("Saved", "Email settings saved.", "success");
  } catch (err) {
    notify("Save failed", err.message || "Could not save email settings.", "error");
  } finally {
    savingMail.value = false;
  }
}

async function sendTest() {
  if (!testTo.value.trim()) {
    notify("Heads up", "Enter a recipient email to test.", "error");
    return;
  }
  sendingTest.value = true;
  try {
    await adminApi.sendTestMail(testTo.value.trim());
    notify("Sent", `Test email sent to ${testTo.value.trim()}.`, "success");
  } catch (err) {
    notify("Send failed", err.message || "Could not send the test email.", "error");
  } finally {
    sendingTest.value = false;
  }
}

/* ---------------- init ---------------- */
onMounted(async () => {
  await loadProfile();
  if (isAdmin.value) {
    activeTab.value = "protection";
    await Promise.all([loadTurnstile(), loadUsers(), loadMail()]);
  }
});
</script>

<template>
  <div class="mx-auto max-w-5xl space-y-6">
    <AdminPageHeader
      title="Settings"
      subtitle="Protection, your account, users, SEO and email."
      category="General Settings"
      icon="settings"
    />

    <!-- Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 dark:border-slate-700">
      <button
        v-for="t in tabs"
        :key="t.id"
        type="button"
        class="inline-flex items-center gap-2 rounded-t-lg px-4 py-2.5 text-sm font-semibold transition"
        :class="activeTab === t.id
          ? 'bg-sis-accent text-white'
          : 'text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800'"
        @click="activeTab = t.id"
      >
        <span class="material-symbols-outlined text-[18px]">{{ t.icon }}</span>
        {{ t.label }}
      </button>
    </div>

    <!-- ================= Protection ================= -->
    <section
      v-if="activeTab === 'protection' && isAdmin"
      class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900"
    >
      <h3 class="mb-1 text-lg font-bold text-slate-800 dark:text-slate-100">Cloudflare Turnstile Protection</h3>
      <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">
        A captcha on the contact form and the admin login page. Get the keys from Cloudflare dashboard → Turnstile.
      </p>

      <label class="mb-5 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Enable protection</span>
        <input v-model="turnstile.enabled" type="checkbox" class="h-5 w-5 accent-[color:var(--sis-accent,#2563eb)]" />
      </label>

      <div class="space-y-4">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Site Key</label>
          <input
            v-model="turnstile.site_key"
            type="text"
            placeholder="0x4AAAAAAA..."
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
          />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">
            Secret Key
            <span v-if="turnstile.has_secret" class="text-xs text-emerald-600">(saved — leave empty to keep it)</span>
          </label>
          <input
            v-model="turnstileSecretInput"
            type="password"
            :placeholder="turnstile.has_secret ? '********' : '0x4AAAAAAA...'"
            class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
          />
        </div>
      </div>

      <div class="mt-6">
        <button
          type="button"
          :disabled="savingTurnstile"
          class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60"
          @click="saveTurnstile"
        >
          <span class="material-symbols-outlined text-[18px]">save</span>
          {{ savingTurnstile ? "Saving..." : "Save" }}
        </button>
      </div>
    </section>

    <!-- ================= Account ================= -->
    <section
      v-if="activeTab === 'account'"
      class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900"
    >
      <h3 class="mb-5 text-lg font-bold text-slate-800 dark:text-slate-100">My Account</h3>
      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Name</label>
          <input v-model="profile.name" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Email</label>
          <input v-model="profile.email" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
      </div>

      <div class="mt-6 rounded-xl bg-slate-50 p-4 dark:bg-slate-800">
        <p class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-200">Change password (optional)</p>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Current password</label>
            <input v-model="profilePw.current_password" type="password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">New password</label>
            <input v-model="profilePw.password" type="password" placeholder="At least 8 characters" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
          </div>
        </div>
      </div>

      <div class="mt-6">
        <button type="button" :disabled="savingProfile" class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60" @click="saveProfile">
          <span class="material-symbols-outlined text-[18px]">save</span>
          {{ savingProfile ? "Saving..." : "Save" }}
        </button>
      </div>
    </section>

    <!-- ================= Users ================= -->
    <section
      v-if="activeTab === 'users' && isAdmin"
      class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900"
    >
      <div class="mb-5 flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Users</h3>
        <button type="button" class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700" @click="startCreateUser">
          <span class="material-symbols-outlined text-[18px]">person_add</span>
          Add user
        </button>
      </div>

      <div v-if="loadingUsers" class="py-8 text-center text-sm text-slate-400">Loading...</div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="border-b border-slate-200 text-slate-500 dark:border-slate-700">
              <th class="px-3 py-2 font-semibold">Name</th>
              <th class="px-3 py-2 font-semibold">Email</th>
              <th class="px-3 py-2 font-semibold">Role</th>
              <th class="px-3 py-2 font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="u in users" :key="u.id" class="border-b border-slate-100 dark:border-slate-800">
              <td class="px-3 py-2.5 text-slate-800 dark:text-slate-100">{{ u.name }}</td>
              <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ u.email }}</td>
              <td class="px-3 py-2.5">
                <span
                  class="rounded-full px-2.5 py-0.5 text-xs font-bold"
                  :class="u.role === 'admin' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                >{{ u.role === 'admin' ? 'Admin' : 'Editor' }}</span>
              </td>
              <td class="px-3 py-2.5">
                <div class="flex gap-2">
                  <button type="button" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800" title="Edit" @click="startEditUser(u)">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                  </button>
                  <button type="button" class="rounded-lg p-1.5 text-red-500 hover:bg-red-50" title="Delete" @click="askDeleteUser(u)">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- User form -->
      <div v-if="userForm" class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-700 dark:bg-slate-800">
        <h4 class="mb-4 text-base font-bold text-slate-800 dark:text-slate-100">
          {{ userForm.id ? "Edit user" : "New user" }}
        </h4>
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Name</label>
            <input v-model="userForm.name" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Email</label>
            <input v-model="userForm.email" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Role</label>
            <select v-model="userForm.role" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
              <option value="editor">Editor</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">
              Password {{ userForm.id ? "(leave empty to keep)" : "" }}
            </label>
            <input v-model="userForm.password" type="password" placeholder="At least 8 characters" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
          </div>
        </div>
        <div class="mt-5 flex gap-2">
          <button type="button" :disabled="savingUser" class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60" @click="saveUser">
            <span class="material-symbols-outlined text-[18px]">save</span>
            {{ savingUser ? "Saving..." : "Save" }}
          </button>
          <button type="button" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300" @click="cancelUserForm">Cancel</button>
        </div>
      </div>
    </section>

    <!-- ================= Email ================= -->
    <section
      v-if="activeTab === 'email' && isAdmin"
      class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900"
    >
      <h3 class="mb-1 text-lg font-bold text-slate-800 dark:text-slate-100">Email settings (SMTP)</h3>
      <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">
        When disabled, the system default sending is used. Enable to use an SMTP server.
      </p>

      <label class="mb-5 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 dark:bg-slate-800">
        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Enable SMTP settings</span>
        <input v-model="mail.enabled" type="checkbox" class="h-5 w-5 accent-[color:var(--sis-accent,#2563eb)]" />
      </label>

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Mailer</label>
          <select v-model="mail.mailer" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            <option value="smtp">SMTP</option>
            <option value="sendmail">Sendmail</option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Encryption</label>
          <select v-model="mail.encryption" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            <option value="tls">TLS</option>
            <option value="ssl">SSL</option>
            <option value="none">None</option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Host</label>
          <input v-model="mail.host" type="text" placeholder="smtp.example.com" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Port</label>
          <input v-model="mail.port" type="number" placeholder="587" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Username</label>
          <input v-model="mail.username" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">
            Password
            <span v-if="mail.has_password" class="text-xs text-emerald-600">(saved — leave empty to keep)</span>
          </label>
          <input v-model="mailPwInput" type="password" :placeholder="mail.has_password ? '********' : ''" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">From address</label>
          <input v-model="mail.from_address" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">From name</label>
          <input v-model="mail.from_name" type="text" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
        <div class="sm:col-span-2">
          <label class="mb-1 block text-sm font-medium text-slate-600 dark:text-slate-300">Contact-form recipient address</label>
          <input v-model="mail.to_address" type="email" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" />
        </div>
      </div>

      <div class="mt-6">
        <button type="button" :disabled="savingMail" class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-60" @click="saveMail">
          <span class="material-symbols-outlined text-[18px]">save</span>
          {{ savingMail ? "Saving..." : "Save" }}
        </button>
      </div>

      <div class="mt-6 rounded-xl bg-slate-50 p-4 dark:bg-slate-800">
        <p class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-200">Send a test email</p>
        <div class="flex flex-wrap items-end gap-3">
          <div class="min-w-[220px] flex-1">
            <input v-model="testTo" type="email" placeholder="you@example.com" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100" />
          </div>
          <button type="button" :disabled="sendingTest" class="inline-flex items-center gap-2 rounded-xl border border-sis-accent px-5 py-2.5 text-sm font-semibold text-sis-accent transition hover:bg-blue-50 disabled:opacity-60" @click="sendTest">
            <span class="material-symbols-outlined text-[18px]">send</span>
            {{ sendingTest ? "Sending..." : "Send test" }}
          </button>
        </div>
        <p class="mt-2 text-xs text-slate-400">Save the settings first, then send the test.</p>
      </div>
    </section>

    <!-- ================= SEO ================= -->
    <section
      v-if="activeTab === 'seo' && isAdmin"
      class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-700 dark:bg-slate-900"
    >
      <h3 class="mb-1 text-lg font-bold text-slate-800 dark:text-slate-100">SEO & search-engine verification</h3>
      <p class="mb-5 text-sm text-slate-500 dark:text-slate-400">
        Google &amp; Bing verification codes and the rest of the SEO settings are managed in the Global SEO section.
      </p>
      <router-link
        :to="{ name: 'admin-seo-settings' }"
        class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
      >
        <span class="material-symbols-outlined text-[18px]">travel_explore</span>
        Open Global SEO settings
      </router-link>
    </section>

    <!-- Delete user confirm -->
    <ConfirmDialog
      v-model="showDeleteUser"
      title="Delete user"
      :message="`Are you sure you want to delete '${userToDelete?.name || 'this user'}'? This action cannot be undone.`"
      confirm-text="Delete"
      cancel-text="Cancel"
      :danger="true"
      icon="delete"
      @confirm="deleteUser"
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
