<template>
  <div class="mx-auto max-w-7xl space-y-6">
    <!-- Header -->
    <AdminPageHeader
      title="About Us Page"
      subtitle="Manage all sections of the About Us page: Hero, Mission & Vision, Empowered Domains, Timeline, IT-OT Divide, and Core Values."
      category="Page Content"
      icon="info"
    >
      <template #actions>
        <!-- Toggle Navbar Link for About Us -->
        <div
          class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-medium text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition"
          :class="{ 'opacity-60 pointer-events-none': isUpdatingNavbar }"
        >
          <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[18px] text-slate-400">link</span>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">In Navbar:</span>
          </div>
          <button
            type="button"
            role="switch"
            :aria-checked="isAboutInNavbar"
            :disabled="isUpdatingNavbar"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
            :class="isAboutInNavbar ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-600'"
            @click.stop="toggleAboutInNavbar"
          >
            <span
              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
              :class="isAboutInNavbar ? 'translate-x-5' : 'translate-x-0'"
            />
          </button>
          <span class="text-xs font-bold" :class="isAboutInNavbar ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400'">
            {{ isAboutInNavbar ? 'Visible' : 'Hidden' }}
          </span>
        </div>

        <a
          href="/about"
          target="_blank"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
        >
          <span class="material-symbols-outlined text-[18px]">open_in_new</span>
          View Page
        </a>
        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
          :disabled="isLoading"
          @click="loadCurrentTab"
        >
          <span class="material-symbols-outlined text-[18px]" :class="{ 'animate-spin': isLoading }">sync</span>
          Refresh
        </button>
      </template>
    </AdminPageHeader>

    <!-- Tab Navigation -->
    <div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-slate-50/70 p-1.5 dark:border-slate-800 dark:bg-slate-900/50">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        type="button"
        @click="activeTab = tab.id"
        class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition"
        :class="
          activeTab === tab.id
            ? 'bg-white text-blue-600 shadow-sm dark:bg-[#071426] dark:text-blue-400'
            : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200'
        "
      >
        <span class="material-symbols-outlined text-[18px]">{{ tab.icon }}</span>
        {{ tab.label }}
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="space-y-4">
      <div class="h-64 animate-pulse rounded-2xl bg-slate-200 dark:bg-slate-800" />
    </div>

    <!-- Error State -->
    <div
      v-else-if="error"
      class="rounded-2xl border border-red-200 bg-red-50 p-6 dark:border-red-900/40 dark:bg-red-950/20"
    >
      <h3 class="font-semibold text-red-700 dark:text-red-400">Failed to load content</h3>
      <p class="mt-1 text-sm text-red-600 dark:text-red-300">{{ error }}</p>
      <button
        type="button"
        class="mt-4 rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
        @click="loadCurrentTab"
      >
        Try Again
      </button>
    </div>

    <!-- Tab Contents -->
    <div v-else class="space-y-6">
      <!-- ============================================================== -->
      <!-- TAB 1: HERO SECTION -->
      <!-- ============================================================== -->
      <section
        v-if="activeTab === 'hero'"
        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#071426]"
      >
        <div class="flex items-center justify-between border-b border-slate-200 pb-5 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:white">About Hero Section</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage headline, subheadline, CTA and background image</p>
          </div>
          <button
            type="button"
            :disabled="isSaving"
            @click="saveHero"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="isSaving" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            {{ isSaving ? "Saving..." : "Save Hero" }}
          </button>
        </div>

        <form @submit.prevent="saveHero" class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
          <div class="space-y-4 md:col-span-2">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Tagline / Eyebrow</label>
              <input
                v-model="heroData.tagline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="e.g. PIONEERING INDUSTRIAL EVOLUTION"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Headline</label>
              <input
                v-model="heroData.headline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="e.g. Architects of the 4th Industrial Revolution"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Subheadline</label>
              <textarea
                v-model="heroData.subheadline"
                rows="3"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="Describe the overarching mission or elevator pitch"
              ></textarea>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Call-to-Action Text</label>
            <input
              v-model="heroData.cta_text"
              type="text"
              class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              placeholder="e.g. Discover Our Expertise"
            />
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Call-to-Action Link</label>
            <input
              v-model="heroData.cta_link"
              type="text"
              class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              placeholder="e.g. /projects or /solution-pages"
            />
          </div>

          <!-- Hero Image -->
          <div class="space-y-2 md:col-span-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Hero Image</label>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
              <div
                v-if="heroImagePreview || heroData.image_url"
                class="relative h-32 w-56 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-800"
              >
                <img
                  :src="heroImagePreview || heroData.image_url"
                  alt="Hero Preview"
                  class="h-full w-full object-cover"
                />
              </div>
              <div class="flex-1 space-y-2">
                <input
                  type="file"
                  accept="image/*"
                  @change="handleHeroImageUpload"
                  class="block w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-800 dark:file:text-blue-400"
                />
                <p class="text-xs text-slate-400">Supports WebP, PNG, JPG, SVG (Max 10MB)</p>
              </div>
            </div>
          </div>
        </form>
      </section>

      <!-- ============================================================== -->
      <!-- TAB 2: MISSION & VISION SECTION -->
      <!-- ============================================================== -->
      <section
        v-if="activeTab === 'vision'"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#071426]"
      >
        <div class="flex items-center justify-between border-b border-slate-200 pb-5 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Mission, Vision & Overview</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage company introductory story, mission card, and vision card</p>
          </div>
          <button
            type="button"
            :disabled="isSaving"
            @click="saveVision"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="isSaving" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            {{ isSaving ? "Saving..." : "Save Overview" }}
          </button>
        </div>

        <div class="space-y-6">
          <!-- Main Section Headers -->
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Section Eyebrow / Title</label>
              <input
                v-model="visionData.title"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="e.g. Who We Are"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Headline</label>
              <input
                v-model="visionData.headline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="e.g. Our Mission & Vision"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Subheadline</label>
              <textarea
                v-model="visionData.subheadline"
                rows="2"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              ></textarea>
            </div>
          </div>          

          <!-- Mission & Vision Cards -->
          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Mission Card -->
            <div class="rounded-xl border border-blue-100 bg-blue-50/30 p-4 dark:border-blue-900/30 dark:bg-blue-950/20">
              <div class="mb-3 flex items-center gap-2 text-blue-600 dark:text-blue-400">
                <span class="material-symbols-outlined text-[20px]">flag</span>
                <h3 class="font-bold">Mission Card</h3>
              </div>
              <div class="space-y-3">
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Eyebrow</label>
                  <input
                    v-model="visionData.mission.tagline"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Title</label>
                  <input
                    v-model="visionData.mission.title"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Icon</label>
                  <div class="mt-1">
                    <IconPicker
                      v-model="visionData.mission.icon"
                      placeholder="Select mission icon..."
                    />
                  </div>
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Description</label>
                  <textarea
                    v-model="visionData.mission.description"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Vision Card -->
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/30 p-4 dark:border-indigo-900/30 dark:bg-indigo-950/20">
              <div class="mb-3 flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
                <span class="material-symbols-outlined text-[20px]">visibility</span>
                <h3 class="font-bold">Vision Card</h3>
              </div>
              <div class="space-y-3">
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Eyebrow</label>
                  <input
                    v-model="visionData.vision.tagline"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Title</label>
                  <input
                    v-model="visionData.vision.title"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Icon</label>
                  <div class="mt-1">
                    <IconPicker
                      v-model="visionData.vision.icon"
                      placeholder="Select vision icon..."
                    />
                  </div>
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Description</label>
                  <textarea
                    v-model="visionData.vision.description"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Why Choose SiS Banner -->
          <div class="rounded-xl border border-blue-200 bg-blue-50/40 p-5 dark:border-blue-900/40 dark:bg-blue-950/20">
            <div class="mb-3 flex items-center gap-2 text-blue-600 dark:text-blue-400">
              <span class="material-symbols-outlined text-[20px]">help_center</span>
              <h3 class="font-bold">Why Choose SiS? Banner</h3>
            </div>
            <div class="space-y-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-500">Banner Title</label>
                <input
                  v-model="visionData.why_choose_us.title"
                  type="text"
                  placeholder="e.g. Why Choose SiS?"
                  class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-500">Banner Description</label>
                <textarea
                  v-model="visionData.why_choose_us.description"
                  rows="2"
                  placeholder="e.g. We combine deep industry expertise with cutting-edge technology to deliver sustainable, future-proof industrial solutions across the MENA region."
                  class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                ></textarea>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- TAB 3: EMPOWERED DOMAINS -->
      <!-- ============================================================== -->
      <section
        v-if="activeTab === 'domains'"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#071426]"
      >
        <div class="flex items-center justify-between border-b border-slate-200 pb-5 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Empowered Domains (Industries)</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage industries highlight cards shown on the About page</p>
          </div>
          <button
            type="button"
            :disabled="isSaving"
            @click="saveDomains"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="isSaving" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            {{ isSaving ? "Saving..." : "Save Domains" }}
          </button>
        </div>

        <div class="space-y-6">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Tagline</label>
              <input
                v-model="domainsData.tagline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Headline</label>
              <input
                v-model="domainsData.headline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Subheadline</label>
              <textarea
                v-model="domainsData.subheadline"
                rows="2"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              ></textarea>
            </div>
          </div>

          <!-- Domains Cards -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Domains List ({{ domainsData.domains.length }})</h3>
              <button
                type="button"
                @click="addDomain"
                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400"
              >
                <span class="material-symbols-outlined text-[16px]">add</span>
                Add Domain Card
              </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div
                v-for="(domain, idx) in domainsData.domains"
                :key="idx"
                class="relative rounded-xl border border-slate-200 bg-slate-50/50 p-4 transition hover:border-slate-300 dark:border-slate-800 dark:bg-slate-900/30"
              >
                <button
                  type="button"
                  @click="removeDomain(idx)"
                  class="absolute right-3 top-3 rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                >
                  <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
                <div class="space-y-3 pr-8">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="block text-[11px] font-bold text-slate-500">Icon</label>
                      <div class="mt-1">
                        <IconPicker
                          v-model="domain.icon"
                          placeholder="Select icon..."
                        />
                      </div>
                    </div>
                    <div>
                      <label class="block text-[11px] font-bold text-slate-500">Domain Title</label>
                      <input
                        v-model="domain.title"
                        type="text"
                        placeholder="e.g. Oil & Gas"
                        class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                      />
                    </div>
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-slate-500">Description</label>
                    <textarea
                      v-model="domain.description"
                      rows="2"
                      class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                      placeholder="Brief details about capabilities in this domain"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- TAB 4: COMPANY TIMELINE -->
      <!-- ============================================================== -->
      <section
        v-if="activeTab === 'timeline'"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#071426]"
      >
        <div class="flex items-center justify-between border-b border-slate-200 pb-5 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Company Timeline & Milestones</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage journey milestones displayed chronologically on the About page</p>
          </div>
          <button
            type="button"
            @click="openMilestoneModal()"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
          >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Add Milestone
          </button>
        </div>

        <!-- Timeline Section Header Configuration -->
        <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-5 dark:border-slate-800 dark:bg-slate-900/30">
          <div class="mb-4 flex items-center justify-between">
            <div>
              <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Section Header (Public Title)</span>
              <p class="text-xs text-slate-400 mt-0.5">Customize the main heading and blue highlight above the timeline track</p>
            </div>
            <button
              type="button"
              :disabled="isSavingHeader"
              @click="saveTimelineHeader"
              class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
            >
              <span v-if="isSavingHeader" class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
              {{ isSavingHeader ? 'Saving...' : 'Save Header' }}
            </button>
          </div>
          <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Tagline / Eyebrow</label>
              <input
                v-model="timelineHeader.tagline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="e.g. Our journey"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Title</label>
              <input
                v-model="timelineHeader.title"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="e.g. A trajectory of"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Title Highlight (Accent Color)</label>
              <input
                v-model="timelineHeader.title_highlight"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                placeholder="e.g. growth."
              />
            </div>
          </div>
        </div>

        <!-- Milestones list -->
        <div class="divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white dark:divide-slate-800 dark:border-slate-800 dark:bg-slate-900/40">
          <div
            v-if="timelineList.length === 0"
            class="p-8 text-center text-sm text-slate-400"
          >
            No milestones found. Click "Add Milestone" to create one.
          </div>
          <div
            v-for="item in timelineList"
            :key="item.id"
            class="flex items-center justify-between gap-4 p-4 transition hover:bg-slate-50 dark:hover:bg-slate-800/50"
          >
            <div class="flex items-center gap-4">
              <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                <span class="material-symbols-outlined text-[22px]">{{ item.icon || 'flag' }}</span>
              </span>
              <div>
                <div class="flex items-center gap-2">
                  <span class="rounded bg-blue-100 px-2 py-0.5 text-xs font-bold text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">{{ item.year }}</span>
                  <h4 class="font-bold text-slate-900 dark:text-white">{{ item.title }}</h4>
                </div>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ item.description }}</p>
              </div>
            </div>
            <div class="flex items-center gap-1 shrink-0">
              <button
                type="button"
                @click="openMilestoneModal(item)"
                class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-blue-600 dark:hover:bg-slate-800 dark:hover:text-blue-400"
              >
                <span class="material-symbols-outlined text-[18px]">edit</span>
              </button>
              <button
                type="button"
                @click="promptDeleteMilestone(item)"
                class="rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
              >
                <span class="material-symbols-outlined text-[18px]">delete</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- TAB 5: IT-OT DIVIDE -->
      <!-- ============================================================== -->
      <section
        v-if="activeTab === 'itot'"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#071426]"
      >
        <div class="flex items-center justify-between border-b border-slate-200 pb-5 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Bridging the IT-OT Divide</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Configure Operational Technology (OT) and Information Technology (IT) columns</p>
          </div>
          <button
            type="button"
            :disabled="isSaving"
            @click="saveItOt"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="isSaving" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            {{ isSaving ? "Saving..." : "Save IT-OT" }}
          </button>
        </div>

        <div class="space-y-6">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Tagline</label>
              <input
                v-model="itotData.tagline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Headline</label>
              <input
                v-model="itotData.headline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Subheadline</label>
              <textarea
                v-model="itotData.subheadline"
                rows="2"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              ></textarea>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- OT Side -->
            <div class="rounded-xl border border-amber-200 bg-amber-50/20 p-5 dark:border-amber-900/30 dark:bg-amber-950/10">
              <div class="mb-4 flex items-center gap-2 text-amber-600 dark:text-amber-400">
                <span class="material-symbols-outlined text-[20px]">precision_manufacturing</span>
                <h3 class="font-bold">Operational Technology (OT)</h3>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Title</label>
                  <input
                    v-model="itotData.ot.title"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Icon</label>
                  <div class="mt-1">
                    <IconPicker
                      v-model="itotData.ot.icon"
                      placeholder="Select OT icon..."
                    />
                  </div>
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Description</label>
                  <textarea
                    v-model="itotData.ot.description"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  ></textarea>
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Tags / Systems (comma-separated)</label>
                  <input
                    v-model="otTagsInput"
                    type="text"
                    placeholder="e.g. SCADA, PLCs, Sensors, DCS"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                  <!-- Live tag badges preview -->
                  <div v-if="parsedOtTags.length" class="mt-2 flex flex-wrap gap-1.5">
                    <span
                      v-for="(tag, idx) in parsedOtTags"
                      :key="idx"
                      class="inline-flex items-center rounded-md bg-amber-100/70 px-2 py-0.5 text-[11px] font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300"
                    >
                      {{ tag }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- IT Side -->
            <div class="rounded-xl border border-sky-200 bg-sky-50/20 p-5 dark:border-sky-900/30 dark:bg-sky-950/10">
              <div class="mb-4 flex items-center gap-2 text-sky-600 dark:text-sky-400">
                <span class="material-symbols-outlined text-[20px]">cloud</span>
                <h3 class="font-bold">Information Technology (IT)</h3>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Title</label>
                  <input
                    v-model="itotData.it.title"
                    type="text"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Icon</label>
                  <div class="mt-1">
                    <IconPicker
                      v-model="itotData.it.icon"
                      placeholder="Select IT icon..."
                    />
                  </div>
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Description</label>
                  <textarea
                    v-model="itotData.it.description"
                    rows="3"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  ></textarea>
                </div>
                <div>
                  <label class="block text-[11px] font-bold text-slate-500">Tags / Systems (comma-separated)</label>
                  <input
                    v-model="itTagsInput"
                    type="text"
                    placeholder="e.g. ERP, Cloud, Analytics, MES"
                    class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                  />
                  <!-- Live tag badges preview -->
                  <div v-if="parsedItTags.length" class="mt-2 flex flex-wrap gap-1.5">
                    <span
                      v-for="(tag, idx) in parsedItTags"
                      :key="idx"
                      class="inline-flex items-center rounded-md bg-sky-100/70 px-2 py-0.5 text-[11px] font-semibold text-sky-800 dark:bg-sky-900/40 dark:text-sky-300"
                    >
                      {{ tag }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- TAB 6: CORE VALUES -->
      <!-- ============================================================== -->
      <section
        v-if="activeTab === 'values'"
        class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-[#071426]"
      >
        <div class="flex items-center justify-between border-b border-slate-200 pb-5 dark:border-slate-800">
          <div>
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Core Values & Principles</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage foundational corporate values cards shown on the About page</p>
          </div>
          <button
            type="button"
            :disabled="isSaving"
            @click="saveCoreValues"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="isSaving" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
            {{ isSaving ? "Saving..." : "Save Values" }}
          </button>
        </div>

        <div class="space-y-6">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Tagline</label>
              <input
                v-model="coreData.tagline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Headline</label>
              <input
                v-model="coreData.headline"
                type="text"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">Subheadline</label>
              <textarea
                v-model="coreData.subheadline"
                rows="2"
                class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              ></textarea>
            </div>
          </div>

          <!-- Principles list -->
          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-bold text-slate-900 dark:text-white">Principles ({{ coreData.principles.length }})</h3>
              <button
                type="button"
                @click="addPrinciple"
                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400"
              >
                <span class="material-symbols-outlined text-[16px]">add</span>
                Add Principle Card
              </button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
              <div
                v-for="(item, idx) in coreData.principles"
                :key="idx"
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900/40 flex flex-col justify-between"
              >
                <!-- Card Header: Step Index + Delete Button -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3.5 dark:border-slate-800/80">
                  <div class="flex items-center gap-2">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-lg bg-blue-50 text-[11px] font-bold text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                      {{ idx + 1 }}
                    </span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                      Principle Card
                    </span>
                  </div>
                  <button
                    type="button"
                    @click="removePrinciple(idx)"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600 transition dark:hover:bg-red-950/40"
                    title="Delete Principle"
                  >
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                  </button>
                </div>

                <div class="space-y-3.5">
                  <!-- Row: Step # & Icon Picker -->
                  <div class="grid grid-cols-3 gap-2.5">
                    <div>
                      <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400">Step #</label>
                      <input
                        v-model="item.step"
                        type="text"
                        placeholder="01"
                        class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs font-semibold outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                      />
                    </div>
                    <div class="col-span-2">
                      <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400">Icon</label>
                      <div class="mt-1">
                        <IconPicker
                          v-model="item.icon"
                          placeholder="Select icon..."
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Title -->
                  <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400">Title</label>
                    <input
                      v-model="item.title"
                      type="text"
                      placeholder="e.g. Precision"
                      class="mt-1 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs font-medium outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                    />
                  </div>

                  <!-- Description -->
                  <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400">Description</label>
                    <textarea
                      v-model="item.description"
                      rows="3"
                      placeholder="Principle details..."
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white p-3 text-xs outline-none transition focus:border-blue-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white resize-none"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Milestone Form Modal -->
    <Teleport to="body">
      <div
        v-if="showMilestoneModal"
        class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
      >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showMilestoneModal = false"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-[#071426] dark:border dark:border-slate-800">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">
              {{ editingMilestone?.id ? "Edit Milestone" : "Add New Milestone" }}
            </h3>
            <button
              @click="showMilestoneModal = false"
              class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800"
            >
              <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
          </div>

          <form @submit.prevent="saveMilestone" class="mt-4 space-y-4">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Year / Range</label>
                <input
                  v-model="milestoneForm.year"
                  required
                  type="text"
                  placeholder="e.g. 2024 or 22–23"
                  class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Icon</label>
                <div class="mt-1">
                  <IconPicker
                    v-model="milestoneForm.icon"
                    placeholder="Select icon..."
                  />
                </div>
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Milestone Title</label>
              <input
                v-model="milestoneForm.title"
                required
                type="text"
                placeholder="e.g. Scaling"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-400">Description</label>
              <textarea
                v-model="milestoneForm.description"
                rows="3"
                placeholder="Brief summary of milestone achievements"
                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"
              ></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button
                type="button"
                @click="showMilestoneModal = false"
                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSaving"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
              >
                <span v-if="isSaving" class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span>
                {{ editingMilestone?.id ? "Update" : "Create" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirmation Dialog -->
    <ConfirmDialog
      v-model="showDeleteDialog"
      title="Delete Milestone"
      :message="`Are you sure you want to delete the milestone '${milestoneToDelete?.title}' (${milestoneToDelete?.year})?`"
      confirm-text="Delete"
      :danger="true"
      icon="delete"
      @confirm="executeDeleteMilestone"
    />

    <!-- Global Toast Notification -->
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
import { ref, reactive, nextTick, onMounted, watch, computed } from "vue";
import { adminAPI } from "@/SIS-Dashboard/api";
import AdminPageHeader from "@/SIS-Dashboard/components/AdminPageHeader.vue";
import ConfirmDialog from "@/SIS-Dashboard/components/ConfirmDialog.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import IconPicker from "@/SIS-Dashboard/components/IconPicker.vue";
import { parseTimelineYear } from "@/api/normalizers.js";
import { useSiteStore } from "@/stores/useSiteStore";
import { useModalScrollLock } from "@/utils/scrollLock.js";

const siteStore = useSiteStore();
const isAboutInNavbar = ref(true);
const isUpdatingNavbar = ref(false);

async function loadNavbarStatus() {
  try {
    const header = await adminAPI.getHeader();
    if (header && Array.isArray(header.nav_links)) {
      const aboutLink = header.nav_links.find((l) => l.key === "about");
      if (aboutLink) {
        isAboutInNavbar.value = aboutLink.is_active !== false;
      }
    }
  } catch (err) {
    console.warn("Could not load header status:", err);
  }
}

async function toggleAboutInNavbar() {
  if (isUpdatingNavbar.value) return;
  isUpdatingNavbar.value = true;
  const newStatus = !isAboutInNavbar.value;
  try {
    const header = await adminAPI.getHeader();
    let navLinks = Array.isArray(header?.nav_links) ? [...header.nav_links] : [];

    const aboutIdx = navLinks.findIndex((l) => l.key === "about");
    if (aboutIdx >= 0) {
      navLinks[aboutIdx] = {
        ...navLinks[aboutIdx],
        is_active: newStatus,
      };
    } else {
      const homeIdx = navLinks.findIndex((l) => l.key === "home");
      const insertIdx = homeIdx >= 0 ? homeIdx + 1 : 1;
      navLinks.splice(insertIdx, 0, {
        id: 6,
        key: "about",
        label: "About Us",
        url: "/about",
        has_dropdown: false,
        is_active: newStatus,
      });
    }

    const payload = {
      button_text: header?.button_text || "Get in Touch",
      button_link: header?.button_link || "/contact",
      button_enabled: header?.button_enabled !== false,
      nav_links: navLinks.map((l) => ({
        id: l.id,
        key: l.key,
        label: l.label,
        url: l.url,
        has_dropdown: Boolean(l.has_dropdown),
        is_active: Boolean(l.is_active),
      })),
    };

    const res = await adminAPI.updateHeader(payload);
    if (res) {
      siteStore.header = res;
      siteStore.headerLoaded = true;
    }
    isAboutInNavbar.value = newStatus;
    notify(
      "Navbar Updated",
      `"About Us" link is now ${newStatus ? "visible in" : "hidden from"} the website navbar.`,
      "success"
    );
  } catch (err) {
    console.error("Failed to update navbar status:", err);
    notify("Update Failed", "Could not update navbar settings. Please try again.", "error");
  } finally {
    isUpdatingNavbar.value = false;
  }
}

// Tabs setup
const tabs = [
  { id: "hero", label: "Hero Section", icon: "stars" },
  { id: "vision", label: "Mission & Vision", icon: "visibility" },
  { id: "domains", label: "Empowered Domains", icon: "domain" },
  { id: "timeline", label: "Timeline & Milestones", icon: "timeline" },
  { id: "itot", label: "IT-OT Divide", icon: "sync_alt" },
  { id: "values", label: "Core Values", icon: "verified_user" },
];

const activeTab = ref("hero");
const isLoading = ref(false);
const isSaving = ref(false);
const error = ref("");

// Toast notification
const showToast = ref(false);
const toast = reactive({
  title: "",
  message: "",
  variant: "success",
});

function notify(title, message, variant = "success") {
  showToast.value = false;
  toast.title = title;
  toast.message = message;
  toast.variant = variant;
  nextTick(() => {
    showToast.value = true;
  });
}

// -------------------------------------------------------------
// TAB 1: HERO STATE
// -------------------------------------------------------------
const heroData = reactive({
  id: 1,
  tagline: "",
  headline: "",
  subheadline: "",
  cta_text: "",
  cta_link: "",
  image: "",
  image_url: "",
});
const heroImageFile = ref(null);
const heroImagePreview = ref("");

function handleHeroImageUpload(e) {
  const file = e.target.files[0];
  if (file) {
    heroImageFile.value = file;
    heroImagePreview.value = URL.createObjectURL(file);
  }
}

async function loadHero() {
  const res = await adminAPI.getAboutHero();
  const data = res?.data || res || {};
  heroData.id = data.id || 1;
  heroData.tagline = data.tagline || "";
  heroData.headline = data.headline || "";
  heroData.subheadline = data.subheadline || "";
  heroData.cta_text = data.cta_text || "";
  heroData.cta_link = data.cta_link || "";
  heroData.image = data.image || "";
  heroData.image_url = data.image_url || "";
  heroImageFile.value = null;
  heroImagePreview.value = "";
}

async function saveHero() {
  isSaving.value = true;
  try {
    let payload;
    if (heroImageFile.value) {
      payload = new FormData();
      payload.append("tagline", heroData.tagline || "");
      payload.append("headline", heroData.headline || "");
      payload.append("subheadline", heroData.subheadline || "");
      payload.append("cta_text", heroData.cta_text || "");
      payload.append("cta_link", heroData.cta_link || "");
      payload.append("image", heroImageFile.value);
    } else {
      payload = {
        tagline: heroData.tagline,
        headline: heroData.headline,
        subheadline: heroData.subheadline,
        cta_text: heroData.cta_text,
        cta_link: heroData.cta_link,
      };
    }
    await adminAPI.updateAboutHero(payload);
    notify("Updated", "About hero section saved successfully.");
    await loadHero();
  } catch (err) {
    notify("Error", err.message || "Failed to update hero section.", "error");
  } finally {
    isSaving.value = false;
  }
}

// -------------------------------------------------------------
// TAB 2: VISION STATE
// -------------------------------------------------------------
const visionData = reactive({
  id: 1,
  title: "",
  headline: "",
  subheadline: "",
  paragraphs: [],
  mission: { tagline: "", title: "", description: "", icon: "flag" },
  vision: { tagline: "", title: "", description: "", icon: "visibility" },
  why_choose_us: { title: "", description: "" },
  pillars: [],
  principles: [],
});

function addParagraph() {
  visionData.paragraphs.push("");
}
function removeParagraph(idx) {
  visionData.paragraphs.splice(idx, 1);
}

async function loadVision() {
  const res = await adminAPI.getAboutVision();
  const data = res?.data || res || {};
  visionData.id = data.id || 1;
  visionData.title = data.title || "";
  visionData.headline = data.headline || "";
  visionData.subheadline = data.subheadline || "";
  visionData.paragraphs = Array.isArray(data.paragraphs) ? [...data.paragraphs] : [];
  visionData.mission = {
    tagline: data.mission?.tagline || "",
    title: data.mission?.title || "",
    description: data.mission?.description || "",
    icon: data.mission?.icon || "flag",
  };
  visionData.vision = {
    tagline: data.vision?.tagline || "",
    title: data.vision?.title || "",
    description: data.vision?.description || "",
    icon: data.vision?.icon || "visibility",
  };
  visionData.why_choose_us = data.why_choose_us || { title: "", description: "" };
  visionData.pillars = Array.isArray(data.pillars) ? [...data.pillars] : [];
  visionData.principles = Array.isArray(data.principles) ? [...data.principles] : [];
}

async function saveVision() {
  isSaving.value = true;
  try {
    const payload = {
      title: visionData.title,
      headline: visionData.headline,
      subheadline: visionData.subheadline,
      paragraphs: visionData.paragraphs.filter((p) => p && p.trim() !== ""),
      mission: visionData.mission,
      vision: visionData.vision,
      why_choose_us: visionData.why_choose_us,
      pillars: visionData.pillars,
      principles: visionData.principles,
    };
    await adminAPI.updateAboutVision(payload);
    notify("Updated", "Mission & Vision section saved successfully.");
    await loadVision();
  } catch (err) {
    notify("Error", err.message || "Failed to update mission & vision.", "error");
  } finally {
    isSaving.value = false;
  }
}

// -------------------------------------------------------------
// TAB 3: DOMAINS STATE
// -------------------------------------------------------------
const domainsData = reactive({
  id: 1,
  tagline: "",
  headline: "",
  subheadline: "",
  domains: [],
});

function addDomain() {
  domainsData.domains.push({
    icon: "engineering",
    title: "New Domain",
    description: "",
  });
}
function removeDomain(idx) {
  domainsData.domains.splice(idx, 1);
}

async function loadDomains() {
  const res = await adminAPI.getAboutDomains();
  const data = res?.data || res || {};
  domainsData.id = data.id || 1;
  domainsData.tagline = data.tagline || "";
  domainsData.headline = data.headline || "";
  domainsData.subheadline = data.subheadline || "";
  domainsData.domains = Array.isArray(data.domains)
    ? data.domains.map((d) => ({
        icon: d.icon || "",
        title: d.title || "",
        description: d.description || "",
      }))
    : [];
}

async function saveDomains() {
  isSaving.value = true;
  try {
    const payload = {
      tagline: domainsData.tagline,
      headline: domainsData.headline,
      subheadline: domainsData.subheadline,
      domains: domainsData.domains,
    };
    await adminAPI.updateAboutDomains(payload);
    notify("Updated", "Empowered domains saved successfully.");
    await loadDomains();
  } catch (err) {
    notify("Error", err.message || "Failed to save domains.", "error");
  } finally {
    isSaving.value = false;
  }
}

// -------------------------------------------------------------
// TAB 4: TIMELINE STATE
// -------------------------------------------------------------
const timelineList = ref([]);
const timelineHeader = reactive({
  tagline: "Our journey",
  title: "A trajectory of",
  title_highlight: "growth.",
});
const isSavingHeader = ref(false);
const showMilestoneModal = ref(false);
useModalScrollLock(showMilestoneModal);
const editingMilestone = ref(null);
const milestoneForm = reactive({
  year: "",
  title: "",
  description: "",
  icon: "flag",
});
const showDeleteDialog = ref(false);
const milestoneToDelete = ref(null);

function sortTimeline(list) {
  if (!Array.isArray(list)) return [];
  return [...list].sort((a, b) => {
    const yA = parseTimelineYear(a?.year);
    const yB = parseTimelineYear(b?.year);
    if (yA !== yB) return yA - yB;
    return (a?.id || 0) - (b?.id || 0);
  });
}

async function loadTimelineHeader() {
  try {
    const res = await adminAPI.getTimelineSection();
    const data = res?.data || res || {};
    if (data && typeof data === "object") {
      timelineHeader.tagline = data.tagline ?? "Our journey";
      timelineHeader.title = data.title ?? "A trajectory of";
      timelineHeader.title_highlight = data.title_highlight ?? "growth.";
    }
  } catch (err) {
    console.warn("Unable to load timeline section header:", err);
  }
}

async function saveTimelineHeader() {
  isSavingHeader.value = true;
  try {
    await adminAPI.updateTimelineSection({
      tagline: timelineHeader.tagline,
      title: timelineHeader.title,
      title_highlight: timelineHeader.title_highlight,
    });
    notify("Updated", "Timeline section header saved successfully.");
    await loadTimelineHeader();
  } catch (err) {
    notify("Error", err.message || "Failed to update timeline section header.", "error");
  } finally {
    isSavingHeader.value = false;
  }
}

async function loadTimeline() {
  await Promise.allSettled([
    (async () => {
      const res = await adminAPI.getTimelineEvents();
      const rawList = Array.isArray(res?.data) ? res.data : (Array.isArray(res) ? res : []);
      timelineList.value = sortTimeline(rawList);
    })(),
    loadTimelineHeader(),
  ]);
}

function openMilestoneModal(item = null) {
  editingMilestone.value = item;
  if (item) {
    milestoneForm.year = item.year || "";
    milestoneForm.title = item.title || "";
    milestoneForm.description = item.description || "";
    milestoneForm.icon = item.icon || "flag";
  } else {
    milestoneForm.year = "";
    milestoneForm.title = "";
    milestoneForm.description = "";
    milestoneForm.icon = "flag";
  }
  showMilestoneModal.value = true;
}

async function saveMilestone() {
  isSaving.value = true;
  try {
    const data = {
      year: milestoneForm.year,
      title: milestoneForm.title,
      description: milestoneForm.description,
      icon: milestoneForm.icon,
    };
    if (editingMilestone.value?.id) {
      await adminAPI.updateTimelineEvent(editingMilestone.value.id, data);
      notify("Updated", "Milestone updated successfully.");
    } else {
      await adminAPI.createTimelineEvent(data);
      notify("Created", "Milestone created successfully.");
    }
    showMilestoneModal.value = false;
    await loadTimeline();
  } catch (err) {
    notify("Error", err.message || "Failed to save milestone.", "error");
  } finally {
    isSaving.value = false;
  }
}

function promptDeleteMilestone(item) {
  milestoneToDelete.value = item;
  showDeleteDialog.value = true;
}

async function executeDeleteMilestone() {
  if (!milestoneToDelete.value?.id) return;
  try {
    await adminAPI.deleteTimelineEvent(milestoneToDelete.value.id);
    notify("Deleted", "Milestone removed successfully.");
    await loadTimeline();
  } catch (err) {
    notify("Error", err.message || "Failed to delete milestone.", "error");
  }
}

// -------------------------------------------------------------
// TAB 5: IT-OT STATE
// -------------------------------------------------------------
const itotData = reactive({
  id: 1,
  tagline: "",
  headline: "",
  subheadline: "",
  ot: { title: "", description: "", icon: "precision_manufacturing", tags: [] },
  it: { title: "", description: "", icon: "cloud", tags: [] },
});

const otTagsInput = ref("");
const itTagsInput = ref("");

const parsedOtTags = computed(() =>
  otTagsInput.value
    .split(",")
    .map((t) => t.trim())
    .filter(Boolean)
);

const parsedItTags = computed(() =>
  itTagsInput.value
    .split(",")
    .map((t) => t.trim())
    .filter(Boolean)
);

async function loadItOt() {
  const res = await adminAPI.getAboutItOt();
  const data = res?.data || res || {};
  itotData.id = data.id || 1;
  itotData.tagline = data.tagline || "";
  itotData.headline = data.headline || "";
  itotData.subheadline = data.subheadline || "";
  const ot = data.ot || data.ot_data || {};
  const it = data.it || data.it_data || {};
  itotData.ot = {
    title: ot.title || "",
    description: ot.description || "",
    icon: ot.icon || "precision_manufacturing",
    tags: Array.isArray(ot.tags) ? [...ot.tags] : [],
  };
  itotData.it = {
    title: it.title || "",
    description: it.description || "",
    icon: it.icon || "cloud",
    tags: Array.isArray(it.tags) ? [...it.tags] : [],
  };

  otTagsInput.value = (itotData.ot.tags || []).join(", ");
  itTagsInput.value = (itotData.it.tags || []).join(", ");
}

async function saveItOt() {
  isSaving.value = true;
  try {
    const payload = {
      tagline: itotData.tagline,
      headline: itotData.headline,
      subheadline: itotData.subheadline,
      ot: {
        ...itotData.ot,
        tags: parsedOtTags.value,
      },
      it: {
        ...itotData.it,
        tags: parsedItTags.value,
      },
    };
    await adminAPI.updateAboutItOt(payload);
    notify("Updated", "IT-OT convergence section saved successfully.");
    await loadItOt();
  } catch (err) {
    notify("Error", err.message || "Failed to save IT-OT section.", "error");
  } finally {
    isSaving.value = false;
  }
}

// -------------------------------------------------------------
// TAB 6: CORE VALUES STATE
// -------------------------------------------------------------
const coreData = reactive({
  id: 1,
  tagline: "",
  headline: "",
  subheadline: "",
  principles: [],
});

function addPrinciple() {
  const nextStep = String(coreData.principles.length + 1).padStart(2, "0");
  coreData.principles.push({
    step: nextStep,
    title: "New Value",
    description: "",
    icon: "verified_user",
  });
}
function removePrinciple(idx) {
  coreData.principles.splice(idx, 1);
}

async function loadCoreValues() {
  const res = await adminAPI.getAboutCore();
  const data = res?.data || res || {};
  coreData.id = data.id || 1;
  coreData.tagline = data.tagline || "";
  coreData.headline = data.headline || "";
  coreData.subheadline = data.subheadline || "";
  coreData.principles = Array.isArray(data.principles)
    ? data.principles.map((p) => ({
        step: p.step || "",
        title: p.title || "",
        description: p.description || "",
        icon: p.icon || "verified",
      }))
    : [];
}

async function saveCoreValues() {
  isSaving.value = true;
  try {
    const payload = {
      tagline: coreData.tagline,
      headline: coreData.headline,
      subheadline: coreData.subheadline,
      principles: coreData.principles,
    };
    await adminAPI.updateAboutCore(payload);
    notify("Updated", "Core values saved successfully.");
    await loadCoreValues();
  } catch (err) {
    notify("Error", err.message || "Failed to save core values.", "error");
  } finally {
    isSaving.value = false;
  }
}

// -------------------------------------------------------------
// LIFECYCLE & TAB SWITCHING
// -------------------------------------------------------------
async function loadCurrentTab() {
  isLoading.value = true;
  error.value = "";
  try {
    if (activeTab.value === "hero") await loadHero();
    else if (activeTab.value === "vision") await loadVision();
    else if (activeTab.value === "domains") await loadDomains();
    else if (activeTab.value === "timeline") await loadTimeline();
    else if (activeTab.value === "itot") await loadItOt();
    else if (activeTab.value === "values") await loadCoreValues();
  } catch (err) {
    error.value = err.message || "Failed to load tab data.";
  } finally {
    isLoading.value = false;
  }
}

watch(activeTab, () => {
  loadCurrentTab();
});

onMounted(() => {
  loadCurrentTab();
  loadNavbarStatus();
});
</script>
