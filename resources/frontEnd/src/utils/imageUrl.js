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

      // If the URL contains a local/private host (e.g. 192.168.1.159:8000 or localhost from Laravel asset()),
      // but the client is in production or has a different API origin, rewrite host & protocol to apiOrigin
      if (apiOrigin) {
        const apiUrl = new URL(apiOrigin);
        if (
          isLocalOrPrivateHost(url.hostname) &&
          !isLocalOrPrivateHost(apiUrl.hostname)
        ) {
          url.protocol = apiUrl.protocol;
          url.host = apiUrl.host;
        } else if (isHttpsPage && apiUrl.protocol === "https:" && url.pathname.includes("/storage/")) {
          url.protocol = apiUrl.protocol;
          if (isLocalOrPrivateHost(url.hostname) && !isLocalOrPrivateHost(apiUrl.hostname)) {
            url.host = apiUrl.host;
          }
        }
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
    return `${apiOrigin}/${clean}`;
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
      return `${apiOrigin}/storage/${clean}`;
    }
  }

  // Frontend public bundled assets (e.g. images/...)
  if (clean.startsWith("images/")) {
    return `${import.meta.env.BASE_URL}${clean}`;
  }

  // Fallback: if it contains a slash, route to backend storage
  if (clean.includes("/")) {
    return `${apiOrigin}/storage/${clean}`;
  }

  return `${import.meta.env.BASE_URL}${clean}`;
}

export const getImageUrl = resolveImageUrl;
export default resolveImageUrl;