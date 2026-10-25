<template>
  <section
    class="relative py-24 overflow-hidden border-y border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)]"
    id="why-it-ot"
    style="background: var(--bg-section-2)"
  >
    <div
      class="absolute inset-0 pointer-events-none opacity-[0.03] dark:opacity-[0.03]"
      style="
        background-image:
          linear-gradient(
            to right,
            rgba(126,165,224,0.3) 1px,
            transparent 1px
          ),
          linear-gradient(
            to bottom,
            rgba(126,165,224,0.3) 1px,
            transparent 1px
          );
        background-size: 100px 100px;
      "
    ></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">

      <!-- Header -->
      <div class="text-center mb-20 reveal reveal-fade-up">

        <template v-if="isLoading">
          <Skeleton class="h-4 w-40 mx-auto mb-4 rounded" />
          <Skeleton class="h-12 w-3/4 max-w-2xl mx-auto mb-6 rounded" />
          <Skeleton class="h-20 w-full max-w-3xl mx-auto rounded" />
        </template>

        <template v-else>
          <span
            class="text-xs font-bold tracking-[0.2em] uppercase text-sis-accent dark:text-[var(--accent-blue)] mb-4 block"
          >
            {{ section.tagline }}
          </span>

          <h2
            class="text-4xl md:text-5xl font-bold text-sis-text dark:text-[#eaf1fb] mb-6"
          >
            {{ section.headline }}
          </h2>

          <p
            class="text-sis-muted dark:text-[#aebfd8] text-lg max-w-3xl mx-auto"
          >
            {{ section.subheadline }}
          </p>
        </template>

      </div>

      <!-- OT / IT -->
      <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-0">

        <!-- OT -->
        <div
          class="lg:w-2/5 p-10 rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] reveal reveal-slide-in hover:border-[var(--bg-accent-30)] dark:hover:border-[var(--accent-blue)]/30 transition-colors"
          style="background: var(--bg-card-2)"
        >

          <template v-if="isLoading">
            <Skeleton class="w-14 h-14 rounded-xl mb-6" />
            <Skeleton class="h-8 w-3/4 mb-4 rounded" />
            <Skeleton class="h-24 w-full mb-6 rounded" />

            <div class="flex flex-wrap gap-2">
              <Skeleton
                v-for="i in 4"
                :key="i"
                class="h-7 w-16 rounded-full"
              />
            </div>
          </template>

          <template v-else>

            <div
              class="w-14 h-14 bg-[var(--bg-accent-10)] dark:bg-[rgba(var(--accent-blue-r), var(--accent-blue-g), var(--accent-blue-b),0.1)] text-sis-accent dark:text-[var(--accent-blue)] rounded-xl flex items-center justify-center mb-6"
            >
              <span class="material-symbols-outlined text-3xl">
                {{ section.ot.icon }}
              </span>
            </div>

            <h3
              class="text-2xl font-bold text-sis-text dark:text-[#eaf1fb] mb-4"
            >
              {{ section.ot.title }}
            </h3>

            <p class="text-sis-muted dark:text-[#aebfd8] mb-6">
              {{ section.ot.description }}
            </p>

            <div class="flex flex-wrap gap-2">
              <span
                v-for="tag in section.ot.tags"
                :key="tag"
                class="px-3 py-1 rounded-full text-xs font-bold border text-sis-muted dark:text-[#aebfd8]"
                style="
                  background: var(--bg-card-2);
                  border-color: var(--border-glass);
                "
              >
                {{ tag }}
              </span>
            </div>

          </template>

        </div>

        <!-- Connection -->
        <div
          class="lg:w-1/5 flex flex-col items-center justify-center reveal reveal-fade-up z-10 py-8 lg:py-0"
        >
          <div
            class="w-1 bg-gradient-to-b lg:bg-gradient-to-r lg:w-full lg:h-1 from-sis-accent via-sis-accent to-[var(--accent-teal)] dark:from-[var(--accent-blue)] dark:via-[var(--accent-blue)] dark:to-[var(--accent-teal)] h-24 lg:-mx-4 opacity-50"
          ></div>

          <div
            class="w-24 h-24 bg-sis-accent dark:bg-[var(--accent-blue)] text-white dark:text-[#04121f] rounded-full flex items-center justify-center shadow-[0_0_40px_rgba(var(--accent-blue-r), var(--accent-blue-g), var(--accent-blue-b),0.4)] my-4 lg:-my-12 z-20"
          >
            <span class="material-symbols-outlined text-4xl">
              sync_alt
            </span>
          </div>

          <div
            class="w-1 bg-gradient-to-b lg:bg-gradient-to-r lg:w-full lg:h-1 from-[var(--accent-teal)] via-sis-accent to-sis-accent dark:from-[var(--accent-teal)] dark:via-[var(--accent-blue)] dark:to-[var(--accent-blue)] h-24 lg:-mx-4 opacity-50"
          ></div>
        </div>

        <!-- IT -->
        <div
          class="lg:w-2/5 p-10 rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] reveal reveal-slide-in hover:border-[var(--bg-accent-30)] dark:hover:border-[var(--accent-blue)]/30 transition-colors"
          style="
            background: var(--bg-card-2);
            transition-delay: 0.2s;
          "
        >

          <template v-if="isLoading">

            <Skeleton class="w-14 h-14 rounded-xl mb-6" />
            <Skeleton class="h-8 w-3/4 mb-4 rounded" />
            <Skeleton class="h-24 w-full mb-6 rounded" />

            <div class="flex flex-wrap gap-2">
              <Skeleton
                v-for="i in 4"
                :key="i"
                class="h-7 w-16 rounded-full"
              />
            </div>

          </template>

          <template v-else>

            <div
              class="w-14 h-14 bg-[var(--bg-accent-10)] dark:bg-[rgba(var(--accent-blue-r), var(--accent-blue-g), var(--accent-blue-b),0.1)] text-sis-accent dark:text-[var(--accent-blue)] rounded-xl flex items-center justify-center mb-6"
            >
              <span class="material-symbols-outlined text-3xl">
                {{ section.it.icon }}
              </span>
            </div>

            <h3
              class="text-2xl font-bold text-sis-text dark:text-[#eaf1fb] mb-4"
            >
              {{ section.it.title }}
            </h3>

            <p class="text-sis-muted dark:text-[#aebfd8] mb-6">
              {{ section.it.description }}
            </p>

            <div class="flex flex-wrap gap-2">
              <span
                v-for="tag in section.it.tags"
                :key="tag"
                class="px-3 py-1 rounded-full text-xs font-bold border text-sis-muted dark:text-[#aebfd8]"
                style="
                  background: var(--bg-card-2);
                  border-color: var(--border-glass);
                "
              >
                {{ tag }}
              </span>
            </div>

          </template>

        </div>

      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { api } from "../../api/index.js";
import { normalizeAboutItOt } from "../../api/normalizers.js";
import Skeleton from "@/components/common/Skeleton.vue";

const section = ref({
  tagline: "",
  headline: "",
  subheadline: "",

  ot: {
    title: "",
    description: "",
    tags: [],
    icon: "",
  },

  it: {
    title: "",
    description: "",
    tags: [],
    icon: "",
  },
});

const isLoading = ref(true);

onMounted(async () => {
  try {
    const response = await api.getAboutItOt();
    const payload = response?.data || response;
    section.value = payload || {};
  } catch (error) {
    console.error("Failed to fetch IT-OT section:", error);

    const fallback = (typeof normalizeAboutItOt === "function" ? normalizeAboutItOt() : null) || {};
    section.value = fallback?.data || fallback;
  } finally {
    isLoading.value = false;
  }
});
</script>