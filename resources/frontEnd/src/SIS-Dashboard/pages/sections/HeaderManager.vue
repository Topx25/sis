<template>
  <main class="min-h-screen bg-slate-50 px-6 py-8 lg:px-10">
    <!-- Header -->
    <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div class="mb-3 flex items-center gap-2 text-sm text-slate-500">
          <span>Dashboard</span>
          <span>/</span>
          <span class="font-medium text-slate-700">Header</span>
        </div>

        <div class="flex items-center gap-3">
          <div class="grid h-11 w-11 place-items-center rounded-xl bg-sis-accent/10 text-sis-accent">
            <span class="material-symbols-outlined text-[24px]">web</span>
          </div>

          <div>
            <h1 class="font-['Plus_Jakarta_Sans',Inter,sans-serif] text-2xl font-bold text-slate-900 lg:text-3xl">
              Header Management
            </h1>
            <p class="mt-1 text-sm text-slate-500">
              Customize navigation labels, destination links, visibility, and the Call-to-Action button.
            </p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex items-center gap-3">
        <a
          href="/"
          target="_blank"
          class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
          <span class="material-symbols-outlined text-[18px]">visibility</span>
          Preview Website
        </a>

        <button
          type="button"
          :disabled="saving || loading"
          @click="saveHeader"
          class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:opacity-50"
        >
          <span v-if="saving" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
          <span v-else class="material-symbols-outlined text-[18px]">save</span>
          {{ saving ? "Saving..." : "Save Changes" }}
        </button>
      </div>
    </div>

    <!-- Error Banner -->
    <div
      v-if="errorMessage"
      class="mb-6 flex items-center justify-between rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm"
    >
      <div class="flex items-center gap-3">
        <span class="material-symbols-outlined text-red-500">error</span>
        <span>{{ errorMessage }}</span>
      </div>
      <button @click="errorMessage = ''" class="text-red-400 hover:text-red-600">
        <span class="material-symbols-outlined text-[18px]">close</span>
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-6">
      <div class="h-44 animate-pulse rounded-2xl bg-white p-6 shadow-sm"></div>
      <div class="h-80 animate-pulse rounded-2xl bg-white p-6 shadow-sm"></div>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-8">
      <!-- ========================================================= -->
      <!-- SECTION 1: WEBSITE BRAND & HEADER LOGO                    -->
      <!-- ========================================================= -->
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
        <div class="mb-6 flex flex-col gap-2 border-b border-slate-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-base font-bold text-slate-900">
              Brand Logo & Navbar Identity
            </h2>
            <p class="text-xs text-slate-500">
              Upload and manage the primary brand logo displayed in the top website navbar.
            </p>
          </div>

          <span
            v-if="hasCustomLogo"
            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200"
          >
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            Custom Logo Active
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"
          >
            Default System Logo
          </span>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
          <!-- Logo Upload Box (5 cols) -->
          <div class="lg:col-span-5 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 text-center">
            <!-- Preview Box (Styled like modern navbar pill / dual preview) -->
            <div class="relative mb-4 flex h-28 w-full items-center justify-center rounded-xl bg-[#0A192F] p-3 shadow-inner border border-slate-800">
              <img
                :src="displayLogoUrl"
                alt="Header Logo"
                class="max-h-20 max-w-full object-contain transition-transform duration-200 hover:scale-105"
              />
              <span class="absolute bottom-1.5 right-2 text-[10px] font-mono text-slate-400">
                Navbar Preview
              </span>
            </div>

            <input
              ref="logoFileInput"
              type="file"
              accept="image/png,image/jpeg,image/webp,image/svg+xml"
              class="hidden"
              @change="onLogoSelected"
            />

            <div class="flex flex-wrap items-center justify-center gap-2">
              <button
                type="button"
                @click="$refs.logoFileInput.click()"
                class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300"
              >
                <span class="material-symbols-outlined text-[16px]">upload</span>
                {{ hasCustomLogo ? "Change Logo" : "Upload Logo" }}
              </button>

              <button
                v-if="hasCustomLogo"
                type="button"
                @click="removeLogo"
                class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100"
              >
                <span class="material-symbols-outlined text-[16px]">delete</span>
                Reset to Default
              </button>
            </div>

            <p class="mt-2 text-[11px] text-slate-400">
              PNG, SVG, WEBP, or JPG (Max 10MB). Transparent background recommended.
            </p>
          </div>

          <!-- Header Live Simulation (7 cols) -->
          <div class="lg:col-span-7 flex flex-col justify-between rounded-2xl border border-slate-100 bg-slate-900/95 p-5 text-white shadow-md">
            <div>
              <div class="flex items-center justify-between pb-3 border-b border-white/10 text-xs text-slate-400 font-medium">
                <span class="flex items-center gap-1.5">
                  <span class="material-symbols-outlined text-[15px] text-blue-400">visibility</span>
                  Live Navbar Mockup
                </span>
                <span class="text-[11px] text-slate-400">Top Navigation Bar</span>
              </div>

              <!-- Miniature Navbar Pill -->
              <div class="mt-4 flex items-center justify-between gap-3 rounded-full border border-white/10 bg-slate-800/80 px-4 py-2 shadow-lg backdrop-blur-md">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-2.5 min-w-0">
                  <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/10 p-0.5">
                    <img
                      :src="displayLogoUrl"
                      alt="Brand"
                      class="h-7 w-7 object-contain"
                    />
                  </div>
                  <span class="font-bold text-xs text-white tracking-tight truncate hidden sm:inline">
                    SiS
                  </span>
                </div>

                <!-- Nav links demo -->
                <div class="hidden md:flex items-center gap-3 text-[11px] font-medium text-slate-300">
                  <span class="text-white font-semibold">Home</span>
                  <span>About Us</span>
                  <span>Services</span>
                  <span>Industries</span>
                </div>

                <!-- CTA Button demo -->
                <div class="shrink-0">
                  <span
                    v-if="form.button_enabled"
                    class="inline-flex items-center gap-1 rounded-full bg-blue-600 px-3 py-1 text-[11px] font-semibold text-white shadow-sm"
                  >
                    <span>{{ form.button_text || "Get in Touch" }}</span>
                    <span class="text-[9px]">→</span>
                  </span>
                  <span v-else class="text-[10px] text-slate-400 italic">
                    (CTA hidden)
                  </span>
                </div>
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between text-[11px] text-slate-400">
              <span>Status: <span class="text-emerald-400 font-medium">{{ logoFile ? "New file ready to save" : hasCustomLogo ? "Custom logo deployed" : "Using system default logo" }}</span></span>
              <span>Position: Header Top-Left</span>
            </div>
          </div>
        </div>
      </section>

      <!-- ========================================================= -->
      <!-- SECTION 2: CALL TO ACTION (CTA) BUTTON                    -->
      <!-- ========================================================= -->
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-5">
          <div>
            <h2 class="text-base font-bold text-slate-900">
              Call-to-Action (CTA) Button
            </h2>
            <p class="text-xs text-slate-500">
              Configure the primary button displayed on the right side of the navbar.
            </p>
          </div>

          <label class="inline-flex cursor-pointer items-center gap-3 select-none">
            <span class="text-xs font-semibold" :class="form.button_enabled ? 'text-sis-accent' : 'text-slate-400'">
              {{ form.button_enabled ? "Button Enabled" : "Button Hidden" }}
            </span>
            <input
              v-model="form.button_enabled"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="peer relative h-6 w-11 rounded-full bg-slate-200 after:absolute after:start-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-sis-accent peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
          </label>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
          <!-- Button Text -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
              Button Label
            </label>
            <input
              v-model="form.button_text"
              type="text"
              placeholder="e.g. Get in Touch"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
            />
          </div>

          <!-- Button Link -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
              Destination URL / Route
            </label>
            <input
              v-model="form.button_link"
              type="text"
              placeholder="e.g. /contact"
              class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
            />
          </div>

          <!-- Button Live Preview -->
          <div>
            <label class="mb-1.5 block text-xs font-semibold text-slate-700">
              Navbar Preview
            </label>
            <div class="flex h-[42px] items-center rounded-xl bg-slate-900 px-4">
              <span
                v-if="form.button_enabled"
                class="inline-flex items-center gap-1.5 rounded-full bg-sis-accent px-3 py-1 text-xs font-semibold text-white shadow-sm"
              >
                <span>{{ form.button_text || "Get in Touch" }}</span>
                <span aria-hidden="true">→</span>
              </span>
              <span v-else class="text-xs italic text-slate-400">
                (Button disabled)
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- ========================================================= -->
      <!-- SECTION 3: NAVIGATION ITEMS (EDIT TEXT & STATUS ONLY)      -->
      <!-- ========================================================= -->
      <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
        <div class="mb-6 flex flex-col gap-2 border-b border-slate-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-base font-bold text-slate-900">
              Navigation Menu Items
            </h2>
            <p class="text-xs text-slate-500">
              Edit the displayed text label and destination URL for each navigation item. Addition and deletion of main sections are locked.
            </p>
          </div>

          <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200/60">
            <span class="material-symbols-outlined text-[14px]">lock</span>
            Fixed Structure (5 Items)
          </span>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200">
          <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
              <tr>
                <th scope="col" class="px-5 py-3.5 w-16 text-center">#</th>
                <th scope="col" class="px-5 py-3.5 w-44">Key Identifier</th>
                <th scope="col" class="px-5 py-3.5">Label (Text)</th>
                <th scope="col" class="px-5 py-3.5 w-36 text-center">Menu Type</th>
                <th scope="col" class="px-5 py-3.5 w-32 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="(link, index) in form.nav_links"
                :key="link.id || link.key || index"
                class="hover:bg-slate-50/70 transition-colors"
              >
                <!-- Index -->
                <td class="px-5 py-4 text-center font-mono text-xs text-slate-400">
                  0{{ index + 1 }}
                </td>

                <!-- Key -->
                <td class="px-5 py-4 font-mono text-xs font-semibold text-slate-700">
                  <span class="rounded bg-slate-100 px-2 py-1 text-slate-600">
                    {{ link.key }}
                  </span>
                </td>

                <!-- Label (Text) Input -->
                <td class="px-5 py-4">
                  <input
                    v-model="link.label"
                    type="text"
                    placeholder="Navigation label"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-900 outline-none transition focus:border-sis-accent focus:ring-1 focus:ring-sis-accent"
                  />
                </td>

                <!-- Menu Type Badge -->
                <td class="px-5 py-4 text-center">
                  <span
                    v-if="link.has_dropdown"
                    class="inline-flex items-center gap-1 rounded-full bg-purple-50 px-2.5 py-1 text-[11px] font-semibold text-purple-700 border border-purple-200"
                  >
                    <span class="material-symbols-outlined text-[13px]">expand_more</span>
                    Mega Menu
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-600"
                  >
                    Direct Link
                  </span>
                </td>

                <!-- Active Toggle Switch -->
                <td class="px-5 py-4 text-center">
                  <button
                    type="button"
                    @click="link.is_active = !link.is_active"
                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition"
                    :class="
                      link.is_active
                        ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                        : 'bg-slate-100 text-slate-500 hover:bg-slate-200'
                    "
                  >
                    <span
                      class="h-1.5 w-1.5 rounded-full"
                      :class="link.is_active ? 'bg-emerald-500' : 'bg-slate-400'"
                    ></span>
                    {{ link.is_active ? "Active" : "Hidden" }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Save Bar Bottom -->
      <div class="flex justify-end pt-4">
        <button
          type="button"
          :disabled="saving || loading"
          @click="saveHeader"
          class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-blue-700 disabled:opacity-50"
        >
          <span v-if="saving" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
          <span v-else class="material-symbols-outlined text-[18px]">save</span>
          {{ saving ? "Saving Changes..." : "Save Header Configuration" }}
        </button>
      </div>
    </div>

    <!-- Toast Notification -->
    <ToastNotification
      v-if="toast.visible"
      :message="toast.message"
      :title="toast.title"
      :variant="toast.variant"
      @close="toast.visible = false"
    />
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import { adminAPI } from "@/SIS-Dashboard/api";
import { useSiteStore } from "@/stores/useSiteStore";
import { resolveImageUrl } from "@/utils/imageUrl.js";

const defaultLogoUrl = `${import.meta.env.BASE_URL}images/sis-panel-img.jpeg`;

const siteStore = useSiteStore();

const loading = ref(true);
const saving = ref(false);
const errorMessage = ref("");

// Logo upload state
const logoFileInput = ref(null);
const logoFile = ref(null);
const logoPreview = ref(null);
const removeLogoFlag = ref(false);

const form = ref({
  id: 1,
  logo_url: null,
  button_text: "Get in Touch",
  button_link: "/contact",
  button_enabled: true,
  nav_links: [
    { id: 1, key: "home", label: "Home", url: "/", has_dropdown: false, is_active: true },
    { id: 6, key: "about", label: "About Us", url: "/about", has_dropdown: false, is_active: true },
    { id: 2, key: "services", label: "Services & Solutions", url: "/services", has_dropdown: true, is_active: true },
    { id: 3, key: "industries", label: "Industries", url: "/industries", has_dropdown: true, is_active: true },
    { id: 4, key: "partner-ecosystem", label: "Partner Ecosystem", url: "/partner-ecosystem", has_dropdown: false, is_active: true },
    { id: 5, key: "use-cases", label: "Use Cases", url: "/use-cases", has_dropdown: false, is_active: true },
  ],
});

const displayLogoUrl = computed(() => {
  if (logoPreview.value) return logoPreview.value;
  if (removeLogoFlag.value) return defaultLogoUrl;
  if (form.value.logo_url) return resolveImageUrl(form.value.logo_url);
  return defaultLogoUrl;
});

const hasCustomLogo = computed(() => {
  if (removeLogoFlag.value) return false;
  return Boolean(logoFile.value || form.value.logo_url);
});

function onLogoSelected(e) {
  const file = e.target.files?.[0];
  if (!file) return;

  if (file.size > 10 * 1024 * 1024) {
    showToast("File Too Large", "Selected logo image exceeds the 10MB limit.", "error");
    return;
  }

  logoFile.value = file;
  removeLogoFlag.value = false;
  logoPreview.value = URL.createObjectURL(file);
}

function removeLogo() {
  logoFile.value = null;
  logoPreview.value = null;
  removeLogoFlag.value = true;
  form.value.logo_url = null;
}

const toast = ref({
  visible: false,
  title: "",
  message: "",
  variant: "success",
});

function showToast(title, message, variant = "success") {
  toast.value = { visible: true, title, message, variant };
  setTimeout(() => {
    toast.value.visible = false;
  }, 4000);
}

async function fetchHeader() {
  loading.value = true;
  errorMessage.value = "";
  try {
    const res = await adminAPI.getHeader();
    if (res) {
      form.value.id = res.id || 1;
      form.value.logo_url = res.logo_url || res.logo_path || null;
      logoFile.value = null;
      logoPreview.value = null;
      removeLogoFlag.value = false;
      form.value.button_text = res.button_text || "Get in Touch";
      form.value.button_link = res.button_link || "/contact";
      form.value.button_enabled = res.button_enabled !== false;
      if (Array.isArray(res.nav_links) && res.nav_links.length > 0) {
        const loadedLinks = res.nav_links.map((link) => ({
          ...link,
          is_active: link.is_active !== false,
          has_dropdown: Boolean(link.has_dropdown),
        }));
        if (!loadedLinks.some((l) => l.key === "about")) {
          const homeIdx = loadedLinks.findIndex((l) => l.key === "home");
          const insertIdx = homeIdx >= 0 ? homeIdx + 1 : 1;
          loadedLinks.splice(insertIdx, 0, {
            id: 6,
            key: "about",
            label: "About Us",
            url: "/about",
            has_dropdown: false,
            is_active: true,
          });
        }
        form.value.nav_links = loadedLinks;
      }
    }
  } catch (err) {
    console.warn("Could not load header configuration:", err);
    errorMessage.value = err?.message || "Failed to load header configuration.";
  } finally {
    loading.value = false;
  }
}

async function saveHeader() {
  saving.value = true;
  errorMessage.value = "";
  try {
    const fd = new FormData();
    if (logoFile.value) {
      fd.append("logo", logoFile.value);
    } else if (removeLogoFlag.value) {
      fd.append("remove_logo", "1");
    }

    fd.append("button_text", form.value.button_text || "");
    fd.append("button_link", form.value.button_link || "");
    fd.append("button_enabled", form.value.button_enabled ? "1" : "0");
    fd.append("nav_links", JSON.stringify(form.value.nav_links.map((l) => ({
      id: l.id,
      key: l.key,
      label: l.label,
      url: l.url,
      has_dropdown: Boolean(l.has_dropdown),
      is_active: Boolean(l.is_active),
    }))));

    const res = await adminAPI.updateHeader(fd);
    if (res) {
      form.value.logo_url = res.logo_url || res.logo_path || null;
      siteStore.header = res;
      siteStore.headerLoaded = true;
    }
    logoFile.value = null;
    removeLogoFlag.value = false;
    showToast("Success", "Header configuration updated successfully.", "success");
  } catch (err) {
    console.error("Failed to update header:", err);
    errorMessage.value = err?.message || "Failed to save header configuration.";
    showToast("Error", err?.message || "Failed to update header settings.", "error");
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  fetchHeader();
});
</script>
