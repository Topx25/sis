<template>
  <teleport to="body">
    <transition name="modal-fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-[#020b13]/80 px-3 py-5 backdrop-blur-sm"
        @click.self="closeModal"
      >
        <!-- Case Study Modal -->
        <div
          class="relative max-h-[90vh] w-full max-w-5xl overflow-hidden rounded-2xl border border-cyan-500/20 bg-[#061522] text-white shadow-[0_25px_80px_rgba(0,0,0,0.55)]"
        >
          <!-- Header -->
          <div
            class="flex items-center justify-between border-b border-white/10 px-4 py-3 sm:px-6"
          >
            <div>
              <span
                class="text-[9px] font-bold uppercase tracking-[0.18em] text-cyan-400"
              >
                Case Study
              </span>
            </div>

            <button
              type="button"
              class="flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 text-lg text-slate-200 transition hover:bg-white/10 hover:text-white"
              aria-label="Close case study"
              @click="closeModal"
            >
              ×
            </button>
          </div>

          <!-- Content -->
          <div class="max-h-[calc(90vh-64px)] overflow-y-auto">
            <div class="grid gap-0 lg:grid-cols-[1.25fr_0.75fr]">
              <!-- Gallery -->
              <div
                class="border-b border-white/10 p-4 sm:p-6 lg:border-b-0 lg:border-r"
              >
                <!-- Main Image -->
                <div
                  class="flex items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-[#081d2a]"
                >
                  <img
                    v-if="activeImage"
                    :src="activeImage"
                    :alt="project.title"
                    class="h-[240px] w-full object-contain object-center bg-[#081d2a] sm:h-[320px] lg:h-[380px]"
                  />

                  <div
                    v-else
                    class="flex h-[240px] w-full items-center justify-center bg-[#0b1c2b] text-sm text-slate-400 sm:h-[320px] lg:h-[380px]"
                  >
                    No project image available
                  </div>
                </div>

                <!-- Navigation -->
                <div
                  v-if="projectImages.length"
                  class="mt-4 flex items-center justify-between gap-3"
                >
                  <button
                    type="button"
                    class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-[10px] font-medium text-slate-200 transition hover:bg-white/10"
                    @click="showPreviousImage"
                  >
                    Previous
                  </button>

                  <!-- Thumbnails -->
                  <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <button
                      v-for="(image, index) in projectImages"
                      :key="`${project.id}-${index}`"
                      type="button"
                      class="h-12 w-16 overflow-hidden rounded-md border transition sm:h-14 sm:w-20"
                      :class="
                        activeImageIndex === index
                          ? 'border-cyan-400 ring-1 ring-cyan-400'
                          : 'border-white/10 hover:border-white/30'
                      "
                      @click="selectImage(index)"
                    >
                      <img
                        :src="image"
                        :alt="`${project.title} preview ${index + 1}`"
                        class="h-full w-full object-contain object-center bg-[#081d2a] p-0.5"
                      />
                    </button>
                  </div>

                  <button
                    type="button"
                    class="rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-[10px] font-medium text-slate-200 transition hover:bg-white/10"
                    @click="showNextImage"
                  >
                    Next
                  </button>
                </div>

                <!-- More Images CTA -->
                <div
                  v-if="hasMoreImages"
                  class="relative mt-5 overflow-hidden rounded-xl border border-cyan-500/20 bg-gradient-to-br from-cyan-500/[0.08] to-blue-500/[0.03] p-4 sm:p-5"
                >
                  <div
                    class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-cyan-400/10 blur-2xl"
                  ></div>

                  <div class="relative flex items-center gap-4">
                    <div
                      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-cyan-400/20 bg-cyan-400/10 text-cyan-400"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"
                        />
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-1.8 1.8-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.03 1.56V20H12.5v-.1a1.7 1.7 0 0 0-1.03-1.56 1.7 1.7 0 0 0-1.88.34l-.06.06-1.8-1.8.06-.06A1.7 1.7 0 0 0 8.13 15a1.7 1.7 0 0 0-1.56-1.03H6.5v-2.55h.07A1.7 1.7 0 0 0 8.13 10a1.7 1.7 0 0 0-.34-1.88l-.06-.06 1.8-1.8.06.06a1.7 1.7 0 0 0 1.88.34A1.7 1.7 0 0 0 12.5 5.1V5h2.55v.1a1.7 1.7 0 0 0 1.03 1.56 1.7 1.7 0 0 0 1.88-.34l.06-.06 1.8 1.8-.06.06A1.7 1.7 0 0 0 19.4 10a1.7 1.7 0 0 0 1.56 1.03H21v2.55h-.04A1.7 1.7 0 0 0 19.4 15Z"
                        />
                      </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-semibold text-white">
                        Want to see more?
                      </p>

                      <p class="mt-1 text-xs leading-5 text-slate-400">
                        Contact our team to access the complete project
                        gallery.
                      </p>
                    </div>

                    <button
                      type="button"
                      class="shrink-0 rounded-lg bg-cyan-500 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-[#03131d] transition hover:bg-cyan-400"
                      @click="openContactForm"
                    >
                      Request Full Gallery
                    </button>
                  </div>
                </div>
              </div>

              <!-- Project Details -->
              <div class="space-y-5 p-4 sm:p-6">
                <div>
                  <div
                    class="mb-2 text-[9px] font-bold uppercase tracking-[0.18em] text-cyan-400"
                  >
                    {{ project.sector?.name || "Industry" }}
                  </div>

                  <h2
                    class="text-2xl font-bold leading-tight text-white sm:text-[28px]"
                  >
                    {{ project.title }}
                  </h2>

                  <div
  v-if="project.client_name"
  class="mt-2 text-sm text-slate-300"
>
  Client:
  <span class="font-medium text-white">
    {{ project.client_name }}
  </span>
</div>

                  <div
                    v-if="project.location"
                    class="mt-1 text-sm text-slate-300"
                  >
                    Location:
                    <span class="font-medium text-white">
                      {{ project.location }}
                    </span>
                  </div>
                </div>

                <!-- Overview -->
<div>
  <h3
    class="mb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-cyan-400"
  >
    Project Overview
  </h3>

  <p class="text-sm leading-6 text-slate-300">
    {{ project.summary || "No summary provided yet." }}
  </p>

  <p
    v-if="project.description"
    class="mt-3 text-sm leading-6 text-slate-400"
  >
    {{ project.description }}
  </p>
</div>

                <!-- Scope -->
                <div v-if="project.scope && project.scope.length">
                  <h3
                    class="mb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-cyan-400"
                  >
                    Scope
                  </h3>

                  <ul class="space-y-2 text-sm text-slate-300">
                    <li
                      v-for="item in project.scope"
                      :key="item"
                      class="flex gap-2"
                    >
                      <span
                        class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400"
                      ></span>

                      <span>{{ item }}</span>
                    </li>
                  </ul>
                </div>

                <!-- Results -->
                <div v-if="project.results && project.results.length">
                  <h3
                    class="mb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-cyan-400"
                  >
                    Results
                  </h3>

                  <ul class="space-y-2 text-sm text-slate-300">
                    <li
                      v-for="item in project.results"
                      :key="item"
                      class="flex gap-2"
                    >
                      <span
                        class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-400"
                      ></span>

                      <span>{{ item }}</span>
                    </li>
                  </ul>
                </div>

                <!-- Metrics -->
                <div v-if="displayMetrics.length">
                  <h3
                    class="mb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-cyan-400"
                  >
                    Key Metrics
                  </h3>

                  <div class="grid grid-cols-2 gap-2">
                    <div
                      v-for="(metric, index) in displayMetrics"
                      :key="`${project.id}-metric-${index}`"
                      class="rounded-xl border border-white/10 bg-white/5 p-3"
                    >
                      <div class="text-lg font-bold text-cyan-400">
                        {{ metric.value }}
                      </div>

                      <div class="mt-1 text-[10px] leading-4 text-slate-400">
                        {{ metric.label }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ============================================================ -->
          <!-- Contact Form Popup                                            -->
          <!-- ============================================================ -->

          <transition name="contact-popup">
            <div
              v-if="showContactForm"
              class="absolute inset-0 z-50 flex items-center justify-center overflow-y-auto bg-[#020b13]/85 px-4 py-6 backdrop-blur-sm"
              @click.self="closeContactForm"
            >
              <div
                class="relative my-auto w-full max-w-md rounded-2xl border border-cyan-500/20 bg-[#071a28] p-5 text-white shadow-[0_25px_80px_rgba(0,0,0,0.65)] sm:p-6"
              >
                <!-- Close -->
                <button
                  type="button"
                  class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full border border-white/10 bg-white/5 text-slate-300 transition hover:bg-white/10 hover:text-white"
                  aria-label="Close contact form"
                  @click="closeContactForm"
                >
                  ×
                </button>

                <!-- Form Header -->
                <div class="pr-8">
                  <span
                    class="text-[9px] font-bold uppercase tracking-[0.18em] text-cyan-400"
                  >
                    Full Project Gallery
                  </span>

                  <h3 class="mt-2 text-xl font-bold text-white">
                    Want to see more?
                  </h3>

                  <p class="mt-2 text-xs leading-5 text-slate-400">
                    Leave your details and our team will get in touch with you
                    regarding this project.
                  </p>
                </div>

                <!-- Success -->
                <div
                  v-if="submitSuccess"
                  class="mt-5 rounded-xl border border-emerald-400/20 bg-emerald-400/10 p-5 text-center"
                >
                  <div
                    class="mx-auto flex h-11 w-11 items-center justify-center rounded-full border border-emerald-400/20 bg-emerald-400/10 text-lg text-emerald-400"
                  >
                    ✓
                  </div>

                  <h4 class="mt-3 text-sm font-semibold text-white">
                    Request Sent Successfully
                  </h4>

                  <p class="mt-2 text-xs leading-5 text-slate-400">
                    Thank you for your interest. Our team will get in touch
                    with you shortly.
                  </p>

                  <button
                    type="button"
                    class="mt-4 rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-[10px] font-bold uppercase tracking-wide text-slate-200 transition hover:bg-white/10"
                    @click="closeContactForm"
                  >
                    Close
                  </button>
                </div>

                <!-- Form -->
                <form
                  v-else
                  class="mt-5 space-y-3"
                  @submit.prevent="submitContactForm"
                >
                  <!-- Name -->
                  <div>
                    <label
                      for="contact-name"
                      class="mb-1.5 block text-[10px] font-semibold uppercase tracking-wide text-slate-400"
                    >
                      Name
                    </label>

                    <input
                      id="contact-name"
                      v-model="contactForm.name"
                      type="text"
                      required
                      placeholder="Your name"
                      class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-cyan-400/50 focus:bg-white/[0.07]"
                    />
                  </div>

                  <!-- Email -->
                  <div>
                    <label
                      for="contact-email"
                      class="mb-1.5 block text-[10px] font-semibold uppercase tracking-wide text-slate-400"
                    >
                      Email
                    </label>

                    <input
                      id="contact-email"
                      v-model="contactForm.email"
                      type="email"
                      required
                      placeholder="your@email.com"
                      class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-cyan-400/50 focus:bg-white/[0.07]"
                    />
                  </div>

                  <!-- Phone -->
                  <div>
                    <label
                      for="contact-phone"
                      class="mb-1.5 block text-[10px] font-semibold uppercase tracking-wide text-slate-400"
                    >
                      Phone
                    </label>

                    <input
                      id="contact-phone"
                      v-model="contactForm.phone"
                      type="tel"
                      required
                      placeholder="+20 1XX XXX XXXX"
                      class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-cyan-400/50 focus:bg-white/[0.07]"
                    />
                  </div>



                  <!-- Message -->
                  <div>
                    <label
                      for="contact-message"
                      class="mb-1.5 block text-[10px] font-semibold uppercase tracking-wide text-slate-400"
                    >
                      Message
                    </label>

                    <textarea
                      id="contact-message"
                      v-model="contactForm.message"
                      rows="3"
                      placeholder="Tell us what you would like to know..."
                      class="w-full resize-none rounded-lg border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white outline-none placeholder:text-slate-500 transition focus:border-cyan-400/50 focus:bg-white/[0.07]"
                    ></textarea>
                  </div>

                  <!-- Error -->
                  <div
                    v-if="submitError"
                    class="rounded-lg border border-red-400/20 bg-red-400/10 px-3 py-2.5 text-xs leading-5 text-red-300"
                  >
                    {{ submitError }}
                  </div>

                  <!-- Submit -->
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="mt-2 flex w-full items-center justify-center rounded-lg bg-cyan-500 px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-[#03131d] transition hover:bg-cyan-400 disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    {{
                      isSubmitting
                        ? "Sending..."
                        : "Request Full Gallery"
                    }}
                  </button>
                </form>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { api } from "@/api";
import { API_ENABLED } from "@/stores/useSiteStore";
import { useModalScrollLock } from "@/utils/scrollLock.js";

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },

  project: {
   type: Object,
   default: () => ({
     id: "",
     title: "",
     client_name: "",
     location: "",
     summary: "",
     description: "",
     cover_image: "",
     images: [],
     scope: [],
     results: [],
     metrics: [],
     gallery_limit: 4,
   }),
 },
});

const emit = defineEmits(["update:modelValue"]);

useModalScrollLock(() => props.modelValue);

import { resolveImageUrl } from "@/utils/imageUrl.js";

/* -------------------------------------------------------------------------- */
/* Gallery                                                                    */
/* -------------------------------------------------------------------------- */

const activeImageIndex = ref(0);

const projectCoverImage = computed(() => {
  return resolveImageUrl(
    props.project?.cover_image || props.project?.coverImage || "",
  );
});

const displayMetrics = computed(() => {
  const raw = props.project?.metrics;
  if (!Array.isArray(raw)) return [];
  const list = [];
  for (let i = 0; i < raw.length; i++) {
    const curr = raw[i] || {};
    const val = String(curr.value || "").trim();
    const lbl = String(curr.label || "").trim();

    if (val && !lbl && raw[i + 1]) {
      const next = raw[i + 1] || {};
      const nextVal = String(next.value || "").trim();
      const nextLbl = String(next.label || "").trim();
      if (!nextVal && nextLbl) {
        list.push({ value: val, label: nextLbl });
        i++;
        continue;
      }
    }

    if (val || lbl) {
      list.push({ value: val, label: lbl });
    }
  }
  return list;
});

const galleryLimit = computed(() => {
  if (props.project?.gallery_limit && Number(props.project.gallery_limit) > 0) {
    return Number(props.project.gallery_limit);
  }

  const tags = Array.isArray(props.project?.tags)
    ? props.project.tags
    : typeof props.project?.tags === "string"
      ? props.project.tags.split(",")
      : [];

  for (const tag of tags) {
    const trimmed = String(tag).trim();
    if (trimmed.startsWith("gallery_limit:")) {
      const parsed = parseInt(trimmed.replace("gallery_limit:", "").trim(), 10);
      if (!isNaN(parsed) && parsed > 0) return parsed;
    }
  }

  return 4;
});

const allProjectImages = computed(() => {
  const list = [];
  const cover = projectCoverImage.value;
  if (cover) {
    list.push(cover);
  }

  const rawImages = Array.isArray(props.project?.images)
    ? props.project.images
    : [];

  for (const item of rawImages) {
    if (!item) continue;
    const resolved = resolveImageUrl(item);
    if (resolved && !list.includes(resolved)) {
      list.push(resolved);
    }
  }

  return list;
});

const projectImages = computed(() => {
  return allProjectImages.value.slice(0, galleryLimit.value);
});

const hasMoreImages = computed(() => {
  return allProjectImages.value.length > galleryLimit.value;
});

const activeImage = computed(() => {
  if (!projectImages.value.length) return projectCoverImage.value || "";

  return (
    projectImages.value[activeImageIndex.value] ||
    projectImages.value[0] ||
    projectCoverImage.value ||
    ""
  );
});

// Reset active image index whenever the opened project changes or images change
watch(
  () => [props.project?.id, props.modelValue],
  () => {
    activeImageIndex.value = 0;
  },
);

watch(
  () => projectImages.value.length,
  (len) => {
    if (activeImageIndex.value >= len) {
      activeImageIndex.value = 0;
    }
  },
);

/* -------------------------------------------------------------------------- */
/* More Images Contact Form                                                   */
/* -------------------------------------------------------------------------- */

const showContactForm = ref(false);
const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref("");

const contactForm = ref({
  name: "",
  email: "",
  phone: "",

  message: "",
});

/* -------------------------------------------------------------------------- */
/* Modal                                                                      */
/* -------------------------------------------------------------------------- */

function closeModal() {
  emit("update:modelValue", false);
}

function openContactForm() {
  submitSuccess.value = false;
  submitError.value = "";
  showContactForm.value = true;
}

function closeContactForm() {
  if (isSubmitting.value) return;

  showContactForm.value = false;
  submitSuccess.value = false;
  submitError.value = "";
}

/* -------------------------------------------------------------------------- */
/* Gallery Actions                                                            */
/* -------------------------------------------------------------------------- */

function selectImage(index) {
  const total = projectImages.value.length;

  if (!total) return;

  activeImageIndex.value = (index + total) % total;
}

function showPreviousImage() {
  const total = projectImages.value.length;

  if (!total) return;

  activeImageIndex.value =
    (activeImageIndex.value - 1 + total) % total;
}

function showNextImage() {
  const total = projectImages.value.length;

  if (!total) return;

  activeImageIndex.value =
    (activeImageIndex.value + 1) % total;
}

/* -------------------------------------------------------------------------- */
/* Contact Form Submission                                                    */
/* -------------------------------------------------------------------------- */

async function submitContactForm() {
  submitError.value = "";
  submitSuccess.value = false;

  const name = contactForm.value.name.trim();
  const email = contactForm.value.email.trim();
  const phone = contactForm.value.phone.trim();
  const message = contactForm.value.message.trim();

  if (!name || !email || !message) {
    submitError.value =
      "Please fill in your name, email, and message.";

    return;
  }

  isSubmitting.value = true;

  try {
    const payload = {
      name,
      email,
      phone,
      message,

      // Project context
      project_id: props.project?.id,
      project_title: props.project?.title,
    };

    if (API_ENABLED) {
      await api.submitContact(payload);
    } else {
      // Demo / fallback mode
      await new Promise((resolve) => setTimeout(resolve, 800));
    }

    submitSuccess.value = true;

    // Reset form after successful submission
    contactForm.value = {
      name: "",
      email: "",
      phone: "",
      message: "",
    };
  } catch (error) {
    submitError.value =
      error?.message ||
      "Something went wrong. Please try again in a moment.";
  } finally {
    isSubmitting.value = false;
  }
}

/* -------------------------------------------------------------------------- */
/* Watchers                                                                   */
/* -------------------------------------------------------------------------- */

watch(
  () => props.modelValue,
  (isOpen) => {
    if (isOpen) {
      activeImageIndex.value = 0;

      showContactForm.value = false;
      submitSuccess.value = false;
      submitError.value = "";
    }
  },
);

watch(
  () => props.project?.id,
  () => {
    activeImageIndex.value = 0;

    showContactForm.value = false;
    submitSuccess.value = false;
    submitError.value = "";
  },
);
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.contact-popup-enter-active,
.contact-popup-leave-active {
  transition: opacity 0.2s ease;
}

.contact-popup-enter-from,
.contact-popup-leave-to {
  opacity: 0;
}
</style>
