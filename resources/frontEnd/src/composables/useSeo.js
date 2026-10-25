import { isRef, onMounted, unref, watch } from "vue";
import { applySeo, DEFAULT_SITE_NAME } from "@/utils/seo.js";
import { useSiteStore } from "@/stores/useSiteStore.js";

/**
 * Vue 3 Composition API hook for reactive SEO metadata management.
 *
 * Supports:
 * - Plain objects: useSeo({ title: "About Us", description: "..." }, 'about')
 * - Computed refs: useSeo(computed(() => ({ title: solution.value?.title })))
 * - Reactive refs: useSeo(seoDataRef)
 * - Getter functions: useSeo(() => ({ title: solution.value?.title }))
 * - Page-specific overrides via optional pageKey: 'home', 'about', 'services', 'projects', 'use-cases', 'partner-ecosystem', 'contact'
 *
 * @param {import('vue').Ref<Object> | Function | Object} metaSource
 * @param {string} [pageKey]
 */
export function useSeo(metaSource, pageKey = null) {
  let siteStore = null;
  try {
    siteStore = useSiteStore();
  } catch {
    // pinia might not be initialized in isolated environments
  }

  function getMetadata() {
    let base = {};
    if (typeof metaSource === "function" && !isRef(metaSource)) {
      base = metaSource() || {};
    } else {
      base = unref(metaSource) || {};
    }

    const key = pageKey || base.pageKey;
    if (key && siteStore?.pagesSeo?.[key]) {
      const override = siteStore.pagesSeo[key];
      return {
        ...base,
        title: override.meta_title || base.title,
        description: override.meta_description || base.description,
        ogTitle: override.og_title || base.ogTitle || override.meta_title || base.title,
        ogDescription: override.og_description || base.ogDescription || override.meta_description || base.description,
        image: override.og_image || base.image || base.ogImage,
        ogImage: override.og_image || base.ogImage || base.image,
        canonical: override.canonical_url || base.canonical,
        keywords: override.meta_keywords || base.keywords || base.meta_keywords,
        robots: override.is_noindex ? "noindex, nofollow" : (override.is_nofollow ? "index, nofollow" : base.robots),
        structuredData: base.structuredData || base.schema,
      };
    }

    return base;
  }

  function update() {
    const meta = getMetadata();
    applySeo(meta);
  }

  // Reactively track changes if metaSource is reactive or a function
  if (isRef(metaSource) || typeof metaSource === "function") {
    watch(
      () => getMetadata(),
      () => {
        update();
      },
      { deep: true, immediate: true },
    );
  } else {
    // Plain object: apply immediately if in mounted lifecycle, otherwise onMounted
    onMounted(() => {
      update();
    });
    // In case SSR or immediate execution is needed
    if (typeof document !== "undefined") {
      update();
    }
  }

  // If pageKey is provided and siteStore exists, watch for dynamic pagesSeo loading
  const effectiveKey = pageKey || (typeof metaSource === "object" ? metaSource?.pageKey : null);
  if (effectiveKey && siteStore) {
    if (!siteStore.pagesSeoLoaded) {
      siteStore.loadPagesSeo().then(() => {
        update();
      });
    }

    watch(
      () => siteStore.pagesSeo?.[effectiveKey],
      () => {
        update();
      },
      { deep: true },
    );
  }

  return {
    update,
  };
}

export { applySeo, DEFAULT_SITE_NAME };
