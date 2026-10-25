import { ref, computed } from "vue";
import { router } from "@/router/index.js";
import { adminAPI } from "@/SIS-Dashboard/api/index.js";

// Session period is 60 minutes (3600 seconds) matching backend AuthService Sanctum token: now()->addMinutes(60)
export const DEFAULT_SESSION_DURATION_SEC = 60 * 60; // 3600 seconds = 60 minutes
export const DEFAULT_SESSION_DURATION_MS = DEFAULT_SESSION_DURATION_SEC * 1000;

export const STORAGE_KEYS = {
  TOKEN: "admin_token",
  USER: "admin_user",
  EXPIRY: "admin_token_expiry",
  REFRESH_TOKEN: "admin_refresh_token",
};

let timeoutTimer = null;
let intervalTimer = null;

// Reactive state
export const remainingSeconds = ref(0);
export const isExpired = ref(false);

export function getSessionExpiry() {
  const expiry = localStorage.getItem(STORAGE_KEYS.EXPIRY);
  return expiry ? Number(expiry) : null;
}

export function isSessionExpired() {
  const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
  if (!token) return true;
  const expiry = getSessionExpiry();
  if (!expiry) return false;
  return Date.now() >= expiry;
}

export function saveSession({ access_token, user, expires_in, refresh_token }) {
  const durationSec = Number(expires_in) || DEFAULT_SESSION_DURATION_SEC;
  const expiryTimestamp = Date.now() + durationSec * 1000;

  localStorage.setItem(STORAGE_KEYS.TOKEN, access_token);
  if (user) {
    localStorage.setItem(STORAGE_KEYS.USER, typeof user === "string" ? user : JSON.stringify(user));
  }
  if (refresh_token) {
    localStorage.setItem(STORAGE_KEYS.REFRESH_TOKEN, refresh_token);
  }
  localStorage.setItem(STORAGE_KEYS.EXPIRY, String(expiryTimestamp));

  isExpired.value = false;
  updateRemainingTime();
  startSessionTimer();
}

export function clearSession() {
  localStorage.removeItem(STORAGE_KEYS.TOKEN);
  localStorage.removeItem(STORAGE_KEYS.USER);
  localStorage.removeItem(STORAGE_KEYS.EXPIRY);
  localStorage.removeItem(STORAGE_KEYS.REFRESH_TOKEN);

  if (timeoutTimer) {
    clearTimeout(timeoutTimer);
    timeoutTimer = null;
  }
  if (intervalTimer) {
    clearInterval(intervalTimer);
    intervalTimer = null;
  }
  remainingSeconds.value = 0;
  isExpired.value = true;
}

export async function handleSessionExpired() {
  clearSession();

  try {
    const currentRoute = router.currentRoute.value;
    if (currentRoute?.name !== "admin-login") {
      await router.push({
        name: "admin-login",
        query: { session_expired: "1" },
      });
      return;
    }
  } catch {
    // Fallback if router not yet ready
  }

  if (
    typeof window !== "undefined" &&
    window.location.pathname.includes("/dashboard") &&
    !window.location.pathname.includes("/dashboard/login")
  ) {
    window.location.href = `${import.meta.env.BASE_URL}dashboard/login?session_expired=1`;
  }
}

export async function logout(callApi = true) {
  if (callApi) {
    try {
      await adminAPI.logout();
    } catch {
      // Ignore API logout failures (e.g. if token already invalid)
    }
  }
  clearSession();
  router.push({ name: "admin-login" });
}

export function updateRemainingTime() {
  const expiry = getSessionExpiry();
  if (!expiry) {
    remainingSeconds.value = 0;
    return;
  }
  const diffMs = expiry - Date.now();
  if (diffMs <= 0) {
    remainingSeconds.value = 0;
    isExpired.value = true;
    handleSessionExpired();
  } else {
    remainingSeconds.value = Math.max(0, Math.floor(diffMs / 1000));
    isExpired.value = false;
  }
}

export function startSessionTimer() {
  if (timeoutTimer) clearTimeout(timeoutTimer);
  if (intervalTimer) clearInterval(intervalTimer);

  const expiry = getSessionExpiry();
  if (!expiry) return;

  const diffMs = expiry - Date.now();
  if (diffMs <= 0) {
    handleSessionExpired();
    return;
  }

  // Update immediately
  updateRemainingTime();

  // Tick every second to update reactive countdown
  intervalTimer = setInterval(() => {
    updateRemainingTime();
  }, 1000);

  // Auto-logout trigger at exact expiry
  timeoutTimer = setTimeout(() => {
    handleSessionExpired();
  }, diffMs);
}

export function formatTimeRemaining(totalSec) {
  if (!totalSec || totalSec <= 0) return "00:00";
  const hours = Math.floor(totalSec / 3600);
  const minutes = Math.floor((totalSec % 3600) / 60);
  const seconds = totalSec % 60;

  const pad = (n) => String(n).padStart(2, "0");

  if (hours > 0) {
    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
  }
  return `${pad(minutes)}:${pad(seconds)}`;
}

// Global browser listeners
if (typeof window !== "undefined") {
  // Sync session state across tabs
  window.addEventListener("storage", (e) => {
    if (e.key === STORAGE_KEYS.TOKEN && !e.newValue) {
      clearSession();
      if (router.currentRoute.value?.meta?.requiresAuth) {
        router.push({ name: "admin-login" });
      }
    }
  });

  // Re-check expiry on tab focus or computer wake-up
  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "visible") {
      const token = localStorage.getItem(STORAGE_KEYS.TOKEN);
      if (token && isSessionExpired()) {
        handleSessionExpired();
      } else if (token) {
        updateRemainingTime();
      }
    }
  });
}

export function useSessionManager() {
  const formattedRemainingTime = computed(() =>
    formatTimeRemaining(remainingSeconds.value)
  );

  return {
    remainingSeconds,
    formattedRemainingTime,
    isExpired,
    saveSession,
    clearSession,
    logout,
    startSessionTimer,
    updateRemainingTime,
    isSessionExpired,
    getSessionExpiry,
    DEFAULT_SESSION_DURATION_SEC,
  };
}
