import { handleSessionExpired } from "../composables/useSessionManager.js";

const API_BASE = (import.meta.env.VITE_API_BASE_URL || "").replace(/\/+$/, "");
const IS_NGROK = API_BASE.includes("ngrok");

function getToken() {
  try {
    return localStorage.getItem("admin_token");
  } catch {
    return null;
  }
}

async function request(path, options = {}, { auth = true } = {}) {
  const headers = { Accept: "application/json"  };
  const token = getToken();

  if (auth && token) {
    headers['Authorization'] = `Bearer ${token}`;
    headers['X-Authorization'] = `Bearer ${token}`;
  }

  if (IS_NGROK) {
    headers["ngrok-skip-browser-warning"] = "true";
  }

  let body = options.body;

  if (body !== undefined && body !== null && !(body instanceof FormData)) {
    headers["Content-Type"] = "application/json";
    body = JSON.stringify(body);
  }

  const res = await fetch(`${path.startsWith("http") ? "" : API_BASE}${path}`, {
    ...options,
    body,
    headers,
  });

  let json = {};

  try {
    json = await res.json();
  } catch {
    /* empty body */
  }

  if (!res.ok) {
    if (res.status === 401 && auth) {
      handleSessionExpired();
    }

    const error = new Error(json?.message || `Request failed (${res.status})`);

    error.status = res.status;
    error.errors = json?.errors || null;

    throw error;
  }

  return json?.data;
}

const ensureFormData = (data) => {
  if (data instanceof FormData) return data;

  const fd = new FormData();

  Object.entries(data || {}).forEach(([key, value]) => {
    if (value === undefined || value === null) return;

    if (Array.isArray(value)) {
      value.forEach((v, idx) => {
        if (typeof v === "object" && !(v instanceof File) && !(v instanceof Blob) && v !== null) {
          Object.entries(v).forEach(([subKey, subVal]) => {
            if (subVal !== undefined && subVal !== null) {
              fd.append(`${key}[${idx}][${subKey}]`, subVal);
            }
          });
        } else {
          fd.append(`${key}[]`, v);
        }
      });
      return;
    }

    if (typeof value === "object" && !(value instanceof File) && !(value instanceof Blob)) {
      Object.entries(value).forEach(([subKey, subVal]) => {
        if (subVal !== undefined && subVal !== null) {
          if (typeof subVal === "object" && !(subVal instanceof File) && !(subVal instanceof Blob)) {
            Object.entries(subVal).forEach(([deepKey, deepVal]) => {
              if (deepVal !== undefined && deepVal !== null) {
                fd.append(`${key}[${subKey}][${deepKey}]`, deepVal);
              }
            });
          } else {
            fd.append(`${key}[${subKey}]`, subVal);
          }
        }
      });
      return;
    }

    fd.append(key, value);
  });

  return fd;
};

const toPayload = (data) => {
  if (data instanceof FormData) return data;

  const hasFile = Object.values(data || {}).some((value) => {
    if (value instanceof File) return true;

    if (Array.isArray(value)) {
      return value.some((item) => item instanceof File);
    }

    return false;
  });

  return hasFile ? ensureFormData(data) : data;
};

// Public read path mapping (these endpoints live outside /admin)
const PUBLIC_SECTION_PATHS = {
  hero: "/hero-home",
  additional: "/why-sis",
  services: "/services",
  clients: "/clients",
};

// Admin write path mapping (singular resource names used by the API)
const ADMIN_SECTION_PATHS = {
  hero: "hero-home",
  additional: "section/why-sis",
  services: "section/services",
  clients: "section/clients",
};

const normalizeCredential = (c) => ({
  id: c?.id,
  username: c?.username ?? "",
  password: c?.password ?? "",
  createdAt: c?.created_at ?? c?.createdAt ?? "",
  expiresAt: c?.expires_at ?? c?.expiresAt ?? "",
  isActive: Boolean(c?.is_active ?? c?.isActive ?? true),

  daysUntilExpiry:
    c?.days_until_expiry ??
    Math.max(
      0,
      Math.ceil(
        (new Date(c?.expires_at || c?.expiresAt || 0).getTime() - Date.now()) /
          86400000,
      ),
    ),
});

// Collection resource → public read path / admin path
const COLLECTION_PATHS = {
  services: "services",
  sectors: "sectors",
  industries: "industries",
  projects: "projects",
  partners: "partner-ecosystem",
  clients: "clients",
  timeline: "timeline",
  pillars: "pillars",
  workFlow: "work-flow",
  workSamples: "work-samples",
};

export const adminApi = {
  // ── Auth ────────────────────────────────────────────────

  login: (payload) =>
    request(
      "/admin/login",
      {
        method: "POST",
        body: payload,
      },
      { auth: false },
    ),

  logout: () =>
    request("/admin/logout", {
      method: "POST",
    }),

  me: () => request("/admin/me"),

  // ── Single-row sections ────────────────────────────────

  getSection: (resource) =>
    request(PUBLIC_SECTION_PATHS[resource] || `/${resource}`),

  updateSection: (resource, id, patch) => {
    const clean = {};

    Object.entries(patch || {}).forEach(([k, v]) => {
      if (v === undefined || v === null) return;
      if (typeof v === "string" && v.startsWith("blob:")) return;

      clean[k] = v;
    });

    return request(
      `/admin/${ADMIN_SECTION_PATHS[resource] || resource}/${id}`,
      {
        method: "PUT",
        body: toPayload(clean),
      },
    );
  },

  // ── Use Cases ─────────────────────────────────────────

  // ── Use Cases / Projects ───────────────────────────────

getUseCaseProjects: (params = {}) => {
  const qs = Object.entries(params)
    .filter(([, v]) => v !== undefined && v !== null && v !== "")
    .map(
      ([k, v]) =>
        `${encodeURIComponent(k)}=${encodeURIComponent(v)}`,
    )
    .join("&");

  return request(`/use-cases${qs ? `?${qs}` : ""}`);
},

createUseCase: (data) =>
  request("/admin/use-cases", {
    method: "POST",
    body: toPayload(data),
  }),

updateUseCase: (id, data) => {
  const payload = toPayload(data);

  if (payload instanceof FormData) {
    payload.append("_method", "PUT");

    return request(`/admin/use-cases/${id}`, {
      method: "POST",
      body: payload,
    });
  }

  return request(`/admin/use-cases/${id}`, {
    method: "PUT",
    body: payload,
  });
},


deleteUseCase: (id) =>
  request(`/admin/use-cases/${id}`, {
    method: "DELETE",
  }),

  // ── Use Cases Page (Header, Stats, Pillars) ────────────

  getUseCasesPage: () => request("/admin/use-cases-page"),

  updateUseCasesPage: (data) =>
    request("/admin/use-cases-page", {
      method: "PUT",
      body: data,
    }),

  // ── Projects ─────────────────────────────────────────

  getProjects: (params = {}) => {
    const qs = Object.entries(params)
      .filter(([, v]) => v !== undefined && v !== null && v !== "")
      .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
      .join("&");

    return request(`/projects${qs ? `?${qs}` : ""}`);
  },

  getProject: (slug) => request(`/projects/${slug}`),

  createProject: (data) =>
    request("/admin/projects", {
      method: "POST",
      body: toPayload(data),
    }),

  updateProject: (id, data) => {
    const payload = toPayload(data);

    if (payload instanceof FormData) {
      payload.append("_method", "PUT");

      return request(`/admin/projects/${id}`, {
        method: "POST",
        body: payload,
      });
    }

    return request(`/admin/projects/${id}`, {
      method: "PUT",
      body: payload,
    });
  },

  deleteProject: (id) =>
    request(`/admin/projects/${id}`, {
      method: "DELETE",
    }),

  // ── Why SiS ───────────────────────────────────────────

  deleteWhySiS: (id) =>
    request(`/admin/home-why-sis/${id}`, {
      method: "DELETE",
    }),

  // ── Generic Collections ───────────────────────────────

  getItems: async (resource) => {
    const res = await request(`/${COLLECTION_PATHS[resource] || resource}`);

    if (resource === "services") {
      return Array.isArray(res?.services) ? res.services : (Array.isArray(res) ? res : []);
    }

    if (resource === "clients") {
      return Array.isArray(res?.clients) ? res.clients : (Array.isArray(res) ? res : []);
    }

    if (resource === "sectors") {
      return res?.sectors ?? [];
    }

    if (resource === "partners") {
      if (
        Array.isArray(res?.featured_partners) ||
        Array.isArray(res?.partners)
      ) {
        const featured = Array.isArray(res.featured_partners)
          ? res.featured_partners
          : [];

        const ecosystem = Array.isArray(res.partners) ? res.partners : [];

        return Array.from(
          new Map(
            [...featured, ...ecosystem].map((partner) => [partner.id, partner]),
          ).values(),
        );
      }

      if (Array.isArray(res)) {
        return res;
      }

      return [];
    }

    return res;
  },

  getPartners: () => request("/partner-ecosystem"),

  getTrustedPartnersPage: () => request("/admin/trusted-partners-page"),

  updateTrustedPartnersPage: (data) =>
    request("/admin/trusted-partners-page", {
      method: "PUT",
      body: data,
    }),

  getSectors: () => request("/industries"),

  getIndustries: () => request("/industries"),

  // Create generic item
  createItem: (resource, data) =>
    request(`/admin/${COLLECTION_PATHS[resource] || resource}`, {
      method: "POST",
      body: toPayload(data),
    }),

  // Update generic item
  updateItem: (resource, id, data) => {
    const payload = toPayload(data);

    if (payload instanceof FormData) {
      payload.append("_method", "PUT");

      return request(
        `/admin/${COLLECTION_PATHS[resource] || resource}/${id}`,
        {
          method: "POST",
          body: payload,
        },
      );
    }

    return request(
      `/admin/${COLLECTION_PATHS[resource] || resource}/${id}`,
      {
        method: "PUT",
        body: payload,
      },
    );
  },

  // Delete generic item
  deleteItem: (resource, id) =>
    request(`/admin/${COLLECTION_PATHS[resource] || resource}/${id}`, {
      method: "DELETE",
    }),

  updateIndustries: (data) => {
    const payload = toPayload(data);

    if (payload instanceof FormData) {
      payload.append("_method", "PUT");

      return request("/admin/industries", {
        method: "POST",
        body: payload,
      });
    }

    return request("/admin/industries", {
      method: "PUT",
      body: payload,
    });
  },

  updateIndustry: async (id, data) => {
    const actualData = data !== undefined ? data : id;
    const actualId = data !== undefined ? id : null;

    const makePayload = () => toPayload(actualData);

    const sendRequest = (endpoint) => {
      const payload = makePayload();
      if (payload instanceof FormData) {
        payload.append("_method", "PUT");

        return request(endpoint, {
          method: "POST",
          body: payload,
        });
      }

      return request(endpoint, {
        method: "PUT",
        body: payload,
      });
    };

    if (actualId) {
      try {
        return await sendRequest(`/admin/sectors/${actualId}`);
      } catch (err) {
        if (err?.status === 404) {
          return await sendRequest(`/admin/industries/${actualId}`);
        }
        throw err;
      }
    }

    return sendRequest("/admin/industries");
  },

  createIndustry: async (data) => {
    const payload = toPayload(data);
    try {
      return await request("/admin/sectors", {
        method: "POST",
        body: payload,
      });
    } catch (err) {
      if (err?.status === 404) {
        return await request("/admin/industries", {
          method: "POST",
          body: payload,
        });
      }
      throw err;
    }
  },

  deleteIndustry: async (id) => {
    try {
      return await request(`/admin/sectors/${id}`, {
        method: "DELETE",
      });
    } catch (err) {
      if (err?.status === 404 || err?.status === 500) {
        return await request(`/admin/industries/${id}`, {
          method: "DELETE",
        });
      }
      throw err;
    }
  },

  // ── Solution Pages ────────────────────────────────────

  getSolutionPages: () => request("/admin/solution-pages"),

  getSolutionPage: (slug) =>
    request(`/admin/solution-pages/${slug}`).catch(() =>
      request(`/solution-pages/${slug}`)
    ),

  createSolutionPage: (data) =>
    request("/admin/solution-pages", {
      method: "POST",
      body: toPayload(data),
    }),

  updateSolutionPage: (slug, data) => {
    if (data instanceof FormData) {
      data.append("_method", "PUT");

      return request(`/admin/solution-pages/${slug}`, {
        method: "POST",
        body: data,
      });
    }

    return request(`/admin/solution-pages/${slug}`, {
      method: "PUT",
      body: data,
    });
  },

  deleteSolutionPage: (slug) =>
    request(`/admin/solution-pages/${slug}`, {
      method: "DELETE",
    }),

  // ── Contact ────────────────────────────────────────────

  getContactRequests: (params = {}) =>
    request(
      `/admin/contact-requests${params.page ? `?page=${params.page}` : ""}`,
    ),

  updateContactStatus: (id, status) =>
    request(`/admin/contact-requests/${id}/status`, {
      method: "PATCH",
      body: { status },
    }),

  replyContact: (id, message) =>
    request(`/admin/contact-requests/${id}/reply`, {
      method: "POST",
      body: { message },
    }),

  getOutbox: () => Promise.resolve([]),

  // ── Contact Page Content ──────────────────────────────

  getContactPage: () => request("/admin/contact-page"),

  updateContactPage: (data) => {
    if (data instanceof FormData) {
      data.append("_method", "PUT");
      return request("/admin/contact-page", {
        method: "POST",
        body: data,
      });
    }
    return request("/admin/contact-page", {
      method: "PUT",
      body: data,
    });
  },

  // ── Hero Slides ───────────────────────────────────────

  getHeroSlides: () => request("/hero-home"),

  createHeroSlide: (data) =>
    request("/admin/home-hero-slide", {
      method: "POST",
      body: toPayload(data),
    }),

  updateHeroSlide: (id, data) => {
    if (data instanceof FormData) {
      data.append("_method", "PUT");

      return request(`/admin/home-hero-slide/${id}`, {
        method: "POST",
        body: data,
      });
    }

    return request(`/admin/home-hero-slide/${id}`, {
      method: "PUT",
      body: data,
    });
  },

  deleteHeroSlide: (id) =>
    request(`/admin/home-hero-slide/${id}`, {
      method: "DELETE",
    }),

  // ── Visitor credentials ───────────────────────────────

  getVisitorCredentials: () =>
    request("/admin/visitor-credentials").then((res) =>
      Array.isArray(res) ? res.map(normalizeCredential) : [],
    ),

  createVisitorCredential: ({ username, password }) =>
    request("/admin/visitor-credentials", {
      method: "POST",
      body: {
        username,
        password,
        days_valid: 7,
      },
    }).then((res) => normalizeCredential(res)),

  updateVisitorCredential: (id, data) =>
    request(`/admin/visitor-credentials/${id}`, {
      method: "PUT",
      body: {
        is_active: Boolean(data.isActive),
      },
    }).then((res) => normalizeCredential(res)),

  deleteVisitorCredential: (id) =>
    request(`/admin/visitor-credentials/${id}`, {
      method: "DELETE",
    }),

  generateRandomCredential: () => {
    const chars =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    const rand = (n) =>
      Array.from(
        { length: n },
        () => chars[Math.floor(Math.random() * chars.length)],
      ).join("");

    return {
      username: "demo_" + rand(8),
      password: rand(12) + "!" + Math.floor(Math.random() * 100),
    };
  },

  // ── Work-sample demo credentials ─────────────────────

  getWorkSampleCredentials: (_workSampleId) => adminApi.getVisitorCredentials(),

  createWorkSampleCredential: (_workSampleId, { username, password }) =>
    adminApi.createVisitorCredential({
      username,
      password,
    }),

  updateWorkSampleCredential: (id, data) =>
    adminApi.updateVisitorCredential(id, data),

  deleteWorkSampleCredential: (id) => adminApi.deleteVisitorCredential(id),

  // ── Footer Management ─────────────────────────────────

  getFooter: () => request("/admin/footer"),

  updateFooterSettings: (data) => {
    const payload = toPayload(data);
    if (payload instanceof FormData) {
      payload.append("_method", "PUT");
      return request("/admin/footer", {
        method: "POST",
        body: payload,
      });
    }
    return request("/admin/footer", {
      method: "PUT",
      body: payload,
    });
  },

  createFooterLink: (data) =>
    request("/admin/footer/links", {
      method: "POST",
      body: data,
    }),

  updateFooterLink: (id, data) =>
    request(`/admin/footer/links/${id}`, {
      method: "PUT",
      body: data,
    }),

  deleteFooterLink: (id) =>
    request(`/admin/footer/links/${id}`, {
      method: "DELETE",
    }),

  updateFooterLinkStatus: (id, isActive) =>
    request(`/admin/footer/links/${id}/status`, {
      method: "PATCH",
      body: { is_active: isActive },
    }),

  reorderFooterLinks: (links) =>
    request("/admin/footer/links/reorder", {
      method: "PATCH",
      body: { links },
    }),

  // ── Header Management ─────────────────────────────────

  getHeader: () => request("/admin/header"),

  updateHeader: (data) => {
    const payload = toPayload(data);
    if (payload instanceof FormData) {
      payload.append("_method", "PUT");
      return request("/admin/header", {
        method: "POST",
        body: payload,
      });
    }
    return request("/admin/header", {
      method: "PUT",
      body: payload,
    });
  },

  // ── About Page Management ──────────────────────────────

  getAboutHero: () => request("/about-hero"),

  updateAboutHero: (data) => {
    const payload = toPayload(data);
    if (payload instanceof FormData) {
      payload.append("_method", "PUT");
      return request("/admin/section/about-hero/1", {
        method: "POST",
        body: payload,
      });
    }
    return request("/admin/section/about-hero/1", {
      method: "PUT",
      body: payload,
    });
  },

  getAboutVision: () => request("/vision"),

  updateAboutVision: (data) =>
    request("/admin/section/vision/1", {
      method: "PUT",
      body: data,
    }),

  getAboutDomains: () => request("/domains"),

  updateAboutDomains: (data) =>
    request("/admin/section/domains/1", {
      method: "PUT",
      body: data,
    }),

  getAboutItOt: () => request("/it-ot"),

  updateAboutItOt: (data) =>
    request("/admin/section/it-ot/1", {
      method: "PUT",
      body: data,
    }),

  getAboutCore: () => request("/core-values"),

  updateAboutCore: (data) =>
    request("/admin/section/core-values/1", {
      method: "PUT",
      body: data,
    }),

  getTimelineSection: () => request("/admin/section/timeline"),

  updateTimelineSection: (data) =>
    request("/admin/section/timeline/1", {
      method: "PUT",
      body: data,
    }),

  getTimelineEvents: () => request("/timeline"),

  createTimelineEvent: (data) =>
    request("/admin/timeline", {
      method: "POST",
      body: data,
    }),

  updateTimelineEvent: (id, data) =>
    request(`/admin/timeline/${id}`, {
      method: "PUT",
      body: data,
    }),

  deleteTimelineEvent: (id) =>
    request(`/admin/timeline/${id}`, {
      method: "DELETE",
    }),

  // ==========================================
  // 🌐 GLOBAL & PAGE-SPECIFIC SEO SETTINGS
  // ==========================================
  getSeoSettings: () => request("/admin/seo-settings"),

  updateSeoSettings: (data) =>
    request("/admin/seo-settings", {
      method: data instanceof FormData ? "POST" : "PUT",
      body: data,
    }),

  getPagesSeo: () => request("/admin/pages-seo"),

  getPageSeo: (key) => request(`/admin/pages-seo/${key}`),

  updatePageSeo: (key, data) => {
    let payload = data;
    if (!(data instanceof FormData)) {
      const fd = new FormData();
      const seo = data.seo || data;
      if (seo.meta_title) fd.append("meta_title", JSON.stringify(seo.meta_title));
      if (seo.meta_description) fd.append("meta_description", JSON.stringify(seo.meta_description));
      if (seo.og_title) fd.append("og_title", JSON.stringify(seo.og_title));
      if (seo.og_description) fd.append("og_description", JSON.stringify(seo.og_description));
      if (seo.canonical_url !== undefined) fd.append("canonical_url", seo.canonical_url || "");
      if (seo.is_noindex !== undefined) fd.append("is_noindex", seo.is_noindex ? "1" : "0");
      if (seo.is_nofollow !== undefined) fd.append("is_nofollow", seo.is_nofollow ? "1" : "0");
      if (seo.schema_type) fd.append("schema_type", seo.schema_type);
      if (seo.og_image_file instanceof File) {
        fd.append("og_image", seo.og_image_file);
      } else if (seo.remove_og_image) {
        fd.append("remove_og_image", "1");
      } else if (typeof seo.og_image === "string") {
        fd.append("og_image", seo.og_image);
      }
      payload = fd;
    }
    return request(`/admin/pages-seo/${key}`, {
      method: "POST",
      body: payload,
    });
  },

  // ---- Settings module: users, profile, protection, email ----
  getUsers: () => request("/admin/users"),
  createUser: (data) => request("/admin/users", { method: "POST", body: data }),
  updateUser: (id, data) =>
    request(`/admin/users/${id}`, { method: "PUT", body: data }),
  deleteUser: (id) => request(`/admin/users/${id}`, { method: "DELETE" }),

  updateProfile: (data) =>
    request("/admin/profile", { method: "PUT", body: data }),

  getTurnstileSettings: () => request("/admin/settings/turnstile"),
  updateTurnstileSettings: (data) =>
    request("/admin/settings/turnstile", { method: "PUT", body: data }),

  getMailSettings: () => request("/admin/settings/mail"),
  updateMailSettings: (data) =>
    request("/admin/settings/mail", { method: "PUT", body: data }),
  sendTestMail: (to) =>
    request("/admin/settings/mail/test", { method: "POST", body: { to } }),
};
