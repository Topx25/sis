<template>
  <GlobalLoader />
  <router-view v-slot="{ Component }">
    <transition name="page-fade" mode="default">
      <component :is="Component" />
    </transition>
  </router-view>

  <a
    v-if="!isDashboard && isFloatingEnabled"
    :href="floatingContactUrl"
    :target="floatingTarget"
    rel="noopener noreferrer"
  >
    <button
      type="button"
      :aria-label="floatingContactLabel"
      :title="floatingContactLabel"
      class="fixed bottom-5 right-5 z-[60] flex h-14 w-14 items-center justify-center rounded-full bg-[#25aad3] text-white shadow-[0_10px_30px_rgba(37,211,102,0.35)] transition-all duration-200 hover:-translate-y-1 hover:bg-[#207ebd] hover:shadow-[0_14px_34px_rgba(37,211,102,0.45)] focus:outline-none focus:ring-4 focus:ring-[#207ebd]/30 sm:bottom-7 sm:right-7"
    >
      <svg class="phone-beat h-8 w-8" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path
          d="M6.62 10.79a15.46 15.46 0 006.59 6.59l2.2-2.2c.28-.28.67-.37 1.02-.25 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1C10.06 21 3 13.94 3 5c0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
        />
      </svg>
    </button>
  </a>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useIntersectionObserver } from '@vueuse/core'
import { useSiteStore } from '@/stores/useSiteStore'
import GlobalLoader from '@/components/global/GlobalLoader.vue'

const siteStore = useSiteStore()
const route = useRoute()
const isDashboard = computed(() => route.path.startsWith('/dashboard'))

const isFloatingEnabled = computed(() => {
  return siteStore.footer?.floating_contact?.enabled !== false;
})

const floatingContactUrl = computed(() => {
  return siteStore.footer?.floating_contact?.url || "https://wa.me/201007568651";
})

const floatingContactLabel = computed(() => {
  return siteStore.footer?.floating_contact?.label || "Contact us on WhatsApp";
})

const floatingTarget = computed(() => {
  return floatingContactUrl.value.startsWith("tel:") ? "_self" : "_blank";
})

onMounted(() => {
  if (!siteStore.loaded) {
    siteStore.loadAll()
  }

  const observerOptions = {
    root: null,
    rootMargin: '0px 0px -100px 0px',
    threshold: 0.1,
  }

  const observeElements = () => {
    document.querySelectorAll('.reveal:not(.active), [data-reveal]:not(.active)').forEach((el) => {
      useIntersectionObserver(el, ([{ isIntersecting }]) => {
        if (isIntersecting) {
          el.classList.add('active')
        }
      }, observerOptions)
    })
  }

  observeElements()

  const mutationObserver = new MutationObserver(() => { observeElements() })
  mutationObserver.observe(document.body, { childList: true, subtree: true })
})
</script>

<style>
@keyframes phone-beat {
  0%, 40%, 100% {
    transform: scale(1);
  }

  10% {
    transform: scale(1.14);
  }

  20% {
    transform: scale(1);
  }

  30% {
    transform: scale(1.1);
  }
}

.phone-beat {
  transform-origin: center;
  animation: phone-beat 1.8s ease-in-out infinite;
}

@media (prefers-reduced-motion: reduce) {
  .phone-beat {
    animation: none;
  }
}
</style>
