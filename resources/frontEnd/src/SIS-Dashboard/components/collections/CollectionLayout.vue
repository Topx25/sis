<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },

  icon: {
    type: String,
    default: "folder",
  },

  description: {
    type: String,
    default: "",
  },

  search: {
    type: String,
    default: "",
  },

  count: {
    type: Number,
    default: 0,
  },

  loading: {
    type: Boolean,
    default: false,
  },

  canCreate: {
    type: Boolean,
    default: false,
  },

  singular: {
    type: String,
    default: "Item",
  },

  pages: {
    type: Number,
    default: 1,
  },

  page: {
    type: Number,
    default: 1,
  },
});

defineEmits([
  "update:search",
  "create",
  "previous",
  "next",
]);
</script>

<template>
  <div class="mx-auto max-w-7xl space-y-6 font-sans text-slate-800">
    <!-- Header Section -->
    <div class="relative">
      <!-- Ambient lighting -->
      <div class="pointer-events-none absolute -left-6 -top-6 h-28 w-28 rounded-full bg-blue-500/5 blur-2xl"></div>

      <div
        class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
      >
        <div class="flex items-center gap-3.5">
          <div
            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50/80 text-blue-600 border border-blue-500/20 shadow-sm"
          >
            <span class="material-symbols-outlined text-[24px]">
              {{ icon }}
            </span>
          </div>

          <div>
            <h1
              class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl"
            >
              {{ title }}
            </h1>

            <p
              v-if="description"
              class="mt-1 text-sm text-slate-500"
            >
              {{ description }}
            </p>
          </div>
        </div>

        <button
          v-if="canCreate"
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/20 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-500/30 active:translate-y-0"
          @click="$emit('create')"
        >
          <span class="material-symbols-outlined text-[19px]">
            add
          </span>

          New {{ singular }}
        </button>
      </div>
    </div>

    <!-- Search / Filter Bar -->
    <div
      class="flex flex-col gap-3 rounded-2xl border border-slate-200/90 bg-white/90 p-4 shadow-sm backdrop-blur-sm sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="relative w-full sm:max-w-md">
        <span
          class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400"
        >
          search
        </span>

        <input
          :value="search"
          type="search"
          :placeholder="`Search ${title.toLowerCase()}...`"
          class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50/60 pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
          @input="
            $emit(
              'update:search',
              $event.target.value,
            )
          "
        />
      </div>

      <div
        class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-500"
      >
        <span class="rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-800">
          {{ count }}
        </span>

        <span>{{ count === 1 ? "item" : "items" }}</span>
      </div>
    </div>

    <!-- Loading Skeleton Grid -->
    <div
      v-if="loading"
      class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
    >
      <div
        v-for="n in 6"
        :key="n"
        class="animate-pulse rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm"
      >
        <div
          class="mb-4 h-11 w-11 rounded-xl bg-slate-100"
        />

        <div
          class="mb-2.5 h-4 w-2/3 rounded-lg bg-slate-100"
        />

        <div
          class="mb-2 h-3.5 w-full rounded-lg bg-slate-100"
        />

        <div
          class="h-3.5 w-4/5 rounded-lg bg-slate-100"
        />
      </div>
    </div>

    <!-- Main Content Slot -->
    <slot v-else />

    <!-- Pagination -->
    <div
      v-if="!loading && pages > 1"
      class="mt-6 flex items-center justify-between rounded-2xl border border-slate-200/80 bg-white px-5 py-3.5 shadow-sm"
    >
      <p class="text-sm text-slate-500">
        Page
        <span class="font-bold text-slate-800">
          {{ page }}
        </span>
        of
        <span class="font-bold text-slate-800">
          {{ pages }}
        </span>
      </p>

      <div class="flex items-center gap-2">
        <button
          type="button"
          :disabled="page <= 1"
          class="inline-flex h-9 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-40"
          @click="$emit('previous')"
        >
          <span class="material-symbols-outlined text-[18px]">
            chevron_left
          </span>
        </button>

        <button
          type="button"
          :disabled="page >= pages"
          class="inline-flex h-9 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 hover:border-slate-300 disabled:cursor-not-allowed disabled:opacity-40"
          @click="$emit('next')"
        >
          <span class="material-symbols-outlined text-[18px]">
            chevron_right
          </span>
        </button>
      </div>
    </div>
  </div>
</template>