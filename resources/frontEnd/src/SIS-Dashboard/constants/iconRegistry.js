/**
 * Centralized Icon Registry for the SiS Platform & Dashboard
 *
 * Icons are stored in the database as stable identifier strings matching
 * Google Material Symbols Outlined (and mapped via UiIcon.vue on public pages).
 */

export const ICON_REGISTRY = [
  // ==========================================
  // 1. Industrial, Automation & Robotics
  // ==========================================
  {
    name: "precision_manufacturing",
    label: "Precision Manufacturing",
    category: "Industrial & Automation",
    keywords: ["robot", "robot-arm", "robotic", "machinery", "automation", "assembly", "factory"],
  },
  {
    name: "factory",
    label: "Factory & Manufacturing",
    category: "Industrial & Automation",
    keywords: ["plant", "industry", "production", "heavy-industry", "manufacturing"],
  },
  {
    name: "smart_toy",
    label: "Robotics & Autonomous",
    category: "Industrial & Automation",
    keywords: ["robot", "ai", "machine", "autonomous", "automation", "cobot"],
  },
  {
    name: "engineering",
    label: "Engineering & Technical",
    category: "Industrial & Automation",
    keywords: ["engineer", "technician", "helmet", "services", "gear", "technical"],
  },
  {
    name: "construction",
    label: "Infrastructure & Construction",
    category: "Industrial & Automation",
    keywords: ["tools", "hammer", "wrench", "build", "infrastructure"],
  },
  {
    name: "conveyor_belt",
    label: "Conveyor & Assembly Line",
    category: "Industrial & Automation",
    keywords: ["conveyor", "assembly", "logistics", "line", "production", "packaging"],
  },
  {
    name: "forklift",
    label: "Material Handling & Warehouse",
    category: "Industrial & Automation",
    keywords: ["forklift", "logistics", "material", "handling", "storage", "warehouse"],
  },
  {
    name: "hardware",
    label: "Heavy Equipment & Hardware",
    category: "Industrial & Automation",
    keywords: ["hardware", "tools", "heavy", "mechanical", "plant"],
  },
  {
    name: "plumbing",
    label: "Industrial Piping & Hydraulics",
    category: "Industrial & Automation",
    keywords: ["pipe", "piping", "plumbing", "hydraulics", "valves", "flow"],
  },
  {
    name: "handyman",
    label: "Field Maintenance Services",
    category: "Industrial & Automation",
    keywords: ["maintenance", "tools", "repair", "service", "technician"],
  },
  {
    name: "warehouse",
    label: "Automated Warehousing",
    category: "Industrial & Automation",
    keywords: ["warehouse", "depot", "logistics", "storage", "distribution"],
  },
  {
    name: "iron",
    label: "Smelting & Metals",
    category: "Industrial & Automation",
    keywords: ["foundry", "steel", "metal", "heavy-industry", "smelting"],
  },
  {
    name: "pallet",
    label: "Palletizing & Packaging",
    category: "Industrial & Automation",
    keywords: ["pallet", "packaging", "dispatch", "logistics", "goods"],
  },
  {
    name: "fire_hydrant",
    label: "Safety Hydraulics & High Pressure",
    category: "Industrial & Automation",
    keywords: ["safety", "pressure", "hydrant", "water", "high-pressure"],
  },
  {
    name: "agriculture",
    label: "Agri-Tech & Processing",
    category: "Industrial & Automation",
    keywords: ["agriculture", "food", "farming", "crop", "grain", "fmcg"],
  },

  // ==========================================
  // 2. Process, Energy & Utilities
  // ==========================================
  {
    name: "oil_barrel",
    label: "Oil & Gas Upstream/Downstream",
    category: "Process & Energy",
    keywords: ["oil", "gas", "oil-gas", "petroleum", "refinery", "pipeline", "barrel"],
  },
  {
    name: "water_drop",
    label: "Water & Wastewater Treatment",
    category: "Process & Energy",
    keywords: ["water", "liquid", "treatment", "desalination", "fluid", "utilities"],
  },
  {
    name: "science",
    label: "Chemical & Petrochemical",
    category: "Process & Energy",
    keywords: ["flask", "chemistry", "chemical", "lab", "laboratory", "formula", "pharma"],
  },
  {
    name: "bolt",
    label: "Electric Power & Grid",
    category: "Process & Energy",
    keywords: ["power", "energy", "electricity", "voltage", "grid", "power-energy"],
  },
  {
    name: "electric_bolt",
    label: "High Voltage Transmission",
    category: "Process & Energy",
    keywords: ["high-voltage", "substation", "transmission", "electric", "power"],
  },
  {
    name: "solar_power",
    label: "Solar Energy & Photovoltaics",
    category: "Process & Energy",
    keywords: ["solar", "sun", "renewables", "green", "pv", "photovoltaic"],
  },
  {
    name: "wind_power",
    label: "Wind Power & Turbines",
    category: "Process & Energy",
    keywords: ["wind", "turbine", "renewables", "clean-energy", "green"],
  },
  {
    name: "speed",
    label: "Flow Rate & Pressure Gauge",
    category: "Process & Energy",
    keywords: ["meter", "pressure", "speedometer", "flow", "instrumentation", "measurement", "gauge"],
  },
  {
    name: "thermostat",
    label: "Thermal & HVAC BMS",
    category: "Process & Energy",
    keywords: ["temperature", "heat", "cool", "thermal", "climate", "bms", "hvac"],
  },
  {
    name: "gas_meter",
    label: "Gas Pipeline Metering",
    category: "Process & Energy",
    keywords: ["pressure", "meter", "gas", "pipeline", "valve", "instrumentation"],
  },
  {
    name: "valve",
    label: "Process Control Valve",
    category: "Process & Energy",
    keywords: ["valve", "pipe", "flow", "control", "piping", "actuator"],
  },
  {
    name: "battery_charging_full",
    label: "BESS & Energy Storage",
    category: "Process & Energy",
    keywords: ["battery", "storage", "bess", "charge", "backup", "ups"],
  },
  {
    name: "electric_meter",
    label: "Smart Energy Metering",
    category: "Process & Energy",
    keywords: ["meter", "kwh", "smart-meter", "consumption", "power"],
  },
  {
    name: "air",
    label: "Pneumatics & Compressed Air",
    category: "Process & Energy",
    keywords: ["air", "pneumatic", "compressor", "wind", "flow"],
  },
  {
    name: "waves",
    label: "Hydroelectric & Wave Dynamics",
    category: "Process & Energy",
    keywords: ["hydro", "water", "dam", "turbine", "waves", "marine"],
  },
  {
    name: "heat_pump",
    label: "Industrial Heat Exchanger",
    category: "Process & Energy",
    keywords: ["heat", "cooling", "exchanger", "thermal", "hvac"],
  },
  {
    name: "propane_tank",
    label: "Cryogenic & Gas Storage",
    category: "Process & Energy",
    keywords: ["tank", "gas", "storage", "vessel", "propane", "lpg"],
  },

  // ==========================================
  // 3. Digital, OT-IT & Hardware
  // ==========================================
  {
    name: "memory",
    label: "PLC & Microcontroller",
    category: "Digital & OT-IT",
    keywords: ["cpu", "chip", "processor", "hardware", "plc", "controller", "microchip"],
  },
  {
    name: "hub",
    label: "Industrial Topology & Bus",
    category: "Digital & OT-IT",
    keywords: ["connect", "network", "node", "gateway", "iiot", "topology", "switch"],
  },
  {
    name: "sensors",
    label: "Sensors & Field Transmitters",
    category: "Digital & OT-IT",
    keywords: ["sensor", "detect", "iot", "signal", "wireless", "telemetry", "transmitters"],
  },
  {
    name: "router",
    label: "Industrial Edge Gateway",
    category: "Digital & OT-IT",
    keywords: ["switch", "ethernet", "scada-network", "modbus", "profinet", "fieldbus", "gateway"],
  },
  {
    name: "computer",
    label: "SCADA Workstation & HMI",
    category: "Digital & OT-IT",
    keywords: ["pc", "desktop", "terminal", "workstation", "hmi", "screen"],
  },
  {
    name: "laptop",
    label: "Field Commissioning Laptop",
    category: "Digital & OT-IT",
    keywords: ["laptop", "field", "testing", "commissioning", "portable"],
  },
  {
    name: "cloud",
    label: "Cloud & Hybrid Telemetry",
    category: "Digital & OT-IT",
    keywords: ["cloud", "cloud-integration", "hybrid", "aws", "azure", "serverless"],
  },
  {
    name: "database",
    label: "Data Historian & SQL",
    category: "Digital & OT-IT",
    keywords: ["storage", "sql", "historian", "data", "scada-db", "server", "records"],
  },
  {
    name: "dns",
    label: "Datacenter Rack Server",
    category: "Digital & OT-IT",
    keywords: ["server", "datacenter", "rack", "hosting", "storage", "infrastructure"],
  },
  {
    name: "terminal",
    label: "CLI & Firmware Logic",
    category: "Digital & OT-IT",
    keywords: ["code", "console", "command", "script", "developer", "api"],
  },
  {
    name: "developer_board",
    label: "Embedded IoT Architecture",
    category: "Digital & OT-IT",
    keywords: ["board", "microcontroller", "embedded", "pcb", "hardware"],
  },
  {
    name: "lan",
    label: "Industrial Ethernet LAN",
    category: "Digital & OT-IT",
    keywords: ["ethernet", "network", "lan", "cable", "switch", "profinet"],
  },
  {
    name: "cell_tower",
    label: "Private 5G & Telemetry Mast",
    category: "Digital & OT-IT",
    keywords: ["5g", "cellular", "tower", "antenna", "wireless", "telemetry"],
  },
  {
    name: "cable",
    label: "Fieldbus & Fiber Cabling",
    category: "Digital & OT-IT",
    keywords: ["cable", "wire", "fiber", "connection", "hardware"],
  },
  {
    name: "wifi",
    label: "Industrial Wireless AP",
    category: "Digital & OT-IT",
    keywords: ["wifi", "wireless", "signal", "radio", "wlan"],
  },
  {
    name: "usb",
    label: "Serial Interface & Comm",
    category: "Digital & OT-IT",
    keywords: ["usb", "serial", "rs485", "rs232", "port", "interface"],
  },
  {
    name: "settings_input_component",
    label: "I/O Remote Rack Modules",
    category: "Digital & OT-IT",
    keywords: ["io", "rack", "module", "input", "output", "plc"],
  },
  {
    name: "storage",
    label: "Industrial SAN Storage",
    category: "Digital & OT-IT",
    keywords: ["storage", "backup", "disks", "drive", "records"],
  },

  // ==========================================
  // 4. AI, Analytics & Software
  // ==========================================
  {
    name: "psychology",
    label: "AI Neural Models",
    category: "AI & Analytics",
    keywords: ["mind", "brain", "ai", "artificial-intelligence", "intelligence", "neural", "deep-learning"],
  },
  {
    name: "auto_awesome",
    label: "Intelligent Generative AI",
    category: "AI & Analytics",
    keywords: ["ai", "copilot", "magic", "smart", "generative", "assistant", "ai-powered"],
  },
  {
    name: "analytics",
    label: "Analytics & Telemetry BI",
    category: "AI & Analytics",
    keywords: ["chart", "graph", "analysis", "insights", "metrics", "kpi", "dashboard"],
  },
  {
    name: "bar_chart",
    label: "Performance Bar Charts",
    category: "AI & Analytics",
    keywords: ["chart", "bars", "reporting", "dashboard", "statistics", "kpi"],
  },
  {
    name: "show_chart",
    label: "Real-Time Trend Waves",
    category: "AI & Analytics",
    keywords: ["chart", "trend", "line", "telemetry", "scada-trend", "graph"],
  },
  {
    name: "trending_up",
    label: "Continuous OEE Growth",
    category: "AI & Analytics",
    keywords: ["trend", "profit", "performance", "optimization", "growth", "savings", "oee"],
  },
  {
    name: "monitoring",
    label: "Live SCADA Monitoring",
    category: "AI & Analytics",
    keywords: ["monitor", "real-time", "supervision", "scada", "telemetry", "live"],
  },
  {
    name: "monitor_heart",
    label: "Predictive Health & Vibe",
    category: "AI & Analytics",
    keywords: ["pulse", "heartbeat", "health", "vibration", "diagnostics", "predictive", "condition"],
  },
  {
    name: "troubleshoot",
    label: "Anomaly Root Cause (RCA)",
    category: "AI & Analytics",
    keywords: ["anomaly", "search", "investigate", "diagnostic", "error", "fault", "rca"],
  },
  {
    name: "radar",
    label: "Perimeter Radar & Lidar",
    category: "AI & Analytics",
    keywords: ["scan", "detection", "perimeter", "sensing", "tracking", "surveillance", "lidar"],
  },
  {
    name: "pie_chart",
    label: "Resource Allocation Pie",
    category: "AI & Analytics",
    keywords: ["pie", "chart", "distribution", "share", "stats"],
  },
  {
    name: "data_exploration",
    label: "Big Data Exploration",
    category: "AI & Analytics",
    keywords: ["data", "explore", "mining", "insights", "analytics"],
  },
  {
    name: "insights",
    label: "Actionable Insights",
    category: "AI & Analytics",
    keywords: ["insights", "trend", "stars", "intelligence", "forecast"],
  },
  {
    name: "timeline",
    label: "Historical Timeline Curves",
    category: "AI & Analytics",
    keywords: ["timeline", "history", "trend", "curve", "events"],
  },
  {
    name: "model_training",
    label: "Machine Learning Training",
    category: "AI & Analytics",
    keywords: ["ml", "train", "algorithm", "model", "ai"],
  },
  {
    name: "query_stats",
    label: "High-Speed Query Telemetry",
    category: "AI & Analytics",
    keywords: ["query", "stats", "telemetry", "metrics", "fast"],
  },
  {
    name: "calculate",
    label: "Algorithmic Computation",
    category: "AI & Analytics",
    keywords: ["math", "compute", "calculator", "algorithm", "formula"],
  },

  // ==========================================
  // 5. Cybersecurity & Safety
  // ==========================================
  {
    name: "security",
    label: "OT Cybersecurity Defense",
    category: "Security & Safety",
    keywords: ["shield", "cybersecurity", "ot-cybersecurity", "protection", "iec-62443", "firewall"],
  },
  {
    name: "shield",
    label: "Perimeter Shield Defense",
    category: "Security & Safety",
    keywords: ["security", "safety", "defense", "armor", "threat-protection"],
  },
  {
    name: "lock",
    label: "Zero-Trust Encryption",
    category: "Security & Safety",
    keywords: ["security", "secure", "credential", "auth", "permission", "password"],
  },
  {
    name: "verified_user",
    label: "Verified Compliance & Audit",
    category: "Security & Safety",
    keywords: ["verified", "user", "compliance", "standard", "audit", "governance"],
  },
  {
    name: "admin_panel_settings",
    label: "Access Policies & RBAC",
    category: "Security & Safety",
    keywords: ["admin", "governance", "policy", "control", "permissions", "settings", "rbac"],
  },
  {
    name: "vpn_key",
    label: "Encrypted VPN Tunnel",
    category: "Security & Safety",
    keywords: ["key", "vpn", "remote-access", "encryption", "tunnel", "secure"],
  },
  {
    name: "gpp_good",
    label: "Certified Safety Guard",
    category: "Security & Safety",
    keywords: ["guard", "safe", "approved", "certified", "trusted", "ok"],
  },
  {
    name: "fingerprint",
    label: "Biometric Field Access",
    category: "Security & Safety",
    keywords: ["fingerprint", "biometric", "identity", "auth", "access"],
  },
  {
    name: "policy",
    label: "Compliance Standard Guidelines",
    category: "Security & Safety",
    keywords: ["policy", "iso", "iec", "regulation", "standard", "compliance"],
  },
  {
    name: "encrypted",
    label: "End-to-End Cryptography",
    category: "Security & Safety",
    keywords: ["encrypt", "crypto", "tls", "lock", "cipher"],
  },
  {
    name: "emergency",
    label: "Emergency E-Stop Action",
    category: "Security & Safety",
    keywords: ["emergency", "e-stop", "critical", "safety", "alert"],
  },
  {
    name: "crisis_alert",
    label: "Critical Alarm Notification",
    category: "Security & Safety",
    keywords: ["alarm", "alert", "danger", "warning", "trip", "incident"],
  },
  {
    name: "health_and_safety",
    label: "EHS Occupational Health",
    category: "Security & Safety",
    keywords: ["ehs", "health", "safety", "hazard", "protection"],
  },
  {
    name: "badge",
    label: "Technician Security ID",
    category: "Security & Safety",
    keywords: ["badge", "id", "card", "credential", "employee"],
  },
  {
    name: "shield_person",
    label: "Personal Protective Shield",
    category: "Security & Safety",
    keywords: ["worker", "safety", "protection", "shield", "personnel"],
  },

  // ==========================================
  // 6. Business, Strategy & Ecosystem
  // ==========================================
  {
    name: "handshake",
    label: "Strategic Alliance & Ecosystem",
    category: "Business & Ecosystem",
    keywords: ["partner", "partnership", "agreement", "collaborate", "ecosystem", "handshake"],
  },
  {
    name: "business",
    label: "Enterprise Corporates",
    category: "Business & Ecosystem",
    keywords: ["company", "corporate", "client", "building", "enterprise"],
  },
  {
    name: "domain",
    label: "Empowered Domains",
    category: "Business & Ecosystem",
    keywords: ["domain", "building", "corporate", "headquarters", "sector", "complex"],
  },
  {
    name: "apartment",
    label: "Commercial Real Estate & BMS",
    category: "Business & Ecosystem",
    keywords: ["building", "facility", "real-estate", "bms", "smart-building", "architecture"],
  },
  {
    name: "groups",
    label: "Multidisciplinary Engineering Team",
    category: "Business & Ecosystem",
    keywords: ["team", "people", "staff", "users", "engineers", "collaboration", "workforce"],
  },
  {
    name: "workspace_premium",
    label: "Premium Certification Badge",
    category: "Business & Ecosystem",
    keywords: ["award", "badge", "certificate", "quality", "tier", "gold", "excellence"],
  },
  {
    name: "military_tech",
    label: "Proven Historical Track Record",
    category: "Business & Ecosystem",
    keywords: ["medal", "achievement", "proven", "track-record", "veteran", "honor"],
  },
  {
    name: "verified",
    label: "Quality Verified Standard",
    category: "Business & Ecosystem",
    keywords: ["check", "badge", "approved", "certified", "standard", "quality"],
  },
  {
    name: "star",
    label: "Core Highlight Feature",
    category: "Business & Ecosystem",
    keywords: ["star", "favorite", "featured", "top", "highlight", "rating"],
  },
  {
    name: "visibility",
    label: "Corporate Strategic Vision",
    category: "Business & Ecosystem",
    keywords: ["vision", "see", "look", "strategy", "future", "insight"],
  },
  {
    name: "flag",
    label: "Strategic Mission & Target",
    category: "Business & Ecosystem",
    keywords: ["flag", "mission", "goal", "target", "objective"],
  },
  {
    name: "rocket_launch",
    label: "Rapid Scaling & Launch",
    category: "Business & Ecosystem",
    keywords: ["rocket", "launch", "grow", "scale", "start", "fast"],
  },
  {
    name: "emoji_events",
    label: "Industry Excellence Trophy",
    category: "Business & Ecosystem",
    keywords: ["trophy", "cup", "winner", "award", "prize", "celebrate"],
  },
  {
    name: "diamond",
    label: "Uncompromising Core Values",
    category: "Business & Ecosystem",
    keywords: ["diamond", "gem", "value", "integrity", "quality", "rare"],
  },
  {
    name: "public",
    label: "Global Regional Presence",
    category: "Business & Ecosystem",
    keywords: ["global", "world", "earth", "international", "presence"],
  },
  {
    name: "corporate_fare",
    label: "Corporate Governance & Ethics",
    category: "Business & Ecosystem",
    keywords: ["corporate", "governance", "headquarters", "building"],
  },
  {
    name: "diversity_3",
    label: "Partner Community Network",
    category: "Business & Ecosystem",
    keywords: ["community", "partners", "diversity", "network", "collaboration"],
  },
  {
    name: "storefront",
    label: "Digital Marketplace Solutions",
    category: "Business & Ecosystem",
    keywords: ["store", "market", "shop", "solutions", "catalog"],
  },

  // ==========================================
  // 7. Operations, Workflow & Tools
  // ==========================================
  {
    name: "settings",
    label: "System Settings & Configuration",
    category: "Operations & Tools",
    keywords: ["gear", "cog", "configuration", "setup", "mechanics", "maintenance"],
  },
  {
    name: "build",
    label: "Field Repairs & Diagnostics",
    category: "Operations & Tools",
    keywords: ["tool", "wrench", "repair", "service", "maintenance", "troubleshooting"],
  },
  {
    name: "tune",
    label: "Control Loop PID Tuning",
    category: "Operations & Tools",
    keywords: ["slider", "calibration", "adjust", "tune", "parameters", "fine-tuning", "pid"],
  },
  {
    name: "inventory_2",
    label: "Spare Parts & Asset Tracking",
    category: "Operations & Tools",
    keywords: ["inventory", "box", "package", "warehouse", "assets", "delivery", "spares"],
  },
  {
    name: "account_tree",
    label: "Hierarchical Architecture & ISA-95",
    category: "Operations & Tools",
    keywords: ["tree", "process", "hierarchy", "structure", "flow", "scada-tree", "workflow", "isa-95"],
  },
  {
    name: "sync",
    label: "Continuous Synchronization",
    category: "Operations & Tools",
    keywords: ["sync", "cycle", "loop", "refresh", "continuous", "realtime", "iteration"],
  },
  {
    name: "sync_alt",
    label: "IT-OT Dual-Way Bridge",
    category: "Operations & Tools",
    keywords: ["it-ot", "bridge", "bidirectional", "exchange", "transfer", "sync"],
  },
  {
    name: "published_with_changes",
    label: "Commissioning & Release",
    category: "Operations & Tools",
    keywords: ["lifecycle", "update", "deploy", "release", "version", "commissioning"],
  },
  {
    name: "checklist",
    label: "Quality Audit Checklist",
    category: "Operations & Tools",
    keywords: ["check", "list", "inspection", "audit", "commissioning", "steps", "tasks"],
  },
  {
    name: "assignment",
    label: "Engineering Documentation",
    category: "Operations & Tools",
    keywords: ["document", "clipboard", "report", "spec", "drawing", "manual", "sheet"],
  },
  {
    name: "support_agent",
    label: "24/7 Service Level Agreement",
    category: "Operations & Tools",
    keywords: ["support", "help", "headset", "hotline", "customer", "service", "24/7", "sla"],
  },
  {
    name: "local_shipping",
    label: "Logistics & Supply Chain",
    category: "Operations & Tools",
    keywords: ["truck", "delivery", "shipping", "transport", "supply-chain", "logistics"],
  },
  {
    name: "eco",
    label: "ESG & Carbon Sustainability",
    category: "Operations & Tools",
    keywords: ["green", "leaf", "environmental", "energy", "sustainable", "eco-friendly", "esg"],
  },
  {
    name: "lightbulb",
    label: "Innovative Solution Design",
    category: "Operations & Tools",
    keywords: ["idea", "innovation", "concept", "solution", "creative", "strategy"],
  },
  {
    name: "schedule",
    label: "Turnaround Schedule & Gantt",
    category: "Operations & Tools",
    keywords: ["time", "clock", "timeline", "history", "duration", "hours", "calendar", "gantt"],
  },
  {
    name: "task_alt",
    label: "Verified Task Milestone",
    category: "Operations & Tools",
    keywords: ["task", "done", "check", "complete", "finish", "ok"],
  },
  {
    name: "track_changes",
    label: "Key Precision Objectives",
    category: "Operations & Tools",
    keywords: ["target", "bullseye", "focus", "goal", "track", "precision"],
  },
  {
    name: "integration_instructions",
    label: "Turnkey System Integration",
    category: "Operations & Tools",
    keywords: ["integration", "install", "turnkey", "instructions", "setup"],
  },
  {
    name: "design_services",
    label: "CAD & Architectural Design",
    category: "Operations & Tools",
    keywords: ["cad", "design", "blueprint", "drafting", "engineering"],
  },
  {
    name: "rule_folder",
    label: "Standard Operating Procedures (SOP)",
    category: "Operations & Tools",
    keywords: ["sop", "rules", "procedure", "docs", "compliance", "standards"],
  },
  {
    name: "all_inclusive",
    label: "End-to-End Complete Lifecycle",
    category: "Operations & Tools",
    keywords: ["lifecycle", "loop", "infinity", "complete", "turnkey", "end-to-end"],
  },
  {
    name: "history",
    label: "Historian Audit Log Trail",
    category: "Operations & Tools",
    keywords: ["history", "log", "trail", "clock", "past", "audit"],
  },

  // ==========================================
  // 8. Communication, Media & UI
  // ==========================================
  {
    name: "mail",
    label: "Direct Email Communication",
    category: "Communication & Media",
    keywords: ["email", "mail", "contact", "letter", "inbox"],
  },
  {
    name: "call",
    label: "Direct Hotline Dispatch",
    category: "Communication & Media",
    keywords: ["phone", "call", "telephone", "contact", "support"],
  },
  {
    name: "chat",
    label: "Operations Real-Time Chat",
    category: "Communication & Media",
    keywords: ["chat", "message", "communication", "talk"],
  },
  {
    name: "notifications",
    label: "System Alerts & Pagers",
    category: "Communication & Media",
    keywords: ["bell", "alert", "notice", "notification", "alarm"],
  },
  {
    name: "campaign",
    label: "Plant-Wide Announcements",
    category: "Communication & Media",
    keywords: ["megaphone", "broadcast", "speaker", "announcement", "notice"],
  },
  {
    name: "link",
    label: "External Hyperlink",
    category: "Communication & Media",
    keywords: ["link", "chain", "connect", "url", "web"],
  },
  {
    name: "qr_code_2",
    label: "Asset QR Scanning Tag",
    category: "Communication & Media",
    keywords: ["qr", "scan", "code", "barcode", "tag"],
  },
  {
    name: "nfc",
    label: "Near-Field Field Readers",
    category: "Communication & Media",
    keywords: ["nfc", "rfid", "contactless", "reader", "sensor"],
  },
  {
    name: "devices",
    label: "Cross-Platform Glass Display",
    category: "Communication & Media",
    keywords: ["responsive", "devices", "mobile", "tablet", "screen"],
  },
  {
    name: "desktop_windows",
    label: "Control Room Multi-Display",
    category: "Communication & Media",
    keywords: ["monitor", "display", "control-room", "scada-wall", "screen"],
  },
  {
    name: "help",
    label: "Technical Knowledge Center",
    category: "Communication & Media",
    keywords: ["help", "faq", "question", "support", "knowledge"],
  },
  {
    name: "info",
    label: "Informational Tooltip",
    category: "Communication & Media",
    keywords: ["info", "details", "about", "note", "tooltip"],
  },
  {
    name: "search",
    label: "Search & Deep Query",
    category: "Communication & Media",
    keywords: ["search", "find", "lookup", "explore", "query"],
  },
];

/**
 * Common legacy alias map matching useCollectionManager.js and UiIcon.vue
 */
export const LEGACY_ICON_ALIASES = {
  mind: "psychology",
  ai: "auto_awesome",
  pc: "computer",
  hu: "groups",
  oil: "oil_barrel",
  "oil-gas": "oil_barrel",
  "robot-arm": "precision_manufacturing",
  robot: "precision_manufacturing",
  cpu: "memory",
  connect: "hub",
  pulse: "monitor_heart",
  chart: "bar_chart",
  analytics: "analytics",
  cloud: "cloud",
  cybersecurity: "security",
  sustainability: "eco",
  infrastructure: "construction",
  manufacturing: "factory",
  chemical: "science",
  water: "water_drop",
  engineering: "engineering",
  power: "bolt",
  "power-energy": "bolt",
  building: "apartment",
};

/**
 * Resolve an icon key to its canonical representation or return the raw key
 */
export function resolveIconName(raw) {
  if (!raw || typeof raw !== "string") return "";
  const clean = raw.trim().toLowerCase();
  return LEGACY_ICON_ALIASES[clean] || clean;
}

/**
 * Find an icon definition from the registry (by exact name or alias)
 */
export function findIcon(name) {
  if (!name || typeof name !== "string") return null;
  const clean = name.trim().toLowerCase();
  const canonical = resolveIconName(clean);

  return (
    ICON_REGISTRY.find(
      (item) => item.name.toLowerCase() === clean || item.name.toLowerCase() === canonical,
    ) || null
  );
}

/**
 * Get human-readable label for any icon (known or unknown)
 */
export function getIconLabel(name) {
  if (!name || typeof name !== "string") return "";
  const found = findIcon(name);
  if (found) return found.label;

  // Prettify unknown identifier: "smart_grid_box" -> "Smart Grid Box"
  return name
    .replace(/[_-]+/g, " ")
    .replace(/\b\w/g, (c) => c.toUpperCase())
    .trim();
}

/**
 * Get list of unique categories
 */
export function getCategories() {
  const cats = new Set(ICON_REGISTRY.map((i) => i.category));
  return ["All", ...Array.from(cats)];
}

/**
 * Client-side search helper matching key, label, category, and keywords
 */
export function searchIcons(query = "", category = "All") {
  const q = String(query || "").trim().toLowerCase();

  return ICON_REGISTRY.filter((icon) => {
    if (category && category !== "All" && icon.category !== category) {
      return false;
    }

    if (!q) return true;

    if (icon.name.toLowerCase().includes(q)) return true;
    if (icon.label.toLowerCase().includes(q)) return true;
    if (icon.category.toLowerCase().includes(q)) return true;
    if (icon.keywords.some((k) => k.toLowerCase().includes(q))) return true;

    return false;
  });
}
