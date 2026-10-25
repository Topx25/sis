const factoryImg = `${import.meta.env.BASE_URL}images/factory.webp`;
const aboutHeroImg = `${import.meta.env.BASE_URL}images/about-hero.webp`;

const logoSiemens = `${import.meta.env.BASE_URL}images/partners/Siemens.png`;
const logoRockwell = `${import.meta.env.BASE_URL}images/partners/Rockwell_Automation_Logo.png`;
const logoSchnider = `${import.meta.env.BASE_URL}images/partners/schnider.jpg`;
const logoAbb = `${import.meta.env.BASE_URL}images/partners/ABB.png`;
const logoMitsubishi = `${import.meta.env.BASE_URL}images/partners/Mitsubishi_Electric.png`;
const logoHuawei = `${import.meta.env.BASE_URL}images/partners/Huawei.png`;
const logoAveva = `${import.meta.env.BASE_URL}images/partners/aveva.jpg`;
const logoPtc = `${import.meta.env.BASE_URL}images/partners/PTC.png`;
const logoEgypt4 = `${import.meta.env.BASE_URL}images/partners/Egypt_4_Industry.jpg`;
const logoOdoo = `${import.meta.env.BASE_URL}images/partners/odoo.svg`;

const logoKhalad = `${import.meta.env.BASE_URL}images/ourClints/khalad-Petroleum.webp`;
const logoSaudiAramco = `${import.meta.env.BASE_URL}images/ourClints/Saudi-Aramco.webp`;
const logoSwcc = `${import.meta.env.BASE_URL}images/ourClints/SWCC.webp`;
const logoQaliobia = `${import.meta.env.BASE_URL}images/ourClints/Qaliobia-Waste-Water.webp`;
const logoUniha = `${import.meta.env.BASE_URL}images/ourClints/uniha.webp`;
const logoGasCool = `${import.meta.env.BASE_URL}images/ourClints/Gas-Cool.webp`;
const logoEmisal = `${import.meta.env.BASE_URL}images/ourClints/emitsal.webp`;
const logoHenkel = `${import.meta.env.BASE_URL}images/ourClints/henkel.webp`;
const logoBariq = `${import.meta.env.BASE_URL}images/ourClints/BariQ.webp`;
const logoTakFoods = `${import.meta.env.BASE_URL}images/ourClints/TAK-Foods-Factory.webp`;
const logoAlTakamol = `${import.meta.env.BASE_URL}images/ourClints/Al-Takamol-Cement.webp`;
const logoGcf = `${import.meta.env.BASE_URL}images/ourClints/gcf.webp`;
const logoDelta = `${import.meta.env.BASE_URL}images/ourClints/delta.webp`;
const logoEnergeya = `${import.meta.env.BASE_URL}images/ourClints/energeya.webp`;
const logoGiza = `${import.meta.env.BASE_URL}images/ourClints/giza_systems.webp`;
const logoOmis = `${import.meta.env.BASE_URL}images/ourClints/omis.webp`;
const logoOrascom = `${import.meta.env.BASE_URL}images/ourClints/orascom_const.webp`;
const logoPlast = `${import.meta.env.BASE_URL}images/ourClints/plast_company.webp`;
const logoTabreed = `${import.meta.env.BASE_URL}images/ourClints/tabreed.webp`;

const sectors = [
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
];

const sectorByName = (name) =>
  sectors.find((s) => s.name === name) || sectors[0];

const projectRows = [
  {
    client: "Khalda Petroleum",
    sector: "Oil & Gas",
    location: "Marsa Matrouh, Egypt",
    title: "Digital oil-field instrumentation",
    summary:
      "Deployment of Well Head Wireless HART Transmitters integrated into SCADA for real-time field control and remote monitoring across multiple well sites.",
    tags: ["SCADA", "HART", "Wireless"],
    logo: logoKhalad,
  },
  {
    client: "Saudi Aramco",
    sector: "Oil & Gas",
    location: "KSA",
    title: "SIL-2 wellhead safety-system upgrades",
    summary:
      "Well Heads Safety System Upgrades using SIEMENS S7-400H, PCS7, Schneider Electric Triconex, and Foxboro technologies to meet SIL-2 safety standards.",
    tags: ["Safety Systems", "SIL-2", "Siemens PCS7"],
    logo: logoSaudiAramco,
  },
  {
    client: "Béjaïa RO Plant",
    sector: "Water & Waste Water",
    location: "Algeria",
    title: "Resilient desalination control",
    summary:
      "Full commissioning of PLC and SCADA systems with network cybersecurity hardening for a 50,000 m³/day Reverse Osmosis desalination facility.",
    tags: ["PLC", "SCADA", "Cybersecurity"],
    logo: null,
  },
  {
    client: "SWCC",
    sector: "Water & Waste Water",
    location: "KSA",
    title: "Large-scale desalination commissioning",
    summary:
      "Hardware commissioning, software logic validation, and field integration for large-scale desalination operations serving 1.2M residents.",
    tags: ["Hardware", "Commissioning", "DCS"],
    logo: logoSwcc,
  },
  {
    client: "Qaliobia Waste Water",
    sector: "Water & Waste Water",
    location: "Egypt",
    title: "End-to-end wastewater delivery",
    summary:
      "Full 6-phase project spanning design, civil, electrical, instrumentation, automation, and startup for a regional waste water treatment plant.",
    tags: ["End-to-End", "Automation", "IIoT"],
    logo: logoQaliobia,
  },
  {
    client: "UNIHA Water Technology",
    sector: "Water & Waste Water",
    location: "Al Arish, Egypt",
    title: "Five-stage RO process automation",
    summary:
      "Full automation of a 5-stage RO water treatment process over an architectural Ethernet backbone, enabling remote diagnostics.",
    tags: ["Ethernet", "RO Automation"],
    logo: logoUniha,
  },
  {
    client: "Gas Cool",
    sector: "Chemical Industries",
    location: "Fayoum, Egypt",
    title: "Chemical process optimisation",
    summary:
      "Advanced automation solutions designed for process optimisation within chemical manufacturing plants.",
    tags: ["Process Control", "Automation"],
    logo: logoGasCool,
  },
  {
    client: "Emisal",
    sector: "Chemical Industries",
    location: "Fayoum, Egypt",
    title: "Centralised safety and valve control",
    summary:
      "Centralised SCADA fire alarm system with WinCC SCADA and Siemens PLC, plus high-pressure steam and chemical valve control for chemical manufacturing.",
    tags: ["SCADA", "Fire Safety", "Siemens"],
    logo: logoEmisal,
  },
  {
    client: "Henkel",
    sector: "Food, Beverage & FMCG",
    location: "6th of October, Egypt",
    title: "Advanced process automation and live validation",
    summary:
      "SCADA upgrade, process automation for chemical mixing, Siemens TIA thermal control, chemical dosing, and AR-assisted live system validation.",
    tags: ["SCADA Upgrade", "AR Validation", "TIA"],
    logo: logoHenkel,
  },
  {
    client: "BariQ",
    sector: "Food, Beverage & FMCG",
    location: "Cairo, Egypt",
    title: "ERP-connected production intelligence",
    summary:
      "Specialised automation with ODOO ERP integration and smart SCADA reporting for real-time granular process monitoring and traceability.",
    tags: ["ERP Integration", "Smart Reporting"],
    logo: logoBariq,
  },
  {
    client: "TAK Foods Factory",
    sector: "Food, Beverage & FMCG",
    location: "Menofia, Egypt",
    title: "Production-line electrical and controls renewal",
    summary:
      "Standardised Siemens PLC platform, modern HMI dashboards, variable frequency drives, and complete electrical panel renewal for production lines.",
    tags: ["Siemens PLC", "VFD", "HMI"],
    logo: logoTakFoods,
  },
  {
    client: "Al Takamol Cement",
    sector: "Heavy Industries",
    location: "Sudan",
    title: "Cement-production automation support",
    summary:
      "Industrial automation and support services for large-scale cement manufacturing, including kiln control and conveyor belt management systems.",
    tags: ["Kiln Control", "PLC", "SCADA"],
    logo: logoAlTakamol,
  },
  {
    client: "Greater Cairo Foundries (GCF)",
    sector: "Heavy Industries",
    location: "Sharqia, Egypt",
    title: "Foundry-wide automation modernisation",
    summary:
      "New Siemens automation system, SCADA WinCC upgrade, and full power/control panel supply, assembly and startup for metal foundry operations.",
    tags: ["WinCC", "Power Panels", "Siemens"],
    logo: logoGcf,
  },
];

const slugify = (s) =>
  s
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "");

const projects = projectRows.map((p, i) => {
  const sector = sectorByName(p.sector);
  return {
    id: i + 1,
    title: p.title,
    slug: slugify(p.title),
    client_name: p.client,
    location: p.location,
    summary: p.summary,
    description: p.summary,
    cover_image: p.logo,
    tags: p.tags,
    sector,
    services: [],
    created_at: new Date(2026, 6, 1 + i).toISOString(),
  };
});

const services = [
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

const partners = [
  {
    id: 1,
    name: "Siemens",
    logo_path: logoSiemens,
    website_url: "https://www.siemens.com",
    type: "technology_partner",
    sort_order: 1,
  },
  {
    id: 2,
    name: "Rockwell Automation",
    logo_path: logoRockwell,
    website_url: "https://www.rockwellautomation.com",
    type: "technology_partner",
    sort_order: 2,
  },
  {
    id: 3,
    name: "Schneider Electric",
    logo_path: logoSchnider,
    website_url: "https://www.se.com",
    type: "technology_partner",
    sort_order: 3,
  },
  {
    id: 4,
    name: "ABB",
    logo_path: logoAbb,
    website_url: "https://global.abb/",
    type: "technology_partner",
    sort_order: 4,
  },
  {
    id: 5,
    name: "Mitsubishi Electric",
    logo_path: logoMitsubishi,
    website_url: "https://www.mitsubishielectric.com",
    type: "technology_partner",
    sort_order: 5,
  },
  {
    id: 6,
    name: "Huawei",
    logo_path: logoHuawei,
    website_url: "https://www.huawei.com",
    type: "technology_partner",
    sort_order: 6,
  },
  {
    id: 7,
    name: "AVEVA",
    logo_path: logoAveva,
    website_url: "https://www.aveva.com",
    type: "ecosystem_network",
    sort_order: 1,
  },
  {
    id: 8,
    name: "PTC",
    logo_path: logoPtc,
    website_url: "https://www.ptc.com",
    type: "ecosystem_network",
    sort_order: 2,
  },
  {
    id: 9,
    name: "Egypt 4 Industry",
    logo_path: logoEgypt4,
    website_url: null,
    type: "ecosystem_network",
    sort_order: 3,
  },
  {
    id: 10,
    name: "Odoo",
    logo_path: logoOdoo,
    website_url: "https://www.odoo.com",
    type: "ecosystem_network",
    sort_order: 4,
  },
];

const clients = [
  { id: 1, name: "Delta", logo_path: logoDelta, sort_order: 1 },
  { id: 2, name: "Emisal", logo_path: logoEmisal, sort_order: 2 },
  { id: 3, name: "Energeya", logo_path: logoEnergeya, sort_order: 3 },
  { id: 4, name: "GCF", logo_path: logoGcf, sort_order: 4 },
  { id: 5, name: "Giza Systems", logo_path: logoGiza, sort_order: 5 },
  { id: 6, name: "Henkel", logo_path: logoHenkel, sort_order: 6 },
  { id: 7, name: "Omis", logo_path: logoOmis, sort_order: 7 },
  {
    id: 8,
    name: "Orascom Construction",
    logo_path: logoOrascom,
    sort_order: 8,
  },
  { id: 9, name: "Plast Company", logo_path: logoPlast, sort_order: 9 },
  { id: 10, name: "Tabreed", logo_path: logoTabreed, sort_order: 10 },
  { id: 11, name: "Uniha", logo_path: logoUniha, sort_order: 11 },
];

const timeline = [
  {
    id: 1,
    year: "2020",
    title: "Foundation",
    description: "Industrial engineering foundations and initial operations.",
    icon: null,
  },
  {
    id: 2,
    year: "2021",
    title: "Validation",
    description: "Refined the offer and established market fit.",
    icon: null,
  },
  {
    id: 3,
    year: "22–23",
    title: "Regional presence",
    description: "International expansion and larger contracts.",
    icon: null,
  },
  {
    id: 4,
    year: "2024",
    title: "Scaling",
    description: "Team expansion and broader delivery capacity.",
    icon: null,
  },
  {
    id: 5,
    year: "2025+",
    title: "Industrial intelligence",
    description: "Emerging technology and continuous innovation.",
    icon: null,
  },
];

const pillars = [
  {
    id: 1,
    step: "01 · Connect",
    title: "Connect any asset",
    body: "PLCs, sensors, drives and legacy equipment onto one secure network over OPC UA, MQTT and Modbus.",
    icon: "connect",
    sort_order: 1,
  },
  {
    id: 2,
    step: "02 · Monitor",
    title: "Monitor in real time",
    body: "Live SCADA dashboards, alarms and trends — every metric visible the moment it moves.",
    icon: "pulse",
    sort_order: 2,
  },
  {
    id: 3,
    step: "03 · Optimize",
    title: "Optimize & predict",
    body: "Analytics, digital twins and predictive maintenance turn data into fewer stoppages and higher output.",
    icon: "chart",
    sort_order: 3,
  },
];

const workFlow = [
  {
    id: 1,
    stage: "STAGE 01",
    title: "Mind",
    body: "Strategic intent and high-level vision. Where corporate goals are translated into actionable digital roadmaps.",
    layer: "STRATEGIC LAYER",
    icon: "mind",
    sort_order: 1,
  },
  {
    id: 2,
    stage: "STAGE 02",
    title: "AI",
    body: "Intelligent processing and advanced analytics. Transforming new data streams into predictive operational insights.",
    layer: "ANALYTIC LAYER",
    icon: "ai",
    sort_order: 2,
  },
  {
    id: 3,
    stage: "STAGE 03",
    title: "PC",
    body: "Digital orchestration and control interfaces. The mission control center for real-time monitoring and command.",
    layer: "ORCHESTRATION",
    icon: "pc",
    sort_order: 3,
  },
  {
    id: 4,
    stage: "STAGE 04",
    title: "Factory",
    body: "Physical production and OT integration. High-precision hardware executing digital commands on the shop floor.",
    layer: "PHYSICAL LAYER",
    icon: "factory",
    sort_order: 4,
  },
  {
    id: 5,
    stage: "STAGE 05",
    title: "Human",
    body: "Empowered workforce and oversight. Experts leveraging systems to make critical decisions and drive safety.",
    layer: "EXPERT LAYER",
    icon: "human",
    sort_order: 5,
  },
];

const workSamples = [
  {
    id: 1,
    slug: "cement-process-automation",
    title: "Cement Plant Process Automation",
    industry: "Cement",
    category: "Cement",
    blurb:
      "Process flow, equipment status, energy consumption and alarm summary for cement production.",
    body: "Process flow, equipment status, energy consumption and alarm summary for cement production.",
    free: true,
    badge: "DEMO ACCESS",
    demo_url: "/work-samples/cement-process-automation/index.html",
    image: null,
    sort_order: 1,
  },
  {
    id: 2,
    slug: "ems-energy-management",
    title: "EMS – Energy Management System",
    industry: "Energy & Utilities",
    category: "Energy & Utilities",
    blurb:
      "Energy breakdown, real-time electrical measurements, shift consumption and monthly savings.",
    body: "Energy breakdown, real-time electrical measurements, shift consumption and monthly savings.",
    free: false,
    badge: "PREMIUM",
    demo_url: "/work-samples/ems-energy-management/index.html",
    image: null,
    sort_order: 2,
  },
  {
    id: 3,
    slug: "bms-building-management",
    title: "BMS – Building Management System",
    industry: "Infrastructure & Buildings",
    category: "Infrastructure & Buildings",
    blurb:
      "HVAC, floor monitoring, lighting management and building utilities in one control view.",
    body: "HVAC, floor monitoring, lighting management and building utilities in one control view.",
    free: false,
    badge: "PREMIUM",
    demo_url: "/work-samples/bms-building-management/index.html",
    image: null,
    sort_order: 3,
  },
  {
    id: 4,
    slug: "cmms-maintenance",
    title: "CMMS – Maintenance Management",
    industry: "Maintenance",
    category: "Maintenance",
    blurb:
      "Work orders, asset health, preventive maintenance and technician performance tracking.",
    body: "Work orders, asset health, preventive maintenance and technician performance tracking.",
    free: false,
    badge: "PREMIUM",
    demo_url: "/work-samples/cmms-maintenance/index.html",
    image: null,
    sort_order: 4,
  },
  {
    id: 5,
    slug: "ai-analysis",
    title: "AI Analysis Dashboard",
    industry: "AI & Analytics",
    category: "AI & Analytics",
    blurb:
      "Anomaly detection, RUL, equipment risk ranking, failure probability and AI recommendations.",
    body: "Anomaly detection, RUL, equipment risk ranking, failure probability and AI recommendations.",
    free: false,
    badge: "PREMIUM",
    demo_url: "/work-samples/ai-analysis/index.html",
    image: null,
    sort_order: 5,
  },
  {
    id: 6,
    slug: "process-automation",
    title: "Process Automation Dashboard",
    industry: "Manufacturing",
    category: "Manufacturing",
    blurb:
      "Industrial process automation across cement, oil and gas plants in a single view.",
    body: "Industrial process automation across cement, oil and gas plants in a single view.",
    free: false,
    badge: "PREMIUM",
    demo_url: "/work-samples/process-automation/index.html",
    image: null,
    sort_order: 6,
  },
];

const contactRequests = [
  {
    id: 6,
    name: "Ahmed Hassan",
    email: "ahmed.hassan@khalda.com.eg",
    phone: "+20 100 111 2233",
    company_name: "Khalda Petroleum",
    message:
      "We are looking to upgrade our wellhead SCADA systems across 3 sites. Can you share a proposal and timeline?",
    status: "unread",
    created_at: "2026-08-15T09:24:00+00:00",
  },
  {
    id: 5,
    name: "Sarah Mahmoud",
    email: "sarah@tabreed.com",
    phone: "+971 50 444 5566",
    company_name: "Tabreed",
    message:
      "Interested in your BMS dashboard solution for our new district cooling plant. Please contact us to discuss requirements.",
    status: "read",
    created_at: "2026-08-14T14:10:00+00:00",
  },
  {
    id: 4,
    name: "Mohamed El-Sayed",
    email: "m.elsayed@emeraldoil.com",
    phone: "+20 122 333 4455",
    company_name: "Emerald Oil",
    message:
      "Need predictive maintenance analytics for rotating equipment in our refinery. What is the typical engagement model?",
    status: "read",
    created_at: "2026-08-13T11:05:00+00:00",
  },
  {
    id: 3,
    name: "Laila Ibrahim",
    email: "laila@gharbiawater.gov.eg",
    phone: "+20 101 222 3344",
    company_name: "Gharbia Water",
    message:
      "We want to modernise our wastewater treatment automation. Requesting a technical meeting with your engineering team.",
    status: "archived",
    created_at: "2026-08-10T08:40:00+00:00",
  },
  {
    id: 2,
    name: "Omar Farouk",
    email: "omar.farouk@henkel.com",
    phone: "+20 109 888 7766",
    company_name: "Henkel",
    message:
      "Following up on the SCADA upgrade proposal. We would like a demo of the live validation dashboard first.",
    status: "archived",
    created_at: "2026-08-08T16:55:00+00:00",
  },
  {
    id: 1,
    name: "Yousef Adel",
    email: "y.adel@arabcement.com",
    phone: "+20 111 444 5566",
    company_name: "Arab Cement",
    message:
      "Exploring ERP integration for production traceability in our cement plant. Please share case studies.",
    status: "archived",
    created_at: "2026-08-05T10:15:00+00:00",
  },
];

export const seedData = {
  hero: [
    {
      id: 1,
      tagline: "Superior Integrated Solutions",
      headline: "Where operational meets information technology",
      subheadline:
        "Delivering world-class PLC, SCADA, EMS, BMS, and IIoT solutions across the MENA region, powered by Siemens, Schneider Electric, and AVEVA technologies.",
      image: factoryImg,
      cta_primary: "Explore Our Work",
      cta_primary_link: "/projects",
      cta_secondary: "Get In Touch",
      cta_secondary_link: "/contact",
      stats: [
        { value: 5, suffix: "+", label: "Years of Excellence" },
        { value: 12, suffix: "+", label: "Industrial Projects" },
        { value: 5, suffix: "", label: "Sectors Served" },
        { value: 10, suffix: "+", label: "Technology Partners" },
      ],
      badge_text: "Operational since 2020 · 12+ industrial deployments live",
      is_active: true,
    },
  ],
  about: [
    {
      id: 1,
      title: "Who We Are",
      headline: "Our Mission & Vision",
      subheadline:
        "Bridging OT and IT across the MENA region with industrial automation and IIoT excellence.",
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
      is_active: true,
    },
  ],
  aboutHero: [
    {
      id: 1,
      tagline: "Pioneering Industrial Evolution",
      headline: "Architects of the 4th Industrial Revolution",
      subheadline:
        "Superior Integrated Solutions (SiS) bridges the critical divide between Operational Technology and Information Technology, engineering the factories of tomorrow across the MENA region.",
      cta_text: "Discover Our Expertise",
      cta_link: "/projects",
      image: aboutHeroImg,
      is_active: true,
    },
  ],
  itOt: [
    {
      id: 1,
      tagline: "The Convergence",
      headline: "Bridging the IT-OT Divide",
      subheadline:
        "Unified visibility across the enterprise — from the control room to the boardroom.",
      ot_data: {
        title: "Operational Technology (OT)",
        description:
          "The hardware and software that detects or causes a change through the direct monitoring and/or control of industrial equipment, assets, processes, and events.",
        tags: ["SCADA", "PLCs", "Sensors", "DCS"],
        icon: "precision_manufacturing",
      },
      it_data: {
        title: "Information Technology (IT)",
        description:
          "The use of computers, storage, networking, and other physical devices to create, process, store, secure, and exchange all forms of electronic data.",
        tags: ["ERP", "Cloud", "Analytics", "MES"],
        icon: "cloud",
      },
      is_active: true,
    },
  ],
  coreValues: [
    {
      id: 1,
      tagline: "What We Stand For",
      headline: "Core Values & Principles",
      subheadline:
        "The principles that guide every system we engineer and every partnership we build.",
      principles: [
        {
          number: "01",
          category: "Excellence",
          icon: "verified",
          title: "Precision",
          description:
            "Execution of highly complex industrial automation and data science with exactitude.",
          link: null,
        },
        {
          number: "02",
          category: "Growth",
          icon: "tips_and_updates",
          title: "Innovation",
          description:
            "Pioneering custom software and IIoT approaches to solve legacy challenges.",
          link: null,
        },
        {
          number: "03",
          category: "Trust",
          icon: "verified_user",
          title: "Reliability",
          description:
            "Successfully implementing and sustaining large-scale solutions for critical regional industries.",
          link: null,
        },
      ],
      is_active: true,
    },
  ],
  domains: [
    {
      id: 1,
      tagline: "Our Domain Expertise",
      headline: "Industries We Empower",
      subheadline:
        "Specialised control and monitoring expertise across the region's most demanding industrial sectors.",
      domains: [
        {
          title: "Oil & Gas",
          icon: "oil_barrel",
          description:
            "Digital oilfield instrumentation, safety systems and SCADA across upstream, midstream and downstream.",
        },
        {
          title: "Chemical Industries",
          icon: "science",
          description:
            "Process optimisation, safety valve control and hazardous environment automation.",
        },
        {
          title: "Heavy Industries",
          icon: "factory",
          description:
            "Cement manufacturing, steel foundries, kiln control and conveyor systems.",
        },
        {
          title: "Water & Waste Water",
          icon: "water_drop",
          description:
            "Desalination, treatment plants and regional water distribution automation.",
        },
        {
          title: "Food, Beverage & FMCG",
          icon: "restaurant",
          description:
            "High-speed packaging, chemical mixing, ERP traceability and thermal control.",
        },
      ],
      is_active: true,
    },
  ],
  services,
  sectors,
  projects,
  partners,
  clients,
  timeline,
  pillars,
  workFlow,
  workSamples,
  contactRequests,
  visitorCredentials: [
    {
      id: 1,
      username: "demo_visitor_1",
      password: "SecurePass123!",
      createdAt: "2026-08-09T10:00:00Z",
      expiresAt: "2026-08-16T10:00:00Z",
      isActive: true,
    },
    {
      id: 2,
      username: "demo_sales",
      password: "Sales@Demo2024",
      createdAt: "2026-08-12T14:30:00Z",
      expiresAt: "2026-08-19T14:30:00Z",
      isActive: true,
    },
    {
      id: 3,
      username: "demo_client",
      password: "ClientAccess2024",
      createdAt: "2026-08-10T09:15:00Z",
      expiresAt: "2026-08-17T09:15:00Z",
      isActive: true,
    },
    {
      id: 4,
      username: "trial_user_001",
      password: "Trial@2024Expired",
      createdAt: "2026-08-08T11:45:00Z",
      expiresAt: "2026-08-15T11:45:00Z",
      isActive: false,
    },
    {
      id: 5,
      username: "demo_partner",
      password: "PartnerAccess2024",
      createdAt: "2026-08-11T13:20:00Z",
      expiresAt: "2026-08-18T13:20:00Z",
      isActive: true,
    },
    {
      id: 6,
      username: "demo_executive",
      password: "Executive@Access24",
      createdAt: "2026-08-14T08:00:00Z",
      expiresAt: "2026-08-21T08:00:00Z",
      isActive: true,
    },
  ],
};
