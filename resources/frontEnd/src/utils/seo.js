/**
 * Centralized SEO & Head Metadata Utility for SiS (Vue 3 / Vite SPA)
 *
 * Provides SSR-safe, reactive, and standardized helpers for managing:
 * - <title>
 * - <meta name="description">
 * - <meta name="robots">
 * - <link rel="canonical">
 * - Open Graph tags (og:title, og:description, og:image, og:url, og:type, og:site_name)
 * - Twitter Card tags (twitter:card, twitter:title, twitter:description, twitter:image)
 * - Dynamic html lang and dir attributes
 */

export const DEFAULT_SITE_NAME =
  import.meta.env.VITE_SITE_NAME || "SiS - Superior Integrated Solutions";

export const DEFAULT_SITE_DESCRIPTION =
  import.meta.env.VITE_SITE_DESCRIPTION ||
  "Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and digital transformation.";

export const DEFAULT_ROBOTS =
  "index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1";

export const PRIVATE_ROBOTS = "noindex, nofollow, noarchive";

/**
 * Resolve runtime origin in an SSR-safe manner.
 * Does not hardcode unverified domains; derives from browser origin or environment.
 */
export function resolveOrigin() {
  if (typeof window !== "undefined" && window.location?.origin) {
    return window.location.origin;
  }
  if (import.meta.env.VITE_SITE_URL) {
    return String(import.meta.env.VITE_SITE_URL).replace(/\/+$/, "");
  }
  return "";
}

/**
 * Strip HTML tags and normalize whitespace for clean meta descriptions.
 */
export function stripHtml(input) {
  if (!input || typeof input !== "string") return "";
  return input
    .replace(/<[^>]*>/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

/**
 * Format document title following SiS brand conventions.
 * Pattern: "{Title} | SiS" or default site name if already branded.
 */
export function formatTitle(title, brandSuffix = " | SiS") {
  if (!title || typeof title !== "string") {
    return DEFAULT_SITE_NAME;
  }
  const clean = title.trim();
  if (!clean) return DEFAULT_SITE_NAME;

  // Recognize "SiS" and "Superior Integrated Solutions" as distinct brand phrases with word boundaries
  if (/\b(SiS|Superior Integrated Solutions)\b/i.test(clean)) {
    return clean;
  }
  return `${clean}${brandSuffix}`;
}

/**
 * Normalize and construct a clean canonical URL.
 * Automatically strips tracking/filter query params and hashes by default.
 */
export function resolveCanonicalUrl(pathOrUrl) {
  const origin = resolveOrigin();

  if (!pathOrUrl || typeof pathOrUrl !== "string") {
    if (typeof window !== "undefined" && window.location?.pathname) {
      pathOrUrl = window.location.pathname;
    } else {
      pathOrUrl = "/";
    }
  }

  try {
    // If already absolute URL
    if (/^https?:\/\//i.test(pathOrUrl)) {
      const parsed = new URL(pathOrUrl);
      const cleanPath =
        parsed.pathname.length > 1
          ? parsed.pathname.replace(/\/+$/, "")
          : parsed.pathname;
      return `${parsed.origin}${cleanPath}`;
    }

    // Relative path
    const pathOnly = pathOrUrl.split("?")[0].split("#")[0] || "/";
    const cleanPath =
      pathOnly.length > 1 ? pathOnly.replace(/\/+$/, "") : pathOnly;
    const formattedPath = cleanPath.startsWith("/") ? cleanPath : `/${cleanPath}`;

    return origin ? `${origin}${formattedPath}` : formattedPath;
  } catch {
    return pathOrUrl;
  }
}

/**
 * Resolve an image URL to an absolute URL suitable for Open Graph / Twitter cards.
 */
export function resolveImageUrl(imagePath) {
  const origin = resolveOrigin();
  const basePath = import.meta.env.BASE_URL || "/";
  const defaultFallback = `${origin}${basePath.endsWith("/") ? basePath : `${basePath}/`}sis-logo.png`;

  if (!imagePath || typeof imagePath !== "string") {
    return defaultFallback;
  }

  const clean = imagePath.trim();
  if (!clean) return defaultFallback;

  // Upgrade insecure http to https where applicable
  if (clean.startsWith("http://")) {
    return clean.replace("http://", "https://");
  }
  if (clean.startsWith("https://")) {
    return clean;
  }

  // Join relative path with origin
  const normalizedPath = clean.startsWith("/") ? clean : `/${clean}`;
  return origin ? `${origin}${normalizedPath}` : normalizedPath;
}

/**
 * SSR-safe helper to update or insert a <meta> element in <head>.
 */
function setMetaTag(attributeName, attributeValue, content) {
  if (typeof document === "undefined") return;

  const selector = `meta[${attributeName}="${attributeValue}"]`;
  let element = document.head.querySelector(selector);

  if (content === null || content === undefined || content === "") {
    if (element) element.remove();
    return;
  }

  if (!element) {
    element = document.createElement("meta");
    element.setAttribute(attributeName, attributeValue);
    document.head.appendChild(element);
  }
  element.setAttribute("content", String(content));
}

/**
 * SSR-safe helper to update or insert a <link> element in <head>.
 */
function setLinkTag(rel, href, attributes = {}) {
  if (typeof document === "undefined") return;

  let selector = `link[rel="${rel}"]`;
  if (attributes.hreflang) {
    selector += `[hreflang="${attributes.hreflang}"]`;
  }

  let element = document.head.querySelector(selector);

  if (!href) {
    if (element) element.remove();
    return;
  }

  if (!element) {
    element = document.createElement("link");
    element.setAttribute("rel", rel);
    document.head.appendChild(element);
  }
  element.setAttribute("href", String(href));
  Object.entries(attributes).forEach(([k, v]) => {
    if (v !== undefined && v !== null) {
      element.setAttribute(k, String(v));
    }
  });
}

/**
 * Centralized function to apply metadata to the active document.
 * Safely updates title, meta description, robots, canonical, OG, Twitter, and html lang/dir.
 *
 * @param {Object} meta
 * @param {string} [meta.title]
 * @param {string} [meta.description]
 * @param {string} [meta.image]
 * @param {string} [meta.canonical]
 * @param {string} [meta.robots]
 * @param {string} [meta.type]
 * @param {string} [meta.siteName]
 * @param {string} [meta.ogTitle]
 * @param {string} [meta.ogDescription]
 * @param {string} [meta.ogImage]
 * @param {string} [meta.twitterTitle]
 * @param {string} [meta.twitterDescription]
 * @param {string} [meta.twitterImage]
 * @param {string} [meta.twitterCard]
 */
export function applySeo(meta = {}) {
  if (typeof document === "undefined") return;

  // 1. Title
  const resolvedTitle = formatTitle(meta.title);
  document.title = resolvedTitle;

  // 2. Meta Description
  const rawDescription = meta.description || DEFAULT_SITE_DESCRIPTION;
  const resolvedDescription = stripHtml(rawDescription).slice(0, 160);
  setMetaTag("name", "description", resolvedDescription);

  // 2.1 Meta Keywords
  const rawKeywords = meta.keywords || meta.meta_keywords || "";
  const resolvedKeywords = Array.isArray(rawKeywords) ? rawKeywords.join(", ") : String(rawKeywords).trim();
  setMetaTag("name", "keywords", resolvedKeywords);

  // 3. Robots
  const resolvedRobots = meta.robots || DEFAULT_ROBOTS;
  setMetaTag("name", "robots", resolvedRobots);

  // 4. Canonical URL
  const resolvedCanonical = resolveCanonicalUrl(meta.canonical);
  setLinkTag("canonical", resolvedCanonical);

  // 5. Open Graph
  const ogTitle = meta.ogTitle ? formatTitle(meta.ogTitle) : resolvedTitle;
  const ogDescription = meta.ogDescription
    ? stripHtml(meta.ogDescription).slice(0, 200)
    : resolvedDescription;
  const ogImage = resolveImageUrl(meta.ogImage || meta.image);
  const ogType = meta.type || "website";
  const siteName = meta.siteName || DEFAULT_SITE_NAME;

  setMetaTag("property", "og:title", ogTitle);
  setMetaTag("property", "og:description", ogDescription);
  setMetaTag("property", "og:image", ogImage);
  setMetaTag("property", "og:url", resolvedCanonical);
  setMetaTag("property", "og:type", ogType);
  setMetaTag("property", "og:site_name", siteName);

  // 6. Twitter Card
  const twitterCard = meta.twitterCard || "summary_large_image";
  const twitterTitle = meta.twitterTitle
    ? formatTitle(meta.twitterTitle)
    : ogTitle;
  const twitterDescription = meta.twitterDescription
    ? stripHtml(meta.twitterDescription).slice(0, 200)
    : ogDescription;
  const twitterImage = resolveImageUrl(
    meta.twitterImage || meta.ogImage || meta.image,
  );

  setMetaTag("name", "twitter:card", twitterCard);
  setMetaTag("name", "twitter:title", twitterTitle);
  setMetaTag("name", "twitter:description", twitterDescription);
  setMetaTag("name", "twitter:image", twitterImage);

  // 7. Backward Compatibility for [data-runtime-url] and [data-runtime-image] attributes
  document.querySelectorAll("[data-runtime-url]").forEach((element) => {
    const attribute = element.tagName === "LINK" ? "href" : "content";
    element.setAttribute(attribute, resolvedCanonical);
  });
  const runtimeOg = document.querySelector(
    'meta[property="og:image"][data-runtime-image]',
  );
  if (runtimeOg) runtimeOg.setAttribute("content", ogImage);

  const runtimeTwitter = document.querySelector(
    'meta[name="twitter:image"][data-runtime-image]',
  );
  if (runtimeTwitter) runtimeTwitter.setAttribute("content", twitterImage);

  // 8. Alternate Hreflang Links
  setLinkTag("alternate", resolvedCanonical, { hreflang: "en" });
  setLinkTag("alternate", resolvedCanonical, { hreflang: "x-default" });

  // 9. Structured Data (JSON-LD)
  if (meta.structuredData || meta.schema) {
    setJsonLd("primary", meta.structuredData || meta.schema);
  } else if (meta.breadcrumbs && Array.isArray(meta.breadcrumbs)) {
    setJsonLd("breadcrumbs", buildBreadcrumbsSchema(meta.breadcrumbs, resolveOrigin()));
  }

  // 10. Language & Text Direction
  const lang = meta.lang || "en";
  const dir = meta.dir || (lang === "ar" ? "rtl" : "ltr");
  document.documentElement.setAttribute("lang", lang);
  document.documentElement.setAttribute("dir", dir);
}

/**
 * SSR-safe helper to update or insert a structured data <script type="application/ld+json"> element.
 */
export function setJsonLd(id, data) {
  if (typeof document === "undefined") return;

  const scriptId = `sis-schema-${id}`;
  let script = document.head.querySelector(`script#${scriptId}`);

  if (!data) {
    if (script) script.remove();
    return;
  }

  if (!script) {
    script = document.createElement("script");
    script.setAttribute("type", "application/ld+json");
    script.setAttribute("id", scriptId);
    document.head.appendChild(script);
  }

  script.textContent = typeof data === "string" ? data : JSON.stringify(data);
}

/**
 * Remove all managed JSON-LD scripts
 */
export function clearJsonLd() {
  if (typeof document === "undefined") return;
  document.head.querySelectorAll('script[type="application/ld+json"][id^="sis-schema-"]').forEach((s) => s.remove());
}

/**
 * Standard Schema.org Organization + WebSite Graph builder
 */
export function buildOrganizationSchema(baseUrl = resolveOrigin()) {
  const origin = baseUrl || resolveOrigin();
  return {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Corporation",
        "@id": `${origin}/#organization`,
        name: "SiS - Superior Integrated Solutions",
        legalName: "Superior Integrated Solutions S.A.E.",
        url: origin,
        logo: {
          "@type": "ImageObject",
          "@id": `${origin}/#logo`,
          url: `${origin}/sis-logo.png`,
          caption: "SiS Logo",
        },
        image: `${origin}/sis-logo.png`,
        description: "Provider of end-to-end industrial automation, process control, OT cybersecurity, and industrial AI solutions.",
        email: "sales@sis-holding.com",
        telephone: "+20 1007 568 651",
        address: {
          "@type": "PostalAddress",
          addressLocality: "Cairo",
          addressCountry: "EG",
        },
        sameAs: [
          "https://www.linkedin.com/company/superior-for-integrated-solutions",
        ],
      },
      {
        "@type": "WebSite",
        "@id": `${origin}/#website`,
        url: origin,
        name: "SiS Superior Integrated Solutions",
        publisher: {
          "@id": `${origin}/#organization`,
        },
        inLanguage: ["en", "ar"],
      },
    ],
  };
}

/**
 * Standard Schema.org BreadcrumbList builder
 * @param {Array<{name: string, path: string}>} items
 * @param {string} baseUrl
 */
export function buildBreadcrumbsSchema(items = [], baseUrl = resolveOrigin()) {
  const origin = baseUrl || resolveOrigin();
  return {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    itemListElement: items.map((item, index) => ({
      "@type": "ListItem",
      position: index + 1,
      name: item.name,
      item: item.path.startsWith("http") ? item.path : `${origin}${item.path.startsWith("/") ? "" : "/"}${item.path}`,
    })),
  };
}

/**
 * Standard Schema.org Service builder for Solutions
 */
export function buildServiceSchema({ name, description, url, serviceType = "Industrial Automation & Engineering" }) {
  const origin = resolveOrigin();
  return {
    "@context": "https://schema.org",
    "@type": "Service",
    "@id": `${url || origin}#service`,
    name: name,
    serviceType: serviceType,
    provider: {
      "@id": `${origin}/#organization`,
    },
    description: description,
    areaServed: "Global",
  };
}
