import {
  computed,
  ref,
  watch,
} from "vue";

import { adminAPI } from "@/SIS-Dashboard/api/index.js";

const DEFAULT_CAPABILITIES = {
  create: false,
  update: false,
  delete: false,
};

const RESOURCE_CAPABILITIES = {
  services: {
    create: true,
    update: true,
    delete: true,
  },

  sectors: {
    create: false,
    update: false,
    delete: false,
  },

  partners: {
    create: false,
    update: false,
    delete: false,
  },

  clients: {
    create: true,
    update: true,
    delete: true,
  },
};

const ICON_MAP = {
  mind: "psychology",
  ai: "auto_awesome",
  pc: "computer",
  hu: "person",
  oil: "oil_barrel",
  "robot-arm": "precision_manufacturing",
  cpu: "memory",
  connect: "hub",
  pulse: "monitor_heart",
  chart: "bar_chart",
};

const WRITE_FIELDS = {
  services: [
    "title",
    "headline",
    "slug",
    "summary",
    "description",
    "icon",
  ],

  sectors: [
    "name",
    "slug",
    "icon",
    "description",
  ],

  partners: [
    "name",
    "slug",
    "logo_path",
    "website_url",
    "type",
    "sort_order",
  ],

  clients: [
    "name",
    "slug",
    "logo_path",
    "sort_order",
  ],
};

const API_BASE_URL =
  import.meta.env.VITE_API_BASE_URL || "";

function normalizeString(value) {
  return String(value ?? "").trim();
}

function slugify(value) {
  return normalizeString(value)
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

function resolveImageUrl(value) {
  if (!value) return "";

  const raw = String(value).trim();

  if (!raw) return "";

  try {
    const apiUrl = new URL(
      API_BASE_URL || window.location.origin,
    );

    const imageUrl = new URL(
      raw,
      apiUrl.origin,
    );

    const isLocalHost = [
      "localhost",
      "127.0.0.1",
      "::1",
    ].includes(imageUrl.hostname);

    if (isLocalHost) {
      imageUrl.protocol = apiUrl.protocol;
      imageUrl.host = apiUrl.host;
    }

    return imageUrl.href;
  } catch {
    return raw;
  }
}

export function useCollectionManager(
  resource,
  schema,
  notify,
) {
  const loading = ref(true);
  const saving = ref(false);

  const items = ref([]);

  const search = ref("");
  const page = ref(1);

  const perPage = 10;

  const modalOpen = ref(false);
  const editingId = ref(null);
  const form = ref({});

  const confirmState = ref({
    open: false,
    item: null,
    title: "",
    message: "",
  });

  let loadSequence = 0;

  const capabilities = computed(
    () =>
      RESOURCE_CAPABILITIES[resource.value] ||
      DEFAULT_CAPABILITIES,
  );

  const canCreate = computed(
    () => capabilities.value.create,
  );

  const canUpdate = computed(
    () => capabilities.value.update,
  );

  const canDelete = computed(
    () => capabilities.value.delete,
  );

  const filtered = computed(() => {
    const list = Array.isArray(items.value)
      ? items.value
      : [];

    const query = search.value
      .trim()
      .toLowerCase();

    if (!query) return list;

    const fields =
      schema.value?.searchFields || [];

    return list.filter((item) =>
      fields.some((field) =>
        String(item?.[field] ?? "")
          .toLowerCase()
          .includes(query),
      ),
    );
  });

  const pages = computed(() =>
    Math.max(
      1,
      Math.ceil(
        filtered.value.length / perPage,
      ),
    ),
  );

  const paginated = computed(() => {
    const safePage = Math.min(
      Math.max(page.value, 1),
      pages.value,
    );

    const start =
      (safePage - 1) * perPage;

    return filtered.value.slice(
      start,
      start + perPage,
    );
  });

  function getMaterialIcon(
    icon,
    fallback = "star",
  ) {
    if (!icon) return fallback;

    const normalized =
      normalizeString(icon).toLowerCase();

    return (
      ICON_MAP[normalized] ||
      normalized ||
      fallback
    );
  }

  function getItemName(item) {
    if (!item) return "item";

    const primary =
      schema.value?.primary;

    return (
      item?.[primary] ||
      item?.title ||
      item?.name ||
      (item?.id
        ? `#${item.id}`
        : "item")
    );
  }

  function emptyForm() {
    const result = {};

    for (
      const field of
      schema.value?.fields || []
    ) {
      result[field.key] = "";
    }

    return result;
  }

  function openCreate() {
    if (!canCreate.value) return;

    form.value = emptyForm();
    editingId.value = null;
    modalOpen.value = true;
  }

  function openEdit(item) {
    if (!canUpdate.value) return;

    const nextForm = emptyForm();

    for (
      const field of
      schema.value?.fields || []
    ) {
      const sourceKey =
        field.fromKey || field.key;

      nextForm[field.key] =
        item?.[sourceKey] ??
        item?.[field.key] ??
        "";
    }

    form.value = nextForm;
    editingId.value = item?.id ?? null;
    modalOpen.value = true;
  }

  function closeModal() {
    modalOpen.value = false;
    editingId.value = null;
    form.value = {};
  }

  function writePayload(payload) {
    const allowed =
      WRITE_FIELDS[resource.value] || [];

    const clean = {};

    Object.entries(payload || {})
      .forEach(([key, value]) => {
        if (
          value === undefined ||
          value === null ||
          value === ""
        ) {
          return;
        }

        if (
          typeof value === "string" &&
          value.startsWith("blob:")
        ) {
          return;
        }

        clean[key] = value;
      });

    const primary =
      schema.value?.primary;

    if (
      primary &&
      clean[primary] &&
      !clean.slug
    ) {
      clean.slug =
        slugify(clean[primary]);
    }

    return Object.fromEntries(
      allowed
        .filter(
          (key) =>
            clean[key] !== undefined,
        )
        .map((key) => [
          key,
          clean[key],
        ]),
    );
  }

  async function load() {
    const currentSequence =
      ++loadSequence;

    loading.value = true;

    try {
      const list =
        await adminAPI.getItems(
          resource.value,
        );

      if (
        currentSequence !==
        loadSequence
      ) {
        return;
      }

      items.value = Array.isArray(list)
        ? list
        : [];
    } catch (error) {
      if (
        currentSequence !==
        loadSequence
      ) {
        return;
      }

      items.value = [];

      notify(
        "Load failed",
        error?.message ||
          "Could not load items.",
        "error",
      );
    } finally {
      if (
        currentSequence ===
        loadSequence
      ) {
        loading.value = false;
      }
    }
  }

  async function save() {
    if (
      editingId.value !== null
        ? !canUpdate.value
        : !canCreate.value
    ) {
      return;
    }

    const primary =
      schema.value?.primary;

    if (
      primary &&
      !normalizeString(
        form.value?.[primary],
      )
    ) {
      notify(
        "Missing fields",
        `${schema.value?.single} needs a ${primary.replace(
          /_/g,
          " ",
        )}.`,
        "error",
      );

      return;
    }

    saving.value = true;

    try {
      const payload =
        writePayload(form.value);

      if (editingId.value !== null) {
        await adminAPI.updateItem(
          resource.value,
          editingId.value,
          payload,
        );

        notify(
          `${schema.value?.single} updated`,
          "Saved successfully.",
          "success",
        );
      } else {
        await adminAPI.createItem(
          resource.value,
          payload,
        );

        notify(
          `${schema.value?.single} created`,
          "Added successfully.",
          "success",
        );
      }

      closeModal();
      await load();
    } catch (error) {
      notify(
        "Save failed",
        error?.message ||
          "Could not save this item.",
        "error",
      );
    } finally {
      saving.value = false;
    }
  }

  function askRemove(item) {
    if (!canDelete.value) return;

    const name =
      getItemName(item);

    confirmState.value = {
      open: true,
      item,
      title: `Delete ${schema.value?.single}?`,
      message: `"${name}" will be permanently removed. This cannot be undone.`,
    };
  }

  function closeConfirm() {
    confirmState.value = {
      open: false,
      item: null,
      title: "",
      message: "",
    };
  }

  async function onDeleteConfirmed() {
    if (!canDelete.value) {
      closeConfirm();
      return;
    }

    const item =
      confirmState.value.item;

    if (!item?.id) {
      closeConfirm();

      notify(
        "Delete failed",
        "This item does not have a valid ID.",
        "error",
      );

      return;
    }

    const name =
      getItemName(item);

    try {
      await adminAPI.deleteItem(
        resource.value,
        item.id,
      );

      closeConfirm();

      notify(
        `${schema.value?.single} deleted`,
        `"${name}" removed.`,
        "success",
      );

      await load();
    } catch (error) {
      notify(
        "Delete failed",
        error?.message ||
          "Could not delete this item.",
        "error",
      );
    }
  }

  watch(search, () => {
    page.value = 1;
  });

  watch(
    () => filtered.value.length,
    () => {
      if (page.value > pages.value) {
        page.value = pages.value;
      }
    },
  );

  watch(
    resource,
    () => {
      items.value = [];
      page.value = 1;
      search.value = "";
      closeModal();

      if (!schema.value) {
        loading.value = false;
        return;
      }

      load();
    },
    { immediate: true },
  );

  return {
    loading,
    saving,
    items,
    search,
    page,
    pages,
    filtered,
    paginated,

    modalOpen,
    editingId,
    form,

    confirmState,

    canCreate,
    canUpdate,
    canDelete,

    getMaterialIcon,
    getItemName,
    resolveImageUrl,

    openCreate,
    openEdit,
    closeModal,

    save,
    askRemove,
    closeConfirm,
    onDeleteConfirmed,

    load,
  };
}