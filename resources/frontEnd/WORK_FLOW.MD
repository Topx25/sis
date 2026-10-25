# WORK_FLOW — Superior Integrated Solutions (SiS)

## 🧱 Tech Stack

| Layer | Technology |
|---|---|
| **Framework** | Vue 3 (Composition API, `<script setup>`) |
| **State** | Pinia |
| **Router** | Vue Router (hash-based `createWebHashHistory`) |
| **Styling** | Tailwind CSS 3 + custom CSS variables |
| **Animation** | GSAP 3 + native IntersectionObserver |
| **Build** | Vite 5 |
| **Icons** | Material Symbols Outlined |

---

## 📁 Project Structure

```
sis demo/
├── index.html                    # SPA entry, font imports
├── vite.config.js                # Vite config + path aliases (@/)
├── tailwind.config.js            # Custom colors, typography scale, shadows
├── postcss.config.js
├── package.json                  # deps: vue, pinia, vue-router, gsap
│
├── src/
│   ├── main.js                   # App bootstrap, smooth-scroll hash handler
│   ├── App.vue                   # Root component, IntersectionObserver for reveals
│   ├── index.css                 # CSS vars, Tailwind directives, reveal keyframes
│   │
│   ├── router/
│   │   └── index.js              # Hash-based routing, 10 routes under MainLayout
│   │
│   ├── layouts/
│   │   └── MainLayout.vue        # NavBar + <router-view> + FooterSection
│   │
│   ├── stores/
│   │   └── useSiteStore.js       # Pinia store, loads all 12 dummydata modules
│   │
│   ├── pages/                    # 12 route page components
│   │   ├── HomePage.vue          # Hero + Integration + Services + Architecture + Workflow + Methodology
│   │   ├── AboutPage.vue
│   │   ├── ServicesPage.vue
│   │   ├── ArchitecturePage.vue
│   │   ├── MethodologyPage.vue
│   │   ├── RoadmapPage.vue
│   │   ├── ProjectsPage.vue
│   │   ├── SectorsPage.vue
│   │   ├── CapabilitiesPage.vue
│   │   ├── TimelinePage.vue
│   │   ├── PartnersPage.vue
│   │   └── ContactPage.vue
│   │
│   ├── components/               # Reusable section components
│   │   ├── NavBar.vue            # Fixed header, desktop + mobile nav
│   │   ├── HeroSection.vue       # Full-screen hero, stats overlay
│   │   ├── IntegrationSection.vue
│   │   ├── ServicesSection.vue   # 4 glass-morphism cards with tilt effect
│   │   ├── ArchitectureSection.vue
│   │   ├── MethodologySection.vue
│   │   ├── Workflow.vue          # SVG diagram + 5 stage cards
│   │   ├── SectionWrapper.vue    # GSAP reveal wrapper
│   │   ├── AnimatedCounter.vue   # GSAP count-up
│   │   ├── FooterSection.vue
│   │   └── about/                # About page sub-components
│   │
│   ├── dummydata/                # 12 static data files (all site content)
│   │   ├── hero.js, about.js, services.js, architecture.js
│   │   ├── methodology.js, roadmap.js, projects.js, sectors.js
│   │   ├── capabilities.js, timeline.js, partners.js, contact.js
│   │
│   ├── images/
│   │   └── home-intro.png        # Hero background image
│   │
│   └── videos/                   # Video assets
│
└── SIS Presentation.pdf          # Business presentation
```

---

## 🔄 Code Flow

### 1. Entry & Bootstrap
```
index.html → src/main.js
                ├── createApp(App)
                ├── createPinia()
                ├── router (hash-based)
                └── mount('#app')
```

### 2. Routing
```
/#/           → MainLayout.vue (NavBar + Footer wrapper)
  ├── /       → HomePage.vue
  ├── /about  → AboutPage.vue
  ├── /roadmap → RoadmapPage.vue
  ├── /projects → ProjectsPage.vue
  ├── /sectors  → SectorsPage.vue
  ├── /capabilities → CapabilitiesPage.vue
  ├── /timeline → TimelinePage.vue
  ├── /partners → PartnersPage.vue
  └── /contact → ContactPage.vue
```

### 3. Data Flow
```
dummydata/*.js  →  Pinia Store (useSiteStore)
                      └── loadAll()  →  sets all state fields
                           ↓
                    Pages & Components
                      └── storeToRefs() / computed → reactive data
```

### 4. Animation System
- **Global reveal**: `App.vue` sets up `IntersectionObserver` on elements with `.reveal` or `[data-reveal]` — fades in + translates up
- **GSAP reveals**: `SectionWrapper.vue` uses GSAP for staggered entries
- **AnimatedCounter**: GSAP count-up on intersection
- **Workflow.vue**: Mouse-following atmospheric glow + staggered card entrance

### 5. Navigation
- Fixed `NavBar` at top (z-50)
- Hash-based routing + `scrollBehavior` for smooth anchor scrolling
- `main.js` has fallback smooth-scroll polyfill

---

## 🎨 Design System

### Colors
| Token | Hex | Usage |
|---|---|---|
| `--sis-primary` | `#1B3A6B` | Dark blue |
| `--sis-accent` | `#2F7EC7` | Links, CTAs |
| `--sis-light` | `#4A9FD4` | Light accent |
| `--sis-panel` | `#F4F7FA` | Page background |
| `--sis-text` | `#1A2332` | Body text |
| `--sis-muted` | `#6B7C93` | Secondary text |

### Typography
| Face | Role |
|---|---|
| **Inter** | Body text, UI labels |
| **Plus Jakarta Sans** | Headings, display text |

### Typography Scale (Tailwind)
| Token | Size | Weight |
|---|---|---|
| `headline-lg` | 32px | 700 |
| `headline-md` | 24px | 600 |
| `headline-sm` | 18px | 600 |
| `body-lg` | 16px | 400 |
| `body-md` | 14px | 400 |
| `label-sm` / `label-caps` | 12px | 500 / 700 |

### Visual Patterns
- **Hero**: Full-screen background image + dark gradient overlay + glass-morphism stat cards
- **Service sections**: Dark (`#0b1326`) backgrounds with circuit-line grids, glass cards with 3D tilt on hover
- **Workflow**: SVG topology diagram + 5 glass stage cards
- **Reveal animations**: Elements fade in with 30px upward translate (0.8s cubic-bezier)
- **Footer**: Light background (`#fff`), minimal, with social links

---

## 🧩 Key Architectural Decisions

1. **Static data in dummydata/** — No backend API. All content lives in JS files. Easy to replace with real API later.
2. **Hash-based routing** — Simplifies deployment. No server-side URL rewriting needed.
3. **Section components on HomePage** — Services, Architecture, Methodology are sections on `/`, not separate routes (though route pages also exist).
4. **Pinia store as data hub** — Single source of truth, but currently loads everything at once with an artificial 600ms delay.
5. **CSS variables + Tailwind** — Design tokens defined in both `index.css` and `tailwind.config.js` (some duplication).
6. **No external image assets** — Only one hero background image. All other visuals are SVG/CSS.

---

## 📋 Current UX Issues (Text-Heavy, Low Engagement)

| Problem | Impact |
|---|---|
| Only 1 background image across entire site | No visual storytelling |
| All data is raw text | Users skim, don't engage |
| No case study visuals (charts, diagrams) | Hard to grasp complex industrial concepts |
| No interactive elements beyond card tilt | Low dwell time |
| Hero section is static | Missed opportunity for first impression |
| No testimonials / social proof section | Low trust signal |
| Contact page is plain text | No incentive to reach out |
| Color palette is conservative (all blues) | Feels corporate, not modern |
| No animations on critical CTAs | Low conversion push |
| Services section text doesn't match dummydata | Content drift |

---

## 💡 Improvement Opportunities (Next Tasks)

1. **Add rich visuals** — Industrial images, diagrams, infographics, data visualizations
2. **Hero animation** — Animated particles, typing effect, video background
3. **Interactive service cards** — Click-to-expand, live preview demos
4. **Case study carousel** — With before/after metrics, project photos
5. **Testimonial carousel** — Client logos + quotes
6. **Live statistics dashboard** — Animated counters with data viz
7. **Improved CTAs** — More prominent, with micro-interactions
8. **Scroll-triggered animations** — Parallax, reveal sequences
9. **Image gallery per project/sector** — Show actual work
10. **Contact page with interactive map, form animations**
