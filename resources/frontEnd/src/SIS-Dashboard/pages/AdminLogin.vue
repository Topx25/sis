<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import ToastNotification from "@/components/global/ToastNotification.vue";
import TurnstileWidget from "@/components/common/TurnstileWidget.vue";
import { adminAPI } from "@/SIS-Dashboard/api/index.js";
import { saveSession, isSessionExpired } from "@/SIS-Dashboard/composables/useSessionManager.js";

const router = useRouter();
const route = useRoute();

const email = ref("");
const password = ref("");
const loading = ref(false);
const showToast = ref(false);
const toast = ref({ title: "", message: "", variant: "success" });
const showPassword = ref(false);
const turnstileToken = ref("");

const sisLogoUrl = `${import.meta.env.BASE_URL}sis-logo.png`;

// Check if already logged in or session expired
onMounted(() => {
  const token = localStorage.getItem("admin_token");
  if (token && !isSessionExpired()) {
    router.push({ name: "admin-home" });
    return;
  }

  if (route.query.session_expired === "1") {
    notify(
      "Session Expired",
      "Your session has ended (60 minutes limit reached). Please sign in again.",
      "warning"
    );
  }
});

function notify(title, message, variant) {
  toast.value = { title, message, variant };
  showToast.value = true;
}

async function handleLogin() {
  if (!email.value?.trim() || !password.value?.trim()) {
    notify("Missing fields", "Please enter both email and password.", "error");
    return;
  }

  loading.value = true;
  try {
    const response = await adminAPI.login({
      email: email.value.trim(),
      password: password.value.trim(),
      turnstile_token: turnstileToken.value,
    });

    // Store token and schedule 60-minute session expiry
    saveSession(response);

    notify("Success", `Welcome back, ${response.user.name}!`, "success");

    // Redirect to dashboard home
    setTimeout(() => {
      router.push({ name: "admin-home" });
    }, 500);
  } catch (e) {
    notify("Login failed", e.message || "Invalid credentials.", "error");
    password.value = "";
  } finally {
    loading.value = false;
  }
}

function handleKeydown(event) {
  if (event.key === "Enter" && !loading.value) {
    handleLogin();
  }
}
</script>

<template>
  <div
    class="relative flex min-h-screen items-center justify-center overflow-hidden bg-sis-panel px-4 py-10"
  >
    <!-- Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <!-- Grid -->
      <div
        class="absolute inset-0 opacity-[0.06]"
        style="
          background-image:
            linear-gradient(rgba(62,127,202,0.16) 1px, transparent 1px),
            linear-gradient(90deg, rgba(62,127,202,0.16) 1px, transparent 1px);
          background-size: 42px 42px;
        "
      ></div>

      <!-- Glows -->
      <div
        class="absolute -right-48 -top-48 h-[520px] w-[520px] rounded-full bg-sis-accent/10 blur-[120px]"
      ></div>

      <div
        class="absolute -bottom-48 -left-48 h-[520px] w-[520px] rounded-full bg-sis-light/10 blur-[120px]"
      ></div>

      <div
        class="absolute left-1/2 top-1/2 h-[320px] w-[320px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-sis-accent/5 blur-[100px]"
      ></div>
    </div>

    <!-- Login Wrapper -->
    <div class="relative z-10 w-full max-w-[430px]">

      <!-- Brand -->
      <div class="mb-8 text-center">
        <div
          class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-2xl border border-sis-border bg-white shadow-lg"
        >
          <img
            :src="sisLogoUrl"
            :alt="'SiS Logo'"
            class="h-11 w-11 object-contain"
          />
        </div>

        <h1 class="font-headline-lg text-2xl font-bold tracking-tight text-sis-text">
          Admin Dashboard
        </h1>

        <p class="mt-2 text-sm text-sis-muted">
          Superior Integrated Solutions
        </p>
      </div>

      <!-- Card -->
      <div
        class="relative overflow-hidden rounded-3xl border border-sis-border bg-white shadow-[0_25px_80px_rgba(27,55,86,0.14)]"
      >
        <!-- Top Accent -->
        <div
          class="h-1 w-full bg-gradient-to-r from-sis-primary via-sis-accent to-sis-light"
        ></div>

        <!-- Content -->
        <div class="px-7 py-8 sm:px-9 sm:py-9">

          <!-- Intro -->
          <div class="mb-8">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-sis-accent/10 px-3 py-1.5">
              <span
                class="h-1.5 w-1.5 rounded-full bg-sis-accent"
              ></span>

              <span
                class="text-[11px] font-semibold uppercase tracking-[0.14em] text-sis-accent"
              >
                Secure Access
              </span>
            </div>

            <h2 class="text-2xl font-bold tracking-tight text-sis-text">
              Welcome back
            </h2>

            <p class="mt-2 text-sm leading-6 text-sis-muted">
              Sign in to manage your dashboard and work samples.
            </p>
          </div>

          <!-- Email -->
          <div class="mb-5">
            <label
              class="mb-2.5 block text-xs font-semibold uppercase tracking-wider text-sis-text/70"
            >
              Email Address
            </label>

            <div class="relative">
              <span
                class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-sis-muted"
              >
                mail
              </span>

              <input
                v-model="email"
                type="email"
                placeholder="admin@sis-automation.com"
                :disabled="loading"
                @keydown="handleKeydown"
                class="w-full rounded-xl border border-sis-border bg-sis-panel/30 py-3.5 pl-11 pr-4 text-sm text-sis-text outline-none transition-all placeholder:text-sis-muted/60 hover:border-sis-accent/30 focus:border-sis-accent focus:bg-white focus:ring-4 focus:ring-sis-accent/10 disabled:cursor-not-allowed disabled:opacity-50"
              />
            </div>
          </div>

          <!-- Password -->
          <div class="mb-7">
            <label
              class="mb-2.5 block text-xs font-semibold uppercase tracking-wider text-sis-text/70"
            >
              Password
            </label>

            <div class="relative">
              <span
                class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[19px] text-sis-muted"
              >
                lock
              </span>

              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="••••••••"
                :disabled="loading"
                @keydown="handleKeydown"
                class="w-full rounded-xl border border-sis-border bg-sis-panel/30 py-3.5 pl-11 pr-12 text-sm text-sis-text outline-none transition-all placeholder:text-sis-muted/60 hover:border-sis-accent/30 focus:border-sis-accent focus:bg-white focus:ring-4 focus:ring-sis-accent/10 disabled:cursor-not-allowed disabled:opacity-50"
              />

              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-sis-muted transition-all hover:bg-sis-accent/10 hover:text-sis-accent"
              >
                <span class="material-symbols-outlined text-[19px]">
                  {{ showPassword ? "visibility" : "visibility_off" }}
                </span>
              </button>
            </div>
          </div>

          <div class="flex justify-center">
            <TurnstileWidget v-model="turnstileToken" />
          </div>

          <!-- Login Button -->
          <button
            @click="handleLogin"
            :disabled="loading"
            class="group relative w-full overflow-hidden rounded-xl bg-sis-primary px-4 py-3.5 font-semibold text-white shadow-lg shadow-sis-primary/20 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-sis-primary/30 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
          >
            <!-- Hover Glow -->
            <span
              class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-700 group-hover:translate-x-full"
            ></span>

            <span class="relative flex items-center justify-center gap-2">
              <span
                v-if="loading"
                class="material-symbols-outlined animate-spin text-[19px]"
              >
                progress_activity
              </span>

              <span
                v-else
                class="material-symbols-outlined text-[19px] transition-transform duration-300 group-hover:translate-x-0.5"
              >
                login
              </span>

              {{ loading ? "Signing in..." : "Sign in" }}
            </span>
          </button>

          <!-- Security Note -->
          <div class="mt-6 flex items-center justify-center gap-2 text-xs text-sis-muted">
            <span class="material-symbols-outlined text-[15px]">
              verified_user
            </span>

            <span>Secure administrative access</span>
          </div>
        </div>

        <!-- Footer -->
        <div
          class="border-t border-sis-border bg-sis-panel/20 px-6 py-4 text-center"
        >
          <p class="text-[11px] text-sis-muted">
            © 2026 Superior Integrated Solutions. All rights reserved.
          </p>
        </div>
      </div>
    </div>

    <ToastNotification
      v-if="showToast"
      :title="toast.title"
      :message="toast.message"
      :variant="toast.variant"
      @close="showToast = false"
    />
  </div>
</template>
