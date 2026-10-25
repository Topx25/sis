<script setup>
import { computed, nextTick, ref } from "vue";
import { onClickOutside } from "@vueuse/core";
import {
  findIcon,
  getCategories,
  getIconLabel,
  resolveIconName,
  searchIcons,
} from "@/SIS-Dashboard/constants/iconRegistry.js";

const props = defineProps({
  modelValue: {
    type: String,
    default: "",
  },
  placeholder: {
    type: String,
    default: "Select icon...",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  clearable: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(["update:modelValue", "change"]);

const isOpen = ref(false);
const searchQuery = ref("");
const selectedCategory = ref("All");
const hoveredIcon = ref(null);
const containerRef = ref(null);
const searchInputRef = ref(null);

const categories = getCategories();

// Close popover when clicking outside
onClickOutside(containerRef, () => {
  if (isOpen.value) {
    closePicker();
  }
});

const currentIcon = computed(() => findIcon(props.modelValue));

const currentLabel = computed(() => {
  if (!props.modelValue) return "";
  return getIconLabel(props.modelValue);
});

// The symbol to render on the trigger preview
const currentRenderIcon = computed(() => {
  if (!props.modelValue) return "";
  return resolveIconName(props.modelValue) || props.modelValue;
});

const isLegacyOrUnknown = computed(() => {
  if (!props.modelValue) return false;
  return !currentIcon.value;
});

const filteredIcons = computed(() => {
  return searchIcons(searchQuery.value, selectedCategory.value);
});

function togglePicker() {
  if (props.disabled) return;
  if (isOpen.value) {
    closePicker();
  } else {
    openPicker();
  }
}

function openPicker() {
  isOpen.value = true;
  searchQuery.value = "";
  selectedCategory.value = "All";
  nextTick(() => {
    searchInputRef.value?.focus();
  });
}

function closePicker() {
  isOpen.value = false;
  searchQuery.value = "";
  hoveredIcon.value = null;
}

function selectIcon(iconName) {
  emit("update:modelValue", iconName);
  emit("change", iconName);
  closePicker();
}

function selectCustom(customName) {
  const clean = String(customName || "").trim().toLowerCase();
  if (!clean) return;
  emit("update:modelValue", clean);
  emit("change", clean);
  closePicker();
}

function clearIcon(e) {
  e.stopPropagation();
  emit("update:modelValue", "");
  emit("change", "");
}

function handleKeydown(e) {
  if (e.key === "Escape" && isOpen.value) {
    closePicker();
  }
}
</script>

<template>
  <div
    ref="containerRef"
    class="relative w-full"
    @keydown="handleKeydown"
  >
    <!-- Trigger Button (Closed State) -->
    <button
      type="button"
      :disabled="disabled"
      @click="togglePicker"
      class="group flex h-10 w-full items-center justify-between gap-2 rounded-xl border bg-white px-2.5 py-1.5 text-left text-xs transition-all outline-none focus:ring-2 focus:ring-blue-500/20 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-[#071426]"
      :class="[
        isOpen
          ? 'border-blue-500 ring-2 ring-blue-500/20 dark:border-blue-400'
          : 'border-slate-200 hover:border-slate-300 dark:border-slate-700 dark:hover:border-slate-600',
      ]"
    >
      <div class="flex min-w-0 items-center gap-2">
        <!-- Icon Badge Preview -->
        <div
          class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border transition-colors"
          :class="[
            modelValue
              ? 'border-blue-200 bg-blue-50 text-blue-600 dark:border-blue-900/60 dark:bg-blue-950/40 dark:text-blue-400'
              : 'border-slate-200 bg-slate-50 text-slate-400 dark:border-slate-800 dark:bg-slate-900/50 dark:text-slate-500',
          ]"
        >
          <span
            v-if="modelValue"
            class="material-symbols-outlined text-[17px]"
          >
            {{ currentRenderIcon }}
          </span>
          <span
            v-else
            class="material-symbols-outlined text-[15px]"
          >
            category
          </span>
        </div>

        <!-- Single-Line Label Text -->
        <div class="min-w-0 flex-1 truncate">
          <span
            v-if="modelValue"
            class="font-semibold text-slate-800 dark:text-slate-200 text-xs"
          >
            {{ currentLabel || modelValue }}
          </span>
          <span
            v-else
            class="text-slate-400 dark:text-slate-500 text-xs"
          >
            {{ placeholder }}
          </span>
        </div>
      </div>

      <!-- Controls Right: Clear + Chevron -->
      <div class="flex items-center gap-0.5 shrink-0">
        <button
          v-if="clearable && modelValue && !disabled"
          type="button"
          @click="clearIcon"
          class="flex h-5 w-5 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300"
          title="Clear icon"
        >
          <span class="material-symbols-outlined text-[14px]">close</span>
        </button>

        <span
          class="material-symbols-outlined text-[17px] text-slate-400 transition-transform duration-200"
          :class="{ 'rotate-180 text-blue-600 dark:text-blue-400': isOpen }"
        >
          expand_more
        </span>
      </div>
    </button>

    <!-- Popover Dropdown (Open State) -->
    <transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 sm:right-auto sm:left-0 top-full z-[120] mt-1.5 w-80 max-w-[90vw] rounded-2xl border border-slate-200 bg-white p-3 shadow-2xl dark:border-slate-800 dark:bg-[#0b132b]"
      >
        <!-- Search Input -->
        <div class="relative mb-2.5">
          <span
            class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400"
          >
            search
          </span>
          <input
            ref="searchInputRef"
            v-model="searchQuery"
            type="text"
            placeholder="Search icons (e.g. robot, energy, shield, cloud)..."
            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-8 text-xs text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/10 dark:border-slate-700 dark:bg-[#071426] dark:text-white dark:focus:bg-[#071426]"
          />
          <button
            v-if="searchQuery"
            type="button"
            @click="searchQuery = ''"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
          >
            <span class="material-symbols-outlined text-[16px]">close</span>
          </button>
        </div>

        <!-- Categories Filter Bar -->
        <div class="no-scrollbar mb-2 flex items-center gap-1.5 overflow-x-auto pb-1.5 pt-0.5">
          <button
            v-for="cat in categories"
            :key="cat"
            type="button"
            @click="selectedCategory = cat"
            class="shrink-0 rounded-lg px-2.5 py-1 text-[11px] font-semibold transition"
            :class="[
              selectedCategory === cat
                ? 'bg-blue-600 text-white shadow-sm'
                : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800/80 dark:text-slate-300 dark:hover:bg-slate-800',
            ]"
          >
            {{ cat }}
          </button>
        </div>

        <!-- Active Legacy / Custom Value Notice -->
        <div
          v-if="isLegacyOrUnknown"
          class="mb-2.5 flex items-center justify-between rounded-xl border border-amber-200 bg-amber-50/80 px-3 py-2 text-xs text-amber-900 dark:border-amber-900/40 dark:bg-amber-950/30 dark:text-amber-300"
        >
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">
              {{ currentRenderIcon }}
            </span>
            <div>
              <span class="font-bold">Current Icon:</span>
              <code class="ml-1 font-mono font-bold">{{ modelValue }}</code>
            </div>
          </div>
          <span class="text-[10px] opacity-75">Preserved until changed</span>
        </div>

        <!-- Icons Grid (Compact Visual Matrix) -->
        <div class="grid max-h-64 grid-cols-6 gap-1.5 overflow-y-auto overscroll-contain p-1 sm:grid-cols-7">
          <button
            v-for="icon in filteredIcons"
            :key="icon.name"
            type="button"
            @click="selectIcon(icon.name)"
            @mouseenter="hoveredIcon = icon"
            @mouseleave="hoveredIcon = null"
            class="group relative flex h-11 w-11 items-center justify-center rounded-xl border transition-all"
            :class="[
              modelValue === icon.name || modelValue === icon.keywords?.[0]
                ? 'border-blue-500 bg-blue-100/70 text-blue-700 ring-2 ring-blue-500/20 dark:border-blue-400 dark:bg-blue-950/70 dark:text-blue-300'
                : 'border-slate-100 bg-slate-50/50 hover:border-blue-300 hover:bg-blue-50/60 hover:text-blue-600 dark:border-slate-800 dark:bg-slate-900/60 dark:hover:border-blue-700 dark:hover:bg-blue-950/40 dark:hover:text-blue-400',
            ]"
            :title="`${icon.label} (${icon.name})`"
          >
            <span
              class="material-symbols-outlined text-[23px] transition-transform duration-150 group-hover:scale-115"
              :class="[
                modelValue === icon.name
                  ? 'text-blue-600 dark:text-blue-400'
                  : 'text-slate-700 dark:text-slate-300',
              ]"
            >
              {{ icon.name }}
            </span>
          </button>
        </div>

        <!-- Empty Results or Custom Direct Selection -->
        <div
          v-if="filteredIcons.length === 0"
          class="flex flex-col items-center justify-center py-6 text-center"
        >
          <span class="material-symbols-outlined mb-1 text-[28px] text-slate-400">
            search_off
          </span>
          <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">
            No matching icons found
          </p>
          <p class="mt-0.5 text-[11px] text-slate-400 dark:text-slate-500">
            Try a different keyword or category.
          </p>

          <!-- Allow using the typed search query as a custom icon identifier -->
          <button
            v-if="searchQuery.trim()"
            type="button"
            @click="selectCustom(searchQuery)"
            class="mt-3 inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100 dark:border-blue-900/60 dark:bg-blue-950/40 dark:text-blue-300"
          >
            <span class="material-symbols-outlined text-[16px]">check</span>
            Use "<span class="font-mono">{{ searchQuery.trim().toLowerCase() }}</span>"
          </button>
        </div>

        <!-- Footer Bar with Live Hover Preview -->
        <div class="mt-2.5 flex items-center justify-between border-t border-slate-100 pt-2 text-[11px] text-slate-500 dark:border-slate-800/80 dark:text-slate-400">
          <div class="flex items-center gap-1.5 truncate pr-2">
            <template v-if="hoveredIcon">
              <span class="material-symbols-outlined text-[15px] text-blue-600 dark:text-blue-400 shrink-0">{{ hoveredIcon.name }}</span>
              <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ hoveredIcon.label }}</span>
              <span class="text-[10px] text-slate-400 font-mono hidden sm:inline">({{ hoveredIcon.name }})</span>
            </template>
            <template v-else-if="modelValue">
              <span class="material-symbols-outlined text-[15px] text-blue-600 dark:text-blue-400 shrink-0">{{ currentRenderIcon }}</span>
              <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ currentLabel }}</span>
            </template>
            <template v-else>
              <span class="text-slate-400">Hover an icon to preview details</span>
            </template>
          </div>
          <span class="shrink-0 text-slate-400 font-mono text-[10px]">{{ filteredIcons.length }} icons</span>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
