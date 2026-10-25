<template>
  <main
    class="min-h-screen bg-sis-panel text-sis-text transition-colors duration-200 dark:bg-[#020c1b] dark:text-white"
  >
    <section
      class="relative overflow-hidden px-4 py-14 sm:px-6 lg:px-8 lg:py-20"
    >
      <div
        class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_20%,rgba(0,174,239,0.06),transparent_35%)]"
      ></div>

      <div
        class="pointer-events-none absolute inset-0 opacity-[0.035]"
        style="
          background-image:
            linear-gradient(rgba(255,255,255,.15) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.15) 1px, transparent 1px);
          background-size: 32px 32px;
        "
      ></div>

      <div class="relative mx-auto max-w-[1500px]">
        <!-- Page Header -->
        <header class="mx-auto mb-12 max-w-3xl text-center">
          <h1
            class="text-3xl font-semibold tracking-tight text-sis-text dark:text-white sm:text-4xl lg:text-[42px]"
          >
            Our
            Partner Ecosystem
          </h1>

          <div class="mt-4 flex items-center justify-center gap-3">
            <span
              class="h-px w-16 bg-gradient-to-r from-transparent to-cyan-400"
            ></span>

            <!-- Dynamic Header Icon -->
            <span
              v-if="partnerHeader?.icon"
              class="material-symbols-outlined text-[32px] text-cyan-400"
            >
              {{ partnerHeader.icon }}
            </span>

            <span
              v-else
              class="material-symbols-outlined text-[32px] text-cyan-400"
            >
              handshake
            </span>

            <span
              class="h-px w-16 bg-gradient-to-l from-transparent to-cyan-400"
            ></span>
          </div>



          <!-- Dynamic Header Subtitle -->
          <p
            class="mx-auto mt-4 max-w-2xl text-sm leading-7 text-sis-muted dark:text-slate-300 sm:text-base"
          >
            {{
              partnerHeader?.subtitle ||
              "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients."
            }}
          </p>
        </header>

        <div class="grid gap-4 xl:grid-cols-[2.2fr_1fr]">
          <!-- Skeleton loading -->
          <template v-if="isLoading">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <div
                v-for="n in 4"
                :key="'skel-featured-' + n"
                class="relative flex min-h-[360px] flex-col overflow-hidden rounded-xl border border-sis-border bg-white/80 p-4 sm:min-h-[460px] sm:p-5 dark:border-slate-700/60 dark:bg-[#061326]/80"
              >
                <div
                  class="mb-5 flex min-h-[90px] items-center sm:mb-8 sm:min-h-[100px]"
                >
                  <div
                    class="h-16 w-32 animate-pulse rounded-md bg-slate-200 dark:bg-slate-700"
                  ></div>
                </div>

                <div
                  class="mb-4 h-px w-7 bg-cyan-400/30 sm:mb-5"
                ></div>

                <div class="space-y-2">
                  <div
                    class="h-3 w-full animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                  ></div>
                  <div
                    class="h-3 w-5/6 animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                  ></div>
                  <div
                    class="h-3 w-4/6 animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                  ></div>
                </div>

                <div
                  class="mt-auto flex justify-center pt-6 sm:pt-7"
                >
                  <div
                    class="h-9 w-[180px] animate-pulse rounded-full bg-slate-200 dark:bg-slate-700"
                  ></div>
                </div>
              </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-2">
              <div
                v-for="n in 5"
                :key="'skel-secondary-' + n"
                class="flex min-h-[82px] items-center gap-4 rounded-lg border border-sis-border bg-white/80 px-5 dark:border-slate-700/60 dark:bg-[#061326]/70"
              >
                <div
                  class="h-9 w-20 animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                ></div>

                <div
                  class="h-3 w-24 animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                ></div>
              </div>
            </div>
          </template>

          <!-- Actual content -->
          <template v-else>
            <!-- Featured Partners -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <article
                v-for="partner in ecosystemPartners"
                :key="partner.name"
                class="group relative flex min-h-[360px] flex-col overflow-hidden rounded-xl border border-sis-border bg-white/80 p-4 shadow-[0_10px_30px_rgba(15,23,42,0.04)] transition duration-300 hover:-translate-y-1 hover:border-cyan-400/50 hover:shadow-[0_12px_40px_rgba(0,205,255,0.08)] sm:min-h-[460px] sm:p-5 dark:border-slate-700/60 dark:bg-[#061326]/80 dark:shadow-none"
              >
                <div
                  class="pointer-events-none absolute inset-x-0 bottom-0 h-40 opacity-30 blur-3xl transition duration-500 group-hover:opacity-50"
                  :style="{
                    background: `radial-gradient(circle at bottom, ${
                      partner.glow || '#00d4ff'
                    }, transparent 65%)`,
                  }"
                ></div>

                <div
                  class="pointer-events-none absolute inset-x-0 bottom-0 h-32 opacity-20"
                  :style="{
                    backgroundImage: `radial-gradient(${
                      partner.glow || '#00d4ff'
                    } 1px, transparent 1px)`,
                    backgroundSize: '7px 7px',
                  }"
                ></div>

                <div
                  class="relative z-10 flex h-full flex-col"
                >
                  <div
                    class="mb-5 flex min-h-[90px] items-center sm:mb-8 sm:min-h-[100px]"
                  >
                    <img
                      :src="partner.logo_url"
                      :alt="partner.name"
                      class="max-h-[80px] max-w-[70%] object-contain object-left sm:max-h-[90px] sm:max-w-[80%]"
                    />
                  </div>

                  <div
                    class="mb-4 h-px w-7 bg-cyan-400 transition-all duration-300 group-hover:w-12 sm:mb-5"
                  ></div>

                  <p
                    class="text-sm leading-6 text-sis-muted dark:text-slate-300 sm:leading-7"
                  >
                    {{ partner.description }}
                  </p>

                  <div
                    class="mt-auto flex justify-center pt-6 sm:pt-7"
                  >
                    <button
                      type="button"
                      @click="handleSolutions"
                      class="group inline-flex w-full max-w-[180px] items-center justify-center gap-2 rounded-full border border-cyan-600/30 bg-gradient-to-b from-cyan-400 to-cyan-500 px-4 py-2.5 text-[10px] font-semibold uppercase tracking-[0.12em] text-[#031520] shadow-[0_1px_0_rgba(255,255,255,0.4)_inset,0_10px_24px_rgba(6,182,212,0.25)] transition-all duration-300 ease-out hover:-translate-y-0.5 hover:from-cyan-300 hover:to-cyan-400 hover:shadow-[0_1px_0_rgba(255,255,255,0.5)_inset,0_16px_32px_rgba(6,182,212,0.35)] active:translate-y-0 active:shadow-[0_1px_0_rgba(255,255,255,0.3)_inset,0_6px_14px_rgba(6,182,212,0.25)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent dark:border-cyan-300/20 dark:from-cyan-400 dark:to-cyan-600 dark:text-[#03141c] dark:shadow-[0_1px_0_rgba(255,255,255,0.15)_inset,0_10px_28px_rgba(34,211,238,0.35),0_0_20px_rgba(34,211,238,0.15)] dark:hover:from-cyan-300 dark:hover:to-cyan-500 dark:hover:shadow-[0_1px_0_rgba(255,255,255,0.2)_inset,0_16px_36px_rgba(34,211,238,0.45),0_0_28px_rgba(34,211,238,0.25)]"
                    >
                      <span class="relative">
                        View Solutions
                      </span>

                      <svg
                        class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      >
                        <path d="M5 12h14M13 6l6 6-6 6" />
                      </svg>
                    </button>
                  </div>
                </div>
              </article>
            </div>

            <!-- Secondary Partners -->
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-2">
              <button
                v-for="partner in secondaryPartners"
                :key="partner.id"
                type="button"
                class="group flex min-h-[82px] cursor-default items-center justify-between rounded-lg border border-sis-border bg-white/80 px-5 text-sis-text transition duration-300 hover:border-cyan-400/50 hover:bg-slate-50 dark:border-slate-700/60 dark:bg-[#061326]/70 dark:text-slate-200 dark:hover:bg-[#091a30]"
              >
                <div class="flex items-center gap-4">
                  <img
                    :src="partner.logo_url"
                    :alt="partner.name"
                    class="h-9 w-20 object-contain object-left"
                  />

                  <span
                    class="text-sm text-sis-text dark:text-slate-200"
                  >
                    {{ partner.name }}
                  </span>
                </div>
              </button>
            </div>
          </template>
        </div>

        <!-- Benefits skeleton -->
        <div
          v-if="isLoading"
          class="mt-8 rounded-xl border border-sis-border bg-slate-50/90 px-5 py-7 shadow-[0_10px_30px_rgba(15,23,42,0.04)] sm:px-7 dark:border-slate-700/60 dark:bg-[#061326]/65 dark:shadow-none"
        >
          <div
            class="grid divide-y divide-sis-border md:grid-cols-2 md:divide-x md:divide-y-0 xl:grid-cols-4 dark:divide-slate-700/60"
          >
            <div
              v-for="n in 4"
              :key="'skel-benefit-' + n"
              class="flex gap-5 px-3 py-6 first:pl-0 last:pr-0 md:px-6 md:py-3"
            >
              <div
                class="flex h-14 w-14 shrink-0 items-center justify-center"
              >
                <div
                  class="h-12 w-12 animate-pulse rounded-lg bg-slate-200 dark:bg-slate-700"
                ></div>
              </div>

              <div class="flex-1 space-y-2">
                <div
                  class="h-4 w-3/4 animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                ></div>

                <div
                  class="h-3 w-full animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                ></div>

                <div
                  class="h-3 w-5/6 animate-pulse rounded bg-slate-200 dark:bg-slate-700"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Benefits content -->
        <div
          v-else
          class="mt-8 rounded-xl border border-sis-border bg-slate-50/90 px-5 py-7 shadow-[0_10px_30px_rgba(15,23,42,0.04)] sm:px-7 dark:border-slate-700/60 dark:bg-[#061326]/65 dark:shadow-none"
        >
          <div
            class="grid divide-y divide-sis-border md:grid-cols-2 md:divide-x md:divide-y-0 xl:grid-cols-4 dark:divide-slate-700/60"
          >
            <div
              v-for="benefit in benefits"
              :key="benefit.title"
              class="flex gap-5 px-3 py-6 first:pl-0 last:pr-0 md:px-6 md:py-3"
            >
              <div
                class="flex h-14 w-14 shrink-0 items-center justify-center text-cyan-400"
              >
                <!-- Handshake -->
                <svg
                  v-if="benefit.icon === 'handshake'"
                  class="h-12 w-12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="m11 17 2 2a2.8 2.8 0 0 0 4-4l-3.2-3.2" />
                  <path d="m7 13 4-4 2 2 2.5-2.5" />
                  <path d="m2 9 4-4 3 3-4 4Z" />
                  <path d="m22 9-4-4-3 3 4 4Z" />
                  <path d="m8 14 2 2" />
                  <path d="m6 16 2 2" />
                </svg>

                <!-- Award / Verified -->
                <svg
                  v-else-if="
                    benefit.icon === 'award' ||
                    benefit.icon === 'verified'
                  "
                  class="h-12 w-12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <circle cx="12" cy="8" r="5" />
                  <path d="m8.5 12-1 9 4.5-3 4.5 3-1-9" />
                  <path
                    d="m12 5 1 2 2 .3-1.5 1.5.5 2.2-2-1-2 1 .5-2.2L9 7.3 11 7Z"
                  />
                </svg>

                <!-- Integration -->
                <svg
                  v-else-if="
                    benefit.icon === 'integration' ||
                    benefit.icon === 'extension'
                  "
                  class="h-12 w-12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <rect
                    x="3"
                    y="3"
                    width="7"
                    height="7"
                    rx="1"
                  />
                  <rect
                    x="14"
                    y="3"
                    width="7"
                    height="7"
                    rx="1"
                  />
                  <rect
                    x="3"
                    y="14"
                    width="7"
                    height="7"
                    rx="1"
                  />
                  <rect
                    x="14"
                    y="14"
                    width="7"
                    height="7"
                    rx="1"
                  />
                  <path d="M10 6.5h4" />
                  <path d="M6.5 10v4" />
                  <path d="M17.5 10v4" />
                  <path d="M10 17.5h4" />
                </svg>

                <!-- Trending Up / Fallback -->
                <svg
                  v-else
                  class="h-12 w-12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M4 20V10" />
                  <path d="M10 20V6" />
                  <path d="M16 20V3" />
                  <path d="M22 20H2" />
                  <path d="m3 9 6-3 5-2 7-3" />
                  <path d="m18 1 3 .2-.5 3" />
                </svg>
              </div>

              <div>
                <h3
                  class="mb-2 text-base font-medium text-sis-text dark:text-white"
                >
                  {{ benefit.title }}
                </h3>

                <p
                  class="text-sm leading-6 text-sis-muted dark:text-slate-300"
                >
                  {{ benefit.description }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
import { useRouter } from "vue-router";
import { onMounted, ref } from "vue";
import { api } from "../../api/index.js";
import { normalizeEcosystem } from "../../api/normalizers.js";

const router = useRouter();

const ecosystemPartners = ref([]);
const secondaryPartners = ref([]);
const benefits = ref([]);
const partnerHeader = ref(null);

const isLoading = ref(true);

onMounted(async () => {
  try {
    const response = await api.getPartners();

    const data = response;

    partnerHeader.value = data?.header || (data?.title ? data : null);
    ecosystemPartners.value = data?.featured_partners || [];
    secondaryPartners.value = data?.partners || [];
    benefits.value = data?.header?.value_badges || data?.value_badges || [];
  } catch (error) {
    console.error("Failed to fetch partners:", error);

    const data = normalizeEcosystem();

    partnerHeader.value = data.header;
    ecosystemPartners.value = data.featured_partners;
    secondaryPartners.value = data.partners;
    benefits.value = data.value_badges;
  } finally {
    isLoading.value = false;
  }
});

function handleSolutions() {
  router.push({ name: "projects" });
}
</script>
