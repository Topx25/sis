const BASE_URL = (import.meta.env.VITE_API_BASE_URL || "").replace(/\/+$/, "");

// ngrok free tier serves an interstitial warning page to browser requests and omits
// the CORS header, which makes every request look like HTTP 200 while being silently
// blocked in the browser. Sending this header tells ngrok to skip that interstitial.
// Scoped to ngrok hosts only; harmless once the API moves to a real domain.
const IS_NGROK = BASE_URL.includes("ngrok");

function toQuery(params) {
  const search = new URLSearchParams();
  Object.entries(params || {}).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== "") {
      search.append(key, value);
    }
  });
  const qs = search.toString();
  return qs ? `?${qs}` : "";
}

async function request(path, options = {}) {





  const token = localStorage.getItem("admin_token");
  const headers = {
    Accept: "application/json",
    ...(options.body ? { "Content-Type": "application/json" } : {}),
    ...(IS_NGROK ? { "ngrok-skip-browser-warning": "true" } : {}),
    ...options.headers,
    ...(token ? { "X-authorization": `Bearer ${token}` } : {}),

  };
  const controller = new AbortController();
  const timeoutId = window.setTimeout(() => controller.abort(), 15000);


  

  let res;
  try {
    res = await fetch(`${BASE_URL}${path}`, {
      ...options,
      headers,
      signal: controller.signal,
    });
  } catch (err) {
    window.clearTimeout(timeoutId);
    if (err?.name === "AbortError") {
      const error = new Error(
        `Request timed out after 15s${BASE_URL ? ` (${BASE_URL}${path})` : ""}`,
      );
      error.status = 408;
      throw error;
    }
    throw err;
  }
  window.clearTimeout(timeoutId);

  const json = await res.json().catch(() => ({}));
  if (!res.ok) {
    const error = new Error(json?.message || `Request failed (${res.status})`);
    error.status = res.status;
    error.errors = json?.errors || null;
    throw error;
  }
  return json?.data;
}

export const httpsUrl = (url) =>
  typeof url === "string" && url.startsWith("http://")
    ? url.replace("http://", "https://")
    : url;

export const api = {
  getProjects: (params) =>
    request(`/projects${toQuery({ per_page: 100, ...params })}`),
  getProject: (slug) => request(`/projects/${slug}`),
  getServices: () => request("/services"),
  getIndustries: () => request("/industries"),
  getSectors: () => request("/industries"),

  getPartners: () => request("/partner-ecosystem"),
  getUseCases: () => request("/use-cases"),

  getAboutHero: () => request("/about-hero"),
  getAboutVision: () => request("/vision"),
  getAboutItOt: () => request("/it-ot"),
  getAboutCore: () => request("/core-values"),
  getDomains: () => request("/domains"),
  getTimeline: () => request("/timeline"),
  getTimelineSection: () => request("/timeline-section"),
  getWorkSamples: () => request("/work-samples"),

  getReason: () => request("/why-sis"),
  getClients: () => request("/clients"),
  getHero: () => request("/hero-home"),
  getSolutionPage: (slug) => request(`/solution-pages/${slug}`),
  getSolutionPagesMenu: () => request("/solution-pages/menu"),
  getContactPage: () => request("/contact-page"),
  getFooter: () => request("/footer"),
  getPagesSeo: (params) => request(`/pages-seo${toQuery(params)}`),
  getPageSeo: (key, params) => request(`/pages-seo/${key}${toQuery(params)}`),
  getHeader: async () => {
    try {
      return await request("/header");
    } catch {
      try {
        return await request("/admin/header");
      } catch {
        return null;
      }
    }
  },
  submitContact: (payload) =>
    request("/contact", { method: "POST", body: JSON.stringify(payload) }),
};
