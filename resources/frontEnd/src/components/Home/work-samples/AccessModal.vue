<template>
  <Teleport to="body">
    <transition name="lightbox-fade">
      <div
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="access-modal-title"
        aria-describedby="access-modal-description"
        @click.self="close"
      >
        <div
          ref="modalPanel"
          class="relative w-full max-w-[420px] rounded-2xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] p-8 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)]"
          style="background: var(--bg-card-2)"
        >
          <button
            aria-label="Close"
            class="absolute right-4 top-4 grid h-10 w-10 place-items-center rounded-full text-sis-muted dark:text-[#7688a6] hover:bg-sis-panel dark:hover:bg-white/10 transition-colors"
            @click="close"
          >
            <span class="material-symbols-outlined" style="font-size: 20px">close</span>
          </button>

          <div class="mb-6">
            <div
              class="mb-4 grid h-12 w-12 place-items-center rounded-xl border border-[rgba(126,165,224,0.3)] text-[var(--accent-blue)]"
              style="background: linear-gradient(150deg, rgba(47,155,224,0.2), rgba(47,155,224,0.04))"
            >
              <span class="material-symbols-outlined" style="font-size: 24px">lock</span>
            </div>
            <h3
              id="access-modal-title"
              class="text-xl font-bold text-sis-text dark:text-[#eaf1fb]"
            >
              Unlock work sample
            </h3>
            <p id="access-modal-description" class="mt-2 text-sm text-sis-muted dark:text-[#aebfd8] leading-relaxed">
              This dashboard is protected. Enter the demo credentials to preview
              it, or contact SIS for full access.
            </p>
          </div>

          <form class="space-y-4" @submit.prevent="handleSubmit">
            <div class="group">
              <label
                for="work-sample-username"
                class="block text-sm font-bold text-sis-muted dark:text-[#aebfd8] mb-2 uppercase tracking-wide"
                >Username</label
              >
              <input
                ref="usernameInput"
                id="work-sample-username"
                v-model="username"
                type="text"
                autocomplete="username"
                placeholder="demo"
                required
                class="w-full bg-sis-panel dark:bg-[#0a1324] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] rounded-xl py-3.5 px-4 text-sis-text dark:text-[#eaf1fb] placeholder-sis-muted dark:placeholder-[#7688a6] focus:outline-none focus:border-sis-accent dark:focus:border-[var(--accent-blue)] focus:ring-4 focus:ring-[var(--ring-accent-10)] dark:focus:ring-[var(--ring-blue-10)] transition-all duration-300"
              />
            </div>
            <div class="group">
              <label
                for="work-sample-password"
                class="block text-sm font-bold text-sis-muted dark:text-[#aebfd8] mb-2 uppercase tracking-wide"
                >Password</label
              >
              <input
                id="work-sample-password"
                v-model="password"
                type="password"
                autocomplete="current-password"
                placeholder="demo"
                required
                class="w-full bg-sis-panel dark:bg-[#0a1324] border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] rounded-xl py-3.5 px-4 text-sis-text dark:text-[#eaf1fb] placeholder-sis-muted dark:placeholder-[#7688a6] focus:outline-none focus:border-sis-accent dark:focus:border-[var(--accent-blue)] focus:ring-4 focus:ring-[var(--ring-accent-10)] dark:focus:ring-[var(--ring-blue-10)] transition-all duration-300"
              />
            </div>

            <p v-if="error" role="alert" class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>

            <button
              type="submit"
              :disabled="submitting"
              class="w-full bg-sis-accent dark:bg-[var(--accent-blue)] text-white dark:text-[#04121f] font-bold text-sm py-3.5 rounded-xl flex items-center justify-center gap-2 hover:brightness-110 transition-all duration-300 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span>{{ submitting ? "Verifying..." : "Unlock dashboard" }}</span>
              <span class="material-symbols-outlined" style="font-size: 18px">{{
                submitting ? "hourglass_empty" : "lock_open"
              }}</span>
            </button>

            <button
              type="button"
              class="w-full rounded-xl border border-[var(--border-30)] dark:border-[rgba(126,165,224,0.14)] py-3.5 text-sm font-bold text-sis-accent dark:text-[var(--accent-blue)] hover:bg-sis-panel dark:hover:bg-white/5 transition-colors"
              @click="goContact"
            >
              Contact Us
            </button>

            <p class="text-center text-xs text-sis-muted dark:text-[#7688a6]">
              Demo credentials — username <b>demo</b> · password <b>demo</b>
            </p>
          </form>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import { useAccessStore } from "@/stores/useAccessStore";
import { useModalScrollLock } from "@/utils/scrollLock.js";

useModalScrollLock(true);

const DEMO_USERNAME = "demo";
const DEMO_PASSWORD = "demo";

const emit = defineEmits(["close", "unlocked"]);
const router = useRouter();
const store = useAccessStore();

const usernameInput = ref(null);
const modalPanel = ref(null);
const username = ref("");
const password = ref("");
const error = ref("");
const submitting = ref(false);

function close() {
  emit("close");
}

function onKeydown(e) {
  if (e.key === "Escape") close();
  if (e.key !== "Tab") return;

  const focusable = modalPanel.value?.querySelectorAll(
    'button:not([disabled]), input:not([disabled])',
  );
  if (!focusable?.length) return;

  const first = focusable[0];
  const last = focusable[focusable.length - 1];
  if (e.shiftKey && document.activeElement === first) {
    e.preventDefault();
    last.focus();
  } else if (!e.shiftKey && document.activeElement === last) {
    e.preventDefault();
    first.focus();
  }
}

onMounted(() => {
  window.addEventListener("keydown", onKeydown);
  if (usernameInput.value) usernameInput.value.focus();
});

onBeforeUnmount(() => {
  window.removeEventListener("keydown", onKeydown);
});

async function handleSubmit() {
  error.value = "";
  submitting.value = true;
  await new Promise((resolve) => setTimeout(resolve, 600));
  submitting.value = false;

  if (username.value.trim() === DEMO_USERNAME && password.value === DEMO_PASSWORD) {
    store.unlock();
    emit("unlocked");
  } else {
    error.value = "Invalid username or password.";
  }
}

function goContact() {
  close();
  router.push({ name: "contact" });
}
</script>
