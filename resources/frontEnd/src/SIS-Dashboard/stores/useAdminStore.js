import { defineStore } from "pinia";
import { adminAPI } from "@/SIS-Dashboard/api/index.js";

const SOLUTION_PAGES_COUNT = 12;

const emptyOverview = () => ({
  projects: 0,
  sectors: 0,
  services: 0,
  partners: 0,
  clients: 0,
  solutionPages: SOLUTION_PAGES_COUNT,
  contactTotal: 0,
  contactUnread: 0,
});

export const useAdminStore = defineStore("admin", {
  state: () => ({
    overviewLoading: false,
    overviewLoaded: false,
    overview: emptyOverview(),
  }),

  actions: {
    async loadOverview() {
      if (this.overviewLoading) return;

      this.overviewLoading = true;

      try {
        const results = await Promise.allSettled([
          adminAPI.getProjects({ per_page: 100 }),
          adminAPI.getSectors(),
          adminAPI.getItems("services"),
          adminAPI.getPartners(),
          adminAPI.getItems("clients"),
          adminAPI.getContactRequests(),
        ]);

        const [
          projectsRes,
          sectorsRes,
          servicesRes,
          partnersRes,
          clientsRes,
          contactRes,
        ] = results;

        const partnersData =
          partnersRes.status === "fulfilled"
            ? partnersRes.value
            : null;

        const partnerCount =
          (partnersData?.technology_partners?.length ?? 0) +
            (partnersData?.ecosystem_networks?.length ?? 0) ||
          (Array.isArray(partnersData)
            ? partnersData.length
            : 0);

        const contact =
          contactRes.status === "fulfilled"
            ? contactRes.value
            : null;

        this.overview = {
          projects:
            projectsRes.status === "fulfilled"
              ? projectsRes.value?.pagination?.total ?? 0
              : 0,

          sectors:
            sectorsRes.status === "fulfilled"
              ? sectorsRes.value?.industries?.length ?? sectorsRes.value?.sectors?.length ?? (Array.isArray(sectorsRes.value) ? sectorsRes.value.length : 0)
              : 0,

          services:
            servicesRes.status === "fulfilled"
              ? servicesRes.value?.length ?? 0
              : 0,

          partners: partnerCount,

          clients:
            clientsRes.status === "fulfilled"
              ? clientsRes.value?.length ?? 0
              : 0,

          solutionPages: SOLUTION_PAGES_COUNT,

          contactTotal:
            contact?.pagination?.total ?? 0,

          contactUnread:
            contact?.requests?.filter(
              (request) => request.status === "unread",
            ).length ?? 0,
        };

        this.overviewLoaded = true;
      } catch (error) {
        console.error("Failed to load dashboard overview:", error);
      } finally {
        this.overviewLoading = false;
      }
    },
  },
});