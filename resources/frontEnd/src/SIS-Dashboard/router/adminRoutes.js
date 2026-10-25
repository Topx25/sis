export const adminRoutes = [
  {
    path: "/dashboard/login",
    name: "admin-login",
    component: () => import("@/SIS-Dashboard/pages/AdminLogin.vue"),
    meta: { title: "Admin Login | SiS", requiresAuth: false },
  },
  {
    path: "/dashboard",
    component: () => import("@/SIS-Dashboard/layouts/AdminLayout.vue"),
    meta: { requiresAuth: true },
    children: [
      {
        path: "",
        name: "admin-home",
        component: () => import("@/SIS-Dashboard/pages/AdminHome.vue"),
        meta: { title: "Dashboard | SiS Admin", requiresAuth: true },
      },
      {
        path: "hero",
        name: "admin-hero",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/HeroManager.vue"),
        meta: {
          title: "Hero | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "use-cases",
        name: "admin-projects",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/UseCasesManager.vue"),
        meta: {
          title: "Use Cases | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "section/:resource",
        name: "admin-section",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/whySisSection.vue"),
        meta: { title: "Section Editor | SiS Admin", requiresAuth: true },
      },
      {
        path: "collection/services",
        name: "admin-services",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/ServicesManager.vue"),
        meta: {
          title: "Services | SiS Admin",
          requiresAuth: true,
        },
      },

      {
        path: "collection/partners",
        name: "admin-partners",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/PartnersManager.vue"),
        meta: {
          title: "Partners | SiS Admin",
          requiresAuth: true,
        },
      },

      {
        path: "collection/clients",
        name: "admin-clients",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/ClientsManager.vue"),
        meta: {
          title: "Clients | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "collection/industries",
        alias: "collection/sectors",
        name: "admin-sectors",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/IndustriesManager.vue"),
        meta: {
          title: "Industries | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "solution-pages",
        name: "admin-solution-pages",
        component: () => import("../pages/sections/SolutionPagesManager.vue"),
        meta: {
          title: "Services & Solutions",
        },
      },
      {
        path: "contact-page",
        name: "admin-contact-page",
        component: () =>
          import("@/SIS-Dashboard/pages/contact/ContactPageManager.vue"),
        meta: { title: "Contact Page | SiS Admin", requiresAuth: true },
      },
      {
        path: "contact",
        name: "admin-contact",
        component: () =>
          import("@/SIS-Dashboard/pages/contact/ContactMessages.vue"),
        meta: { title: "Contact Messages | SiS Admin", requiresAuth: true },
      },
      {
        path: "work-sample-credentials/:id?",
        name: "admin-work-sample-credentials",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/WorkSampleCredentials.vue"),
        meta: {
          title: "Work Sample Access | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "header",
        name: "admin-header",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/HeaderManager.vue"),
        meta: {
          title: "Header | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "footer",
        name: "admin-footer",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/FooterManager.vue"),
        meta: {
          title: "Footer & Socials | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "about-page",
        alias: "about",
        name: "admin-about",
        component: () =>
          import("@/SIS-Dashboard/pages/sections/AboutManager.vue"),
        meta: {
          title: "About Us | SiS Admin",
          requiresAuth: true,
        },
      },
      {
        path: "seo-settings",
        alias: "seo",
        name: "admin-seo-settings",
        component: () =>
          import("@/SIS-Dashboard/pages/seo/GlobalSeoManager.vue"),
        meta: {
          title: "Global SEO & Metadata | SiS Admin",
          requiresAuth: true,
        },
      },
    ],
  },
];
