<script setup>
import { onMounted, reactive, ref } from "vue";
import { adminAPI } from "@/SIS-Dashboard/api";
import ToastNotification from "@/components/global/ToastNotification.vue";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";

const loading = ref(true);
const saving = ref(false);
const recordExists = ref(false);

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

const form = reactive({
  website: "",
  email: "",
  emails: [""],
  phones: [""],
  cta_title: {
    en: "",
    ar: "",
  },
  cta_description: {
    en: "",
    ar: "",
  },
  cta_link_text: {
    en: "",
    ar: "",
  },
  cta_link: "/contact",
});

function toCleanString(val) {
  if (val === null || val === undefined) return "";
  if (typeof val === "string") return val;
  if (typeof val === "number") return String(val);
  return "";
}

function extractLocalized(val) {
  if (!val) return { en: "", ar: "" };
  if (typeof val === "string") return { en: val, ar: "" };
  if (typeof val === "object" && !Array.isArray(val)) {
    return {
      en: toCleanString(val.en),
      ar: toCleanString(val.ar),
    };
  }
  return { en: "", ar: "" };
}

function resetForm(data = null) {
  if (!data || typeof data !== "object") {
    recordExists.value = false;
    form.website = "";
    form.email = "";
    form.emails = [""];
    form.phones = [""];
    form.cta_title = { en: "", ar: "" };
    form.cta_description = { en: "", ar: "" };
    form.cta_link_text = { en: "", ar: "" };
    form.cta_link = "/contact";
    return;
  }

  recordExists.value = Boolean(data.id || data.website || data.email || data.emails);
  form.website = toCleanString(data.website);
  form.email = toCleanString(data.email);

  if (Array.isArray(data.emails) && data.emails.length > 0) {
    form.emails = data.emails.map((e) => toCleanString(e));
  } else if (typeof data.emails === "string" && data.emails.trim()) {
    form.emails = data.emails.split(/[,\n;]+/).map((s) => s.trim()).filter(Boolean);
  } else if (typeof data.email === "string" && data.email.trim()) {
    form.emails = data.email.split(/[,\n;]+/).map((s) => s.trim()).filter(Boolean);
  } else {
    form.emails = [""];
  }

  if (Array.isArray(data.phones) && data.phones.length > 0) {
    form.phones = data.phones.map((p) => toCleanString(p));
  } else if (typeof data.phones === "string" && data.phones.trim()) {
    form.phones = data.phones.split(/[,\n;]+/).map((s) => s.trim()).filter(Boolean);
  } else {
    form.phones = [""];
  }

  form.cta_title = extractLocalized(data.cta_title);
  form.cta_description = extractLocalized(data.cta_description);
  form.cta_link_text = extractLocalized(data.cta_link_text);
  form.cta_link = toCleanString(data.cta_link) || "/contact";
}

function addEmail() {
  form.emails.push("");
}

function removeEmail(index) {
  if (form.emails.length > 1) {
    form.emails.splice(index, 1);
  } else {
    form.emails[0] = "";
  }
}

function addPhone() {
  form.phones.push("");
}

function removePhone(index) {
  if (form.phones.length > 1) {
    form.phones.splice(index, 1);
  } else {
    form.phones[0] = "";
  }
}

async function loadData() {
  loading.value = true;
  try {
    const data = await adminAPI.getContactPage();
    resetForm(data);
  } catch (error) {
    console.error("Failed to load contact page:", error);
    resetForm(null);
    notify("Warning", error?.message || "Failed to load Contact Page data. You can still create it.", "error");
  } finally {
    loading.value = false;
  }
}

async function saveData() {
  if (saving.value) return;

  saving.value = true;
  try {
    const cleanedEmails = form.emails
      .map((e) => String(e || "").trim())
      .filter(Boolean);

    const cleanedPhones = form.phones
      .map((p) => String(p || "").trim())
      .filter(Boolean);

    const payload = {
      website: form.website.trim(),
      email: cleanedEmails[0] || "",
      emails: cleanedEmails,
      phones: cleanedPhones,
      cta_title: {
        en: form.cta_title.en.trim(),
        ar: form.cta_title.ar.trim(),
      },
      cta_description: {
        en: form.cta_description.en.trim(),
        ar: form.cta_description.ar.trim(),
      },
      cta_link_text: {
        en: form.cta_link_text.en.trim(),
        ar: form.cta_link_text.ar.trim(),
      },
      cta_link: form.cta_link.trim() || "/contact",
    };

    const res = await adminAPI.updateContactPage(payload);
    if (res) {
      resetForm(res);
    } else {
      recordExists.value = true;
    }

    notify("Saved successfully", "Contact Page content has been updated.", "success");
  } catch (error) {
    console.error("Failed to save contact page:", error);
    notify("Save failed", error?.message || "Failed to update Contact Page.", "error");
  } finally {
    saving.value = false;
  }
}

onMounted(loadData);
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-6">
    <ToastNotification
      v-if="showToast"
      :title="toast.title"
      :message="toast.message"
      :variant="toast.variant"
      @close="showToast = false"
    />

    <!-- Header Card -->
    <AdminPageHeader
      title="Contact Page Content"
      subtitle="Configure public contact details, phone numbers, and call-to-action cards on the website."
      category="Communication"
      icon="contact_page"
    >
      <template #actions>
        <button
          type="button"
          :disabled="loading || saving"
          @click="loadData"
          class="inline-flex items-center gap-2 rounded-xl border border-sis-border bg-white px-4 py-2.5 text-sm font-semibold text-sis-text shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <span class="material-symbols-outlined text-[18px]">refresh</span>
          Refresh
        </button>

        <button
          type="button"
          :disabled="loading || saving"
          @click="saveData"
          class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
        >
          <span
            v-if="saving"
            class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
          ></span>
          <span v-else class="material-symbols-outlined text-[18px]">save</span>
          {{ saving ? "Saving..." : "Save Changes" }}
        </button>
      </template>
    </AdminPageHeader>

    <!-- Status Banner if no record yet -->
    <div
      v-if="!loading && !recordExists"
      class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-300"
    >
      <span class="material-symbols-outlined mt-0.5 text-[20px] text-amber-600 dark:text-amber-400">
        info
      </span>
      <div class="text-sm">
        <span class="font-bold">No Contact Page record found yet.</span>
        Fill out the information below and click <strong>"Save Changes"</strong> to initialize the Contact Page. The public website will safely fall back to default information until saved.
      </div>
    </div>

    <!-- Loading State -->
    <div
      v-if="loading"
      class="flex min-h-[400px] items-center justify-center rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-[#0b132b]"
    >
      <div class="text-center">
        <div
          class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-blue-600 dark:border-slate-700"
        ></div>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
          Loading Contact Page data...
        </p>
      </div>
    </div>

    <!-- Editor Form -->
    <form v-else @submit.prevent="saveData" class="space-y-6">
      <!-- Section 1: Contact Information -->
      <section
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#0b132b]"
      >
        <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
              Primary Contact Details
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
              Basic company contact channels displayed in the Contact section.
            </p>
          </div>
          <span
            class="material-symbols-outlined text-[28px] text-blue-600 dark:text-blue-400"
          >
            contacts
          </span>
        </div>

        <!-- Website -->
        <div class="mb-6">
          <label
            for="contact-website"
            class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300"
          >
            Website URL
          </label>
          <div class="relative">
            <span
              class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
            >
              language
            </span>
            <input
              id="contact-website"
              v-model="form.website"
              type="text"
              class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-11 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#071426] dark:text-white"
              placeholder="www.sis-eg.org"
            />
          </div>
          <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
            Company website URL or domain.
          </p>
        </div>

        <div class="grid gap-8 lg:grid-cols-2 border-t border-slate-100 pt-6 dark:border-slate-800">
          <!-- Email Addresses Repeater -->
          <div>
            <div class="mb-3 flex items-center justify-between">
              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                  Email Addresses
                </label>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                  Add one or more email addresses for sales, support, or general inquiries.
                </p>
              </div>
              <button
                type="button"
                @click="addEmail"
                class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100 dark:border-blue-900/40 dark:bg-blue-950/30 dark:text-blue-300"
              >
                <span class="material-symbols-outlined text-[16px]">add</span>
                Add Email
              </button>
            </div>

            <div class="space-y-3">
              <div
                v-for="(email, index) in form.emails"
                :key="index"
                class="flex items-center gap-3"
              >
                <div class="relative flex-1">
                  <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                  >
                    mail
                  </span>
                  <input
                    v-model="form.emails[index]"
                    type="email"
                    class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#071426] dark:text-white"
                    :placeholder="index === 0 ? 'sales@sis-eg.org (Primary)' : 'info@sis-eg.org'"
                  />
                </div>

                <button
                  type="button"
                  @click="removeEmail(index)"
                  class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:border-slate-700 dark:hover:border-red-900 dark:hover:bg-red-950/30 dark:hover:text-red-400"
                  title="Remove Email"
                >
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Phone Numbers Repeater -->
          <div>
            <div class="mb-3 flex items-center justify-between">
              <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                  Phone Numbers
                </label>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                  Add one or more phone numbers for direct hotline access.
                </p>
              </div>
              <button
                type="button"
                @click="addPhone"
                class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100 dark:border-blue-900/40 dark:bg-blue-950/30 dark:text-blue-300"
              >
                <span class="material-symbols-outlined text-[16px]">add</span>
                Add Phone
              </button>
            </div>

            <div class="space-y-3">
              <div
                v-for="(phone, index) in form.phones"
                :key="index"
                class="flex items-center gap-3"
              >
                <div class="relative flex-1">
                  <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                  >
                    call
                  </span>
                  <input
                    v-model="form.phones[index]"
                    type="text"
                    class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#071426] dark:text-white"
                    :placeholder="index === 0 ? '+20 1007568651 (Primary)' : '+20 1008279573'"
                  />
                </div>

                <button
                  type="button"
                  @click="removePhone(index)"
                  class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 dark:border-slate-700 dark:hover:border-red-900 dark:hover:bg-red-950/30 dark:hover:text-red-400"
                  title="Remove Phone"
                >
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Section 2: Call to Action (CTA) & Welcoming Card -->
      <section
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#0b132b]"
      >
        <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
              Call to Action & Welcoming Card
            </h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
              Headline and banner text displayed next to the contact form in both English and Arabic.
            </p>
          </div>
          <span
            class="material-symbols-outlined text-[28px] text-blue-600 dark:text-blue-400"
          >
            handshake
          </span>
        </div>

        <div class="space-y-6">
          <!-- CTA Title -->
          <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-[#071426]/50">
            <h3 class="mb-3 text-sm font-bold text-slate-800 dark:text-slate-200">
              CTA Title / Headline
            </h3>
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                  English
                </label>
                <input
                  v-model="form.cta_title.en"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#0a192d] dark:text-white"
                  placeholder="Ready to Transform Your Operations?"
                />
              </div>
            </div>
          </div>

          <!-- CTA Description -->
          <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-[#071426]/50">
            <h3 class="mb-3 text-sm font-bold text-slate-800 dark:text-slate-200">
              CTA Description
            </h3>
            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                  English
                </label>
                <textarea
                  v-model="form.cta_description.en"
                  rows="3"
                  class="w-full resize-y rounded-xl border border-slate-300 bg-white p-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#0a192d] dark:text-white"
                  placeholder="Whether you're modernizing production, integrating AI into industrial workflows, or planning your digital transformation journey..."
                ></textarea>
              </div>
              
            </div>
          </div>

          <!-- CTA Link Text & URL -->
          <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-[#071426]/50">
            <h3 class="mb-3 text-sm font-bold text-slate-800 dark:text-slate-200">
              CTA Link Button
            </h3>
            <div class="grid gap-4 md:grid-cols-3">
              <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                  Link Text (English)
                </label>
                <input
                  v-model="form.cta_link_text.en"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#0a192d] dark:text-white"
                  placeholder="Let's build the future of manufacturing together."
                />
              </div>

            

              <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                  Link Destination (URL)
                </label>
                <input
                  v-model="form.cta_link"
                  type="text"
                  class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#0a192d] dark:text-white"
                  placeholder="/contact"
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Bottom Save Action Bar -->
      <div class="flex items-center justify-end gap-3 pt-2">
        <button
          type="button"
          :disabled="loading || saving"
          @click="loadData"
          class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-[#0f1d38] dark:text-slate-200"
        >
          Cancel / Revert
        </button>

        <button
          type="submit"
          :disabled="loading || saving"
          class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50"
        >
          <span
            v-if="saving"
            class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
          ></span>
          <span v-else class="material-symbols-outlined text-[18px]">save</span>
          {{ saving ? "Saving Changes..." : "Save Changes" }}
        </button>
      </div>
    </form>
  </div>
</template>
