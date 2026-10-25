<template>
  <section class="relative border-y border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] py-20 px-4 sm:px-8 overflow-hidden" style="background: var(--bg-section-2)">
    <div class="w-full max-w-[1380px] mx-auto">
      <!-- Section Header -->
      <div class="text-center mb-12 reveal reveal-fade-up">
        <span class="inline-block text-xs font-bold tracking-[0.2em] uppercase text-sis-accent dark:text-[var(--accent-blue)] mb-3">
          {{ sectionHeader.tagline || 'Our journey' }}
        </span>
        <h2 class="text-sis-text dark:text-[#eaf1fb] leading-[1.04] mt-[10px]" style="font-size: clamp(2.4rem, 4.5vw, 4.75rem); font-weight: 700;">
          <template v-if="sectionHeader.title_highlight">
            {{ sectionHeader.title || 'A trajectory of' }}<br /><span class="text-sis-accent dark:text-[var(--accent-blue)]">{{ sectionHeader.title_highlight }}</span>
          </template>
          <template v-else>
            {{ sectionHeader.title || 'A trajectory of growth.' }}
          </template>
        </h2>
      </div>

      <!-- Controls & Legend Bar -->
      <div class="flex items-center justify-between mb-8 px-2">
        <!-- Stages Legend -->
        <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
          <span class="inline-flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Completed Stages</span>
          </span>
          <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">•</span>
          <span class="inline-flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            <span class="text-blue-600 dark:text-blue-400 font-semibold">Active Rollout</span>
          </span>
          <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">•</span>
          <span class="inline-flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
            <span>Coming Stages (Roadmap)</span>
          </span>
        </div>

        <!-- Next / Prev Chevrons -->
        <div class="flex items-center gap-2 ml-auto">
          <button
            type="button"
            @click="scrollTrack(-280)"
            :disabled="isAtStart"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-200 shadow-sm transition hover:border-blue-500 hover:text-blue-600 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
            aria-label="Previous milestones"
          >
            <UiIcon name="chevron-left" :size="16" />
          </button>
          <button
            type="button"
            @click="scrollTrack(280)"
            :disabled="isAtEnd"
            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-200 shadow-sm transition hover:border-blue-500 hover:text-blue-600 active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed"
            aria-label="Next milestones"
          >
            <UiIcon name="chevron-right" :size="16" />
          </button>
        </div>
      </div>

      <!-- Horizontal Timeline Track -->
      <div
        ref="trackRef"
        @scroll="handleScroll"
        class="overflow-x-auto no-scrollbar scroll-smooth snap-x snap-mandatory pb-6 pt-2"
      >
        <!-- Loaded Milestones -->
        <div v-if="timelineLoaded" class="flex gap-4 sm:gap-5 w-full min-w-full px-1">
          <article
            v-for="(item, index) in enrichedMilestones"
            :key="item.id || item.year || index"
            class="flex-1 min-w-[210px] max-w-[270px] flex-shrink-0 flex flex-col snap-start"
          >
            <!-- ================= TOP CONNECTOR & NODE ================= -->
            <div class="relative h-12 flex items-center justify-center mb-4">
              <!-- Left Connector Line (only connects to previous card) -->
              <div
                v-if="index > 0"
                class="absolute -left-[10px] right-1/2 top-1/2 -translate-y-1/2 h-[2.5px]"
                :class="item.type === 'future' ? 'bg-slate-200 dark:bg-slate-800' : 'bg-blue-600 dark:bg-blue-500'"
              ></div>

              <!-- Right Connector Line (only connects to next card) -->
              <div
                v-if="index < enrichedMilestones.length - 1"
                class="absolute left-1/2 -right-[10px] top-1/2 -translate-y-1/2 h-[2.5px]"
                :class="enrichedMilestones[index + 1]?.type === 'future' ? 'bg-slate-200 dark:bg-slate-800' : 'bg-blue-600 dark:bg-blue-500'"
              ></div>

              <!-- Node: Active Stage -->
              <div
                v-if="item.type === 'active'"
                class="relative z-10 w-9 h-9 rounded-full bg-blue-100/90 dark:bg-blue-900/50 border border-blue-300 dark:border-blue-700/70 flex items-center justify-center shadow-[0_0_16px_rgba(37,99,235,0.3)] transition-transform hover:scale-110"
              >
                <div class="w-6 h-6 rounded-full bg-white dark:bg-[#071826] border-2 border-blue-600 dark:border-blue-400 flex items-center justify-center">
                  <div class="w-2 h-2 rounded-full bg-blue-600 dark:bg-blue-400 animate-pulse"></div>
                </div>
              </div>

              <!-- Node: Coming / Future Stage -->
              <div
                v-else-if="item.type === 'future'"
                class="relative z-10 w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 flex items-center justify-center transition-transform hover:scale-110"
              >
                <div class="w-2 h-2 rounded-full bg-slate-400 dark:bg-slate-500"></div>
              </div>

              <!-- Node: Completed Stage -->
              <div
                v-else
                class="relative z-10 w-7 h-7 rounded-full bg-blue-50 dark:bg-blue-950/70 border border-blue-200 dark:border-blue-800 flex items-center justify-center transition-transform hover:scale-110"
              >
                <div class="w-2 h-2 rounded-full bg-blue-600 dark:bg-blue-400"></div>
              </div>
            </div>

            <!-- ================= MILESTONE CARD ================= -->
            <!-- 1. Active Card (Highlighted) -->
            <div
              v-if="item.type === 'active'"
              class="relative rounded-2xl bg-white dark:bg-[#071826] border-2 border-blue-500 dark:border-blue-400 p-6 flex flex-col justify-between shadow-[0_12px_36px_rgba(37,99,235,0.12)] hover:-translate-y-1 transition-all duration-300 min-h-[310px] overflow-hidden group"
            >
              <!-- Decorative top-right curved accent -->
              <div class="absolute top-0 right-0 w-12 h-12 bg-blue-50 dark:bg-blue-900/25 rounded-bl-3xl pointer-events-none"></div>

              <div>
                <div class="flex items-center gap-1.5 text-xs font-bold font-mono tracking-tight text-blue-600 dark:text-blue-400 mb-2">
                  <span>{{ item.year }}</span>
                  <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug mb-3">
                  {{ item.title }}
                </h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                  {{ item.text }}
                </p>
              </div>

              <div class="mt-6 pt-4 border-t border-blue-100 dark:border-blue-900/40 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400">
                  {{ item.statusTag }}
                </span>
              </div>
            </div>

            <!-- 2. Future / Coming Stages Card (Dashed) -->
            <div
              v-else-if="item.type === 'future'"
              class="relative rounded-2xl bg-slate-50/60 dark:bg-slate-900/30 border-2 border-dashed border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between min-h-[310px] hover:border-slate-300 dark:hover:border-slate-700 hover:-translate-y-0.5 transition-all duration-300 group"
            >
              <div>
                <div class="text-xs font-semibold font-mono tracking-tight text-slate-400 dark:text-slate-500 mb-2">
                  {{ item.year }}
                </div>
                <h3 class="text-base font-bold text-slate-600 dark:text-slate-300 leading-snug mb-3">
                  {{ item.title }}
                </h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 leading-relaxed">
                  {{ item.text }}
                </p>
              </div>

              <div class="mt-6 pt-4 border-t border-slate-200/60 dark:border-slate-800/60 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-600"></span>
                <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">
                  {{ item.statusTag }}
                </span>
              </div>
            </div>

            <!-- 3. Completed Stages Card (Standard) -->
            <div
              v-else
              class="relative rounded-2xl bg-white dark:bg-[#071826] border border-slate-100 dark:border-slate-800/80 p-6 flex flex-col justify-between shadow-[0_4px_24px_rgba(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] hover:-translate-y-1 transition-all duration-300 min-h-[310px] group"
            >
              <div>
                <div class="text-xs font-bold font-mono tracking-tight text-blue-600 dark:text-blue-400 mb-2">
                  {{ item.year }}
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white leading-snug mb-3">
                  {{ item.title }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  {{ item.text }}
                </p>
              </div>

              <div class="mt-6 pt-4 border-t border-slate-50 dark:border-slate-800/40 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 capitalize">
                  {{ item.statusTag }}
                </span>
              </div>
            </div>
          </article>
        </div>

        <!-- Skeleton State -->
        <div v-else class="flex gap-4 min-w-full w-max px-2">
          <div v-for="i in 5" :key="i" class="w-[230px] min-w-[220px] max-w-[260px] flex-shrink-0 flex flex-col">
            <div class="h-12 flex items-center justify-center mb-4">
              <Skeleton class="w-7 h-7 rounded-full" />
            </div>
            <div class="rounded-2xl border border-slate-100 dark:border-slate-800 p-6 min-h-[310px] space-y-3 bg-white dark:bg-[#071826]">
              <Skeleton class="h-4 w-16 rounded-md" />
              <Skeleton class="h-6 w-32 rounded-md" />
              <Skeleton class="h-3 w-full rounded-md" />
              <Skeleton class="h-3 w-4/5 rounded-md" />
              <div class="pt-16">
                <Skeleton class="h-3 w-20 rounded-md" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useSiteStore } from '@/stores/useSiteStore'
import Skeleton from '@/components/common/Skeleton.vue'
import UiIcon from '@/components/common/UiIcon.vue'
import { api } from '@/api/index.js'
import { parseTimelineYear } from '@/api/normalizers.js'

const siteStore = useSiteStore()
const { timeline, timelineLoaded } = storeToRefs(siteStore)

const sectionHeader = ref({
  tagline: 'Our journey',
  title: 'A trajectory of',
  title_highlight: 'growth.',
})

const trackRef = ref(null)
const isAtStart = ref(true)
const isAtEnd = ref(false)

const handleScroll = () => {
  if (!trackRef.value) return
  const { scrollLeft, scrollWidth, clientWidth } = trackRef.value
  isAtStart.value = scrollLeft <= 10
  isAtEnd.value = scrollLeft + clientWidth >= scrollWidth - 10
}

const scrollTrack = (offset) => {
  if (!trackRef.value) return
  trackRef.value.scrollBy({ left: offset, behavior: 'smooth' })
}

onMounted(async () => {
  if (typeof siteStore.loadTimeline === 'function') {
    siteStore.loadTimeline().catch(() => {})
  }
  try {
    const res = await api.getTimelineSection?.()
    const data = res?.data || res
    if (data && typeof data === 'object') {
      if (data.tagline !== undefined) sectionHeader.value.tagline = data.tagline
      if (data.title !== undefined) sectionHeader.value.title = data.title
      if (data.title_highlight !== undefined) sectionHeader.value.title_highlight = data.title_highlight
    }
  } catch (err) {
    console.warn('Unable to load timeline section header:', err)
  }
  setTimeout(() => {
    handleScroll()
  }, 250)
})

const milestones = computed(() => {
  const isLoaded = Boolean(timelineLoaded?.value)
  const list = timeline?.value
  if (isLoaded) {
    return Array.isArray(list) ? list : []
  }
  if (Array.isArray(list) && list.length > 0) {
    return list
  }
  return []
})

watch(milestones, () => {
  setTimeout(() => {
    handleScroll()
  }, 100)
})

// Auto-enrich milestones with stage types (past, active, future) and status badges
const enrichedMilestones = computed(() => {
  const list = milestones.value || []
  if (!list.length) return []

  // Find the active milestone: contains current era 2025/2026 or marked active
  let activeIndex = list.findIndex(m => {
    const y = String(m?.year || '')
    const t = String(m?.title || '').toLowerCase()
    return y.includes('2025') || y.includes('2026') || m?.is_active || t.includes('automation')
  })

  // If no 2025/2026 found, find first future stage and make the preceding stage active
  if (activeIndex === -1) {
    const firstFutureIndex = list.findIndex(m => {
      const y = String(m?.year || '')
      return y.includes('+') || parseTimelineYear(y) >= 2027
    })
    if (firstFutureIndex > 0) {
      activeIndex = firstFutureIndex - 1
    } else if (list.length > 0) {
      activeIndex = Math.max(0, list.length - 1)
    }
  }

  const pastStatusBadges = ['Established', 'Market-fit', 'Expanded', 'Scaled', 'Delivered', 'Grown']

  return list.map((m, idx) => {
    const yStr = String(m?.year || '').trim()
    const yNum = parseTimelineYear(yStr)

    let type = 'past'
    let statusTag = m?.status || m?.tag || ''

    if (idx === activeIndex) {
      type = 'active'
      if (!statusTag) statusTag = 'In Active Rollout'
    } else if (idx > activeIndex || yStr.includes('+') || yNum >= 2027 || m?.is_future) {
      type = 'future'
      if (!statusTag) statusTag = 'Roadmap'
    } else {
      type = 'past'
      if (!statusTag) {
        statusTag = pastStatusBadges[idx] || 'Established'
      }
    }

    return {
      ...m,
      type,
      statusTag,
    }
  })
})
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
