<template>
    <main
        class="mt-20 min-h-screen bg-sis-panel px-3 py-6 text-[13px] text-sis-text transition-colors duration-200 dark:bg-[#020b13] dark:text-white sm:px-5 lg:px-8">
        <div
            class="mx-auto w-full max-w-[1440px] rounded-[28px] border border-sis-border p-3 shadow-[0_20px_60px_rgba(15,23,42,0.04)] dark:border-cyan-500/15 sm:p-5 lg:p-7 bg-[linear-gradient(105deg,#064985_0%,#0b4f91_25%,#303575_50%,#713064_75%,#b3134d_100%)]">
            <section class="flex flex-row flex-wrap items-center justify-between gap-5">
                <div class="min-w-[260px] flex-1 lg:max-w-[52%]">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="h-px w-5 bg-cyan-400"></span>
                        <span class="text-[9px] font-semibold uppercase tracking-[0.18em] text-cyan-400">
                            Our Work
                        </span>
                    </div>

                    <h1
                        class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-[42px]">
                        {{ pageHeader?.title || 'Projects & Case Studies' }}
                    </h1>

                    <p class="mt-2 max-w-[430px] text-sm leading-6 text-white/80 sm:text-[15px]">
                        {{ pageHeader?.subtitle || 'Real-world automation and digital transformation projects delivered across multiple industries using leading technologies.' }}
                    </p>
                </div>

                <div class="grid w-full max-w-[520px] grid-cols-2 gap-2 sm:grid-cols-4">
                    <div v-for="stat in stats" :key="stat.label"
                        class="rounded-2xl border border-sis-border bg-white/80 px-3 py-3 text-center shadow-[0_10px_25px_rgba(15,23,42,0.04)] ring-1 ring-white/60 backdrop-blur-sm dark:border-cyan-500/20 dark:bg-[#081c2a]/85 dark:shadow-[0_8px_25px_rgba(0,0,0,0.2)]">
                        <div class="flex justify-center text-cyan-600">
                            <UiIcon :name="stat.icon" :size="20" />
                        </div>

                        <div class="mt-1 text-lg font-bold text-sis-text dark:text-white sm:text-xl">
                            {{ stat.value }}
                        </div>

                        <div class="text-[10px] text-sis-muted dark:text-slate-400 sm:text-xs">
                            {{ stat.label }}
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div
            class="mt-5 overflow-hidden rounded-2xl border border-sis-border bg-white/70 shadow-[0_8px_24px_rgba(15,23,42,0.04)] backdrop-blur-sm dark:border-cyan-500/20 dark:bg-[#061522]/80 dark:shadow-[0_10px_30px_rgba(0,0,0,0.18)]">
            <div class="flex gap-1 overflow-x-auto p-1.5 scrollbar-hide">
                <button v-for="category in categories" :key="category" type="button"
                    @click="handleCategorySelect(category)"
                    :aria-pressed="selectedCategory === category"
                    class="shrink-0 rounded-xl px-3 py-2 text-[10px] font-medium transition-all duration-200 sm:text-xs"
                    :class="selectedCategory === category
                        ? 'bg-cyan-400 text-[#03121d] shadow-[0_0_15px_rgba(34,211,238,0.15)]'
                        : 'text-sis-muted hover:bg-slate-200 hover:text-sis-text dark:text-slate-400 dark:hover:bg-white/5 dark:hover:text-white'
                        ">
                    {{ category }}
                </button>

                <div
                    class="ml-auto hidden min-w-[170px] items-center gap-2 rounded-xl border border-sis-border bg-[#f8fafc] px-3 text-sis-muted sm:flex dark:border-white/10 dark:bg-[#04111c] dark:text-slate-500">
                    <UiIcon name="search" :size="14" />

                    <label for="project-search" class="sr-only">Search projects</label>
                    <input id="project-search" v-model="searchQuery" type="search" placeholder="Search projects..."
                        class="w-full border-0 bg-transparent text-[10px] text-sis-text outline-none placeholder:text-sis-muted dark:text-white dark:placeholder:text-slate-600 sm:text-xs" />
                </div>
            </div>
        </div>

        <div class="mt-5">
            <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 xl:grid-cols-4">
                    <template v-if="isLoading">
                        <article v-for="item in 4" :key="item"
                            class="overflow-hidden rounded-[22px] border border-sis-border bg-[#f5f5f5] p-3 dark:border-cyan-500/15 dark:bg-[#061522]">
                            <Skeleton class="aspect-[1.65/1] w-full rounded-xl" />
                            <div class="mt-3 space-y-2">
                                <Skeleton class="h-4 w-3/5 rounded-full" />
                                <Skeleton class="h-3 w-2/5 rounded-full" />
                                <Skeleton class="mt-4 h-3 w-full rounded-full" />
                                <Skeleton class="h-3 w-5/6 rounded-full" />
                            </div>
                        </article>
                    </template>

                    <template v-else>
                    <article v-for="project in displayedProjects" :key="project.id"
                        class="group overflow-hidden rounded-[22px] border border-sis-border bg-[#f5f5f5] shadow-[0_12px_28px_rgba(15,23,42,0.04)] transition-all duration-300 hover:-translate-y-1 hover:border-cyan-400/40 hover:shadow-[0_15px_32px_rgba(14,116,144,0.08)] dark:border-cyan-500/15 dark:bg-[#061522] dark:shadow-[0_12px_30px_rgba(2,6,23,0.2)]">
                        <div class="relative aspect-[1.65/1] overflow-hidden">
                            <img :src="resolveImageUrl(project.cover_image || project.coverImage || project.images?.[0]) || fallbackImage" :alt="project.title"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-[#f5f5f5] via-transparent to-transparent dark:from-[#061522] dark:via-transparent dark:to-transparent">
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3
                                        class="text-sm font-semibold leading-tight text-sis-text transition-colors group-hover:text-cyan-400 dark:text-white sm:text-[15px]">
                                        {{ project.title }}
                                    </h3>

                                    <p class="mt-1 text-[10px] text-sis-muted dark:text-slate-500 sm:text-xs">
                                        {{ project.client }}
                                    </p>
                                </div>

                                <span
                                    class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-2 py-1 text-[8px] font-semibold uppercase tracking-[0.14em] text-cyan-400">
                                    {{ getIndustryTag(project.industry) }}
                                </span>
                            </div>

                            <div
                                class="mt-2 border-t border-sis-border pt-2 text-[10px] leading-[1.6] text-sis-muted dark:border-white/5 dark:text-slate-400 sm:text-xs">


                                <div v-if="project.location" class="mt-1">
                                    <span class="text-sis-text dark:text-slate-600">Location:</span>
                                    {{ project.location }}
                                </div>

                                <div class="mt-2 text-sis-text dark:text-slate-300">
    {{ truncateText(project.description, 120) }}
</div>
                            </div>

                            <button type="button"
                                class="mt-2 flex w-full items-center justify-end gap-1 text-[10px] font-semibold text-cyan-400 sm:text-xs"
                                @click="openCaseStudy(project)">
                                View Case Study
                                <span class="transition-transform group-hover:translate-x-1">→</span>
                            </button>
                        </div>
                    </article>
                    </template>
                </div>

                <div class="mt-4 flex items-center justify-center">
                    <button v-if="canToggleAllProjects" type="button"
                        class="group flex items-center gap-2.5 bg-cyan-400 rounded-full border px-5 py-2.5 text-xs font-semibold text-black transition hover:bg-cyan-400 hover:text-[#02101b] sm:px-6 sm:py-3 sm:text-sm"
                        @click="toggleProjectList">
                        <span>
                            {{ showAllProjects ? 'Show Less' : 'Explore All Projects' }}
                        </span>

                        <span class="transition-transform" :class="showAllProjects ? 'rotate-180' : 'group-hover:translate-x-1'">
                            ↓
                        </span>
                    </button>
                </div>
            </div>

            <div
                class="mt-5 grid grid-cols-2 overflow-hidden rounded-[22px] border border-sis-border bg-[linear-gradient(135deg,_rgba(255,255,255,0.9)_0%,_rgba(240,244,247,0.95)_100%)] shadow-[0_15px_30px_rgba(15,23,42,0.04)] sm:grid-cols-4 dark:border-cyan-500/15 dark:bg-[linear-gradient(135deg,_rgba(6,21,34,0.9)_0%,_rgba(8,28,42,0.95)_100%)] dark:shadow-[0_15px_30px_rgba(2,6,23,0.2)]">                <div v-for="item in valuePoints" :key="item.title"
                    class="flex items-center gap-3 border-b border-sis-border p-4 last:border-0 sm:border-b-0 sm:border-r sm:last:border-r-0 dark:border-white/5">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-400">
                        <UiIcon :name="item.icon" :size="23" />
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-sis-text dark:text-white sm:text-[15px]">
                            {{ item.title }}
                        </h3>

                        <p class="mt-0.5 text-[10px] leading-tight text-sis-muted dark:text-slate-500 sm:text-xs">
                            {{ item.description }}
                        </p>
                    </div>
                </div>
            </div>

        <CaseStudyModal v-model="isModalOpen" :project="selectedProject" />


    </main>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import UiIcon from "@/components/common/UiIcon.vue";
import Skeleton from "@/components/common/Skeleton.vue";
import CaseStudyModal from "@/components/case-study-modal/CaseStudyModal.vue";
import { api } from "@/api";
import { normalizeUseCases } from "@/api/normalizers";
import { resolveImageUrl } from "@/utils/imageUrl.js";
import { useSeo } from "@/composables/useSeo.js";

useSeo({
    title: "Use Cases | SiS",
    description: "Real-world automation and digital transformation projects delivered across multiple industries using leading technologies.",
    canonical: "/use-cases",
}, "use-cases");

const selectedCategory = ref("All Projects");
const searchQuery = ref("");
const showAllProjects = ref(false);
const isModalOpen = ref(false);
const isLoading = ref(true);

const projects = ref([]);
const selectedProject = ref(null);
const featuredProject = ref(null);
const pageHeader = ref(null);
const stats = ref([]);
const valuePoints = ref([]);

const fallbackImage = `${import.meta.env.BASE_URL}images/industry-oil-gas.webp`;

function normalizeIndustryLabel(value) {
    if (!value) return "";

    const normalized = value.trim();

    const canonicalMap = {
        "water & wastewater": "Water & Wastewater",
        "food & beverage": "Food, Beverage & FMCG",
        "food, beverage & fmcg": "Food, Beverage & FMCG",
    };

    return canonicalMap[normalized.toLowerCase()] || normalized;
}

const categories = computed(() => {
    const values = projects.value
        .map((project) => normalizeIndustryLabel(project.industry))
        .filter(Boolean);

    return ["All Projects", ...new Set(values)];
});

const featuredMetrics = computed(() => {
    return featuredProject.value?.metrics || [];
});

const filteredProjects = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();

    return projects.value.filter((project) => {
        const matchesCategory =
            selectedCategory.value === "All Projects" ||
            normalizeIndustryLabel(project.industry) ===
                normalizeIndustryLabel(selectedCategory.value);

        const matchesSearch =
            !query ||
            project.title.toLowerCase().includes(query) ||
            project.client.toLowerCase().includes(query) ||
            normalizeIndustryLabel(project.industry)
                .toLowerCase()
                .includes(query) ||
            (project.location || "").toLowerCase().includes(query);

        return matchesCategory && matchesSearch;
    });
});

const displayedProjects = computed(() => {
    if (
        !showAllProjects.value &&
        selectedCategory.value === "All Projects" &&
        !searchQuery.value.trim()
    ) {
        return filteredProjects.value.slice(0, 4);
    }

    return filteredProjects.value;
});

const canToggleAllProjects = computed(() => {
    return (
        filteredProjects.value.length > 4 &&
        selectedCategory.value === "All Projects" &&
        !searchQuery.value.trim()
    );
});

function handleCategorySelect(category) {
    selectedCategory.value = category;

    if (category !== "All Projects") {
        showAllProjects.value = true;
    } else {
        showAllProjects.value = false;
    }
}


onMounted(async () => {
    try {
        const response = await api.getUseCases();

        projects.value = response.projects || [];
        featuredProject.value = response.featured_project || null;
        pageHeader.value = response.header || null;
        stats.value = Array.isArray(response.header?.stats) && response.header.stats.length
            ? response.header.stats
            : [];
        valuePoints.value = Array.isArray(response.pillars) && response.pillars.length
            ? response.pillars
            : (response.header?.pillars || []);

        selectedProject.value = response.featured_project || null;
    } catch (error) {
        console.error("Failed to fetch use cases:", error);

        const data = normalizeUseCases();

        projects.value = data.projects;
        featuredProject.value = data.featured_project;
        pageHeader.value = data.header;
        stats.value = data.header?.stats || [];
        valuePoints.value = data.pillars || [];

        selectedProject.value = data.featured_project;
    } finally {
        isLoading.value = false;
    }
});

function toggleProjectList() {
    showAllProjects.value = !showAllProjects.value;
}

function openCaseStudy(project) {
    selectedProject.value = project;
    isModalOpen.value = true;
}

function getIndustryTag(industry) {
    if (!industry) return "Project";

    return industry
        .split(" ")
        .slice(0, 2)
        .join(" ")
        .replace(/&/g, "")
        .trim();
}

function truncateText(text, maxLength) {
    if (!text) return "No description provided yet.";

    if (text.length <= maxLength) return text;

    return `${text.slice(0, maxLength).trim()}...`;
}
</script>

