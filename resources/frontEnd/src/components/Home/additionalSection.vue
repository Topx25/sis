<template>
	<section
		id="additional"
		class="relative overflow-hidden border-y border-[var(--border-30)] bg-[linear-gradient(120deg,#eef7ff_0%,#dcecfb_42%,#eadff3_100%)] px-7 py-20 text-sis-text dark:border-white/10 dark:bg-[linear-gradient(120deg,#061827_0%,#092e50_46%,#241d45_100%)] dark:text-white lg:px-12 lg:py-28 xl:px-16"
	>
		<div
			class="pointer-events-none absolute -right-32 top-1/2 h-96 w-96 -translate-y-1/2 rounded-full bg-[var(--accent-blue)]/15 blur-3xl dark:bg-cyan-400/10"
			aria-hidden="true"
		></div>

		<div
			class="relative z-10 mx-auto grid max-w-[1440px] gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:gap-24"
		>
			<!-- Skeleton State -->
			<template v-if="isLoading">
				<div>
					<div class="mb-5 inline-flex items-center gap-4">
						<Skeleton class="h-px w-12 rounded-full lg:w-16" />
						<Skeleton class="h-3 w-16 rounded-full" />
					</div>

					<Skeleton class="mb-4 h-12 w-3/4 rounded-xl" />
					<Skeleton class="h-12 w-1/2 rounded-xl" />

					<div class="mt-8 space-y-5">
						<div
							v-for="n in 4"
							:key="`skel-${n}`"
							class="flex gap-5 py-5 first:pt-0 last:pb-0"
						>
							<Skeleton class="h-4 w-6 shrink-0 rounded" />

							<div class="space-y-2">
								<Skeleton class="h-5 w-48 rounded-full" />
								<Skeleton class="h-4 w-72 rounded-full" />
							</div>
						</div>
					</div>
				</div>

				<div
					class="additional-statement relative overflow-hidden rounded-2xl border border-white/40 bg-white/45 p-8 backdrop-blur-md dark:border-white/15 dark:bg-[#071827]/55 sm:p-12 lg:p-16"
				>
					<Skeleton class="h-3 w-28 rounded-full" />

					<div class="mt-7 space-y-4">
						<Skeleton class="h-10 w-full rounded-xl" />
						<Skeleton class="h-10 w-4/5 rounded-xl" />
						<Skeleton class="h-10 w-3/4 rounded-xl" />
					</div>

					<div class="mt-10 flex items-center gap-3">
						<Skeleton class="h-2 w-2 shrink-0 rounded-full" />
						<Skeleton class="h-4 w-64 rounded-full" />
					</div>
				</div>
			</template>

			<!-- Loaded State -->
			<template v-else>
				<!-- Website characteristics -->
				<div data-reveal>
					<div class="mb-5 inline-flex items-center gap-4">
						<span
							class="h-px w-12 bg-sis-accent lg:w-16"
						></span>

						<span
							class="font-mono text-xs font-semibold uppercase tracking-[0.28em] text-sis-accent"
						>
							Why SiS
						</span>
					</div>

					<!-- Dynamic Section Left Title -->
					<h2
						class="max-w-[34rem] text-[clamp(2rem,4vw,4rem)] font-bold leading-[1.05] tracking-tight text-sis-text dark:text-white"
					>
						{{ localized(section?.left_title) }}
					</h2>

					<!-- Dynamic Features -->
					<div
						class="mt-8 divide-y divide-slate-300/70 dark:divide-white/15"
					>
						<div
							v-for="(characteristic, index) in characteristics"
							:key="characteristic.id ?? characteristic.title"
							class="additional-card group relative flex gap-5 py-5 first:pt-0 last:pb-0"
							:style="{ '--card-delay': `${index * 140}ms` }"
						>
							<span
								class="additional-card__number font-mono text-sm font-semibold text-sis-accent/80 dark:text-cyan-300/80"
							>
								0{{ index + 1 }}
							</span>

							<div class="relative">
								<h3
									class="text-lg font-bold text-sis-text transition-colors group-hover:text-sis-accent dark:text-white dark:group-hover:text-cyan-300"
								>
									{{ characteristic.title }}
								</h3>

								<p
									class="mt-1.5 max-w-[30rem] text-sm leading-relaxed text-sis-muted dark:text-slate-300"
								>
									{{ characteristic.description }}
								</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Dynamic Positioning Statement -->
				<div
					class="additional-statement relative overflow-hidden rounded-2xl border border-white/40 bg-white/45 p-8 shadow-[0_24px_70px_rgba(20,70,120,0.14)] backdrop-blur-md dark:border-white/15 dark:bg-[#071827]/55 dark:shadow-[0_24px_70px_rgba(0,0,0,0.24)] sm:p-12 lg:p-16"
					data-reveal
				>
					<div
						class="additional-statement__sheen pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 -skew-x-12 bg-white/30 blur-2xl dark:bg-cyan-200/10"
						aria-hidden="true"
					></div>

					<div
						class="additional-statement__orbit pointer-events-none absolute right-0 top-0 h-40 w-40 translate-x-1/3 -translate-y-1/3 rounded-full border border-white/40 dark:border-cyan-300/20"
						aria-hidden="true"
					></div>

					<div
						class="additional-statement__orbit additional-statement__orbit--inner pointer-events-none absolute right-10 top-8 h-20 w-20 rounded-full border border-sis-accent/30 dark:border-cyan-300/20"
						aria-hidden="true"
					></div>

					<div
						class="absolute bottom-8 right-10 h-16 w-16 border-b border-r border-sis-accent/60"
						aria-hidden="true"
					></div>

					<!-- Dynamic Subtitle -->
					<span
						class="font-mono text-xs font-semibold uppercase tracking-[0.28em] text-sis-accent dark:text-cyan-300"
					>
						{{ localized(section?.right_subtitle) }}
					</span>

					<!-- Dynamic Main Title -->
					<blockquote
						class="mt-7 max-w-[650px] text-[clamp(1.9rem,3.5vw,3.7rem)] font-semibold leading-[1.08] tracking-tight text-sis-text dark:text-white"
					>
						{{ localized(section?.right_title) }}
					</blockquote>

					<!-- Dynamic Description -->
					<div
						class="mt-10 flex items-center gap-3 text-sm font-semibold text-sis-muted dark:text-slate-300"
					>
						<span
							class="h-2 w-2 rounded-full bg-[var(--accent-teal)] shadow-[0_0_12px_var(--accent-teal)]"
						></span>

						{{ localized(section?.right_description) }}
					</div>
				</div>
			</template>
		</div>
	</section>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { api } from "../../api/index.js";
import { normalizeWhySis } from "../../api/normalizers.js";
import Skeleton from "../common/Skeleton.vue";

const characteristics = ref([]);
const section = ref(null);
const isLoading = ref(true);

/*
 * The public website is English-only.
 *
 * The backend returns localized objects like:
 *
 * {
 *   en: "...",
 *   ar: "..."
 * }
 *
 * We only use the English value.
 */
function localized(value) {
	if (!value) return "";

	if (typeof value === "string") {
		return value;
	}

	return value?.en ?? "";
}

onMounted(async () => {
	try {
		const response = await api.getReason();

		console.log("Why SiS response:", response);

		/*
		 * New API structure:
		 *
		 * {
		 *   section: {...},
		 *   features: [...]
		 * }
		 */
		if (
			response &&
			typeof response === "object" &&
			!Array.isArray(response)
		) {
			section.value = response.section ?? null;

			characteristics.value = Array.isArray(response.features)
				? response.features
				: [];

			return;
		}

		/*
		 * Fallback for the old response structure.
		 */
		if (Array.isArray(response)) {
			characteristics.value = response;
			return;
		}

		throw new Error("Invalid Why SiS response format.");
	} catch (error) {
		console.error("Failed to fetch Why SiS:", error);

		/*
		 * Keep the existing static fallback.
		 */
		const fallback = normalizeWhySis();

		if (Array.isArray(fallback)) {
			characteristics.value = fallback;
		} else {
			characteristics.value = fallback?.features ?? [];
			section.value = fallback?.section ?? null;
		}
	} finally {
		isLoading.value = false;
	}
});
</script>

<style scoped>
.additional-card {
	animation: additional-card-in 700ms cubic-bezier(0.22, 1, 0.36, 1) both;
	animation-delay: var(--card-delay);
	transition: transform 300ms ease;
}

.additional-card::before {
	position: absolute;
	top: 0;
	bottom: 0;
	left: -1.5rem;
	width: 2px;
	content: "";
	background: linear-gradient(
		180deg,
		transparent,
		var(--accent-blue),
		var(--accent-teal),
		transparent
	);
	opacity: 0;
	transform: scaleY(0.3);
	transition:
		opacity 300ms ease,
		transform 300ms ease;
}

.additional-card:hover {
	transform: translateX(0.5rem);
}

.additional-card:hover::before {
	opacity: 1;
	transform: scaleY(1);
}

.additional-card__number {
	transition:
		transform 300ms ease,
		color 300ms ease;
}

.additional-card:hover .additional-card__number {
	transform: translateX(0.25rem);
}

.additional-statement__sheen {
	animation: additional-sheen 7s ease-in-out infinite;
}

.additional-statement__orbit {
	animation: additional-orbit 12s linear infinite;
}

.additional-statement__orbit--inner {
	animation-direction: reverse;
	animation-duration: 8s;
}

@keyframes additional-card-in {
	from {
		opacity: 0;
		transform: translateX(-1.25rem);
	}

	to {
		opacity: 1;
		transform: translateX(0);
	}
}

@keyframes additional-sheen {
	0%,
	100% {
		transform: translateX(-40%) skewX(-12deg);
		opacity: 0;
	}

	35% {
		opacity: 0.7;
	}

	70% {
		transform: translateX(420%) skewX(-12deg);
		opacity: 0;
	}
}

@keyframes additional-orbit {
	from {
		transform: translate(33%, -33%) rotate(0deg);
	}

	to {
		transform: translate(33%, -33%) rotate(360deg);
	}
}

@media (prefers-reduced-motion: reduce) {
	.additional-card,
	.additional-statement__sheen,
	.additional-statement__orbit {
		animation: none;
	}
}
</style>

