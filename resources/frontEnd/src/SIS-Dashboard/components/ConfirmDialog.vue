<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-[1000] flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
        @click="cancel"
      ></div>
      <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">
        <div class="flex items-start gap-4 px-6 pt-6">
          <span
            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full"
            :class="
              danger
                ? 'bg-red-500/10 text-red-500'
                : 'bg-sis-accent/10 text-sis-accent'
            "
          >
            <span class="material-symbols-outlined text-[22px]">{{ icon }}</span>
          </span>
          <div class="min-w-0 flex-1">
            <h3 class="font-headline-md text-base font-semibold text-sis-text">
              {{ title }}
            </h3>
            <p class="mt-1 text-sm leading-relaxed text-sis-muted">
              {{ message }}
            </p>
          </div>
          <button
            @click="cancel"
            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sis-muted transition-colors hover:bg-sis-panel hover:text-sis-text"
          >
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>
        <div class="flex justify-end gap-3 px-6 py-5">
          <button
            @click="cancel"
            class="rounded-lg border border-sis-border bg-white px-4 py-2 text-sm font-medium text-sis-muted transition-colors hover:bg-sis-panel hover:text-sis-text"
          >
            {{ cancelText }}
          </button>
          <button
            @click="confirm"
            :class="
              danger
                ? 'bg-red-500 text-white hover:bg-red-600'
                : 'bg-sis-accent text-white hover:bg-sis-accent/90'
            "
            class="rounded-lg px-4 py-2 text-sm font-semibold transition-colors"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { onBeforeUnmount, watch } from "vue";
import { useModalScrollLock } from "@/utils/scrollLock.js";

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: "Confirm" },
  message: { type: String, default: "Are you sure?" },
  confirmText: { type: String, default: "Confirm" },
  cancelText: { type: String, default: "Cancel" },
  danger: { type: Boolean, default: false },
  icon: { type: String, default: "delete" },
});

const emit = defineEmits(["update:modelValue", "confirm", "cancel"]);

useModalScrollLock(() => props.modelValue);

function close() {
  emit("update:modelValue", false);
}

function cancel() {
  close();
  emit("cancel");
}

function confirm() {
  emit("confirm");
  close();
}

function onKeydown(e) {
  if (e.key === "Escape" && props.modelValue) cancel();
}

watch(
  () => props.modelValue,
  (open) => {
    if (open) window.addEventListener("keydown", onKeydown);
    else window.removeEventListener("keydown", onKeydown);
  },
);

onBeforeUnmount(() => window.removeEventListener("keydown", onKeydown));
</script>
