import { createRouter, createWebHistory } from "vue-router";
import { navigating } from "@/stores/navigationState";
import { applySeo, PRIVATE_ROBOTS } from "@/utils/seo.js";
import { useSiteStore } from "@/stores/useSiteStore.js";

const defaultTitle = "SiS - Superior Integrated Solutions";
import MainLayout from "@/layouts/MainLayout.vue";
import { adminRoutes } from "@/SIS-Dashboard/router/adminRoutes";

const HomePage = () => import("@/pages/Home/HomePage.vue");
const AboutPage = () => import("@/pages/About/AboutPage.vue");
const ServicesPage = () => import("@/pages/services/ServicesPage.vue");
const ProjectsPage = () => import("@/pages/projects/ProjectsPage.vue");
const EcosystemPage = () =>
  import("@/pages/partnerEcosystem/partnerEcosystemPage.vue");
const ContactPage = () => import("@/pages/Contact/ContactPage.vue");
const UseCases = () => import("@/pages/useCases/UseCasesPage.vue");

const routes = [
  {
    path: "/",
    component: MainLayout,
    children: [
      {
        path: "",
        name: "home",
        component: HomePage,
        meta: {
          title: defaultTitle,
          seo: {
            title: defaultTitle,
            description:
              "Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and digital transformation.",
          },
        },
      },
      {
        path: "about",
        name: "about",
        component: AboutPage,
        meta: {
          title: "About Us | SiS",
          seo: {
            title: "About Us | SiS",
            description:
              "Discover SiS - our mission, engineering pillars, industrial automation heritage, and leadership bridging operational technology with enterprise digital solutions.",
          },
        },
      },
      {
        path: "services",
        name: "services",
        component: ServicesPage,
        meta: {
          title: "Services | SiS",
          seo: {
            title: "Services | SiS",
            description:
              "Explore turnkey industrial engineering and automation services delivered by SiS, including DCS migrations, cybersecurity, and systems integration.",
          },
        },
      },
      // removed separate routes for architecture/methodology
      {
        path: "projects",
        name: "projects",
        component: ProjectsPage,
        meta: {
          title: "Projects | SiS",
          seo: {
            title: "Projects | SiS",
            description:
              "Proven industrial automation, SCADA, and OT projects engineered for mission-critical operations across key industrial sectors.",
          },
        },
      },
      {
        path: "partner-ecosystem",
        name: "partner-ecosystem",
        component: EcosystemPage,
        meta: {
          title: "Partner Ecosystem | SiS",
          seo: {
            title: "Partner Ecosystem | SiS",
            description:
              "Explore the global industrial automation and technology leaders partnering with Superior Integrated Solutions.",
          },
        },
      },
      {
        path: "contact",
        name: "contact",
        component: ContactPage,
        meta: {
          title: "Contact Us | SiS",
          seo: {
            title: "Contact Us | SiS",
            description:
              "Contact SiS engineering and sales for consultations, industrial automation inquiries, and mission-critical technical support.",
          },
        },
      },
      {
        path: "solution-pages/:slug",
        name: "solution-page",
        component: () => import("@/pages/services&solutions/BasicServicesPage.vue"),
        meta: {
          title: "Services & Solutions | SiS",
          seo: {
            title: "Services & Solutions | SiS",
            description:
              "Turnkey industrial engineering, automation, and digital solutions engineered by SiS.",
          },
        },
      },
      {
        path: "use-cases",
        name: "use-cases",
        component: UseCases,
        meta: {
          title: "Use Cases | SiS",
          seo: {
            title: "Use Cases | SiS",
            description:
              "Real-world industrial automation and digital transformation case studies delivered across multiple industries.",
          },
        },
      },
      {
        path: ":pathMatch(.*)*",
        name: "not-found",
        component: () => import("@/pages/NotFound/NotFoundPage.vue"),
        meta: {
          title: "Page Not Found | SiS",
          robots: PRIVATE_ROBOTS,
          seo: {
            title: "Page Not Found | SiS",
            description:
              "The page you are looking for does not exist or has been moved.",
            robots: PRIVATE_ROBOTS,
          },
        },
      },
    ],
  },
  ...adminRoutes,
];

export const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    if (to.hash) {
      return { el: to.hash, behavior: "smooth" };
    }
    return { top: 0 };
  },
});

router.beforeEach((to, from, next) => {
  navigating.value = true;

  // Check if route requires authentication
  const requiresAuth = to.matched.some((record) => record.meta?.requiresAuth);
  const token = localStorage.getItem("admin_token");
  const expiry = localStorage.getItem("admin_token_expiry");
  const isExpired = Boolean(expiry && Date.now() >= Number(expiry));

  if (requiresAuth) {
    if (!token || isExpired) {
      if (isExpired) {
        localStorage.removeItem("admin_token");
        localStorage.removeItem("admin_user");
        localStorage.removeItem("admin_token_expiry");
        localStorage.removeItem("admin_refresh_token");
        next({ name: "admin-login", query: { session_expired: "1" } });
        return;
      }
      // Redirect to login if trying to access protected route without token
      next({ name: "admin-login" });
      return;
    }
  } else if (to.name === "admin-login" && token && !isExpired) {
    // Redirect to dashboard if already logged in and session active
    next({ name: "admin-home" });
    return;
  }

  next();
});

router.afterEach((to) => {
  const isPrivate = to.matched.some(
    (record) => record.meta?.requiresAuth || record.name === "admin-login"
  );
  const isNotFound = to.matched.length === 0 || to.name === "not-found";

  let pageOverride = null;
  try {
    const siteStore = useSiteStore();
    if (siteStore?.pagesSeo && to.name && siteStore.pagesSeo[to.name]) {
      pageOverride = siteStore.pagesSeo[to.name];
    }
  } catch {
    // pinia not yet active
  }

  const seoConfig = {
    ...to.meta?.seo,
    title: isNotFound
      ? "Page Not Found | SiS"
      : pageOverride?.meta_title || to.meta?.title || to.meta?.seo?.title || defaultTitle,
    description: pageOverride?.meta_description || to.meta?.seo?.description,
    canonical: pageOverride?.canonical_url || to.path,
    image: pageOverride?.og_image,
    robots: isPrivate || isNotFound
      ? PRIVATE_ROBOTS
      : (pageOverride?.is_noindex ? "noindex, nofollow" : to.meta?.robots || to.meta?.seo?.robots),
  };

  applySeo(seoConfig);
  navigating.value = false;
});

