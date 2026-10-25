<template>
  <main class="min-h-screen bg-slate-50 px-6 py-8 lg:px-10">
    <!-- Header -->
    <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div class="mb-3 flex items-center gap-2 text-sm text-slate-500">
          <span>Dashboard</span>
          <span>/</span>
          <span class="font-medium text-slate-700">Footer</span>
        </div>

        <div class="flex items-center gap-3">
          <div class="grid h-11 w-11 place-items-center rounded-xl bg-sis-accent/10 text-sis-accent">
            <span class="material-symbols-outlined text-[24px]">call_to_action</span>
          </div>

          <div>
            <h1 class="font-['Plus_Jakarta_Sans',Inter,sans-serif] text-2xl font-bold text-slate-900 lg:text-3xl">
              Footer Management
            </h1>
            <p class="mt-1 text-sm text-slate-500">
              Manage company information, copyright, navigation links, and social & contact links.
            </p>
          </div>
        </div>
      </div>

      <!-- Quick Action / Preview Link -->
      <div class="flex items-center gap-3">
        <a
          href="/"
          target="_blank"
          class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
          <span class="material-symbols-outlined text-[18px]">visibility</span>
          Preview Website
        </a>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-8 flex flex-wrap gap-2 border-b border-slate-200 pb-2">
      <button
        type="button"
        @click="activeTab = 'settings'"
        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition"
        :class="
          activeTab === 'settings'
            ? 'bg-sis-accent text-white shadow-sm'
            : 'text-slate-600 hover:bg-white hover:text-slate-900'
        "
      >
        <span class="material-symbols-outlined text-[19px]">tune</span>
        Company & Copyright
      </button>

      <button
        type="button"
        @click="activeTab = 'navigation'"
        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition"
        :class="
          activeTab === 'navigation'
            ? 'bg-sis-accent text-white shadow-sm'
            : 'text-slate-600 hover:bg-white hover:text-slate-900'
        "
      >
        <span class="material-symbols-outlined text-[19px]">link</span>
        Navigation Links ({{ navigationLinks.length }})
      </button>

      <button
        type="button"
        @click="activeTab = 'contact'"
        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition"
        :class="
          activeTab === 'contact'
            ? 'bg-sis-accent text-white shadow-sm'
            : 'text-slate-600 hover:bg-white hover:text-slate-900'
        "
      >
        <span class="material-symbols-outlined text-[19px]">share</span>
        Social & Contact Links ({{ contactLinks.length }})
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-sm">
      <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-sis-accent border-r-transparent"></div>
      <p class="mt-3 text-sm text-slate-500">Loading footer configuration...</p>
    </div>

    <!-- Error Banner -->
    <div
      v-if="errorMessage"
      class="mb-6 flex items-start justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm"
    >
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-[20px] text-red-500">error</span>
        <span>{{ errorMessage }}</span>
      </div>
      <button @click="errorMessage = ''" class="text-red-400 hover:text-red-700">
        <span class="material-symbols-outlined text-[18px]">close</span>
      </button>
    </div>

    <div v-if="!loading">
      <!-- ========================================== -->
      <!-- TAB 1: Company & Settings                  -->
      <!-- ========================================== -->
      <section v-if="activeTab === 'settings'" class="space-y-6">
        <form @submit.prevent="saveSettings" class="space-y-6">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-lg font-bold text-slate-900">Company Information</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage the brand description and footer logo.</p>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
              <!-- Logo Upload & Preview -->
              <div class="lg:col-span-1">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Company Logo</label>
                <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-6 text-center">
                  <div class="relative mb-4 flex h-24 w-full items-center justify-center rounded-xl bg-white p-2 shadow-sm border border-slate-100">
                    <img
                      :src="logoPreview || settings.logo || defaultLogoUrl"
                      alt="Footer Logo"
                      class="max-h-20 max-w-full object-contain"
                    />
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
                      class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                      Change Logo
                    </button>
                    <button
                      v-if="settings.logo || logoFile"
                      type="button"
                      @click="removeLogo"
                      class="rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                    >
                      Remove Logo
                    </button>
                  </div>
                  <p class="mt-2 text-[11px] text-slate-400">PNG, JPG, WEBP or SVG (Max 10MB)</p>
                </div>
              </div>

              <!-- Description -->
              <div class="lg:col-span-2 space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-slate-700 mb-1.5">Company Description</label>
                  <textarea
                    v-model="settings.description"
                    rows="4"
                    placeholder="Enter company description displayed in footer..."
                    class="w-full rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/10"
                  ></textarea>
                  <p class="mt-1 text-xs text-slate-400">
                    Typically 1-2 sentences summarizing SIS value proposition across MENA.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Copyright Settings -->
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-lg font-bold text-slate-900">Copyright & Year</h2>
            <p class="text-xs text-slate-500 mt-0.5">Control the copyright text and dynamic year resolution.</p>

            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Copyright Text</label>
                <input
                  v-model="settings.copyright_text"
                  type="text"
                  placeholder="Superior Integrated Solutions. All rights reserved."
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/10"
                />
              </div>

              <div class="flex items-center pt-6">
                <label class="relative flex cursor-pointer items-center gap-3">
                  <input
                    v-model="settings.dynamic_year"
                    type="checkbox"
                    class="peer sr-only"
                  />
                  <div class="h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-sis-accent peer-checked:after:translate-x-full peer-checked:after:border-white after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-['']"></div>
                  <div>
                    <span class="text-sm font-semibold text-slate-800">Dynamic Current Year</span>
                    <p class="text-xs text-slate-400">Automatically displays current year (e.g. {{ new Date().getFullYear() }})</p>
                  </div>
                </label>
              </div>
            </div>

            <!-- Live Preview -->
            <div class="mt-6 rounded-xl border border-slate-100 bg-slate-50 p-4">
              <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Live Copyright Preview:</span>
              <p class="mt-1 font-mono text-xs text-slate-700">
                © {{ settings.dynamic_year ? new Date().getFullYear() : '2026' }} {{ settings.copyright_text || 'Superior Integrated Solutions. All rights reserved.' }}
              </p>
            </div>
          </div>

          <!-- Floating Contact Button Settings -->
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-lg font-bold text-slate-900">Floating Contact Button</h2>
                <p class="text-xs text-slate-500 mt-0.5">Configure the floating action button at the bottom-right of the site.</p>
              </div>

              <label class="relative flex cursor-pointer items-center gap-3">
                <input
                  v-model="settings.floating_contact_enabled"
                  type="checkbox"
                  class="peer sr-only"
                />
                <div class="h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-sis-accent peer-checked:after:translate-x-full peer-checked:after:border-white after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-['']"></div>
                <span class="text-sm font-medium text-slate-700">
                  {{ settings.floating_contact_enabled ? "Enabled" : "Disabled" }}
                </span>
              </label>
            </div>

            <div v-if="settings.floating_contact_enabled" class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Action URL</label>
                <input
                  v-model="settings.floating_contact_url"
                  type="text"
                  placeholder="https://wa.me/201007568651 or tel:+201007568651"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/10"
                />
                <p class="mt-1 text-xs text-slate-400">Supports WhatsApp links, telephone (tel:), or mailto: URLs.</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tooltip / Label</label>
                <input
                  v-model="settings.floating_contact_label"
                  type="text"
                  placeholder="Contact us on WhatsApp"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-sis-accent focus:ring-2 focus:ring-sis-accent/10"
                />
              </div>
            </div>
          </div>

          <!-- Submit Bar -->
          <div class="flex justify-end gap-3">
            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50"
            >
              <span v-if="saving" class="h-4 w-4 animate-spin rounded-full border-2 border-white border-r-transparent"></span>
              <span class="material-symbols-outlined text-[19px]" v-else>save</span>
              {{ saving ? "Saving Settings..." : "Save Footer Settings" }}
            </button>
          </div>
        </form>
      </section>

      <!-- ========================================== -->
      <!-- TAB 2: Navigation Links                    -->
      <!-- ========================================== -->
      <section v-if="activeTab === 'navigation'" class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-lg font-bold text-slate-900">Footer Navigation Links</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage links displayed in the top row of the footer (e.g. Home, About, Services, Contact).</p>
          </div>

          <button
            type="button"
            @click="openAddLinkModal('navigation')"
            class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-90"
          >
            <span class="material-symbols-outlined text-[19px]">add</span>
            Add Navigation Link
          </button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <table class="w-full text-left text-sm text-slate-600">
            <thead class="border-b border-slate-100 bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
              <tr>
                <th class="px-6 py-4">Order</th>
                <th class="px-6 py-4">Label</th>
                <th class="px-6 py-4">URL</th>
                <th class="px-6 py-4">Target</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="navigationLinks.length === 0">
                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-400">
                  No navigation links found. Click "Add Navigation Link" to create one.
                </td>
              </tr>
              <tr
                v-for="(link, index) in navigationLinks"
                :key="link.id"
                class="transition hover:bg-slate-50/60"
              >
                <!-- Order -->
                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-500">
                  <div class="flex items-center gap-1.5">
                    <span>{{ link.sort_order }}</span>
                    <div class="flex flex-col">
                      <button
                        type="button"
                        :disabled="index === 0"
                        @click="moveLink(link, 'up', 'navigation')"
                        class="text-slate-400 hover:text-sis-accent disabled:opacity-20"
                        title="Move Up"
                      >
                        <span class="material-symbols-outlined text-[14px]">expand_less</span>
                      </button>
                      <button
                        type="button"
                        :disabled="index === navigationLinks.length - 1"
                        @click="moveLink(link, 'down', 'navigation')"
                        class="text-slate-400 hover:text-sis-accent disabled:opacity-20"
                        title="Move Down"
                      >
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                      </button>
                    </div>
                  </div>
                </td>

                <!-- Label -->
                <td class="px-6 py-4 font-semibold text-slate-900">
                  {{ link.label }}
                </td>

                <!-- URL -->
                <td class="px-6 py-4 font-mono text-xs text-slate-500">
                  {{ link.url }}
                </td>

                <!-- Target -->
                <td class="px-6 py-4 text-xs">
                  <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 font-mono text-[11px] text-slate-600">
                    {{ link.target || '_self' }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-6 py-4">
                  <button
                    type="button"
                    @click="toggleStatus(link)"
                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition"
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
                    {{ link.is_active ? "Active" : "Inactive" }}
                  </button>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      type="button"
                      @click="editLink(link)"
                      class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                      title="Edit Link"
                    >
                      <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button
                      type="button"
                      @click="confirmDelete(link)"
                      class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600"
                      title="Delete Link"
                    >
                      <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- ========================================== -->
      <!-- TAB 3: Social & Contact Links              -->
      <!-- ========================================== -->
      <section v-if="activeTab === 'contact'" class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-lg font-bold text-slate-900">Social & Contact Links</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage social icons, WhatsApp, and phone contacts displayed in the footer right bar.</p>
          </div>

          <button
            type="button"
            @click="openAddLinkModal('contact')"
            class="inline-flex items-center gap-2 rounded-xl bg-sis-accent px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:opacity-90"
          >
            <span class="material-symbols-outlined text-[19px]">add</span>
            Add Contact Link
          </button>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <table class="w-full text-left text-sm text-slate-600">
            <thead class="border-b border-slate-100 bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
              <tr>
                <th class="px-6 py-4">Order</th>
                <th class="px-6 py-4">Icon / Type</th>
                <th class="px-6 py-4">Label</th>
                <th class="px-6 py-4">URL</th>
                <th class="px-6 py-4">Target</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-if="contactLinks.length === 0">
                <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-400">
                  No social or contact links found. Click "Add Contact Link" to create one.
                </td>
              </tr>
              <tr
                v-for="(link, index) in contactLinks"
                :key="link.id"
                class="transition hover:bg-slate-50/60"
              >
                <!-- Order -->
                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-500">
                  <div class="flex items-center gap-1.5">
                    <span>{{ link.sort_order }}</span>
                    <div class="flex flex-col">
                      <button
                        type="button"
                        :disabled="index === 0"
                        @click="moveLink(link, 'up', 'contact')"
                        class="text-slate-400 hover:text-sis-accent disabled:opacity-20"
                        title="Move Up"
                      >
                        <span class="material-symbols-outlined text-[14px]">expand_less</span>
                      </button>
                      <button
                        type="button"
                        :disabled="index === contactLinks.length - 1"
                        @click="moveLink(link, 'down', 'contact')"
                        class="text-slate-400 hover:text-sis-accent disabled:opacity-20"
                        title="Move Down"
                      >
                        <span class="material-symbols-outlined text-[14px]">expand_more</span>
                      </button>
                    </div>
                  </div>
                </td>

                <!-- Icon & Type -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-lg border border-slate-200 bg-slate-50 font-mono text-xs text-slate-700">
                      {{ link.icon ? link.icon.slice(0, 3) : 'link' }}
                    </span>
                    <div>
                      <span class="block text-xs font-bold text-slate-800">{{ link.icon || 'link' }}</span>
                      <span class="block text-[10px] uppercase tracking-wider text-slate-400">{{ link.type || 'custom' }}</span>
                    </div>
                  </div>
                </td>

                <!-- Label -->
                <td class="px-6 py-4 font-semibold text-slate-900">
                  {{ link.label }}
                </td>

                <!-- URL -->
                <td class="px-6 py-4 font-mono text-xs text-slate-500 truncate max-w-[200px]" :title="link.url">
                  {{ link.url }}
                </td>

                <!-- Target -->
                <td class="px-6 py-4 text-xs">
                  <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 font-mono text-[11px] text-slate-600">
                    {{ link.target || '_blank' }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-6 py-4">
                  <button
                    type="button"
                    @click="toggleStatus(link)"
                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition"
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
                    {{ link.is_active ? "Active" : "Inactive" }}
                  </button>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      type="button"
                      @click="editLink(link)"
                      class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                      title="Edit Link"
                    >
                      <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button
                      type="button"
                      @click="confirmDelete(link)"
                      class="rounded-lg p-1.5 text-red-400 transition hover:bg-red-50 hover:text-red-600"
                      title="Delete Link"
                    >
                      <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- ========================================== -->
    <!-- ADD / EDIT LINK MODAL                      -->
    <!-- ========================================== -->
    <Teleport to="body">
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="modalOpen"
          class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
        >
          <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeLinkModal"></div>

          <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
              <h3 class="text-lg font-bold text-slate-900">
                {{ editingLink ? "Edit Footer Link" : "Add Footer Link" }}
              </h3>
              <button @click="closeLinkModal" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-[20px]">close</span>
              </button>
            </div>

            <form @submit.prevent="saveLink" class="mt-6 space-y-4">
              <!-- Category & Type -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                  <select
                    v-model="linkForm.category"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none focus:border-sis-accent"
                  >
                    <option value="navigation">Navigation</option>
                    <option value="contact">Contact / Social</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Type</label>
                  <select
                    v-model="linkForm.type"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none focus:border-sis-accent"
                  >
                    <option value="navigation">Navigation</option>
                    <option value="social">Social</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="phone">Phone</option>
                    <option value="email">Email</option>
                    <option value="custom">Custom</option>
                  </select>
                </div>
              </div>

              <!-- Label -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Label *</label>
                <input
                  v-model="linkForm.label"
                  type="text"
                  required
                  placeholder="e.g. LinkedIn, WhatsApp, About"
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-sis-accent"
                />
              </div>

              <!-- URL -->
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">URL / Link Target *</label>
                <input
                  v-model="linkForm.url"
                  type="text"
                  required
                  placeholder="https://..., /about, tel:+..., mailto:..."
                  class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-mono text-slate-900 outline-none focus:border-sis-accent"
                />
                <p class="mt-1 text-[11px] text-slate-400">
                  Accepts web URLs, internal paths (/about, #hero), tel:, or mailto:
                </p>
              </div>

              <!-- Icon Identifier (For Contact Links) -->
              <div v-if="linkForm.category === 'contact' || linkForm.icon">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Icon Identifier</label>
                <div class="flex items-center gap-2">
                  <input
                    v-model="linkForm.icon"
                    type="text"
                    placeholder="linkedin, whatsapp, phone, email, etc."
                    class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none focus:border-sis-accent"
                  />
                  <div class="flex flex-wrap gap-1">
                    <button
                      v-for="preset in ['linkedin', 'whatsapp', 'phone', 'email']"
                      :key="preset"
                      type="button"
                      @click="linkForm.icon = preset"
                      class="rounded-lg border border-slate-200 px-2 py-1 text-[11px] text-slate-600 hover:bg-slate-50"
                    >
                      {{ preset }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Target & Sort Order -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Target Window</label>
                  <select
                    v-model="linkForm.target"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none focus:border-sis-accent"
                  >
                    <option value="_self">Same Window (_self)</option>
                    <option value="_blank">New Tab (_blank)</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-semibold text-slate-700 mb-1">Sort Order</label>
                  <input
                    v-model.number="linkForm.sort_order"
                    type="number"
                    min="0"
                    placeholder="1"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-900 outline-none focus:border-sis-accent"
                  />
                </div>
              </div>

              <!-- Active Status -->
              <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="linkForm.is_active"
                    type="checkbox"
                    class="rounded text-sis-accent focus:ring-sis-accent"
                  />
                  <span class="text-sm text-slate-700">Active (Visible on public footer)</span>
                </label>
              </div>

              <!-- Modal Error -->
              <div v-if="modalError" class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-600">
                {{ modalError }}
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button
                  type="button"
                  @click="closeLinkModal"
                  class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="savingLink"
                  class="rounded-xl bg-sis-accent px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:opacity-90 disabled:opacity-50"
                >
                  {{ savingLink ? "Saving..." : editingLink ? "Save Changes" : "Create Link" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>
    </Teleport>

    <!-- ========================================== -->
    <!-- CONFIRM DELETE DIALOG                      -->
    <!-- ========================================== -->
    <ConfirmDialog
      v-model="deleteDialogOpen"
      title="Delete Footer Link"
      :message="`Are you sure you want to delete '${linkToDelete?.label}'? This action cannot be undone.`"
      confirm-text="Delete Link"
      cancel-text="Cancel"
      :danger="true"
      icon="delete"
      @confirm="executeDelete"
    />

    <!-- ========================================== -->
    <!-- TOAST NOTIFICATION                         -->
    <!-- ========================================== -->
    <ToastNotification
      v-if="toast.visible"
      :message="toast.message"
      :type="toast.type"
      @close="toast.visible = false"
    />
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import { adminAPI } from "@/SIS-Dashboard/api";

const defaultLogoUrl = `${import.meta.env.BASE_URL}images/sis-panel-img.jpeg`;

const activeTab = ref("settings");
const loading = ref(true);
const saving = ref(false);
const savingLink = ref(false);
const errorMessage = ref("");
const modalError = ref("");

const settings = ref({
  logo: null,
  logo_path: null,
  description: "",
  copyright_text: "",
  dynamic_year: true,
  floating_contact_enabled: true,
  floating_contact_url: "",
  floating_contact_label: "",
});

const allLinks = ref([]);

// File Upload State
const logoFileInput = ref(null);
const logoFile = ref(null);
const logoPreview = ref(null);
const removeLogoFlag = ref(false);

// Modal state
const modalOpen = ref(false);
const editingLink = ref(null);
const linkForm = ref({
  category: "navigation",
  type: "navigation",
  label: "",
  url: "",
  target: "_self",
  icon: "",
  sort_order: 1,
  is_active: true,
});

// Delete dialog
const deleteDialogOpen = ref(false);
const linkToDelete = ref(null);

// Toast
const toast = ref({
  visible: false,
  message: "",
  type: "success",
});

function showToast(message, type = "success") {
  toast.value = { visible: true, message, type };
  setTimeout(() => {
    toast.value.visible = false;
  }, 4000);
}

const navigationLinks = computed(() => {
  return allLinks.value
    .filter((l) => l.category === "navigation")
    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
});

const contactLinks = computed(() => {
  return allLinks.value
    .filter((l) => l.category === "contact")
    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
});

onMounted(() => {
  fetchFooter();
});

async function fetchFooter() {
  loading.value = true;
  errorMessage.value = "";
  try {
    const res = await adminAPI.getFooter();
    if (res?.settings) {
      settings.value = {
        ...res.settings,
        dynamic_year: Boolean(res.settings.dynamic_year),
        floating_contact_enabled: res.settings.floating_contact_enabled !== false,
      };
    }
    allLinks.value = res?.links || [];
  } catch (err) {
    console.error("Failed to load footer data:", err);
    errorMessage.value = err.message || "Failed to load footer configuration.";
  } finally {
    loading.value = false;
  }
}

function onLogoSelected(e) {
  const file = e.target.files?.[0];
  if (!file) return;

  if (file.size > 10 * 1024 * 1024) {
    errorMessage.value = "Selected logo image exceeds the 10MB file limit.";
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
  if (settings.value) {
    settings.value.logo = null;
    settings.value.logo_path = null;
  }
}

async function saveSettings() {
  saving.value = true;
  errorMessage.value = "";
  try {
    const fd = new FormData();
    if (logoFile.value) {
      fd.append("logo", logoFile.value);
    } else if (removeLogoFlag.value) {
      fd.append("remove_logo", "1");
    }

    fd.append("description", settings.value.description || "");
    fd.append("copyright_text", settings.value.copyright_text || "");
    fd.append("dynamic_year", settings.value.dynamic_year ? "1" : "0");
    fd.append("floating_contact_enabled", settings.value.floating_contact_enabled ? "1" : "0");
    fd.append("floating_contact_url", settings.value.floating_contact_url || "");
    fd.append("floating_contact_label", settings.value.floating_contact_label || "");

    const res = await adminAPI.updateFooterSettings(fd);
    if (res?.settings) {
      settings.value = {
        ...res.settings,
        dynamic_year: Boolean(res.settings.dynamic_year),
        floating_contact_enabled: res.settings.floating_contact_enabled !== false,
      };
    }

    logoFile.value = null;
    removeLogoFlag.value = false;
    showToast("Footer settings successfully saved!");
  } catch (err) {
    console.error("Failed to save settings:", err);
    errorMessage.value = err.message || "Failed to save footer settings.";
  } finally {
    saving.value = false;
  }
}

function openAddLinkModal(category = "navigation") {
  editingLink.value = null;
  modalError.value = "";
  const count = category === "navigation" ? navigationLinks.value.length : contactLinks.value.length;

  linkForm.value = {
    category,
    type: category === "navigation" ? "navigation" : "social",
    label: "",
    url: "",
    target: category === "navigation" ? "_self" : "_blank",
    icon: category === "contact" ? "linkedin" : "",
    sort_order: count + 1,
    is_active: true,
  };
  modalOpen.value = true;
}

function editLink(link) {
  editingLink.value = link;
  modalError.value = "";
  linkForm.value = {
    category: link.category || "navigation",
    type: link.type || (link.category === "navigation" ? "navigation" : "custom"),
    label: link.label || "",
    url: link.url || "",
    target: link.target || "_self",
    icon: link.icon || "",
    sort_order: link.sort_order ?? 1,
    is_active: Boolean(link.is_active),
  };
  modalOpen.value = true;
}

function closeLinkModal() {
  modalOpen.value = false;
  editingLink.value = null;
  modalError.value = "";
}

async function saveLink() {
  savingLink.value = true;
  modalError.value = "";
  try {
    if (editingLink.value) {
      const updated = await adminAPI.updateFooterLink(editingLink.value.id, linkForm.value);
      const idx = allLinks.value.findIndex((l) => l.id === editingLink.value.id);
      if (idx !== -1 && updated) {
        allLinks.value[idx] = updated;
      }
      showToast("Link successfully updated!");
    } else {
      const created = await adminAPI.createFooterLink(linkForm.value);
      if (created) {
        allLinks.value.push(created);
      }
      showToast("Link successfully created!");
    }
    closeLinkModal();
  } catch (err) {
    console.error("Failed to save link:", err);
    modalError.value = err.message || "Failed to save link.";
  } finally {
    savingLink.value = false;
  }
}

async function toggleStatus(link) {
  const newStatus = !link.is_active;
  try {
    const updated = await adminAPI.updateFooterLinkStatus(link.id, newStatus);
    link.is_active = updated?.is_active ?? newStatus;
    showToast(`Link ${link.is_active ? "activated" : "deactivated"}!`);
  } catch (err) {
    console.error("Failed to toggle status:", err);
    showToast(err.message || "Failed to change status", "error");
  }
}

function confirmDelete(link) {
  linkToDelete.value = link;
  deleteDialogOpen.value = true;
}

async function executeDelete() {
  if (!linkToDelete.value) return;
  const id = linkToDelete.value.id;
  try {
    await adminAPI.deleteFooterLink(id);
    allLinks.value = allLinks.value.filter((l) => l.id !== id);
    showToast("Link successfully deleted!");
  } catch (err) {
    console.error("Failed to delete link:", err);
    showToast(err.message || "Failed to delete link.", "error");
  } finally {
    linkToDelete.value = null;
  }
}

async function moveLink(link, direction, category) {
  const list = category === "navigation" ? [...navigationLinks.value] : [...contactLinks.value];
  const idx = list.findIndex((l) => l.id === link.id);
  if (idx === -1) return;

  const targetIdx = direction === "up" ? idx - 1 : idx + 1;
  if (targetIdx < 0 || targetIdx >= list.length) return;

  // Swap in array
  const temp = list[idx];
  list[idx] = list[targetIdx];
  list[targetIdx] = temp;

  // Re-assign sort orders
  const payload = list.map((item, index) => ({
    id: item.id,
    sort_order: index + 1,
  }));

  try {
    const updatedList = await adminAPI.reorderFooterLinks(payload);
    if (Array.isArray(updatedList)) {
      // Update local allLinks
      updatedList.forEach((upd) => {
        const found = allLinks.value.find((l) => l.id === upd.id);
        if (found) {
          found.sort_order = upd.sort_order;
        }
      });
    }
    showToast("Links reordered successfully!");
  } catch (err) {
    console.error("Failed to reorder links:", err);
    showToast(err.message || "Failed to save order.", "error");
  }
}
</script>
