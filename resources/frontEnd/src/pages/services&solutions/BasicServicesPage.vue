<script setup>
import { ref, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { api, httpsUrl } from "@/api";
import UiIcon from "@/components/common/UiIcon.vue";
import Skeleton from "@/components/common/Skeleton.vue";
import ToastNotification from "@/components/global/ToastNotification.vue";
import { useSeo } from "@/composables/useSeo.js";
import { buildServiceSchema } from "@/utils/seo.js";

const fallbackHero = `${import.meta.env.BASE_URL}images/factory-floor.webp`;
const heroImageSrc = ref(fallbackHero);

function onHeroImageError(e) {
    if (e?.target && e.target.src !== fallbackHero) {
        e.target.src = fallbackHero;
    }
}

const route = useRoute();
const router = useRouter();

const solution = ref(null);
const selectedService = ref(null);
const isLoading = ref(false);
const error = ref(null);
const isRetrying = ref(false);

const showToast = ref(false);
const toast = ref({
    title: "",
    message: "",
    variant: "success",
});

function notify(title, message, variant = "success") {
    showToast.value = false;
    setTimeout(() => {
        toast.value = {
            title,
            message,
            variant,
        };
        showToast.value = true;
    }, 10);
}

const slug = computed(() => route.params.slug);

const seoMeta = computed(() => {
    if (!solution.value || (solution.value.slug && solution.value.slug !== slug.value)) {
        return {
            title: "Services & Solutions | SiS",
            canonical: route.path,
        };
    }
    const sol = solution.value;
    const explicitSeo = sol.seo || {};

    const title = explicitSeo.meta_title || (sol.title ? `${sol.title} | SiS` : "Services & Solutions | SiS");
    const description = explicitSeo.meta_description || sol.subtitle || sol.description || "";
    const image = explicitSeo.og_image || sol.hero_image_url || sol.hero_image || fallbackHero;
    const canonical = explicitSeo.canonical_url || route.path;

    return {
        title,
        description,
        image,
        canonical,
        ogTitle: explicitSeo.og_title || title,
        ogDescription: explicitSeo.og_description || description,
        twitterTitle: explicitSeo.og_title || title,
        twitterDescription: explicitSeo.og_description || description,
        robots: explicitSeo.is_noindex ? "noindex, nofollow" : undefined,
        breadcrumbs: [
            { name: "Home", path: "/" },
            { name: "Services & Solutions", path: "/services" },
            { name: sol.title || "Solution", path: `/solution-pages/${sol.slug || slug.value}` },
        ],
        structuredData: buildServiceSchema({
            name: title,
            description: description,
            url: canonical,
        }),
    };
});

useSeo(seoMeta);

let activeRequestId = 0;

const loadSolution = async () => {
    const requestedSlug = slug.value;
    if (!requestedSlug) {
        solution.value = null;
        return;
    }

    const requestId = ++activeRequestId;
    solution.value = null;
    isLoading.value = true;
    error.value = null;
    selectedService.value = null;

    try {
        const data = await api.getSolutionPage(requestedSlug);
        if (requestId === activeRequestId && slug.value === requestedSlug) {
            solution.value = data;
        }
    } catch (err) {
        if (requestId === activeRequestId) {
            console.error("Failed to load solution page:", err);
            solution.value = null;
            error.value = err;
            notify("Error", "Failed to load solution page. Please try again.", "error");
        }
    } finally {
        if (requestId === activeRequestId) {
            isLoading.value = false;
        }
    }
};

watch(slug, loadSolution, { immediate: true });

watch(
    () => solution.value,
    (val) => {
        if (!val) {
            heroImageSrc.value = fallbackHero;
            return;
        }
        const raw = val.hero_image_url || val.hero_image;
heroImageSrc.value = raw || fallbackHero;
    },
    { immediate: true },
);

const retry = async () => {
    if (isRetrying.value || isLoading.value) return;

    isRetrying.value = true;
    try {
        await loadSolution();
    } catch (err) {
        notify("Error", "Failed to reload service details.", "error");
    } finally {
        isRetrying.value = false;
    }
};

const handleContact = () => {
    closeModal();
    router.push({ name: "contact" });
};

// Modal state
const openModal = (service) => {
    selectedService.value = service;
};

const closeModal = () => {
    selectedService.value = null;
};

const getWhyIcon = (reason) => {
    if (typeof reason === "object" && reason !== null) {
        return reason.icon || "check";
    }
    return "check";
};

const getWhyText = (reason) => {
    if (typeof reason === "object" && reason !== null) {
        return reason.text || reason.title || reason.point || "";
    }
    return reason || "";
};

const getServiceDetails = (service) => {
    if (!service) return [];

    const detailSets = {
        "System Integration": [
            "PLC, SCADA & HMI",
            "Unified control layer",
            "Plant-wide visibility",
        ],
        "FAT / SAT": [
            "Factory validation",
            "Site acceptance testing",
            "Performance sign-off",
        ],
        Commissioning: [
            "Startup support",
            "Field verification",
            "Operator handover",
        ],
        "Migration & Upgrade": [
            "Legacy assessment",
            "Phased modernization",
            "Reduced downtime",
        ],
        "Design & Consultation": [
            "Solution architecture",
            "Engineering guidance",
            "Project roadmap",
        ],
        "Technical Documentation": [
            "System drawings",
            "Operation manuals",
            "Test documentation",
        ],
        "Site Survey": [
            "On-site assessment",
            "Data collection",
            "Execution planning",
        ],
        "Feasibility Study": [
            "Technical review",
            "Cost evaluation",
            "Viability report",
        ],
        "Project Management": [
            "Delivery planning",
            "Progress governance",
            "Budget control",
        ],
        "Lifecycle Support": [
            "Preventive care",
            "Rapid response",
            "Reliability improvement",
        ],
    };

    const fallbackDetails = detailSets[service?.title] || [
        "Expert delivery",
        "Practical guidance",
        "Long-term value",
    ];

    const focus =
        typeof service.focus === "string" && service.focus.trim()
            ? service.focus.trim()
            : fallbackDetails[0];

    const approach =
        typeof service.approach === "string" && service.approach.trim()
            ? service.approach.trim()
            : fallbackDetails[1];

    const outcome =
        typeof service.outcome === "string" && service.outcome.trim()
            ? service.outcome.trim()
            : fallbackDetails[2];

    return [
        { label: "Focus", value: focus },
        { label: "Approach", value: approach },
        { label: "Outcome", value: outcome },
    ];
};
</script>

<template>
    <main v-if="isLoading"
        class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-purple-50/30 font-sans text-slate-800 transition-colors duration-300 dark:from-[#020b13] dark:via-[#0a1628] dark:to-[#12081f] dark:text-white mt-20"
        aria-live="polite">
        <div v-if="isRetrying"
            class="fixed left-1/2 top-24 z-50 flex -translate-x-1/2 items-center gap-2 rounded-full border border-blue-200 bg-white/95 px-4 py-2 text-sm font-medium text-blue-700 shadow-lg backdrop-blur dark:border-blue-400/30 dark:bg-[#0c1624]/95 dark:text-blue-300"
            role="status">
            <svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reloading service details…
        </div>

        <!-- ============ SKELETON HERO ============ -->
        <section
            class="relative min-h-[320px] overflow-hidden border-b border-blue-200/50 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-transparent dark:border-white/10 dark:from-blue-900/20 dark:via-purple-900/20">
            <div
                class="absolute right-4 top-4 bottom-4 w-full overflow-hidden rounded-3xl border border-white/60 shadow-[0_24px_70px_rgba(37,99,235,0.18)] md:w-[60%] lg:right-8 lg:w-[55%] dark:border-white/15">
                <Skeleton class="h-full w-full" />
            </div>

            <div
                class="relative z-10 mx-auto flex min-h-[320px] w-full max-w-[1600px] flex-col px-5 pb-8 pt-6 sm:px-8 lg:px-8 xl:px-10">
                <div class="flex items-center gap-2">
                    <Skeleton class="h-3 w-12 rounded-full" />
                    <Skeleton class="h-3 w-3 rounded-full" />
                    <Skeleton class="h-3 w-28 rounded-full" />
                    <Skeleton class="h-3 w-3 rounded-full" />
                    <Skeleton class="h-3 w-20 rounded-full" />
                </div>

                <div class="mt-8 max-w-[640px] space-y-4">
                    <Skeleton class="h-9 w-3/4 rounded-xl" />
                    <Skeleton class="h-9 w-1/2 rounded-xl" />
                    <Skeleton class="mt-4 h-4 w-2/3 rounded-full" />
                </div>

                <div class="mt-auto grid max-w-[780px] grid-cols-1 gap-6 pt-8 sm:grid-cols-3">
                    <div v-for="i in 3" :key="i" class="flex items-start gap-3">
                        <Skeleton class="h-12 w-12 shrink-0 rounded-xl" />
                        <div class="space-y-2">
                            <Skeleton class="h-3 w-24 rounded-full" />
                            <Skeleton class="h-3 w-32 rounded-full" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============ SKELETON CONTENT ============ -->
        <div class="mx-auto w-full max-w-[1600px] space-y-12 px-4 py-10 sm:px-6 lg:px-8 lg:py-14 xl:px-10">
            <section class="space-y-6">
                <Skeleton class="h-4 w-32 rounded-full" />

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div v-for="i in 5" :key="i"
                        class="flex min-h-[170px] flex-col justify-between rounded-2xl border border-slate-200/80 bg-white/80 p-5 dark:border-slate-700/60 dark:bg-[#0c1624]/80">
                        <div class="space-y-4">
                            <Skeleton class="h-11 w-11 rounded-lg" />
                            <Skeleton class="h-4 w-3/4 rounded-full" />
                            <Skeleton class="h-3 w-full rounded-full" />
                            <Skeleton class="h-3 w-2/3 rounded-full" />
                        </div>
                        <Skeleton class="mt-5 h-3 w-24 rounded-full" />
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <div
                    class="lg:col-span-6 rounded-2xl border border-slate-200/80 bg-white/70 p-7 dark:border-slate-700/50 dark:bg-[#0c1624]/70">
                    <Skeleton class="h-4 w-40 rounded-full" />
                    <div class="mt-8 grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-6">
                        <div v-for="i in 6" :key="i" class="flex flex-col items-center space-y-2">
                            <Skeleton class="h-12 w-12 rounded-full" />
                            <Skeleton class="h-3 w-16 rounded-full" />
                        </div>
                    </div>
                </div>

                <div
                    class="lg:col-span-3 rounded-2xl border border-slate-200/80 bg-white/70 p-7 dark:border-slate-700/50 dark:bg-[#0c1624]/70">
                    <Skeleton class="h-4 w-24 rounded-full" />
                    <div class="mt-6 space-y-3">
                        <Skeleton v-for="i in 4" :key="i" class="h-3 w-full rounded-full" />
                    </div>
                </div>

                <div
                    class="lg:col-span-3 rounded-2xl border border-slate-200/80 bg-white/70 p-7 dark:border-slate-700/50 dark:bg-[#0c1624]/70">
                    <Skeleton class="h-4 w-28 rounded-full" />
                    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                        <div v-for="i in 6" :key="i" class="flex flex-col items-center space-y-2">
                            <Skeleton class="h-10 w-10 rounded-lg" />
                            <Skeleton class="h-3 w-12 rounded-full" />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <main v-else-if="solution"
        class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/40 to-purple-50/30 font-sans text-slate-800 transition-colors duration-300 dark:from-[#020b13] dark:via-[#0a1628] dark:to-[#12081f] dark:text-white mt-20">
        <!-- =========================================================
         HERO
    ========================================================== -->
        <section v-reveal
            class="relative min-h-[320px] overflow-hidden border-b border-blue-200/50 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-transparent dark:border-white/10 dark:from-blue-900/20 dark:via-purple-900/20">
            <!-- RIGHT IMAGE -->
            <div
                class="absolute right-4 top-4 bottom-4 w-full overflow-hidden rounded-3xl border border-white/60 shadow-[0_24px_70px_rgba(37,99,235,0.18)] md:w-[60%] lg:right-8 lg:w-[55%] dark:border-white/15 dark:shadow-[0_24px_70px_rgba(0,0,0,0.35)]">
                <img :src="heroImageSrc" :alt="solution.title || 'Solution'"
                    @error="onHeroImageError"
                    class="h-full w-full scale-[1.03] object-cover object-center transition-transform duration-700 hover:scale-110" />

                <!-- Gradient overlays -->
                <div
                    class="absolute inset-0 bg-gradient-to-r from-slate-50 via-slate-50/55 to-transparent dark:from-[#020b13] dark:via-[#020b13]/45 dark:to-transparent">
                </div>
                <div
                    class="absolute inset-0 bg-gradient-to-t from-slate-50/80 via-transparent to-transparent dark:from-[#020b13]/80">
                </div>
            </div>

            <!-- Extra left fade -->
            <div
                class="pointer-events-none absolute inset-0 bg-gradient-to-r from-slate-50 via-slate-50/40 to-transparent dark:from-[#020b13] dark:via-[#020b13]/70 dark:to-transparent">
            </div>

            <!-- CONTENT -->
            <div
                class="relative z-10 mx-auto flex min-h-[320px] w-full max-w-[1600px] flex-col px-5 pb-8 pt-6 sm:px-8 lg:px-8 xl:px-10">
                <!-- Breadcrumb -->
                <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <span>Home</span>
                    <span class="text-slate-400">›</span>
                    <span>Services &amp; Solutions</span>
                    <span class="text-slate-400">›</span>

                    <span
                        class="font-semibold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">
                        {{ solution.title }}
                    </span>
                </nav>

                <!-- Main Text -->
                <div class="mt-6 max-w-[640px]" data-reveal>
                    <h1 class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-5xl">
                        {{ solution.title }}
                    </h1>

                    <p
                        class="mt-4 max-w-[560px] text-lg leading-relaxed text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">
                        {{ solution.subtitle }}
                    </p>
                </div>

                <!-- FEATURES -->
                <div class="service-feature-grid mt-auto grid max-w-[780px] grid-cols-1 gap-6 pt-8 sm:grid-cols-3"
                    data-reveal>
                    <div v-for="badge in solution.badges || []" :key="badge.title" class="flex items-start gap-3">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/20">
                            <UiIcon :name="badge.icon" :size="24" />
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-slate-800 dark:text-white">
                                {{ badge.title }}
                            </h4>

                            <p class="mt-1 text-sm leading-snug text-slate-500 dark:text-slate-400">
                                {{ badge.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto w-full max-w-[1600px] space-y-12 px-4 py-10 sm:px-6 lg:px-8 lg:py-14 xl:px-10">
            <!-- ===================== SERVICES ===================== -->
            <section v-reveal class="space-y-6">
                <h2 class="text-sm font-bold tracking-widest uppercase text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600"
                    data-reveal>
                    Our Services
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <article v-for="service in solution.services || []" :key="service.title" @click="openModal(service)"
                        class="group relative flex min-h-[170px] cursor-pointer flex-col justify-between rounded-2xl border border-slate-200/80 bg-white/80 p-5 shadow-sm backdrop-blur transition-all duration-300 hover:-translate-y-1 hover:border-transparent hover:shadow-xl hover:shadow-blue-500/10 dark:border-slate-700/60 dark:bg-[#0c1624]/80 dark:hover:shadow-purple-500/10"
                        data-reveal>
                        <!-- Gradient border on hover -->
                        <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 opacity-0 transition-opacity duration-300 group-hover:opacity-100 -z-10">
                        </div>

                        <div class="absolute inset-[1px] rounded-[11px] bg-white dark:bg-[#0c1624] -z-10"></div>

                        <div class="space-y-4">
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500/10 to-purple-500/10 text-blue-600 dark:from-blue-400/20 dark:to-purple-400/20 dark:text-blue-400">
                                <UiIcon :name="service.icon" :size="20" />
                            </div>

                            <h3 class="text-base font-semibold text-slate-800 dark:text-white">
                                {{ service.title }}
                            </h3>

                            <p class="text-sm leading-relaxed text-slate-500 dark:text-slate-400 line-clamp-3">
                                {{ service.description }}
                            </p>
                        </div>

                        <!-- Bottom arrow -->
                        <div class="mt-5 flex items-center gap-1 text-xs font-semibold text-blue-600">
                            Explore solution
                            <span>➜</span>
                        </div>
                    </article>
                </div>
            </section>

            <!-- ===================== PROCESS + WHY + INDUSTRIES ===================== -->
            <section v-reveal class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <!-- Process -->
                <div class="lg:col-span-6 rounded-2xl border border-slate-200/80 bg-white/70 p-7 shadow-sm backdrop-blur dark:border-slate-700/50 dark:bg-[#0c1624]/70 mt-5 mb-5"
                    data-reveal>
                    <h2
                        class="mb-6 text-sm font-bold tracking-widest uppercase text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                        {{ solution.approach_title || "Our Process" }}
                    </h2>

                    <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 md:grid-cols-6">
                        <div v-for="step in solution.approach_steps || []" :key="step.number"
                            class="flex flex-col items-center text-center space-y-2">
                            <span class="text-xs font-mono text-slate-400">
                                {{ step.number }}
                            </span>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-md shadow-blue-500/25">
                                <UiIcon :name="step.icon" :size="20" />
                            </div>

                            <span class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                {{ step.title }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Why SIS -->
                <div class="lg:col-span-3 rounded-2xl border border-slate-200/80 bg-white/70 p-7 shadow-sm backdrop-blur dark:border-slate-700/50 dark:bg-[#0c1624]/70 mt-5 mb-5"
                    data-reveal>
                    <h2
                        class="mb-5 text-sm font-bold tracking-widest uppercase text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                        {{ solution.why_us_title || "Why SIS?" }}
                    </h2>

                    <ul class="space-y-3">
                        <li
  v-for="(reason, index) in solution.why_us_points || []"
  :key="index"
  class="flex items-start gap-2.5 text-sm text-slate-700 dark:text-slate-300"
>
  <span
    class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-[10px] font-bold text-white"
  >
    <UiIcon :name="getWhyIcon(reason)" :size="14" />
  </span>

  <span>{{ getWhyText(reason) }}</span>
</li>
                    </ul>
                </div>

                <!-- Industries -->
                <div class="lg:col-span-3 rounded-2xl border border-slate-200/80 bg-white/70 p-7 shadow-sm backdrop-blur dark:border-slate-700/50 dark:bg-[#0c1624]/70 mt-5 mb-5"
                    data-reveal>
                    <h2
                        class="mb-5 text-sm font-bold tracking-widest uppercase text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                        {{ solution.industries_title || "Industries We Serve" }}
                    </h2>

                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div
  v-for="(industry, index) in solution.industries || []"
  :key="industry.title || index"
  class="flex flex-col items-center space-y-2"
>
  <div
    class="flex h-10 w-10 items-center justify-center text-transparent bg-clip-text bg-gradient-to-br from-blue-600 to-purple-600"
  >
    <UiIcon
      :name="industry.icon"
      :size="24"
      class="text-blue-600 dark:text-blue-400"
    />
  </div>

  <span
    class="text-[11px] font-medium leading-tight text-slate-700 dark:text-slate-300"
  >
    {{ industry.title }}
  </span>
</div>
                    </div>
                </div>
            </section>

            <!-- ===================== CTA ===================== -->
            <section v-reveal
                class="rounded-2xl border border-slate-200/80 bg-gradient-to-r from-blue-600 to-purple-600 p-6 shadow-lg md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4" data-reveal>
                    <div class="hidden rounded-xl bg-white/20 p-3 text-white sm:block">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-white">
                            {{ solution.cta?.headline || "Ready to Start Your Project?" }}
                        </h3>

                        <p class="mt-1 text-sm text-blue-100">
                            {{
                                solution.cta?.subheadline ||
                            "Let’s discuss how we can help you achieve operational excellence."
                            }}
                        </p>
                    </div>
                </div>

                <button @click="handleContact"
                    class="w-full md:w-auto px-7 py-3.5 bg-white text-blue-700 font-semibold text-sm rounded-xl shadow-md transition hover:bg-blue-50 hover:shadow-lg flex items-center justify-center gap-2">
                    <span>
                        {{ solution.cta?.button_text || "Contact Our Experts" }}
                    </span>

                    <span>→</span>
                </button>
            </section>
        </div>

        <!-- ===================== MODAL ===================== -->
        <Teleport to="body">
            <div v-if="selectedService" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @click.self="closeModal">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

                <!-- Modal content -->
                <div
                    class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl border border-slate-200 bg-white p-7 shadow-[0_25px_90px_rgba(15,23,42,0.25)] dark:border-slate-700 dark:bg-[#0c1624] sm:p-9">
                    <!-- Gradient accent bar -->
                    <div
                        class="absolute left-0 right-0 top-0 h-1.5 rounded-t-3xl bg-gradient-to-r from-blue-500 via-cyan-400 to-purple-600">
                    </div>

                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/25">
                                <UiIcon :name="selectedService.icon" :size="28" />
                            </div>

                            <div>
                                <span
                                    class="font-mono text-[10px] uppercase tracking-[0.2em] text-blue-600 dark:text-cyan-300">
                                    {{ solution.title || "Solution" }}
                                </span>

                                <h3 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">
                                    {{ selectedService.title }}
                                </h3>
                            </div>
                        </div>

                        <button @click="closeModal"
                            class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-200 transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <p class="mt-7 text-base leading-relaxed text-slate-600 dark:text-slate-300">
                        {{ selectedService.description }}
                    </p>

                    <div class="mt-7 grid gap-3 sm:grid-cols-3">
                        <div v-for="detail in getServiceDetails(selectedService)" :key="detail.label"
                            class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-purple-50 p-4 dark:border-white/10 dark:from-blue-950/40 dark:to-purple-950/30">
                            <span
                                class="font-mono text-[10px] uppercase tracking-[0.16em] text-blue-600 dark:text-cyan-300">
                                {{ detail.label }}
                            </span>

                            <p class="mt-2 text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">
                                {{ detail.value }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button @click="closeModal"
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                            Close
                        </button>

                        <button @click="handleContact"
                            class="rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:shadow-lg">
                            Discuss this service <span aria-hidden="true">→</span>
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </main>

    <main v-else
        class="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50/40 to-purple-50/30 font-sans text-slate-800 dark:from-[#020b13] dark:via-[#0a1628] dark:to-[#12081f] dark:text-white mt-20 px-4">
        <div
            class="w-full max-w-md rounded-3xl border border-slate-200 bg-white/80 p-8 text-center shadow-xl backdrop-blur dark:border-slate-700/60 dark:bg-[#0c1624]/80">
            <div
                class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500/10 to-orange-500/10 text-red-500">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>

            <h1 class="mt-6 text-2xl font-bold text-slate-900 dark:text-white">
                Something went wrong
            </h1>

            <p class="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                We couldn't load this page right now. Please try again in a few moments.
            </p>

            <button type="button" @click="retry" :disabled="isRetrying"
                class="mt-7 inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-70 disabled:hover:translate-y-0">
                <svg v-if="isRetrying" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ isRetrying ? 'Retrying...' : 'Try Again' }}
            </button>
        </div>
    </main>

    <!-- Toast Notification -->
    <ToastNotification
        v-if="showToast"
        :title="toast.title"
        :message="toast.message"
        :variant="toast.variant"
        @close="showToast = false"
    />
</template>
