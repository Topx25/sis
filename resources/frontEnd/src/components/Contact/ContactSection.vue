<template>
  <section
    id="contact"
    class="relative border-y border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] py-24 px-6 overflow-hidden"
    style="background: var(--bg-section-2)"
  >
    <div
      class="absolute top-0 right-0 w-[500px] h-[500px] bg-[var(--bg-accent-5)] dark:bg-[var(--accent-blue)]/5 rounded-full blur-3xl pointer-events-none transform translate-x-1/2 -translate-y-1/2"
    ></div>
    <div
      class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-[var(--bg-accent-5)] dark:bg-[var(--accent-blue)]/5 rounded-full blur-3xl pointer-events-none transform -translate-x-1/2 translate-y-1/2"
    ></div>

    <div class="max-w-7xl mx-auto relative z-10">
      <div class="text-center mb-6 reveal reveal-fade-up">
        <span
          class="inline-block text-xs font-bold tracking-[0.2em] uppercase text-sis-accent dark:text-[var(--accent-blue)] mb-3"
          >Get in Touch</span
        >
        <h2
          class="text-xl md:text-xl font-black text-sis-text dark:text-[#eaf1fb] leading-tight mb-4"
        >
          Ready to transform your
          <span class="text-sis-accent dark:text-[var(--accent-blue)]"
            >operations?</span
          >
        </h2>
        <p
          v-if="ready"
          class="text-sis-muted dark:text-[#aebfd8] text-lg max-w-2xl mx-auto"
        >
          {{
            contactInfo?.headline ||
            "Let's discuss how our intelligent OT-IT solutions can elevate your industrial performance."
          }}
        </p>
        <Skeleton v-else class="h-5 w-96 max-w-full rounded-full mx-auto" />
      </div>

      <div class="grid lg:grid-cols-[1fr_1.2fr] gap-8 lg:gap-12 items-start">
        <div
          class="flex flex-col gap-8 reveal reveal-fade-up"
          style="transition-delay: 0.1s"
        >
          <template v-if="ready">
            <div
              class="rounded-xl p-8 border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] hover:shadow-md transition-shadow duration-300"
              style="background: var(--bg-card-2)"
            >
              <h3
                class="text-2xl font-bold text-sis-text dark:text-[#eaf1fb] mb-8"
              >
                Contact Information
              </h3>
              <div class="space-y-8">
                <div class="flex items-start gap-5">
                  <div
                    class="w-14 h-14 rounded-xl bg-[var(--bg-accent-8)] dark:bg-[rgba(var(--accent-blue-r), var(--accent-blue-g), var(--accent-blue-b),0.08)] text-sis-accent dark:text-[var(--accent-blue)] flex items-center justify-center flex-shrink-0"
                  >
                    <span
                      class="material-symbols-outlined"
                      style="font-size: 26px"
                      >language</span
                    >
                  </div>
                  <div>
                    <p
                      class="text-xs font-bold tracking-widest uppercase text-sis-muted dark:text-[#7688a6] mb-1"
                    >
                      Website
                    </p>
                    <a
                      :href="websiteUrl"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="text-lg font-semibold text-sis-text dark:text-[#eaf1fb] hover:text-sis-accent dark:hover:text-[var(--accent-blue)] transition-colors"
                      >{{ contactInfo?.website }}</a
                    >
                  </div>
                </div>
                <div class="flex items-start gap-5">
                  <div
                    class="w-14 h-14 rounded-xl bg-[var(--bg-accent-8)] dark:bg-[rgba(var(--accent-blue-r), var(--accent-blue-g), var(--accent-blue-b),0.08)] text-sis-accent dark:text-[var(--accent-blue)] flex items-center justify-center flex-shrink-0"
                  >
                    <span
                      class="material-symbols-outlined"
                      style="font-size: 26px"
                      >mail</span
                    >
                  </div>
                  <div>
                    <p
                      class="text-xs font-bold tracking-widest uppercase text-sis-muted dark:text-[#7688a6] mb-1"
                    >
                      Email
                    </p>
                    <div class="space-y-1">
                      <a
                        v-for="mail in contactEmails"
                        :key="mail"
                        :href="'mailto:' + mail"
                        class="block text-lg font-semibold text-sis-text dark:text-[#eaf1fb] hover:text-sis-accent dark:hover:text-[var(--accent-blue)] transition-colors"
                        >{{ mail }}</a
                      >
                    </div>
                  </div>
                </div>
                <div class="flex items-start gap-5">
                  <div
                    class="w-14 h-14 rounded-xl bg-[var(--bg-accent-8)] dark:bg-[rgba(var(--accent-blue-r), var(--accent-blue-g), var(--accent-blue-b),0.08)] text-sis-accent dark:text-[var(--accent-blue)] flex items-center justify-center flex-shrink-0"
                  >
                    <span
                      class="material-symbols-outlined"
                      style="font-size: 26px"
                      >call</span
                    >
                  </div>
                  <div>
                    <p
                      class="text-xs font-bold tracking-widest uppercase text-sis-muted dark:text-[#7688a6] mb-1"
                    >
                      Phone
                    </p>
                    <div class="space-y-1">
                      <p
                        v-for="phone in contactInfo?.phones"
                        :key="phone"
                        class="text-lg font-semibold text-sis-text dark:text-[#eaf1fb]"
                      >
                        {{ phone }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <div
              class="rounded-xl p-8 border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)]"
              style="background: var(--bg-card-2)"
            >
              <Skeleton class="h-7 w-48 rounded-full mb-8" />
              <div class="space-y-8">
                <div class="flex items-start gap-5">
                  <Skeleton class="w-14 h-14 rounded-xl flex-shrink-0" />
                  <div class="flex-1 space-y-2">
                    <Skeleton class="h-3 w-16 rounded-full" />
                    <Skeleton class="h-5 w-48 rounded-full" />
                  </div>
                </div>
                <div class="flex items-start gap-5">
                  <Skeleton class="w-14 h-14 rounded-xl flex-shrink-0" />
                  <div class="flex-1 space-y-2">
                    <Skeleton class="h-3 w-16 rounded-full" />
                    <Skeleton class="h-5 w-56 rounded-full" />
                  </div>
                </div>
                <div class="flex items-start gap-5">
                  <Skeleton class="w-14 h-14 rounded-xl flex-shrink-0" />
                  <div class="flex-1 space-y-2">
                    <Skeleton class="h-3 w-16 rounded-full" />
                    <Skeleton class="h-5 w-40 rounded-full" />
                  </div>
                </div>
              </div>
            </div>
          </template>

          <!-- support ticket -->

          <!-- <div class="rounded-xl p-8 text-white relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #1a3a5c, #0e1e34)">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl transform translate-x-8 -translate-y-8 pointer-events-none"></div>
            <h4 class="text-xl font-bold mb-2">Need Technical Support?</h4>
            <p class="text-white/60 text-sm mb-6 leading-relaxed">Our engineering team is available 24/7 for critical infrastructure assistance.</p>
            <a href="#" @click.prevent class="inline-flex items-center gap-2 text-white font-bold text-sm hover:gap-3 hover:text-white/90 transition-all duration-300 active:scale-[0.97]">
              OPEN SUPPORT TICKET
              <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
            </a>
          </div> -->

          <!-- welcoming message -->

          <div
            class="rounded-xl p-8 border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] relative overflow-hidden"
            style="background: var(--bg-card-2)"
          >
            <!-- Decorative Accent -->
            <div
              class="absolute top-0 right-0 w-36 h-36 bg-[var(--bg-accent-8)] dark:bg-[rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.08)] rounded-full blur-3xl translate-x-10 -translate-y-10 pointer-events-none"
            ></div>

            <!-- Icon -->
            <div
              class="w-14 h-14 rounded-xl bg-[var(--bg-accent-8)] dark:bg-[rgba(var(--accent-blue-r),var(--accent-blue-g),var(--accent-blue-b),0.08)] text-sis-accent dark:text-[var(--accent-blue)] flex items-center justify-center mb-6"
            >
              <span class="material-symbols-outlined text-[26px]">
                handshake
              </span>
            </div>

            <h4
              class="text-2xl font-bold text-sis-text dark:text-[#eaf1fb] mb-3"
            >
              {{ contactInfo?.cta_title || "Ready to Transform Your Operations?" }}
            </h4>

            <p class="text-sis-muted dark:text-[#aebfd8] leading-relaxed mb-6">
              {{
                contactInfo?.cta_description ||
                "Whether you're modernizing production, integrating AI into industrial workflows, or planning your digital transformation journey, our specialists are ready to help you build smarter, more connected operations."
              }}
            </p>

            <div
              class="inline-flex items-center gap-2 text-sis-accent dark:text-[var(--accent-blue)] font-semibold"
            >
              <span class="material-symbols-outlined text-[20px]">
                arrow_forward
              </span>
              {{
                contactInfo?.cta_link_text ||
                "Let's build the future of manufacturing together."
              }}
            </div>
          </div>
        </div>

        <template v-if="ready">
          <form
            class="rounded-xl h-full p-10 lg:p-12 border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] reveal reveal-fade-up"
            style="background: var(--bg-card-2); transition-delay: 0.2s"
            @submit.prevent="handleSubmit"
          >
            <div class="mb-10">
              <h3
                class="text-3xl font-black text-sis-text dark:text-[#eaf1fb] mb-3"
              >
                Send us a message
              </h3>
              <p class="text-sis-muted dark:text-[#aebfd8]">
                Fill out the form below and our team will get back to you within
                24 hours.
              </p>
            </div>

            <div class="space-y-6">
              <div class="group">
                <label
                  for="contact-name"
                  class="block text-sm font-bold text-sis-muted dark:text-[#aebfd8] mb-2 uppercase tracking-wide"
                  >Full Name</label
                >
                <div class="relative">
                  <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-sis-muted dark:text-[#7688a6] group-focus-within:text-sis-accent dark:group-focus-within:text-[var(--accent-blue)] transition-colors"
                    >person</span
                  >
                  <input
                    id="contact-name"
                    v-model="form.name"
                    type="text"
                    autocomplete="name"
                    class="w-full bg-sis-panel dark:bg-[#0a1324] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] rounded-xl py-4 pl-12 pr-4 text-sis-text dark:text-[#eaf1fb] placeholder-sis-muted dark:placeholder-[#7688a6] focus:outline-none focus:border-sis-accent dark:focus:border-[var(--accent-blue)] focus:ring-4 focus:ring-[var(--ring-accent-10)] dark:focus:ring-[var(--ring-blue-10)] transition-all duration-300"
                    placeholder="John Doe"
                    required
                  />
                </div>
              </div>

              <div class="group">
                <label
                  for="contact-email"
                  class="block text-sm font-bold text-sis-muted dark:text-[#aebfd8] mb-2 uppercase tracking-wide"
                  >Company Email</label
                >
                <div class="relative">
                  <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-sis-muted dark:text-[#7688a6] group-focus-within:text-sis-accent dark:group-focus-within:text-[var(--accent-blue)] transition-colors"
                    >business_center</span
                  >
                  <input
                    id="contact-email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    class="w-full bg-sis-panel dark:bg-[#0a1324] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] rounded-xl py-4 pl-12 pr-4 text-sis-text dark:text-[#eaf1fb] placeholder-sis-muted dark:placeholder-[#7688a6] focus:outline-none focus:border-sis-accent dark:focus:border-[var(--accent-blue)] focus:ring-4 focus:ring-[var(--ring-accent-10)] dark:focus:ring-[var(--ring-blue-10)] transition-all duration-300"
                    placeholder="john@company.com"
                    required
                  />
                </div>
              </div>

              <div class="group">
                <label
                  for="contact-phone"
                  class="block text-sm font-bold text-sis-muted dark:text-[#aebfd8] mb-2 uppercase tracking-wide"
                  >Phone Number</label
                >
                <div class="relative">
                  <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-sis-muted dark:text-[#7688a6] group-focus-within:text-sis-accent dark:group-focus-within:text-[var(--accent-blue)] transition-colors"
                    >call</span
                  >
                  <input
                    id="contact-phone"
                    v-model="form.phone"
                    type="tel"
                    autocomplete="tel"
                    class="w-full bg-sis-panel dark:bg-[#0a1324] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] rounded-xl py-4 pl-12 pr-4 text-sis-text dark:text-[#eaf1fb] placeholder-sis-muted dark:placeholder-[#7688a6] focus:outline-none focus:border-sis-accent dark:focus:border-[var(--accent-blue)] focus:ring-4 focus:ring-[var(--ring-accent-10)] dark:focus:ring-[var(--ring-blue-10)] transition-all duration-300"
                    placeholder="+20 100 000 0000"
                  />
                </div>
              </div>

              <div class="group">
                <label
                  for="contact-company"
                  class="block text-sm font-bold text-sis-muted dark:text-[#aebfd8] mb-2 uppercase tracking-wide"
                  >Company Name</label
                >
                <div class="relative">
                  <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-sis-muted dark:text-[#7688a6] group-focus-within:text-sis-accent dark:group-focus-within:text-[var(--accent-blue)] transition-colors"
                    >apartment</span
                  >
                  <input
                    id="contact-company"
                    v-model="form.company_name"
                    type="text"
                    autocomplete="organization"
                    class="w-full bg-sis-panel dark:bg-[#0a1324] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] rounded-xl py-4 pl-12 pr-4 text-sis-text dark:text-[#eaf1fb] placeholder-sis-muted dark:placeholder-[#7688a6] focus:outline-none focus:border-sis-accent dark:focus:border-[var(--accent-blue)] focus:ring-4 focus:ring-[var(--ring-accent-10)] dark:focus:ring-[var(--ring-blue-10)] transition-all duration-300"
                    placeholder="Your Company Ltd."
                  />
                </div>
              </div>

              <div class="group">
                <label
                  for="contact-message"
                  class="block text-sm font-bold text-sis-muted dark:text-[#aebfd8] mb-2 uppercase tracking-wide"
                  >Project Details</label
                >
                <textarea
                  id="contact-message"
                  v-model="form.message"
                  rows="5"
                  class="w-full bg-sis-panel dark:bg-[#0a1324] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] rounded-xl p-4 text-sis-text dark:text-[#eaf1fb] placeholder-sis-muted dark:placeholder-[#7688a6] focus:outline-none focus:border-sis-accent dark:focus:border-[var(--accent-blue)] focus:ring-4 focus:ring-[var(--ring-accent-10)] dark:focus:ring-[var(--ring-blue-10)] transition-all duration-300 resize-none"
                  placeholder="Tell us about your requirements, timeline, and goals..."
                  required
                ></textarea>
              </div>

              <button
                type="submit"
                :disabled="submitting"
                class="w-full bg-sis-accent dark:bg-[var(--accent-blue)] text-white dark:text-[#04121f] font-bold text-lg py-4 rounded-xl flex items-center justify-center gap-2 hover:brightness-110 hover:-translate-y-1 hover:shadow-[0_15px_30px_-10px_rgba(var(--accent-blue-r), var(--accent-blue-g), var(--accent-blue-b),0.4)] transition-all duration-300 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0"
              >
                <span>{{ submitting ? "Sending..." : "Send Message" }}</span>
                <span class="material-symbols-outlined" style="font-size: 20px"
                  >send</span
                >
              </button>
              <p
                class="text-center text-xs text-sis-muted dark:text-[#7688a6] mt-4"
              >
                By submitting this form, you agree to our privacy policy and
                terms of service.
              </p>
            </div>
          </form>
        </template>
        <template v-else>
          <div
            class="rounded-xl p-10 lg:p-12 border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)]"
            style="background: var(--bg-card-2)"
          >
            <div class="mb-10 space-y-2">
              <Skeleton class="h-8 w-56 rounded-full" />
              <Skeleton class="h-4 w-72 rounded-full" />
            </div>
            <div class="space-y-6">
              <div class="space-y-2">
                <Skeleton class="h-4 w-24 rounded-full" />
                <Skeleton class="h-14 w-full rounded-xl" />
              </div>
              <div class="space-y-2">
                <Skeleton class="h-4 w-32 rounded-full" />
                <Skeleton class="h-14 w-full rounded-xl" />
              </div>
              <div class="space-y-2">
                <Skeleton class="h-4 w-28 rounded-full" />
                <Skeleton class="h-32 w-full rounded-xl" />
              </div>
              <Skeleton class="h-14 w-full rounded-xl" />
            </div>
          </div>
        </template>

        <ToastNotification
          v-if="showToast"
          :title="toast.title"
          :message="toast.message"
          :variant="toast.variant"
          @close="showToast = false"
        />
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref, reactive, watch, onMounted, onUnmounted } from "vue";
import { storeToRefs } from "pinia";
import { API_ENABLED, useSiteStore } from "@/stores/useSiteStore";
import { api } from "@/api/index.js";
import ToastNotification from "@/components/global/ToastNotification.vue";
import Skeleton from "@/components/common/Skeleton.vue";
import fallbackContact from "@/dummydata/contact";

const siteStore = useSiteStore();
const { contact, loaded } = storeToRefs(siteStore);

const contactInfo = computed(() => contact.value || fallbackContact);

const contactEmails = computed(() => {
  if (Array.isArray(contactInfo.value?.emails) && contactInfo.value.emails.length > 0) {
    return contactInfo.value.emails;
  }
  if (contactInfo.value?.email) {
    return [contactInfo.value.email];
  }
  return ["sales@sis-eg.org"];
});

const ready = ref(false);
let fallbackTimer = null;

onMounted(() => {
  if (loaded.value) {
    ready.value = true;
    if (!siteStore.contactLoaded) {
      siteStore.loadContact();
    }
    return;
  }
  if (!siteStore.loading) {
    siteStore.loadAll();
  }
  fallbackTimer = window.setTimeout(() => {
    ready.value = true;
  }, 8000);
});

watch(
  () => loaded.value,
  (value) => {
    if (value) {
      ready.value = true;
      if (fallbackTimer) window.clearTimeout(fallbackTimer);
    }
  },
);

onUnmounted(() => {
  if (fallbackTimer) window.clearTimeout(fallbackTimer);
});

const websiteUrl = computed(() => {
  const value = contactInfo.value?.website?.trim();
  if (!value) return "#";

  try {
    const url = new URL(
      /^https?:\/\//i.test(value) ? value : `https://${value}`,
    );
    return ["http:", "https:"].includes(url.protocol) ? url.href : "#";
  } catch {
    return "#";
  }
});

const form = reactive({ name: "", email: "", phone: "", company_name: "", message: "" });
const submitting = ref(false);
const showToast = ref(false);
const toast = reactive({
  title: "Message Sent Successfully!",
  message: "Thank you for reaching out. Our team will get back to you within 24 hours.",
  variant: "success",
});

async function handleSubmit() {
  submitting.value = true;
  try {
    if (API_ENABLED) {
    await api.submitContact({
        name: form.name,
        email: form.email,
        phone: form.phone,
        company_name: form.company_name,
        message: form.message,
      });
    } else {
      await new Promise((resolve) => setTimeout(resolve, 800));
    }
    toast.title = "Message Sent Successfully!";
    toast.message = "Thank you for reaching out. Our team will get back to you within 24 hours.";
    toast.variant = "success";
    form.name = "";
    form.email = "";
    form.phone = "";
    form.company_name = "";
    form.message = "";
  } catch (error) {
    toast.title = "Message Not Sent";
    toast.message =
      error?.message || "Something went wrong. Please try again or contact us directly.";
    toast.variant = "error";
  } finally {
    submitting.value = false;
    showToast.value = true;
  }
}
</script>
