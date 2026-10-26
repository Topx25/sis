<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";

/**
 * Cloudflare Turnstile widget. Self-configuring: reads /site-config and only
 * renders when protection is enabled and a site key is set. Emits the token
 * via v-model; stays invisible (renders nothing) when disabled.
 */
const emit = defineEmits(["update:modelValue", "verified"]);

const API_BASE = (import.meta.env.VITE_API_BASE_URL || "").replace(/\/+$/, "");
const SCRIPT_SRC =
  "https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit";

const enabled = ref(false);
const siteKey = ref("");
const containerRef = ref(null);
let widgetId = null;

function loadScript() {
  return new Promise((resolve, reject) => {
    if (window.turnstile) return resolve();

    const existing = document.querySelector(
      'script[src^="https://challenges.cloudflare.com/turnstile"]',
    );
    if (existing) {
      existing.addEventListener("load", () => resolve());
      existing.addEventListener("error", reject);
      if (window.turnstile) resolve();
      return;
    }

    const s = document.createElement("script");
    s.src = SCRIPT_SRC;
    s.async = true;
    s.defer = true;
    s.onload = () => resolve();
    s.onerror = reject;
    document.head.appendChild(s);
  });
}

function renderWidget() {
  if (!window.turnstile || !containerRef.value || !siteKey.value) return;
  widgetId = window.turnstile.render(containerRef.value, {
    sitekey: siteKey.value,
    callback: (token) => {
      emit("update:modelValue", token);
      emit("verified", token);
    },
    "expired-callback": () => emit("update:modelValue", ""),
    "error-callback": () => emit("update:modelValue", ""),
  });
}

onMounted(async () => {
  try {
    const res = await fetch(`${API_BASE}/site-config`, {
      headers: { Accept: "application/json" },
    });
    const json = await res.json();
    const t = json?.data?.turnstile || {};
    enabled.value = !!t.enabled && !!t.site_key;
    siteKey.value = t.site_key || "";

    if (enabled.value) {
      await loadScript();
      if (!window.turnstile) {
        await new Promise((r) => setTimeout(r, 400));
      }
      renderWidget();
    }
  } catch {
    // Fail silently — no captcha shown, backend stays the source of truth.
  }
});

onBeforeUnmount(() => {
  try {
    if (widgetId && window.turnstile) window.turnstile.remove(widgetId);
  } catch {
    /* ignore */
  }
});

defineExpose({ enabled });
</script>

<template>
  <div v-if="enabled" ref="containerRef" class="my-3"></div>
</template>
