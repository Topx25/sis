<script setup>
import {
  onBeforeUnmount,
  ref,
  watch,
} from "vue";
import { resolveImageUrl } from "@/utils/imageUrl.js";

const props = defineProps({
  modelValue: {
    type: [String, Object],
    default: "",
  },

  label: {
    type: String,
    default: "Image",
  },

  accept: {
    type: String,
    default: "image/*",
  },
});

const emit = defineEmits([
  "update:modelValue",
]);

const inputRef = ref(null);
const preview = ref("");

let objectUrl = null;
let currentFile = null;

function isFileOrBlob(value) {
  if (!value) return false;
  if (typeof Blob !== "undefined" && value instanceof Blob) return true;
  if (typeof File !== "undefined" && value instanceof File) return true;
  return (
    typeof value === "object" &&
    typeof value.size === "number" &&
    typeof value.type === "string"
  );
}

function revokeObjectUrl() {
  if (!objectUrl) return;

  URL.revokeObjectURL(objectUrl);
  objectUrl = null;
  currentFile = null;
}

function updatePreview(value) {
  if (isFileOrBlob(value)) {
    // If already showing this exact file/blob with an active object URL, keep it
    if (currentFile === value && objectUrl) {
      return;
    }

    revokeObjectUrl();
    currentFile = value;
    objectUrl = URL.createObjectURL(value);
    preview.value = objectUrl;
    return;
  }

  // Not a File/Blob: revoke any previous object URL
  revokeObjectUrl();

  // If a string (persisted URL or empty)
  if (typeof value === "string") {
    // Never persist or treat a raw blob: string as a valid server image
    if (value.startsWith("blob:")) {
      preview.value = "";
      return;
    }
    preview.value = value ? resolveImageUrl(value) : "";
    return;
  }

  preview.value = "";
}

function handleChange(event) {
  const file = event.target.files?.[0];
  if (!file) return;

  // Immediately display the blob preview
  revokeObjectUrl();
  currentFile = file;
  objectUrl = URL.createObjectURL(file);
  preview.value = objectUrl;

  emit(
    "update:modelValue",
    file,
  );
}

function removeImage() {
  revokeObjectUrl();
  preview.value = "";

  if (inputRef.value) {
    inputRef.value.value = "";
  }

  emit(
    "update:modelValue",
    "",
  );
}

function openPicker() {
  inputRef.value?.click();
}

watch(
  () => props.modelValue,
  (val) => {
    updatePreview(val);
  },
  { immediate: true },
);

onBeforeUnmount(() => {
  revokeObjectUrl();
});
</script>

<template>
  <div class="space-y-2">
    <label
      class="block text-sm font-medium text-sis-text"
    >
      {{ label }}
    </label>

    <div
      class="overflow-hidden rounded-xl border border-sis-border bg-sis-panel/40"
    >
      <div
        v-if="preview"
        class="relative aspect-[16/8] w-full overflow-hidden"
      >
        <img
          :src="preview"
          :alt="label"
          class="h-full w-full object-contain"
        />

        <button
          type="button"
          class="absolute right-2 top-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-black/60 text-white transition hover:bg-black/80"
          @click="removeImage"
        >
          <span
            class="material-symbols-outlined text-[18px]"
          >
            close
          </span>
        </button>
      </div>

      <button
        v-else
        type="button"
        class="flex min-h-[150px] w-full flex-col items-center justify-center gap-2 text-sis-muted transition hover:bg-sis-panel hover:text-sis-text"
        @click="openPicker"
      >
        <span
          class="material-symbols-outlined text-[34px]"
        >
          cloud_upload
        </span>

        <span class="text-sm font-medium">
          Choose image
        </span>

        <span class="text-xs text-sis-muted">
          PNG, JPG, WEBP
        </span>
      </button>
    </div>

    <input
      ref="inputRef"
      type="file"
      :accept="accept"
      class="hidden"
      @change="handleChange"
    />

    <button
      v-if="preview"
      type="button"
      class="text-xs font-medium text-sis-accent hover:underline"
      @click="openPicker"
    >
      Change image
    </button>
  </div>
</template>