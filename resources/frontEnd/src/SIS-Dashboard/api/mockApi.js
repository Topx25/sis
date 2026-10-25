import {
  db,
  nextId,
  findRow,
  insertRow,
  updateRow,
  removeRow,
  nowIso,
} from "../mock/db.js";

const LATENCY = 250;

const delay = (ms = LATENCY) =>
  new Promise((resolve) => setTimeout(resolve, ms));

const slugify = (s) =>
  String(s || "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");

const apiError = (message, status = 400, errors = null) =>
  Object.assign(new Error(message), { status, errors });

const notFound = (message = "Resource not found") => apiError(message, 404);

const ACTIVE_ADMIN = {
  id: 1,
  name: "SiS System Administrator",
  email: "admin@sis-automation.com",
};

const TOKENS = new Set();

// Auth is now enabled - all authenticated endpoints require a valid token
const AUTH_ENABLED = true;

const COLLECTIONS = {
  services: "services",
  sectors: "sectors",
  projects: "projects",
  partners: "partners",
  clients: "clients",
  timeline: "timeline",
  pillars: "pillars",
  workFlow: "workFlow",
  workSamples: "workSamples",
};

const SINGLE_ROWS = {
  hero: "hero",
  about: "about",
  aboutHero: "aboutHero",
  itOt: "itOt",
  coreValues: "coreValues",
  domains: "domains",
};

// ---- serialization helpers (mirror the Laravel resources) ----
const serializePartner = (p) => ({
  id: p.id,
  name: p.name,
  logo_url: p.logo_path ?? null,
  website_url: p.website_url,
  type: p.type ?? "technology_partner",
  sort_order: p.sort_order,
});
const serializeClient = (c) => ({
  id: c.id,
  name: c.name,
  src: c.logo_path ?? null,
  logo_url: c.logo_path ?? null,
  sort_order: c.sort_order,
});
const serializeProject = (p) => ({
  id: p.id,
  title: p.title,
  slug: p.slug,
  client_name: p.client_name,
  location: p.location,
  summary: p.summary,
  description: p.description ?? p.summary,
  cover_image: p.cover_image ?? null,
  images: Array.isArray(p.images) ? p.images : [],
  gallery_limit: p.gallery_limit ?? 4,
  tags: p.tags ?? [],
  sector: p.sector ?? null,
  services: p.services ?? [],
  created_at: p.created_at ?? nowIso(),
});
const serializeWorkSample = (w) => ({
  id: w.id,
  slug: w.slug,
  title: w.title,
  industry: w.industry ?? null,
  category: w.category ?? w.industry,
  image: w.image ?? null,
  image_path: w.image ?? null,
  image_url: w.image ?? null,
  free: Boolean(w.free),
  blurb: w.blurb ?? null,
  body: w.body ?? w.blurb,
  badge: w.badge,
  demo_url: w.demo_url ?? null,
  sort_order: w.sort_order,
});

const serializeRequest = (r) => ({
  id: r.id,
  name: r.name,
  email: r.email,
  phone: r.phone ?? null,
  company_name: r.company_name ?? null,
  message: r.message,
  status: r.status,
  reply: r.reply ?? null,
  replied_at: r.replied_at ?? null,
  created_at: r.created_at,
});

const requireAuth = (token) => {
  if (!AUTH_ENABLED) return;
  if (!token || !TOKENS.has(token)) throw apiError("Unauthenticated.", 401);
};

// ---- route handlers ----
export const mockApi = {
  // ── Auth ────────────────────────────────────────────────
  async login({ email, password }) {
    await delay(400);
    if (email !== ACTIVE_ADMIN.email || password !== "password123") {
      throw apiError("Invalid login credentials.", 422, {
        email: ["Invalid login credentials."],
      });
    }
    const token = `mock_${Date.now()}_${Math.random().toString(36).slice(2)}`;
    TOKENS.add(token);
    return {
      user: { ...ACTIVE_ADMIN },
      access_token: token,
      token_type: "Bearer",
      expires_in: 3600,
    };
  },

  async logout(token) {
    await delay();
    requireAuth(token);
    TOKENS.delete(token);
    return null;
  },

  async me(token) {
    await delay();
    requireAuth(token);
    return { ...ACTIVE_ADMIN };
  },

  // ── Single-row sections ─────────────────────────────────
  async getSection(resource, token) {
    await delay();
    const key = SINGLE_ROWS[resource];
    if (!key) throw apiError(`Unknown section resource: ${resource}`, 404);
    const row =
      (db[key] || []).find((r) => r.is_active !== false) || db[key]?.[0];
    if (!row) throw notFound(`${resource} section data not found.`);
    return { ...row };
  },

  async updateSection(resource, id, patch, token) {
    await delay();
    requireAuth(token);
    const key = SINGLE_ROWS[resource];
    if (!key) throw apiError(`Unknown section resource: ${resource}`, 404);
    const row = updateRow(db[key], id, patch);
    if (!row) throw notFound(`${resource} section not found.`);
    return { ...row };
  },

  // ── Projects (full CRUD) ────────────────────────────────
  async getProjects(
    { per_page = 10, page = 1, search = "", sector = "" } = {},
    token,
  ) {
    await delay();
    let rows = db.projects.slice();
    if (sector) rows = rows.filter((p) => p.sector?.slug === sector);
    if (search) {
      const q = String(search).toLowerCase();
      rows = rows.filter((p) =>
        [p.title, p.client_name, p.summary].some((f) =>
          String(f ?? "")
            .toLowerCase()
            .includes(q),
        ),
      );
    }
    const total = rows.length;
    const perPage = Math.max(1, Number(per_page) || 10);
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    const currentPage = Math.min(Math.max(1, Number(page) || 1), totalPages);
    const start = (currentPage - 1) * perPage;
    return {
      projects: rows.slice(start, start + perPage).map(serializeProject),
      pagination: {
        total,
        count: rows.length,
        per_page: perPage,
        current_page: currentPage,
        total_pages: totalPages,
      },
    };
  },

  async getProject(slug, token) {
    await delay();
    const row = db.projects.find((p) => p.slug === slug);
    if (!row) throw notFound("Project not found.");
    return serializeProject(row);
  },

  async createProject(data, token) {
    await delay(400);
    requireAuth(token);
    if (!data.title || !data.client_name || !data.summary) {
      throw apiError("The given data was invalid.", 422, {
        ...(data.title ? {} : { title: ["The title field is required."] }),
        ...(data.client_name
          ? {}
          : { client_name: ["The client name field is required."] }),
        ...(data.summary
          ? {}
          : { summary: ["The summary field is required."] }),
      });
    }
    const sector = findRow(db.sectors, data.sector_id) || db.sectors[0] || null;
    const row = {
      id: nextId(db.projects),
      title: data.title,
      slug: slugify(data.title),
      client_name: data.client_name,
      location: data.location ?? null,
      summary: data.summary,
      description: data.description ?? data.summary,
      cover_image: data.cover_image ?? null,
      images: Array.isArray(data.images) ? data.images : [],
      gallery_limit: data.gallery_limit !== undefined ? Number(data.gallery_limit) : 4,
      tags: data.tags ?? [],
      sector,
      services: (data.service_ids || [])
        .map((id) => findRow(db.services, id))
        .filter(Boolean),
      created_at: nowIso(),
    };
    insertRow(db.projects, row);
    return serializeProject(row);
  },

  async updateProject(id, data, token) {
    await delay(400);
    requireAuth(token);
    const row = updateRow(db.projects, id, {
      ...(data.title !== undefined
        ? { title: data.title, slug: slugify(data.title) }
        : {}),
      ...(data.client_name !== undefined
        ? { client_name: data.client_name }
        : {}),
      ...(data.location !== undefined ? { location: data.location } : {}),
      ...(data.summary !== undefined ? { summary: data.summary } : {}),
      ...(data.description !== undefined
        ? { description: data.description }
        : {}),
      ...(data.cover_image !== undefined
        ? { cover_image: data.cover_image }
        : {}),
      ...(data.images !== undefined ? { images: Array.isArray(data.images) ? data.images : [] } : {}),
      ...(data.gallery_limit !== undefined ? { gallery_limit: Number(data.gallery_limit) } : {}),
      ...(data.tags !== undefined ? { tags: data.tags } : {}),
      ...(data.sector_id !== undefined
        ? { sector: findRow(db.sectors, data.sector_id) || row?.sector }
        : {}),
    });
    if (!row) throw notFound("Project not found.");
    if (data.service_ids)
      row.services = data.service_ids
        .map((id) => findRow(db.services, id))
        .filter(Boolean);
    return serializeProject(row);
  },

  async deleteProject(id, token) {
    await delay();
    requireAuth(token);
    if (!removeRow(db.projects, id)) throw notFound("Project not found.");
    return null;
  },

  // ── Generic collections ─────────────────────────────────
  async getItems(resource, token) {
    await delay();
    const key = COLLECTIONS[resource];
    if (!key) throw apiError(`Unknown resource: ${resource}`, 404);
    const rows = db[key];
    if (resource === "projects") return rows.map(serializeProject);
    if (resource === "partners") return rows.map(serializePartner);
    if (resource === "clients") return rows.map(serializeClient);
    if (resource === "workSamples") return rows.map(serializeWorkSample);
    return rows.map((r) => ({ ...r }));
  },

  async getSectors(token) {
    await delay();
    const sectors = db.sectors.map((s) => ({
      ...s,
      projects_count: db.projects.filter((p) => p.sector?.id === s.id).length,
    }));
    const capabilities = {
      title: "The SiS Capability Matrix",
      columns: [
        "SCADA Upgrades",
        "ERP Integration",
        "Hardware Commissioning",
        "Safety & Shutdown Systems",
        "Network Cybersecurity",
      ],
      rows: [
        { client: "Henkel", capabilities: [true, false, true, false, false] },
        { client: "BariQ", capabilities: [true, true, true, true, false] },
        { client: "Gas Cool", capabilities: [true, false, true, false, true] },
        { client: "Emisal", capabilities: [true, true, true, true, false] },
        {
          client: "Saudi Aramco",
          capabilities: [true, false, true, true, true],
        },
        { client: "Béjaïa RO", capabilities: [true, false, true, true, true] },
      ],
    };
    return { sectors, capabilities };
  },

  async getPartners(token) {
    await delay();
    return {
      technology_partners: db.partners
        .filter((p) => p.type === "technology_partner")
        .map(serializePartner),
      ecosystem_networks: db.partners
        .filter((p) => p.type === "ecosystem_network")
        .map(serializePartner),
    };
  },

  async createItem(resource, data, token) {
    await delay(400);
    requireAuth(token);
    const key = COLLECTIONS[resource];
    if (!key) throw apiError(`Unknown resource: ${resource}`, 404);
    const row = {
      id: nextId(db[key]),
      ...data,
      slug: data.slug ?? (data.title ? slugify(data.title) : undefined),
      sort_order: data.sort_order ?? db[key].length + 1,
      created_at: nowIso(),
    };
    insertRow(db[key], row);
    return this.serializeResource(resource, row);
  },

  async updateItem(resource, id, data, token) {
    await delay(400);
    requireAuth(token);
    const key = COLLECTIONS[resource];
    if (!key) throw apiError(`Unknown resource: ${resource}`, 404);
    const patch = { ...data };
    if (patch.title) patch.slug = slugify(patch.title);
    const row = updateRow(db[key], id, patch);
    if (!row) throw notFound(`${resource} item not found.`);
    return this.serializeResource(resource, row);
  },

  async deleteItem(resource, id, token) {
    await delay();
    requireAuth(token);
    const key = COLLECTIONS[resource];
    if (!key) throw apiError(`Unknown resource: ${resource}`, 404);
    if (!removeRow(db[key], id)) throw notFound(`${resource} item not found.`);
    return null;
  },

  serializeResource(resource, row) {
    if (resource === "projects") return serializeProject(row);
    if (resource === "partners") return serializePartner(row);
    if (resource === "clients") return serializeClient(row);
    if (resource === "workSamples") return serializeWorkSample(row);
    return { ...row };
  },

  // ── Contact / messages ──────────────────────────────────
  async getContactRequests({ per_page = 15, page = 1 } = {}, token) {
    await delay();
    const rows = db.contactRequests
      .slice()
      .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    const total = rows.length;
    const perPage = Math.max(1, Number(per_page) || 15);
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    const currentPage = Math.min(Math.max(1, Number(page) || 1), totalPages);
    const start = (currentPage - 1) * perPage;
    return {
      requests: rows.slice(start, start + perPage).map(serializeRequest),
      pagination: { total, current_page: currentPage, total_pages: totalPages },
    };
  },

  async updateContactStatus(id, status, token) {
    await delay();
    requireAuth(token);
    if (!["unread", "read", "archived"].includes(status)) {
      throw apiError("The given data was invalid.", 422, {
        status: ["The selected status is invalid."],
      });
    }
    const row = updateRow(db.contactRequests, id, { status });
    if (!row) throw notFound("Contact inquiry not found.");
    return serializeRequest(row);
  },

  async replyContact(id, message, token) {
    await delay(400);
    requireAuth(token);
    if (!message || String(message).trim().length < 10) {
      throw apiError("The given data was invalid.", 422, {
        message: ["The message field must be at least 10 characters."],
      });
    }
    const row = findRow(db.contactRequests, id);
    if (!row) throw notFound("Contact inquiry not found.");
    row.reply = String(message).trim();
    row.replied_at = nowIso();
    row.status = "read";
    db.outbox = db.outbox || [];
    db.outbox.push({
      to: row.email,
      subject: `Re: Your message to SiS`,
      body: row.reply,
      sent_at: nowIso(),
    });
    return serializeRequest(row);
  },

  async getOutbox(token) {
    await delay();
    requireAuth(token);
    return (db.outbox || []).slice();
  },

  // ── Visitor Access Credentials (Global) ─────────────────
  async getVisitorCredentials(token) {
    await delay();
    if (!db.visitorCredentials) db.visitorCredentials = [];
    return db.visitorCredentials.map((c) => ({
      id: c.id,
      username: c.username,
      password: c.password,
      createdAt: c.createdAt,
      expiresAt: c.expiresAt,
      isActive: c.isActive,
      daysUntilExpiry: Math.max(
        0,
        Math.ceil(
          (new Date(c.expiresAt).getTime() - new Date().getTime()) / 86400000,
        ),
      ),
    }));
  },

  async createVisitorCredential(data, token) {
    await delay(300);
    requireAuth(token);
    if (!data.username || !data.password) {
      throw apiError("The given data was invalid.", 422, {
        ...(data.username ? {} : { username: ["Username is required."] }),
        ...(data.password ? {} : { password: ["Password is required."] }),
      });
    }
    if (!db.visitorCredentials) db.visitorCredentials = [];
    const expiresAt = new Date();
    expiresAt.setDate(expiresAt.getDate() + 7);
    const credential = {
      id: nextId(db.visitorCredentials),
      username: String(data.username).trim(),
      password: String(data.password).trim(),
      createdAt: nowIso(),
      expiresAt: expiresAt.toISOString(),
      isActive: true,
    };
    insertRow(db.visitorCredentials, credential);
    return {
      ...credential,
      daysUntilExpiry: 7,
    };
  },

  async deleteVisitorCredential(id, token) {
    await delay();
    requireAuth(token);
    if (!db.visitorCredentials) db.visitorCredentials = [];
    if (!removeRow(db.visitorCredentials, id)) {
      throw notFound("Credential not found.");
    }
    return null;
  },

  async updateVisitorCredential(id, data, token) {
    await delay(300);
    requireAuth(token);
    if (!db.visitorCredentials) db.visitorCredentials = [];
    const patch = {};
    if (data.isActive !== undefined) patch.isActive = Boolean(data.isActive);
    const row = updateRow(db.visitorCredentials, id, patch);
    if (!row) throw notFound("Credential not found.");
    return {
      ...row,
      daysUntilExpiry: Math.max(
        0,
        Math.ceil(
          (new Date(row.expiresAt).getTime() - new Date().getTime()) / 86400000,
        ),
      ),
    };
  },

  async generateRandomCredential() {
    const chars =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    const username =
      "demo_" +
      Array.from(
        { length: 8 },
        () => chars[Math.floor(Math.random() * chars.length)],
      ).join("");
    const password =
      Array.from(
        { length: 12 },
        () => chars[Math.floor(Math.random() * chars.length)],
      ).join("") +
      "!" +
      Math.floor(Math.random() * 100);
    return { username, password };
  },

  async getUseCasesPage() {
    await delay();
    if (!db.useCasesPage) {
      db.useCasesPage = {
        title: "Projects & Case Studies",
        subtitle: "Real-world automation and digital transformation projects delivered across multiple industries using leading technologies.",
        stats: [
          { value: "500+", label: "Projects Delivered", icon: "inventory_2" },
          { value: "250+", label: "Happy Clients", icon: "groups" },
          { value: "15+", label: "Years of Experience", icon: "calendar_today" },
          { value: "12+", label: "Countries Served", icon: "public" },
        ],
        pillars: [
          { title: "Innovation", description: "Driving smarter solutions with cutting-edge technologies", icon: "diamond" },
          { title: "Reliability", description: "Proven performance and long-term partnership", icon: "verified_user" },
          { title: "Global Reach", description: "Delivering value across 12+ countries", icon: "public" },
          { title: "Sustainability", description: "Building a smarter and cleaner tomorrow", icon: "eco" },
        ],
        is_active: true,
      };
    }
    return { ...db.useCasesPage };
  },

  async updateUseCasesPage(data) {
    await delay(300);
    db.useCasesPage = {
      ...(db.useCasesPage || {}),
      ...data,
    };
    return { ...db.useCasesPage };
  },

  async getFooter() {
    await delay(200);
    return {
      settings: {
        description: "Bridging OT and IT across the MENA region with industrial automation and IIoT excellence.",
        copyright_text: "Superior Integrated Solutions. All rights reserved.",
        dynamic_year: true,
        floating_contact_enabled: true,
        floating_contact_url: "https://wa.me/201007568651",
        floating_contact_icon: "whatsapp",
        floating_contact_label: "Contact us on WhatsApp",
      },
      links: [],
      contact_links: [],
      navigation_links: [],
    };
  },

  async updateFooterSettings(data) {
    await delay(200);
    return data;
  },

  async createFooterLink(data) {
    await delay(200);
    return { id: Date.now(), ...data };
  },

  async updateFooterLink(id, data) {
    await delay(200);
    return { id, ...data };
  },

  async deleteFooterLink(_id) {
    await delay(200);
    return true;
  },

  headerData: null,

  async getHeader() {
    await delay(200);
    if (!this.headerData) {
      this.headerData = {
        id: 1,
        logo_path: null,
        logo_url: null,
        nav_links: [
          { id: 1, key: "home", label: "Home", url: "/", has_dropdown: false, is_active: true },
          { id: 6, key: "about", label: "About Us", url: "/about", has_dropdown: false, is_active: true },
          { id: 2, key: "services", label: "Services & Solutions", url: "/services", has_dropdown: true, is_active: true },
          { id: 3, key: "industries", label: "Industries", url: "/industries", has_dropdown: true, is_active: true },
          { id: 4, key: "partner-ecosystem", label: "Partner Ecosystem", url: "/partner-ecosystem", has_dropdown: false, is_active: true },
          { id: 5, key: "use-cases", label: "Use Cases", url: "/use-cases", has_dropdown: false, is_active: true },
        ],
        button_text: "Get in Touch",
        button_link: "/contact",
        button_enabled: true,
      };
    }
    return this.headerData;
  },

  async updateHeader(data) {
    await delay(200);
    const current = await this.getHeader();
    if (typeof FormData !== "undefined" && data instanceof FormData) {
      if (data.has("logo")) {
        const file = data.get("logo");
        if (typeof file === "object" && file && typeof URL !== "undefined") {
          current.logo_url = URL.createObjectURL(file);
        }
      } else if (data.get("remove_logo") === "1") {
        current.logo_url = null;
        current.logo_path = null;
      }
      if (data.has("button_text")) current.button_text = data.get("button_text");
      if (data.has("button_link")) current.button_link = data.get("button_link");
      if (data.has("button_enabled")) current.button_enabled = data.get("button_enabled") === "1";
      if (data.has("nav_links")) {
        try {
          current.nav_links = JSON.parse(data.get("nav_links"));
        } catch {}
      }
    } else if (typeof data === "object" && data) {
      Object.assign(current, data);
    }
    this.headerData = current;
    return current;
  },

  async getPagesSeo() {
    await delay(200);
    return [
      { key: "home", name: "Home Page", path: "/", fallback_title: { en: "SiS - Superior Integrated Solutions", ar: "سوبيريور للحلول المتكاملة" }, seo: {} },
      { key: "about", name: "About Us", path: "/about", fallback_title: { en: "About Us | SiS", ar: "من نحن | سوبيريور للحلول المتكاملة" }, seo: {} },
      { key: "services", name: "Services Catalog", path: "/services", fallback_title: { en: "Services & Solutions | SiS", ar: "الخدمات والحلول" }, seo: {} },
      { key: "projects", name: "Projects & Case Studies", path: "/projects", fallback_title: { en: "Projects & Case Studies | SiS", ar: "المشاريع" }, seo: {} },
      { key: "use-cases", name: "Use Cases", path: "/use-cases", fallback_title: { en: "Use Cases | SiS", ar: "حالات الاستخدام" }, seo: {} },
      { key: "partner-ecosystem", name: "Partner Ecosystem", path: "/partner-ecosystem", fallback_title: { en: "Partner Ecosystem | SiS", ar: "منظومة الشركاء" }, seo: {} },
      { key: "contact", name: "Contact Us", path: "/contact", fallback_title: { en: "Contact Us | SiS", ar: "اتصل بنا" }, seo: {} },
    ];
  },

  async getPageSeo(key) {
    await delay(150);
    const pages = await this.getPagesSeo();
    return pages.find((p) => p.key === key) || pages[0];
  },

  async updatePageSeo(key, data) {
    await delay(300);
    return { key, ...data };
  },
};
