const API_BASE = (import.meta.env.VITE_API_BASE_URL || "").replace(
  /\/+$/,
  "",
);

function getApiOrigin() {
  if (API_BASE && (API_BASE.startsWith("http://") || API_BASE.startsWith("https://"))) {
    try {
      return new URL(API_BASE).origin;
    } catch {
      // Fallback below
    }
  }
  if (typeof window !== "undefined" && window.location?.origin) {
    return window.location.origin;
  }
  return "";
}

// Derive the API root (".../api") from the configured API base, so storage URLs
// resolve to "<root>/storage/..." — i.e. "/api/storage/...", where the Laravel
// public disk is actually served (the app lives under the "/api" subfolder).
function getApiRoot() {
  // API_BASE is like "/api/v1" (relative) or "https://host/api/v1" (absolute).
  const withoutVersion = API_BASE.replace(/\/v1$/, "");
  if (
    withoutVersion.startsWith("http://") ||
    withoutVersion.startsWith("https://")
  ) {
    return withoutVersion;
  }
  const origin =
    typeof window !== "undefined" && window.location?.origin
      ? window.location.origin
      : "";
  return origin + withoutVersion;
}

const LOCAL_HOSTS = new Set(["localhost", "127.0.0.1", "::1"]);

function isLocalOrPrivateHost(hostname) {
  if (!hostname) return false;
  if (LOCAL_HOSTS.has(hostname.toLowerCase())) return true;
  // Match LAN/private IP addresses: 192.168.x.x, 10.x.x.x, 172.16-31.x.x
  if (/^192\.168\./.test(hostname)) return true;
  if (/^10\./.test(hostname)) return true;
  if (/^172\.(1[6-9]|2[0-9]|3[0-1])\./.test(hostname)) return true;
  return false;
}

export function resolveImageUrl(src) {
  if (!src) return "";

  const raw = String(src).trim();
  if (!raw) return "";

  // Blob or Data URLs (used for instant preview of newly selected files in browser)
  if (raw.startsWith("blob:") || raw.startsWith("data:")) {
    return raw;
  }

  const apiOrigin = getApiOrigin();
  const isHttpsPage =
    typeof window !== "undefined" && window.location?.protocol === "https:";

  // Check if raw is already an absolute HTTP/HTTPS URL
  if (raw.startsWith("http://") || raw.startsWith("https://")) {
    try {
      const url = new URL(raw);

      // If page is loaded over HTTPS, upgrade any HTTP image URL to HTTPS to avoid mixed content block
      if (isHttpsPage && url.protocol === "http:") {
        url.protocol = "https:";
      }

      // Site-managed storage assets: always serve them from the domain currently
      // showing the site, so the same content works on ANY domain it is served
      // from (e.g. an addon domain) regardless of the host baked into the stored
      // URL. External (non-storage) images are left untouched.
      if (
        url.pathname.includes("/storage/") &&
        typeof window !== "undefined" &&
        window.location?.host
      ) {
        url.protocol = window.location.protocol;
        url.host = window.location.host;
      }

      if (url.pathname.includes("training-consulting.jpg")) {
        return `${import.meta.env.BASE_URL}images/training.webp`;
      }

      return url.href;
    } catch {
      return raw;
    }
  }

  // Handle relative paths
  const clean = raw.replace(/^\/+/, "");

  // If path already starts with storage/
  if (clean.startsWith("storage/")) {
    return `${getApiRoot()}/${clean}`;
  }

  // Known backend storage directories
  const STORAGE_FOLDERS = [
    "projects",
    "collections",
    "partners",
    "ourClints",
    "work-samples",
    "solution-pages",
    "gallery",
    "uploads",
  ];

  for (const folder of STORAGE_FOLDERS) {
    if (clean.startsWith(folder + "/")) {
      return `${getApiRoot()}/storage/${clean}`;
    }
  }

  // Frontend public bundled assets (e.g. images/...)
  if (clean.startsWith("images/")) {
    return `${import.meta.env.BASE_URL}${clean}`;
  }

  // Fallback: if it contains a slash, route to backend storage
  if (clean.includes("/")) {
    return `${getApiRoot()}/storage/${clean}`;
  }

  return `${import.meta.env.BASE_URL}${clean}`;
}

export const getImageUrl = resolveImageUrl;
export default resolveImageUrl;