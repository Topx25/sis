import { defineStore } from "pinia";
import contact from "@/dummydata/contact";
import industries from "@/dummydata/industries";
import projects from "@/dummydata/projects";
import services from "@/dummydata/services";
import sectors from "@/dummydata/sectors";
import timeline from "@/dummydata/timeline";
import { workSamples } from "@/dummydata/workSamples";
import { api } from "@/api/index.js";
import {
  normalizeProjects,
  normalizeSectors,
  normalizeServices,
  normalizeTimeline,
  normalizeWorkSamples,
  normalizeContactPage,
} from "@/api/normalizers.js";

export const API_ENABLED = true; // Set to true to enable API calls, false to use dummy data

export const useSiteStore = defineStore("site", {
  state: () => ({
    loaded: false,
    loading: false,
    projectsLoaded: false,
    sectorsLoaded: false,
    workSamplesLoaded: false,
    contactLoaded: false,
    footerLoaded: false,
    projects: null,
    sectors: null,
    contact: null,
    footer: null,
    header: null,
    headerLoaded: false,
    timeline: timeline,
    timelineLoaded: false,
    industries: null,
    services: null,
    servicesSection: null,
    servicesLoaded: false,
    pagesSeo: {},
    pagesSeoLoaded: false,
  }),
  actions: {
    async loadPagesSeo(force = false) {
      if (this.pagesSeoLoaded && !force) return this.pagesSeo;
      try {
        const data = await api.getPagesSeo();
        if (data && typeof data === "object") {
          this.pagesSeo = data;
          this.pagesSeoLoaded = true;
          return data;
        }
      } catch (err) {
        console.warn("Unable to load dynamic pages SEO:", err);
      }
      return this.pagesSeo || {};
    },
    async loadHeader(force = false) {
      if (this.headerLoaded && this.header && !force) return this.header;
      try {
        const data = await api.getHeader();
        if (data) {
          this.header = data;
          this.headerLoaded = true;
          return data;
        }
      } catch (err) {
        console.warn("Unable to load header dynamically:", err);
      }
      return null;
    },
    async loadFooter(force = false) {
      if (this.footerLoaded && this.footer && !force) return this.footer;
      try {
        const data = await api.getFooter();
        if (data) {
          this.footer = data;
          this.footerLoaded = true;
          return data;
        }
      } catch (err) {
        console.warn("Unable to load footer dynamically:", err);
      }
      return null;
    },
    async loadContact(force = false) {
      if (this.contactLoaded && !force) return;
      if (!API_ENABLED) {
        this.contact = contact;
        this.contactLoaded = true;
        return;
      }
      try {
        const res = await api.getContactPage();
        this.contact = normalizeContactPage(res);
      } catch (error) {
        console.error("Unable to load contact page:", error);
        this.contact = this.contact ?? null;
      } finally {
        this.contactLoaded = true;
      }
    },
    async loadTimeline(force = false) {
      if (this.timelineLoaded && !force) return;
      if (!API_ENABLED) {
        this.timeline = timeline;
        this.timelineLoaded = true;
        return;
      }
      try {
        const res = await api.getTimeline();
        const apiTimeline = normalizeTimeline(res);
        this.timeline = Array.isArray(apiTimeline) && apiTimeline.length > 0 ? apiTimeline : timeline;
      } catch (error) {
        console.warn("Unable to load timeline:", error);
        this.timeline = this.timeline || timeline;
      } finally {
        this.timelineLoaded = true;
      }
    },
    async loadAll() {
      if (this.loading) return;
      this.loading = true;

      // Some deployments expose only a subset of the CMS endpoints. Keep a
      // missing endpoint from aborting the whole initial load (and leaving
      // section-level skeletons visible indefinitely).
      const unavailable = (endpoint) =>
        Promise.reject(new Error(`${endpoint} endpoint is unavailable`));

      try {
        if (API_ENABLED) {
        const results = await Promise.allSettled([
          api.getProjects(),
          (api.getIndustries?.() ?? api.getSectors?.() ?? unavailable("Industries")),
          api.getServices(),
          api.getWorkSamples?.() ?? unavailable("Work samples"),
          api.getContactPage?.() ?? unavailable("Contact page"),
          api.getFooter?.() ?? unavailable("Footer"),
          api.getHeader?.() ?? unavailable("Header"),
          api.getTimeline?.() ?? unavailable("Timeline"),
        ]);
        const [
          projectsRes,
          sectorsRes,
          servicesRes,
          workSamplesRes,
          contactRes,
          footerRes,
          headerRes,
          timelineRes,
        ] = results;

        const projectsOk = projectsRes.status === "fulfilled";
        const sectorsOk = sectorsRes.status === "fulfilled";
        const servicesOk = servicesRes.status === "fulfilled";
        const workSamplesOk = workSamplesRes.status === "fulfilled";
        const contactOk = contactRes?.status === "fulfilled";
        const footerOk = footerRes?.status === "fulfilled";
        const headerOk = headerRes?.status === "fulfilled";
        const timelineOk = timelineRes?.status === "fulfilled";

        if (servicesOk && servicesRes.value) {
          const norm = normalizeServices(servicesRes.value);
          this.services = norm.services?.length ? norm.services : services;
          this.servicesSection = norm.section || null;
          this.servicesLoaded = true;
        }

        if (footerOk && footerRes.value) {
          this.footer = footerRes.value;
          this.footerLoaded = true;
        }

        if (headerOk && headerRes.value) {
          this.header = headerRes.value;
          this.headerLoaded = true;
        }

        const apiProjects = projectsOk
          ? normalizeProjects(projectsRes.value)
          : [];
        this.projects = apiProjects.length > 0 ? apiProjects : projects;

        const apiWorkSamples = workSamplesOk
          ? normalizeWorkSamples(workSamplesRes.value)
          : [];
        this.workSamples =
          apiWorkSamples.length > 0 ? apiWorkSamples : workSamples;
        this.workSamplesLoaded = true;

        const rawSectors = sectorsRes.value;
        const apiSectors = sectorsOk
          ? (Array.isArray(rawSectors?.industries)
              ? rawSectors.industries
              : Array.isArray(rawSectors?.sectors)
                ? rawSectors.sectors
                : Array.isArray(rawSectors?.data?.industries)
                  ? rawSectors.data.industries
                  : Array.isArray(rawSectors?.data?.sectors)
                    ? rawSectors.data.sectors
                    : normalizeSectors(rawSectors, this.projects))
          : [];
        this.sectors = Array.isArray(apiSectors) && apiSectors.length > 0 ? apiSectors : sectors;

        const apiTimeline = timelineOk
          ? normalizeTimeline(timelineRes.value)
          : [];
        this.timeline = Array.isArray(apiTimeline) && apiTimeline.length > 0 ? apiTimeline : timeline;
        this.timelineLoaded = true;

        const apiContact = contactOk
          ? normalizeContactPage(contactRes.value)
          : null;
        this.contact = apiContact;
        this.contactLoaded = true;

        this.projectsLoaded = true;

        } else {
        this.projects = projects;
        this.services = services;
        this.sectors = sectors;
        this.timeline = timeline;
        this.workSamples = workSamples;
        this.contact = contact;
        this.contactLoaded = true;
        this.projectsLoaded = true;
      
        this.timelineLoaded = true;
        this.workSamplesLoaded = true;
        }


      if (Array.isArray(this.sectors) && this.sectors.length > 0) {
        this.industries = this.sectors.map((sec, idx) => {
          const dummy = industries.find((d) =>
            d.title?.toLowerCase() === sec.name?.toLowerCase() ||
            d.title?.toLowerCase().includes(sec.slug?.replace(/-/g, " ").toLowerCase()) ||
            sec.slug?.toLowerCase().includes(d.title?.toLowerCase().slice(0, 4))
          ) || (industries[idx] || {});

          const relevantPrograms = Array.isArray(sec.relevant_programs) && sec.relevant_programs.length > 0
            ? sec.relevant_programs
            : (dummy.clients || []);

          const typicalScope = Array.isArray(sec.typical_scope) && sec.typical_scope.length > 0
            ? sec.typical_scope
            : (dummy.scope || []);

          return {
            ...dummy,
            id: sec.id,
            slug: sec.slug,
            icon: sec.icon || dummy.icon || "factory",
            title: sec.name || dummy.title,
            text: sec.description || dummy.text,
            projects_count: sec.projects_count,
            image: sec.image_url || sec.image || dummy.image || null,
            image_url: sec.image_url || (typeof sec.image === "string" ? sec.image : null),
            relevant_programs: relevantPrograms,
            typical_scope: typicalScope,
            clients: relevantPrograms,
            scope: typicalScope,
          };
        });
      } else {
        this.industries = industries;
      }

      this.loaded = true;
      } catch (error) {
        // Ensure every independently rendered section can leave its loading
        // state even if the initial API bootstrap itself fails unexpectedly.
        this.projects = this.projects ?? projects;
        this.services = this.services ?? services;
        this.sectors = this.sectors ?? sectors;
        this.timeline = this.timeline ?? timeline;
        this.workSamples = this.workSamples ?? workSamples;
        this.contact = this.contact ?? null;
        this.contactLoaded = true;
        this.projectsLoaded = true;
        this.sectorsLoaded = true;
        this.servicesLoaded = true;
        this.timelineLoaded = true;
        this.workSamplesLoaded = true;
        this.loaded = true;
        console.error("Unable to complete site data loading:", error);
      } finally {
      this.loading = false;
      }
    },
  },
});
