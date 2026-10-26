<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import { RouterLink, RouterView } from "vue-router";
import { onClickOutside } from "@vueuse/core";
import { useSessionManager } from "@/SIS-Dashboard/composables/useSessionManager.js";

const route = useRoute();
const router = useRouter();
const showUserMenu = ref(false);
const mobileNavOpen = ref(false);
const userMenuRef = ref(null);

onClickOutside(userMenuRef, () => {
  showUserMenu.value = false;
});

function handleKeydown(e) {
  if (e.key === "Escape") {
    showUserMenu.value = false;
    mobileNavOpen.value = false;
  }
}

const { formattedRemainingTime, startSessionTimer, logout } = useSessionManager();

onMounted(() => {
  startSessionTimer();
  window.addEventListener("keydown", handleKeydown);
});

onBeforeUnmount(() => {
  window.removeEventListener("keydown", handleKeydown);
});

const pageTitle = computed(() => route.meta?.title || "Dashboard");
const sisLogoUrl = `${import.meta.env.BASE_URL}images/sis-panel-img.jpeg`;

const adminUser = computed(() => {
  const user = localStorage.getItem("admin_user");
  return user ? JSON.parse(user) : null;
});

function handleLogout() {
  showUserMenu.value = false;
  logout();
}

const expandedMenus = ref({
  "Home Page": true,
});

function toggleMenu(label) {
  expandedMenus.value[label] = !expandedMenus.value[label];
}

function isMenuOpen(item) {
  if (isChildActive(item)) return true;
  return Boolean(expandedMenus.value[item.label]);
}

function isChildActive(item) {
  if (!item.children) return false;
  return item.children.some((child) => isActive(child));
}

const navGroups = [
  {
    label: "Main",
    items: [
      {
        label: "Overview",
        icon: "dashboard",
        to: { name: "admin-home" },
        soon: false,
      },
    ],
  },

  {
    label: "Website Pages",
    items: [
      {
        label: "Home Page",
        icon: "home",
        children: [
          {
            label: "Hero Section",
            icon: "view_carousel",
            to: { name: "admin-hero" },
          },
          {
            label: "Why SiS Banner",
            icon: "lightbulb",
            to: {
              name: "admin-section",
              params: { resource: "additional" },
            },
          },
          {
            label: "Services Showcase",
            icon: "handyman",
            to: { name: "admin-services" },
          },
          {
            label: "Clients & Partners",
            icon: "groups",
            to: { name: "admin-clients" },
          },
        ],
      },
      {
        label: "About Us",
        icon: "info",
        to: { name: "admin-about" },
        soon: false,
      },
      {
        label: "Services & Solutions",
        icon: "engineering",
        to: { name: "admin-solution-pages" },
        soon: false,
      },
      {
        label: "Industries",
        icon: "factory",
        to: { name: "admin-sectors" },
        soon: false,
      },
      {
        label: "Partner Ecosystem",
        icon: "handshake",
        to: { name: "admin-partners" },
        soon: false,
      },
      {
        label: "Use Cases",
        icon: "folder_special",
        to: { name: "admin-projects" },
        soon: false,
      },
      {
        label: "Contact Page",
        icon: "contact_page",
        to: { name: "admin-contact-page" },
        soon: false,
      },
    ],
  },

  {
    label: "Inquiries & Leads",
    items: [
      {
        label: "Contact Messages",
        icon: "mark_email_unread",
        to: { name: "admin-contact" },
        soon: false,
      },
    ],
  },

  {
    label: "Settings & Global Layout",
    items: [
      {
        label: "Header & Navigation",
        icon: "web",
        to: { name: "admin-header" },
        soon: false,
      },
      {
        label: "Footer & Socials",
        icon: "call_to_action",
        to: { name: "admin-footer" },
        soon: false,
      },
      {
        label: "Global SEO",
        icon: "travel_explore",
        to: { name: "admin-seo-settings" },
        soon: false,
      },
      {
        label: "Settings",
        icon: "settings",
        to: { name: "admin-settings" },
        soon: false,
      },
    ],
  },
];

const isActive = (item) => {
  if (item.soon || !item.to) return false;

  if (route.name !== item.to.name) return false;

  if (item.to.params) {
    return Object.entries(item.to.params).every(
      ([k, v]) => String(route.params[k]) === String(v),
    );
  }

  return true;
};
</script>

<template>
  <div class="relative min-h-screen bg-[#F8FAFC] text-slate-800">
    <!-- Ambient background glows -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
      <div class="absolute -top-40 right-0 h-96 w-96 rounded-full bg-blue-500/[0.03] blur-3xl"></div>
      <div class="absolute top-1/2 left-72 h-96 w-96 rounded-full bg-indigo-500/[0.02] blur-3xl"></div>
    </div>

    <!-- Mobile navigation backdrop -->
    <button
      v-if="mobileNavOpen"
      type="button"
      aria-label="Close navigation"
      class="fixed inset-0 z-30 bg-slate-950/50 backdrop-blur-sm lg:hidden"
      @click="mobileNavOpen = false"
    ></button>

    <!-- Sidebar -->
    <aside
      class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-slate-800/60 bg-gradient-to-b from-[#0A192F] via-[#0E2445] to-[#0A162B] text-white shadow-2xl transition-transform duration-300 lg:translate-x-0"
      :class="mobileNavOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <!-- Brand -->
      <div class="px-5 pb-5 pt-6">
        <div
          class="flex items-center gap-3.5 rounded-2xl border border-white/10 bg-white/[0.05] p-3.5 shadow-[0_4px_20px_rgba(0,0,0,0.15)] backdrop-blur-md"
        >
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white p-1 shadow-md ring-2 ring-white/10"
          >
            <img
              :src="sisLogoUrl"
              alt="SiS Logo"
              class="h-8 w-8 object-contain"
            />
          </div>

          <div class="min-w-0">
            <p
              class="truncate font-sans text-[15px] font-bold tracking-tight text-white"
            >
              SiS Dashboard
            </p>

            <p
              class="mt-0.5 truncate text-[10px] font-semibold uppercase tracking-[0.14em] text-blue-300/70"
            >
              Superior Integrated
            </p>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="mx-5 border-t border-white/[0.08]"></div>

      <!-- Nav -->
      <nav class="flex-1 overflow-y-auto px-4 py-6">
        <div
          v-for="group in navGroups"
          :key="group.label"
          class="mb-7 last:mb-0"
        >
          <p
            class="mb-2.5 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-blue-200/40"
          >
            {{ group.label }}
          </p>

          <ul class="space-y-1.5">
            <li v-for="item in group.items" :key="item.label">
              <!-- Item with Children (Collapsible Submenu) -->
              <div v-if="item.children" class="space-y-1">
                <button
                  type="button"
                  @click="toggleMenu(item.label)"
                  class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
                  :class="[
                    isChildActive(item)
                      ? 'bg-white/[0.08] text-white font-semibold'
                      : 'text-slate-300/70 hover:bg-white/[0.06] hover:text-white',
                  ]"
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <span
                      class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition-all duration-200"
                      :class="[
                        isChildActive(item)
                          ? 'bg-blue-600/30 text-blue-400 border border-blue-500/30'
                          : 'bg-white/[0.04] text-slate-400 group-hover:bg-white/[0.08] group-hover:text-white',
                      ]"
                    >
                      <span class="material-symbols-outlined text-[19px]">
                        {{ item.icon }}
                      </span>
                    </span>
                    <span class="truncate">{{ item.label }}</span>
                  </div>

                  <span
                    class="material-symbols-outlined text-[18px] text-slate-400 transition-transform duration-200"
                    :class="{ 'rotate-180 text-white': isMenuOpen(item) }"
                  >
                    expand_more
                  </span>
                </button>

                <!-- Sub-items with connecting vertical line -->
                <transition
                  enter-active-class="transition duration-150 ease-out"
                  enter-from-class="opacity-0 -translate-y-1"
                  enter-to-class="opacity-100 translate-y-0"
                  leave-active-class="transition duration-100 ease-in"
                  leave-from-class="opacity-100 translate-y-0"
                  leave-to-class="opacity-0 -translate-y-1"
                >
                  <ul
                    v-if="isMenuOpen(item)"
                    class="ml-4 pl-3.5 border-l border-white/10 space-y-1 my-1"
                  >
                    <li v-for="child in item.children" :key="child.label">
                      <RouterLink
                        :to="child.to"
                        @click="mobileNavOpen = false"
                        class="group relative flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs font-medium transition-all duration-150"
                        :class="[
                          isActive(child)
                            ? 'bg-blue-600 text-white shadow-sm font-semibold'
                            : 'text-slate-400 hover:bg-white/[0.06] hover:text-slate-200',
                        ]"
                      >
                        <span
                          class="material-symbols-outlined text-[16px] shrink-0"
                          :class="isActive(child) ? 'text-white' : 'text-slate-400 group-hover:text-slate-200'"
                        >
                          {{ child.icon }}
                        </span>
                        <span class="truncate">{{ child.label }}</span>
                      </RouterLink>
                    </li>
                  </ul>
                </transition>
              </div>

              <!-- Standard Item (No Children) -->
              <RouterLink
                v-else-if="!item.soon && item.to"
                :to="item.to"
                @click="mobileNavOpen = false"
                class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200"
                :class="
                  isActive(item)
                    ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-[0_4px_16px_rgba(37,99,235,0.35)]'
                    : 'text-slate-300/70 hover:bg-white/[0.07] hover:text-white'
                "
              >
                <!-- Active Indicator Glow -->
                <span
                  v-if="isActive(item)"
                  class="absolute -left-1 top-1/2 h-5 w-1 -translate-y-1/2 rounded-full bg-cyan-300 shadow-[0_0_10px_rgba(103,232,249,0.8)]"
                ></span>

                <!-- Icon -->
                <span
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition-all duration-200"
                  :class="
                    isActive(item)
                      ? 'bg-white/20 text-white shadow-sm'
                      : 'bg-white/[0.04] text-slate-400 group-hover:bg-white/[0.08] group-hover:text-white'
                  "
                >
                  <span class="material-symbols-outlined text-[19px]">
                    {{ item.icon }}
                  </span>
                </span>

                <span class="truncate">
                  {{ item.label }}
                </span>
              </RouterLink>

              <!-- Coming Soon -->
              <button
                v-else
                type="button"
                disabled
                class="group flex w-full cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2 text-sm text-slate-500"
                title="Coming soon"
              >
                <span
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/[0.02] text-slate-600"
                >
                  <span class="material-symbols-outlined text-[19px]">
                    {{ item.icon }}
                  </span>
                </span>

                <span class="truncate">
                  {{ item.label }}
                </span>

                <span
                  class="ml-auto rounded-md border border-white/[0.08] bg-white/[0.03] px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-slate-500"
                >
                  Soon
                </span>
              </button>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Sidebar Footer -->
      <div class="border-t border-white/[0.08] p-4">
        <a
          href="/"
          target="_blank"
          rel="noopener"
          class="group flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-4 py-3 text-sm font-medium text-slate-300 transition-all duration-200 hover:border-blue-400/40 hover:bg-white/[0.08] hover:text-white hover:shadow-[0_4px_16px_rgba(0,0,0,0.2)]"
        >
          <span class="flex items-center gap-3">
            <span
              class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/10 text-blue-400 transition-colors group-hover:bg-blue-500/20 group-hover:text-blue-300"
            >
              <span class="material-symbols-outlined text-[18px]">
                language
              </span>
            </span>

            <span>View Website</span>
          </span>

          <span
            class="material-symbols-outlined text-[17px] text-slate-500 transition-transform duration-200 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 group-hover:text-blue-300"
          >
            open_in_new
          </span>
        </a>
      </div>
    </aside>

    <!-- Main column -->
    <div class="lg:pl-72">
      <!-- Topbar -->
      <header
        class="sticky top-0 z-30 flex h-[73px] items-center justify-between border-b border-slate-200/80 bg-white/85 px-4 backdrop-blur-xl sm:px-6 lg:px-8 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.03)]"
      >
        <!-- Page Title -->
        <div class="flex min-w-0 items-center gap-3">
          <button
            type="button"
            aria-label="Open navigation"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition lg:hidden"
            @click="mobileNavOpen = true"
          >
            <span class="material-symbols-outlined text-[21px]">menu</span>
          </button>

          <div class="h-6 w-1 rounded-full bg-gradient-to-b from-blue-600 to-indigo-600 shadow-[0_0_8px_rgba(37,99,235,0.4)]"></div>

          <div class="min-w-0">
            <h1
              class="truncate font-sans text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl"
            >
              {{ pageTitle }}
            </h1>

            <p class="hidden text-[11px] font-medium text-slate-400 sm:block">
              Superior Integrated Solutions · Management Portal
            </p>
          </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-3">
          <!-- Session Timer Badge -->
          <div
            class="hidden items-center gap-2 rounded-full border border-slate-200/90 bg-slate-50/90 px-3.5 py-1.5 text-xs text-slate-600 shadow-sm backdrop-blur-md sm:flex"
            :title="`Session duration: 60 minutes. Remaining: ${formattedRemainingTime}`"
          >
            <span class="material-symbols-outlined text-[16px] text-blue-600">
              schedule
            </span>
            <span class="text-[11px] font-medium text-slate-500">Session:</span>
            <span class="font-mono text-xs font-bold text-slate-800">
              {{ formattedRemainingTime }}
            </span>
          </div>

          <!-- Admin Badge -->
          <div
            class="hidden items-center gap-2 rounded-full border border-blue-500/20 bg-blue-50/80 px-3.5 py-1.5 shadow-[0_1px_3px_rgba(37,99,235,0.08)] sm:flex"
          >
            <span class="relative flex h-2 w-2">
              <span
                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-500 opacity-60"
              ></span>

              <span
                class="relative inline-flex h-2 w-2 rounded-full bg-blue-600"
              ></span>
            </span>

            <span
              class="text-[11px] font-bold uppercase tracking-wider text-blue-700"
            >
              Admin
            </span>
          </div>

          <!-- Separator -->
          <div class="hidden h-7 w-px bg-slate-200 sm:block"></div>

          <!-- User Menu -->
          <div ref="userMenuRef" class="relative">
            <button
              type="button"
              @click="showUserMenu = !showUserMenu"
              class="group flex items-center gap-2.5 rounded-2xl p-1.5 pr-2.5 transition-all hover:bg-slate-100/80"
            >
              <div
                class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-md shadow-blue-600/20 ring-2 ring-white"
              >
                <span class="material-symbols-outlined text-[19px]">
                  person
                </span>
              </div>

              <div class="hidden text-left sm:block">
                <p
                  class="max-w-[130px] truncate text-sm font-bold text-slate-800"
                >
                  {{ adminUser?.name || "Admin" }}
                </p>

                <p class="text-[10px] font-medium text-slate-400">
                  Administrator
                </p>
              </div>

              <span
                class="material-symbols-outlined hidden text-[17px] text-slate-400 transition-transform duration-200 sm:block"
                :class="{ 'rotate-180 text-blue-600': showUserMenu }"
              >
                expand_more
              </span>
            </button>

            <!-- Dropdown -->
            <transition
              enter-active-class="transition duration-150 ease-out"
              enter-from-class="transform scale-95 opacity-0"
              enter-to-class="transform scale-100 opacity-100"
              leave-active-class="transition duration-100 ease-in"
              leave-from-class="transform scale-100 opacity-100"
              leave-to-class="transform scale-95 opacity-0"
            >
              <div
                v-if="showUserMenu"
                class="absolute right-0 top-full z-[100] mt-3 w-64 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-[0_20px_50px_rgba(15,23,42,0.15)]"
              >
                <div class="border-b border-slate-100 bg-gradient-to-br from-slate-50 to-blue-50/30 px-4 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-sm"
                    >
                      <span class="material-symbols-outlined text-[20px]">
                        person
                      </span>
                    </div>

                    <div class="min-w-0">
                      <p class="truncate text-sm font-bold text-slate-900">
                        {{ adminUser?.name }}
                      </p>

                      <p class="mt-0.5 truncate text-xs text-slate-500">
                        {{ adminUser?.email }}
                      </p>
                    </div>
                  </div>
                </div>

                <div class="border-b border-slate-100 bg-slate-50/40 px-4 py-3 text-xs">
                  <div class="flex items-center justify-between text-slate-500">
                    <span>Session Period:</span>
                    <span class="font-semibold text-slate-700">60 minutes</span>
                  </div>
                  <div class="mt-1 flex items-center justify-between">
                    <span class="text-slate-500">Auto logout in:</span>
                    <span class="font-mono font-bold text-blue-600">{{ formattedRemainingTime }}</span>
                  </div>
                </div>

                <div class="p-2">
                  <button
                    type="button"
                    @click="handleLogout"
                    class="flex w-full cursor-pointer items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-50 active:bg-rose-100"
                  >
                    <span
                      class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600"
                    >
                      <span class="material-symbols-outlined text-[18px]">
                        logout
                      </span>
                    </span>

                    <span>Sign out</span>
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main
        class="sis-admin-main min-h-[calc(100vh-73px)] px-4 py-6 sm:px-6 sm:py-8 lg:px-8"
      >
        <RouterView />
      </main>
    </div>
  </div>
</template>
