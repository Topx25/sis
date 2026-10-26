import { httpsUrl } from "./index.js";
import { localImageUrl } from "./imageFallback.js";
import useCasesProjects from "@/dummydata/useCasesProjects";

const initials = (name = "") =>
  String(name)
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((word) => word[0])
    .join("")
    .toUpperCase();


export function normalizeProjects(result) {
  const list = Array.isArray(result?.projects)
    ? result.projects
    : Array.isArray(result)
      ? result
      : [];
  return list.map((project) => ({
    id: project.id,
    client: project.client_name || project.client || "",
    sector: project.sector?.name || project.sector || "",
    location: project.location || "",
    title: project.title || "",
    summary: project.summary || "",
    tags: project.tags || [],
    gallery_limit: project.gallery_limit,
    logo: localImageUrl(
      project.cover_image
        ? httpsUrl(project.cover_image)
        : project.logo
          ? httpsUrl(project.logo)
          : "",
    ),
    mark:
      project.cover_image || project.logo
        ? ""
        : initials(project.client_name || project.client),
  }));
}

export function normalizeSectors() {
  return {
    success: true,
    message: "Sectors retrieved successfully.",
    data: {
      sectors: [
        {
          id: 1,
          name: "Oil & Gas",
          slug: "oil-gas",
          icon: "oil",
          description:
            "Upstream, midstream, and downstream digital oilfield instrumentation and safety systems.",
          projects_count: 2,
        },
        {
          id: 2,
          name: "Water & Waste Water",
          slug: "water-waste-water",
          icon: "water",
          description:
            "Desalination plants, wastewater treatment, and regional water distribution automation.",
          projects_count: 4,
        },
        {
          id: 3,
          name: "Chemical Industries",
          slug: "chemical-industries",
          icon: "science",
          description:
            "Chemical process optimization, safety valve control, and hazardous environment automation.",
          projects_count: 2,
        },
        {
          id: 4,
          name: "Food, Beverage & FMCG",
          slug: "food-beverage-fmcg",
          icon: "fastfood",
          description:
            "High-speed packaging, chemical mixing, ERP traceability, and thermal process control.",
          projects_count: 3,
        },
        {
          id: 5,
          name: "Heavy Industries",
          slug: "heavy-industries",
          icon: "factory",
          description:
            "Cement manufacturing, steel foundries, kiln control, and conveyor systems.",
          projects_count: 2,
        },
      ],
      capabilities: {
        title: "The SiS Capability Matrix",
        columns: [
          "SCADA Upgrades",
          "ERP Integration",
          "Hardware Commissioning",
          "Safety & Shutdown Systems",
          "Network Cybersecurity",
        ],
        rows: [
          {
            client: "Henkel",
            capabilities: [true, false, true, false, false],
          },
          {
            client: "BariQ",
            capabilities: [true, true, true, true, false],
          },
          {
            client: "Gas Cool",
            capabilities: [true, false, true, false, true],
          },
          {
            client: "Emisal",
            capabilities: [true, true, true, true, false],
          },
          {
            client: "Saline Water",
            capabilities: [true, false, true, true, true],
          },
          {
            client: "Saudi Aramco",
            capabilities: [true, false, true, true, true],
          },
          {
            client: "B\u00e9ja\u00efa RO",
            capabilities: [true, false, true, true, true],
          },
        ],
      },
    },
  };
}

export function normalizeServices(data = []) {
  const root =
    data && typeof data === "object" && !Array.isArray(data) && data.data
      ? data.data
      : data;

  if (root && typeof root === "object" && !Array.isArray(root)) {
    const list = Array.isArray(root.services)
      ? root.services
      : Array.isArray(root)
        ? root
        : [];
    return {
      section: root.section || null,
      services: list.length > 0 ? list : getFallbackServices(),
    };
  }

  const list = Array.isArray(root) ? root : [];
  return {
    section: null,
    services: list.length > 0 ? list : getFallbackServices(),
  };
}
export function getFallbackServices() {
  return [
    {
      id: 1,
      title: "Industrial Automation",
      slug: "industrial-automation",
      summary:
        "Pioneering technology solutions for the modern factory and large-scale infrastructure implementation.",
      description:
        "Pioneering technology solutions for the modern factory and large-scale infrastructure implementation.",
      icon: "robot-arm",
    },
    {
      id: 2,
      title: "SCADA & Dashboard",
      slug: "scada-dashboard",
      summary:
        "Delivering real-time monitoring and robust control of industrial processes via Supervisory Control and Data Acquisition.",
      description:
        "Delivering real-time monitoring and robust control of industrial processes via Supervisory Control and Data Acquisition.",
      icon: "monitor",
    },
    {
      id: 3,
      title: "IIoT & Data Analytics",
      slug: "iiot-data-analytics",
      summary:
        "Connecting physical assets to digital ecosystems to transform raw telemetry into actionable intelligence.",
      description:
        "Connecting physical assets to digital ecosystems to transform raw telemetry into actionable intelligence.",
      icon: "cpu",
    },
    {
      id: 4,
      title: "Custom Solutions & Outsourcing",
      slug: "custom-solutions-outsourcing",
      summary:
        "Tailored engineering services and AI-powered technologies that drive efficiency, agility, and measurable results.",
      description:
        "Tailored engineering services and AI-powered technologies that drive efficiency, agility, and measurable results.",
      icon: "settings",
    },
  ];
}
  

export function normalizeWorkSamples(result) {
  const list = Array.isArray(result)
    ? result
    : Array.isArray(result?.work_samples)
      ? result.work_samples
      : [];
  return list.map((sample) => ({
    id: sample.id,
    title: sample.title || "",
    industry: sample.category || sample.industry || "",
    blurb: sample.body || sample.blurb || "",
    badge: sample.badge || "",
    slug:
      sample.slug ||
      slugFromDemoUrl(sample.demo_url) ||
      String(sample.id ?? ""),
    imageUrl: localImageUrl(
      sample.image_url
        ? httpsUrl(sample.image_url)
        : sample.image
          ? httpsUrl(sample.image)
          : "",
    ),
    demoUrl: sample.demo_url ? httpsUrl(sample.demo_url) : "",
    free: sample.free,
  }));
}

export function parseTimelineYear(yearStr) {
  if (!yearStr) return 0;
  const match = String(yearStr).match(/\d+/);
  if (!match) return 0;
  let num = parseInt(match[0], 10);
  if (num < 100) {
    num += 2000;
  }
  return num;
}

export function normalizeTimeline(result) {
  const list = Array.isArray(result)
    ? result
    : Array.isArray(result?.data)
      ? result.data
      : Array.isArray(result?.timeline)
        ? result.timeline
        : [];
  const mapped = list.map((event) => ({
    id: event.id,
    year: String(event.year || ""),
    title: event.title || "",
    text: event.description || event.text || "",
    icon: event.icon || "",
  }));

  return mapped.sort((a, b) => {
    const yA = parseTimelineYear(a.year);
    const yB = parseTimelineYear(b.year);
    if (yA !== yB) return yA - yB;
    return (a.id || 0) - (b.id || 0);
  });
}

const CLIENT_IMAGES = [
  ["Delta", "delta"],
  ["Emisal", "emitsal"],
  ["Energeya", "energeya"],
  ["GCF", "gcf"],
  ["Giza Systems", "giza_systems"],
  ["Henkel", "henkel"],
  ["Omis", "omis"],
  ["Orascom Construction", "orascom_const"],
  ["Plast Company", "plast_company"],
  ["Tabreed", "tabreed"],
  ["Uniha", "uniha"],
];

const clientFallback = CLIENT_IMAGES.map(([name, file], i) => ({
  id: i + 1,
  name,
  src: `${import.meta.env.BASE_URL}images/ourClints/${file}.webp`,
  logo_url: `${import.meta.env.BASE_URL}images/ourClints/${file}.jpg`,
  sort_order: i + 1,
}));

export function normalizeClients(result) {
  let list = [];
  if (Array.isArray(result)) {
    list = result;
  } else if (result && typeof result === "object") {
    if (Array.isArray(result.clients)) {
      list = result.clients;
    } else if (Array.isArray(result.data)) {
      list = result.data;
    } else if (result.data && typeof result.data === "object" && Array.isArray(result.data.clients)) {
      list = result.data.clients;
    }
  }

  if (!list.length) return clientFallback;

  return list.map((client) => {
    const src = client.src || client.logo_url || "";

    return {
      id: client.id,
      name: client.name || "",
      src,
      logo_url: src,
      sort_order: client.sort_order,
    };
  });
}

export function normalizeHero() {
  return [
    {
      "id": 1,
      "src": "images/about-hero.webp",
      "alt": "Industrial automation control interface",
      "title": "Engineering smarter industrial operations",
      "description": "Connect automation, data, and intelligence to build more efficient and reliable operations."
    },
    {
      "id": 2,
      "src": "images/about-image.png",
      "alt": "Industrial energy management monitoring dashboard",
      "title": "Turning industrial data into insight",
      "description": "Transform operational data into clearer decisions, better performance, and measurable efficiency."
    },
    {
      "id": 3,
      "src": "images/controlling-2.webp",
      "alt": "Industrial maintenance management system",
      "title": "Reliability built into every operation",
      "description": "Keep critical assets performing with proactive monitoring, predictive maintenance, and smarter workflows."
    },
    {
      "id": 4,
      "src": "images/factory.webp",
      "alt": "Smart manufacturing industrial facility",
      "title": "Building the connected factory",
      "description": "Integrate machines, systems, and people to create smarter, more connected manufacturing environments."
    }
  ] }
  

  export function normalizeWhySis(){
    return [
    {
      "id": 1,
      "title": "Industrial intelligence",
      "description": "Turn operational data into clear decisions across your plant, people, and platforms."
    },
    {
      "id": 2,
      "title": "Built to connect",
      "description": "Bridge OT and IT with practical systems that work with the technology you already have."
    },
    {
      "id": 3,
      "title": "Designed for momentum",
      "description": "Create a resilient foundation that can adapt as your goals, markets, and operations evolve."
    }
  ] }

  const GLOW_COLORS = ["#00d4ff", "#00ff55", "#9d55ff", "#c254ff"];

export function normalizeEcosystem(result) {
  const fallback = {
    header: {
      id: 1,
      title: "Our Trusted Partners",
      subtitle:
        "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
      icon: "handshake",
      is_active: true,
      home_subtitle: "Our Partners",
      home_title: "Building stronger",
      home_title_highlight: "ecosystems",
      home_description:
        "We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.",
    },
    featured_partners: [
      {
        id: 1,
        name: "Siemens",
        logo_url: "/images/partners/Siemens.png",
        website_url: "https://www.siemens.com",
        type: "technology_partner",
        is_featured: true,
        tagline: "Ingenuity for life",
        description:
          "Driving innovation and digital transformation with industry-leading automation and digitalization technologies.",
        glow: "#00d4ff",
        solutions_link: "/services",
        sort_order: 1,
      },
      {
        id: 3,
        name: "Schneider Electric",
        logo_url: "/images/partners/schnider.webp",
        website_url: "https://www.se.com",
        type: "technology_partner",
        is_featured: true,
        tagline: "Life Is On",
        description:
          "Enabling sustainable and efficient solutions for energy management and industrial automation.",
        glow: "#00ff55",
        solutions_link: "/services",
        sort_order: 2,
      },
      {
        id: 7,
        name: "AVEVA",
        logo_url: "/images/partners/aveva.webp",
        website_url: "https://www.aveva.com",
        type: "technology_partner",
        is_featured: true,
        tagline: "Transforming Industrial Operations",
        description:
          "Empowering industries with software that drives performance improvement, engineering and innovation.",
        glow: "#9d55ff",
        solutions_link: "/services",
        sort_order: 3,
      },
      {
        id: 11,
        name: "Odoo",
        logo_url: "/images/partners/odoo.svg",
        website_url: "https://www.odoo.com",
        type: "technology_partner",
        is_featured: true,
        tagline: "All-in-One Business Software",
        description:
          "All-in-one business management software to streamline operations, boost productivity and drive business growth.",
        glow: "#c254ff",
        solutions_link: "/services",
        sort_order: 4,
      },
    ],
    partners: [
      {
        id: 2,
        name: "Rockwell Automation",
        logo_url: "/images/partners/Rockwell_Automation_Logo.png",
        website_url: "https://www.rockwellautomation.com",
        type: "technology_partner",
        is_featured: false,
        tagline: null,
        description: null,
        glow: null,
        solutions_link: "/services",
        sort_order: 2,
      },
      {
        id: 4,
        name: "ABB",
        logo_url: "/images/partners/ABB.png",
        website_url: "https://www.abb.com",
        type: "technology_partner",
        is_featured: false,
        tagline: null,
        description: null,
        glow: null,
        solutions_link: "/services",
        sort_order: 4,
      },
      {
        id: 5,
        name: "Mitsubishi Electric",
        logo_url: "/images/partners/Mitsubishi_Electric.png",
        website_url: "https://www.mitsubishielectric.com",
        type: "technology_partner",
        is_featured: false,
        tagline: null,
        description: null,
        glow: null,
        solutions_link: "/services",
        sort_order: 5,
      },
      {
        id: 6,
        name: "Huawei",
        logo_url: "/images/partners/Huawei.png",
        website_url: "https://www.huawei.com",
        type: "technology_partner",
        is_featured: false,
        tagline: null,
        description: null,
        glow: null,
        solutions_link: "/services",
        sort_order: 6,
      },
      {
        id: 8,
        name: "PTC",
        logo_url: "/images/partners/PTC.png",
        website_url: "https://www.ptc.com",
        type: "ecosystem_network",
        is_featured: false,
        tagline: null,
        description: null,
        glow: null,
        solutions_link: "/services",
        sort_order: 8,
      },
      {
        id: 9,
        name: "Egypt 4 Industry",
        logo_url: "/images/partners/Egypt_4_Industry.jpg",
        website_url: "https://egypt4industry.com",
        type: "ecosystem_network",
        is_featured: false,
        tagline: null,
        description: null,
        glow: null,
        solutions_link: "/services",
        sort_order: 9,
      },
      {
        id: 10,
        name: "Odoo",
        logo_url: "/images/partners/odoo.svg",
        website_url: "https://www.odoo.com",
        type: "ecosystem_network",
        is_featured: false,
        tagline: null,
        description: null,
        glow: null,
        solutions_link: "/services",
        sort_order: 10,
      },
    ],
    value_badges: [
      {
        title: "Strong Alliances",
        description:
          "Strategic partnerships with global technology leaders.",
        icon: "handshake",
      },
      {
        title: "Certified Excellence",
        description:
          "Certified experts delivering industry best practices.",
        icon: "verified",
      },
      {
        title: "Integrated Solutions",
        description:
          "Seamless integration of leading technologies.",
        icon: "extension",
      },
      {
        title: "Shared Success",
        description:
          "Driving innovation and growth together.",
        icon: "trending_up",
      },
    ],
  };

  if (!result || typeof result !== "object") {
    return fallback;
  }

  const allPartners = Array.isArray(result.partners)
    ? result.partners
    : Array.isArray(result)
      ? result
      : [];

  const normalizePartner = (partner, index) => ({
    id: partner.id,
    name: partner.name || "",
    logo_url: partner.logo_url
      ? httpsUrl(partner.logo_url)
      : partner.logo
        ? httpsUrl(partner.logo)
        : "",
    website_url: partner.website_url || "",
    type: partner.type || "technology_partner",
    is_featured: Boolean(partner.is_featured),
    tagline: partner.tagline || null,
    description: partner.description || "",
    glow: partner.glow || GLOW_COLORS[index % GLOW_COLORS.length],
    solutions_link: partner.solutions_link || "/services",
    sort_order: partner.sort_order || index + 1,
  });

  const featuredPartners = Array.isArray(result.featured_partners)
    ? result.featured_partners.map((p, i) => normalizePartner(p, i))
    : allPartners
        .filter((p) => p.is_featured)
        .map((p, i) => normalizePartner(p, i));

  const secondaryPartners = allPartners
    .filter((p) => !p.is_featured)
    .map((p, i) => normalizePartner(p, i));

  const valueBadges = Array.isArray(result.value_badges)
    ? result.value_badges
    : Array.isArray(result.value_badge)
      ? result.value_badge
      : fallback.value_badges;

  return {
    header: {
      ...fallback.header,
      ...(result.header || {}),
    },
    featured_partners: featuredPartners.length ? featuredPartners : fallback.featured_partners,
    partners: secondaryPartners.length ? secondaryPartners : fallback.partners,
    value_badges: valueBadges.length ? valueBadges : fallback.value_badges,
  };
}
  
export function normalizeUseCases(result) {
  const normalizeProject = (project = {}) => ({
    id: project.id,
    title: project.title || "",
    client: project.client_name || project.client || "",
    industry: project.sector?.name || project.industry || "",
    location: project.location || "",
    shortDescription:
      project.summary || project.shortDescription || project.description || "",
    coverImage: httpsUrl(project.cover_image || project.coverImage),
    images: Array.isArray(project.images)
      ? project.images.map((image) => httpsUrl(image))
      : [],
    metrics: Array.isArray(project.metrics) ? project.metrics : [],
    scope: Array.isArray(project.scope) ? project.scope : [],
    technologies: Array.isArray(project.technologies)
      ? project.technologies
      : [],
    results: Array.isArray(project.results) ? project.results : [],
    slug: project.slug || "",
    isFeatured: Boolean(project.is_featured || project.isFeatured),
    sector: project.sector || null,
    services: Array.isArray(project.services) ? project.services : [],
    tags: Array.isArray(project.tags)
      ? project.tags
      : typeof project.tags === "string"
        ? project.tags.split(",")
        : [],
    gallery_limit: project.gallery_limit,
    createdAt: project.created_at || project.createdAt || null,
  });

  const fallbackProjects = useCasesProjects.map(normalizeProject);
  const fallback = {
    header: {
      title: "Projects & Case Studies",
      subtitle:
        "Real-world automation and digital transformation projects delivered across multiple industries using leading technologies.",
      cta: {
        text: "Explore All Projects",
        link: "/projects",
      },
      stats: [
        { value: "200+", label: "Projects Delivered", icon: "cube" },
        { value: "150+", label: "Happy Clients", icon: "connect" },
        { value: "15+", label: "Years of Experience", icon: "calendar" },
        { value: "9+", label: "Countries Served", icon: "globe" },
      ],
      pillars: [
        {
          title: "Innovation",
          description: "Driving smart solutions with cutting-edge technologies.",
          icon: "cube",
        },
        {
          title: "Reliability",
          description: "Proven performance and long-term partnership.",
          icon: "shield",
        },
        {
          title: "Global Reach",
          description: "Delivering value across 12+ countries.",
          icon: "globe",
        },
        {
          title: "Sustainability",
          description: "Building a smarter and cleaner tomorrow.",
          icon: "energy",
        },
      ],
    },
    featured_project: fallbackProjects[0] || null,
    projects: fallbackProjects,
    sectors: [],
    pillars: [],
  };

  if (!result || typeof result !== "object") {
    return fallback;
  }

  const projects = Array.isArray(result.projects)
    ? result.projects.map(normalizeProject)
    : fallback.projects;

  const featuredProject = result.featured_project
    ? normalizeProject(result.featured_project)
    : projects.find((project) => project.isFeatured) || projects[0] || null;

  return {
    header: {
      ...fallback.header,
      ...(result.header || {}),
    },

    featured_project: featuredProject,

    projects,

    sectors: Array.isArray(result.sectors)
      ? result.sectors
      : [],

    pillars: Array.isArray(result.pillars) && result.pillars.length
      ? result.pillars
      : Array.isArray(result.header?.pillars) && result.header.pillars.length
        ? result.header.pillars
        : fallback.pillars,
  };
}


export function normalizeAboutVision() {
  return {
    success: true,
    message: "About section retrieved successfully.",
    data: {
      id: 1,
      title: "Who We Are",
      headline: "Our Mission & Vision",
      subheadline:
        "Driving industrial excellence through intelligent IT-OT convergence across the MENA region.",
      paragraphs: [
        "Superior Integrated Solutions (SiS) is a specialist provider of industrial automation, IIoT, and data-driven engineering solutions tailored to heavy industries across MENA.",
        "Our approach combines field-proven OT engineering with modern data science and cloud-first analytics to deliver measurable business outcomes such as reduced downtime and optimized throughput.",
        "Our multidisciplinary team includes control systems engineers, data scientists, and systems integrators who work alongside client teams from feasibility through sustained operations.",
      ],
      mission: {
        tagline: "OUR MISSION",
        title: "Our Mission",
        description:
          "To bridge Operational Technology (OT) and Information Technology (IT) through real-time data-driven decision-making.",
        icon: "flag",
      },
      vision: {
        tagline: "OUR VISION",
        title: "Our Vision",
        description:
          "To establish a future standard of predictive maintenance and complete process optimization across the region's heaviest industries.",
        icon: "visibility",
      },
      why_choose_us: {
        title: "Why Choose SiS?",
        description:
          "We combine deep industry expertise with cutting-edge technology to deliver sustainable, future-proof industrial solutions across the MENA region.",
      },
      why_sis: {
        title: "Why Choose SiS?",
        description:
          "We combine deep industry expertise with cutting-edge technology to deliver sustainable, future-proof industrial solutions across the MENA region.",
      },
      pillars: [
        {
          icon: "mission",
          title: "Mission",
          body: "To bridge Operational Technology (OT) and Information Technology (IT) through real-time data-driven decision-making.",
        },
        {
          icon: "vision",
          title: "Vision",
          body: "To establish a future standard of predictive maintenance and complete process optimization across the region's heaviest industries.",
        },
        {
          icon: "values",
          title: "Core Values",
          body: "Delivering excellence through a team of highly skilled engineers, blending deep industrial legacy with modern data science.",
        },
      ],
      principles: [
        {
          title: "Precision",
          body: "Execution of highly complex industrial automation and data science with exactitude.",
        },
        {
          title: "Innovation",
          body: "Pioneering custom software and IIoT approaches to solve legacy challenges.",
        },
        {
          title: "Reliability",
          body: "Successfully implementing and sustaining large-scale solutions for critical regional industries.",
        },
      ],
    },
  };
}

export function normalizeAboutHero() {
  return {
    success: true,
    message: "About hero section retrieved successfully.",
    data: {
      id: 1,
      tagline: "PIONEERING INDUSTRIAL EVOLUTION",
      headline: "Architects of the 4th Industrial Revolution",
      subheadline:
        "Superior Integrated Solutions (SiS) bridges the critical divide between Operational Technology and Information Technology, engineering the intelligent ecosystems of tomorrow.",
      cta_text: "Discover Our Expertise",
      cta_link: "/projects",
      image: "images/about-hero.webp",
      image_path: "images/about-hero.webp",
      image_url: "images/about-hero.webp",
    },
  };
}

export function normalizeAboutItOt() {
  return {
    success: true,
    message: "IT-OT divide section retrieved successfully.",
    data: {
      id: 1,
      tagline: "THE CONVERGENCE",
      headline: "Bridging the IT-OT Divide",
      subheadline:
        "Historically, the shop floor and the top floor spoke different languages. SiS acts as the master translator, unifying these domains into a single, cohesive nervous system.",
      ot: {
        title: "Operational Technology (OT)",
        description:
          "The hardware and software that detects or causes a change through the direct monitoring and/or control of industrial equipment, assets, processes, and events.",
        tags: ["SCADA", "PLCs", "Sensors", "DCS"],
        icon: "precision_manufacturing",
      },
      it: {
        title: "Information Technology (IT)",
        description:
          "The use of computers, storage, networking, and other physical devices to create, process, store, secure, and exchange all forms of electronic data.",
        tags: ["ERP", "Cloud", "Analytics", "MES"],
        icon: "cloud",
      },
    },
  };
}

export function normalizeAboutCore() {
  return {
    success: true,
    message: "Core values section retrieved successfully.",
    data: {
      id: 1,
      tagline: "WHAT WE STAND FOR",
      headline: "Core Values & Principles",
      subheadline:
        "Delivering excellence through a team of highly skilled engineers, blending deep industrial legacy with modern data science.",
      principles: [
        {
          step: "01",
          title: "Precision",
          description:
            "Execution of highly complex industrial automation and data science with exactitude.",
          icon: "verified_user",
        },
        {
          step: "02",
          title: "Innovation",
          description:
            "Pioneering custom software and IIoT approaches to solve legacy challenges.",
          icon: "lightbulb",
        },
        {
          step: "03",
          title: "Reliability",
          description:
            "Successfully implementing and sustaining large-scale solutions for critical regional industries.",
          icon: "verified",
        },
      ],
    },
  };
}

export function normalizeDomains() {
  return {
    success: true,
    message: "Empowered domains section retrieved successfully.",
    data: {
      id: 1,
      tagline: "OUR DOMAIN EXPERTISE",
      headline: "Industries We Empower",
      subheadline:
        "Deep technical understanding applied across the most demanding industrial sectors.",
      domains: [
        {
          icon: "oil",
          title: "Oil & Gas",
          description:
            "Deployment of Well Head Wireless HART Transmitters, SCADA integration, and Well Heads Safety System Upgrades for upstream oil and gas operations.",
        },
        {
          icon: "science",
          title: "Chemical Industries",
          description:
            "Advanced automation solutions for process optimisation within chemical manufacturing plants, including centralised SCADA systems.",
        },
        {
          icon: "engineering",
          title: "Heavy Industries",
          description:
            "Industrial automation and support services for large-scale cement manufacturing and metal foundry operations.",
        },
        {
          icon: "water",
          title: "Water & Waste Water",
          description:
            "PLC and SCADA commissioning, network cybersecurity hardening, and full treatment process automation for water infrastructure.",
        },
        {
          icon: "factory",
          title: "Food, Beverage & FMCG",
          description:
            "SCADA upgrades, ERP integration, AR-assisted validation, and specialised automation for food production and chemical mixing.",
        },
      ],
    },
  };
}

function toCleanText(val) {
  if (val === null || val === undefined) return "";
  if (typeof val === "string") return val.trim();
  if (typeof val === "number") return String(val);
  if (typeof val === "object") {
    if (Array.isArray(val)) {
      return val.map((v) => toCleanText(v)).filter(Boolean).join(" ");
    }
    return toCleanText(val.en || val.ar || Object.values(val)[0] || "");
  }
  return "";
}

function toEmailsList(val) {
  if (!val) return [];
  if (Array.isArray(val)) {
    return val
      .map((item) => toCleanText(item))
      .filter(Boolean);
  }
  if (typeof val === "string") {
    return val
      .split(/[,\n;]+/)
      .map((s) => s.trim())
      .filter(Boolean);
  }
  return [];
}

function toPhonesList(val) {
  if (!val) return [];
  if (Array.isArray(val)) {
    return val
      .map((item) => toCleanText(item))
      .filter(Boolean);
  }
  if (typeof val === "string") {
    return val
      .split(/[,\n;]+/)
      .map((s) => s.trim())
      .filter(Boolean);
  }
  return [];
}

export function normalizeContactPage(raw) {
  if (!raw || typeof raw !== "object") {
    return null;
  }

  const data = raw.data !== undefined ? raw.data : raw;
  if (!data || typeof data !== "object") {
    return null;
  }

  const website = toCleanText(data.website);
  const emails = toEmailsList(data.emails || data.email);
  const email = emails[0] || toCleanText(data.email) || "";
  const phones = toPhonesList(data.phones);
  const headline =
    toCleanText(data.headline) ||
    toCleanText(data.title) ||
    "";
  const ctaTitle = toCleanText(data.cta_title);
  const ctaDescription = toCleanText(data.cta_description);
  const ctaLinkText = toCleanText(data.cta_link_text);
  const ctaLink = toCleanText(data.cta_link);

  // If every field is empty/null, return null so existing fallbackContact is used
  if (!headline && !website && emails.length === 0 && phones.length === 0 && !ctaTitle && !ctaDescription) {
    return null;
  }

  return {
    id: data.id ?? null,
    headline,
    website,
    email,
    emails,
    phones,
    cta_title: ctaTitle,
    cta_description: ctaDescription,
    cta_link_text: ctaLinkText,
    cta_link: ctaLink,
  };
}
