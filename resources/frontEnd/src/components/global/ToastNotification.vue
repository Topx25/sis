<template>
  <Teleport to="body">
    <transition name="toast-slide">
      <div
        v-if="visible"
        :role="isError ? 'alert' : 'status'"
        :aria-live="isError ? 'assertive' : 'polite'"
        class="fixed top-6 left-4 right-4 sm:left-auto sm:right-6 z-[99999] flex items-center gap-4 bg-white dark:bg-[#101b31] rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] p-5 pr-4 max-w-md"
      >
        <div
          class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
          :class="isError
            ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
            : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'"
        >
          <span class="material-symbols-outlined" style="font-size: 22px;">{{ isError ? 'error' : 'check' }}</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-bold text-[#191c1e] dark:text-[#eaf1fb] text-sm">{{ title }}</p>
          <p class="text-xs text-[#6b7280] dark:text-[#aebfd8] mt-0.5">{{ message }}</p>
        </div>
        <button
          aria-label="Dismiss notification"
          @click="close"
          class="w-8 h-8 rounded-full hover:bg-sis-panel dark:hover:bg-white/10 flex items-center justify-center text-sis-muted dark:text-[#9ca3af] hover:text-sis-text dark:hover:text-[#eaf1fb] transition-colors flex-shrink-0"
        >
          <span class="material-symbols-outlined" style="font-size: 18px;">close</span>
        </button>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  title: { type: String, default: 'Message Sent' },
  message: { type: String, default: "We'll get back to you within 24 hours." },
  duration: { type: Number, default: 5000 },
  variant: { type: String, default: 'success' },
})

const isError = computed(() => props.variant === 'error')

const emit = defineEmits(['close'])
const visible = ref(false)
let timer = null

onMounted(() => {
  visible.value = true
  timer = setTimeout(close, props.duration)
})

onBeforeUnmount(() => {
  clearTimeout(timer)
})

function close() {
  visible.value = false
  clearTimeout(timer)
  setTimeout(() => emit('close'), 300)
}
</script>
