<script setup>
import { computed, onMounted } from "vue";
import { storeToRefs } from "pinia";
import { useAdminStore } from "@/SIS-Dashboard/stores/useAdminStore.js";
import { isMock } from "@/SIS-Dashboard/api/index.js";
import { useRouter } from "vue-router";

const admin = useAdminStore();
const router = useRouter();
const { overview, overviewLoading, overviewLoaded } = storeToRefs(admin);

const statCards = computed(() => [
  {
    icon: "folder_special",
    label: "Use Cases",
    path: "/dashboard/use-cases",
    value: overview.value.projects,
  },
  {
    icon: "factory",
    label: "Industries",
    path: "/dashboard/collection/industries",
    value: overview.value.sectors,
  },
  {
    icon: "handyman",
    label: "Services",
    path: "/dashboard/collection/services",
    value: overview.value.services,
  },
  {
    icon: "handshake",
    label: "Technology Partners",
    path: "/dashboard/collection/partners",
    value: overview.value.partners,
  },
  {
    icon: "groups",
    label: "Clients",
    path: "/dashboard/collection/clients",
    value: overview.value.clients,
  },
  {
    icon: "engineering",
    label: "Services & Solutions",
    path: "/dashboard/solution-pages",
    value: overview.value.solutionPages,
  },
  {
    icon: "mark_email_unread",
    label: "Messages",
    path: "/dashboard/contact",
    value: overview.value.contactTotal,
    badge: overview.value.contactUnread,
  },
]);

const planned = [
  {
    icon: "tune",
    title: "Content Sections",
    description: "Edit every site section (hero, about, services, sectors, partners, clients, timeline, ...) with a live preview.",
  },
  {
    icon: "folder",
    title: "Projects Manager",
    description: "Create, edit and delete the projects shown on the site — wired to the real backend CRUD.",
  },
  {
    icon: "mark_email_unread",
    title: "Contact Replies",
    description: "Reply to website inquiries — replies are emailed back to the user's inbox.",
  },
];

onMounted(() => {
  if (!overviewLoaded.value) admin.loadOverview();
});
</script>

<template>
  <div class="space-y-8">

    <!-- Welcome Hero Banner -->
    <section
      class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-[#0B1E3D] via-[#102B59] to-[#08152B] p-7 text-white shadow-[0_20px_60px_-15px_rgba(11,30,61,0.3)] sm:p-10"
    >
      <!-- Ambient Spotlights -->
      <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -right-20 -top-24 h-80 w-80 rounded-full bg-blue-500/20 blur-[100px]"></div>
        <div class="absolute -bottom-32 left-1/4 h-72 w-72 rounded-full bg-cyan-400/15 blur-[90px]"></div>
        <div class="absolute right-12 top-10 h-44 w-44 rounded-full border border-white/[0.04]"></div>
        <div class="absolute right-24 top-20 h-24 w-24 rounded-full border border-white/[0.04]"></div>
      </div>

      <div class="relative flex flex-wrap items-start justify-between gap-8">
        <div class="max-w-3xl">
          <!-- Platform Badge -->
          <div
            class="mb-5 inline-flex items-center gap-2 rounded-full border border-blue-400/20 bg-blue-500/10 px-3.5 py-1.5 backdrop-blur-md"
          >
            <span class="h-2 w-2 rounded-full bg-cyan-300 shadow-[0_0_8px_rgba(103,232,249,0.8)]"></span>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-blue-200">
              SiS · Admin Operations Center
            </p>
          </div>

          <!-- Headline -->
          <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl text-white">
            Superior control,
            <span class="bg-gradient-to-r from-blue-300 via-sky-200 to-indigo-200 bg-clip-text text-transparent">
              all in one place.
            </span>
          </h2>

          <p class="mt-3.5 max-w-2xl text-sm leading-relaxed text-slate-300/80 sm:text-base">
            Real-time synchronization with your public website. Manage content, monitor incoming inquiries, and update system modules seamlessly.
          </p>

          <!-- Quick Feature Badges -->
          <div class="mt-7 flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/[0.05] px-3.5 py-2 backdrop-blur-md">
              <span class="material-symbols-outlined text-[18px] text-cyan-300">
                dashboard_customize
              </span>
              <span class="text-xs font-semibold text-slate-200">
                Content Operations
              </span>
            </div>

            <div class="flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/[0.05] px-3.5 py-2 backdrop-blur-md">
              <span class="material-symbols-outlined text-[18px] text-cyan-300">
                sync_alt
              </span>
              <span class="text-xs font-semibold text-slate-200">
                Live Data Feed
              </span>
            </div>
          </div>
        </div>

        <!-- Backend Status Badge -->
        <div
          class="flex shrink-0 items-center gap-2.5 rounded-full border border-white/10 bg-white/[0.06] px-4 py-2.5 shadow-inner backdrop-blur-md"
          :title="isMock ? 'Mock data mode active' : 'Live backend connected'"
        >
          <span class="relative flex h-2.5 w-2.5">
            <span
              class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-60"
              :class="isMock ? 'bg-amber-400' : 'bg-emerald-400'"
            ></span>
            <span
              class="relative inline-flex h-2.5 w-2.5 rounded-full"
              :class="isMock ? 'bg-amber-400' : 'bg-emerald-400'"
            ></span>
          </span>

          <span class="text-xs font-bold uppercase tracking-wider text-slate-200">
            {{ isMock ? "Mock data" : "Live Backend" }}
          </span>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section>
      <div class="mb-5 flex items-end justify-between">
        <div>
          <div class="flex items-center gap-2">
            <span class="h-4 w-1 rounded-full bg-blue-600"></span>
            <p class="text-xs font-extrabold uppercase tracking-wider text-blue-600">
              Overview Metrics
            </p>
          </div>
          <h3 class="mt-1 text-xl font-bold tracking-tight text-slate-900">
            Platform Statistics
          </h3>
        </div>

        <span class="hidden text-xs font-medium text-slate-400 sm:block">
          Click any card to manage section
        </span>
      </div>

      <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Skeleton Loading -->
        <template v-if="overviewLoading && !overviewLoaded">
          <div
            v-for="n in statCards.length"
            :key="`skeleton-${n}`"
            class="animate-pulse rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm"
          >
            <div class="flex items-center justify-between">
              <div class="h-12 w-12 rounded-2xl bg-slate-100"></div>
              <div class="h-5 w-16 rounded-full bg-slate-100"></div>
            </div>
            <div class="mt-6 h-8 w-24 rounded-lg bg-slate-100"></div>
            <div class="mt-2.5 h-4 w-32 rounded-lg bg-slate-100"></div>
          </div>
        </template>

        <!-- Dynamic Stat Cards -->
        <template v-else>
          <div
            v-for="card in statCards"
            :key="card.label"
            role="button"
            tabindex="0"
            :title="`Open ${card.label}`"
            @click="router.push(card.path)"
            @keydown.enter="router.push(card.path)"
            @keydown.space.prevent="router.push(card.path)"
            class="group relative cursor-pointer overflow-hidden rounded-2xl border border-slate-200/90 bg-white/90 p-5 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.04)] backdrop-blur-sm transition-all duration-300 hover:-translate-y-1.5 hover:border-blue-400/50 hover:shadow-[0_20px_35px_-10px_rgba(37,99,235,0.15)] focus:outline-none focus:ring-4 focus:ring-blue-500/20"
          >
            <!-- Glowing accent gradient top bar on hover -->
            <div
              class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-400 opacity-0 transition-opacity duration-300 group-hover:opacity-100"
            ></div>

            <!-- Corner ambient glow -->
            <div
              class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-blue-500/0 blur-xl transition-all duration-500 group-hover:bg-blue-500/10"
            ></div>

            <div class="flex items-start justify-between gap-3">
              <!-- Icon Container -->
              <div
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-50 to-indigo-50/80 text-blue-600 shadow-sm transition-all duration-300 group-hover:scale-105 group-hover:border-blue-600 group-hover:bg-gradient-to-br group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white group-hover:shadow-md group-hover:shadow-blue-500/30"
              >
                <span class="material-symbols-outlined text-[24px]">
                  {{ card.icon }}
                </span>
              </div>

              <!-- Unread Badge if applicable -->
              <div
                v-if="card.badge"
                class="inline-flex items-center gap-1 rounded-full bg-rose-50 border border-rose-200/80 px-2.5 py-1 text-[11px] font-bold text-rose-600 shadow-sm"
              >
                <span class="h-1.5 w-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                <span>{{ card.badge }} unread</span>
              </div>
            </div>

            <!-- Metric Value and Label -->
            <div class="mt-4">
              <p class="font-sans text-3xl font-extrabold tracking-tight text-slate-900 transition-colors group-hover:text-blue-600">
                {{ card.value }}
              </p>

              <p class="mt-1 truncate text-xs font-bold uppercase tracking-wider text-slate-500">
                {{ card.label }}
              </p>
            </div>

            <!-- Bottom Manage Link -->
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs font-semibold text-slate-400 transition-colors group-hover:text-blue-600">
              <span>Manage</span>

              <span
                class="material-symbols-outlined text-[17px] transition-transform duration-200 group-hover:translate-x-1"
              >
                arrow_forward
              </span>
            </div>
          </div>
        </template>
      </div>
    </section>




  </div>
</template>