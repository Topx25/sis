# 03 — التخزين وإدارة الحالة (Storing & State Management)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

هذا الملف يجيب على: **أين تُخزَّن البيانات؟** (التخزين / Storing)

---

## 1. نظرة عامة

هناك **ثلاثة أماكن للتخزين** في المشروع:

| المكان | ماذا يُخزَّن | متى يُستخدم |
|---|---|---|
| **Pinia Store** (`useSiteStore`) | كل بيانات الموقع (الـ sections) | المصدر الأساسي الذي تقرأ منه المكونات |
| **ملفات `src/dummydata/*.js`** | البيانات التجريبية (المحتوى الحالي) | مصدر البيانات عند `API_ENABLED=false` |
| **`localStorage`** | اختيار الثيم (`sis-theme`) | يستمر حتى بعد إغلاق المتصفح |
| **`navigationState.js`** (ref عادية) | حالة التنقل | شريط تحميل GlobalLoader |

---

## 2. الـ Store الرئيسي: `useSiteStore` (Pinia)

**الملف:** `src/stores/useSiteStore.js`

### 2.1 مفتاح التفعيل (API_ENABLED)

```js
export const API_ENABLED = false;
```

- **`false`** (الوضع الحالي): لا توجد طلبات شبكة، والبيانات تُؤخذ من `dummydata` مباشرة.
- **`true`**: تفعيل مسار الـ API بالكامل (انظر [`05-api-integration (ar).md`](./05-api-integration%20%28ar%29.md)).

### 2.2 الحالة (State)

```js
state: () => ({
  loaded: false,
  loading: false,
  // أعلام تحميل لكل نطاق يعتمد على API:
  projectsLoaded: false,
  sectorsLoaded: false,
  servicesLoaded: false,
  partnersLoaded: false,
  timelineLoaded: false,
  // حقول البيانات:
  hero: null,
  about: null,
  services: null,
  architecture: null,
  methodology: null,
  roadmap: null,
  projects: null,
  sectors: null,
  capabilities: null,
  partners: null,
  contact: null,
  industries: null,
  timeline: null,
})
```

### 2.3 الحقول (Fields) ومصدرها ومستهلكها

| حقل الـ Store | المصدر (Dummy) | المكونات المستهلكة |
|---|---|---|
| `hero` | `dummydata/hero.js` | `HeroSection.vue` |
| `about` | `dummydata/about.js` | (مكوّن `AboutSection` القديم فقط — غير مستخدم حالياً) |
| `services` | `dummydata/services.js` | `ServicesSection.vue` |
| `architecture` | `dummydata/architecture.js` | `ArchitectureSection.vue` |
| `methodology` | `dummydata/methodology.js` | `MethodologySection.vue` |
| `roadmap` | `dummydata/roadmap.js` | (لم يُعرض بعد — Future) |
| `projects` | `dummydata/projects.js` | `ProjectsSection.vue` |
| `sectors` | `dummydata/sectors.js` | `IntegrationSection.vue` |
| `capabilities` | `dummydata/capabilities.js` | `IntegrationSection.vue` |
| `partners` | `dummydata/partners.js` | `PartnersSection.vue` |
| `contact` | `dummydata/contact.js` | `ContactSection.vue` |
| `industries` | `dummydata/industries.js` | `ProjectsSection.vue` |
| `timeline` | `dummydata/timeline.js` | `TimelineSection.vue` |

### 2.4 الإجراء (Action): `loadAll()`

```js
async loadAll() {
  if (this.loading) return;
  this.loading = true;

  if (API_ENABLED) {
    // 5 طلبات GET متوازية عبر Promise.allSettled
    // تعيين أعلام التحميل + تشغيل الـ normalizers
  } else {
    // تعيين البيانات مباشرة من الـ dummydata
    this.projects = projects;
    this.services = services;
    this.sectors = sectors;
    this.partners = partners;
    this.timeline = timeline;
    this.projectsLoaded = true;
    this.sectorsLoaded = true;
    this.servicesLoaded = true;
    this.partnersLoaded = true;
    this.timelineLoaded = true;
  }

  // مجالات لا تعتمد على API — تُعيّن دائماً:
  this.hero = hero;
  this.about = about;
  this.architecture = architecture;
  this.methodology = methodology;
  this.roadmap = roadmap;
  this.capabilities = capabilities;
  this.contact = contact;
  this.industries = industries;

  this.loaded = true;
  this.loading = false;
}
```

### 2.5 أعلام التحميل (Loading Flags)

- `projectsLoaded / sectorsLoaded / servicesLoaded / partnersLoaded / timelineLoaded`:
  - في وضع الـ API: تعكس نجاح/فشل كل طلب على حدة.
  - في وضع Dummy: `true` جميعها فوراً.
- `loaded`: علم عام يعني انتهاء `loadAll()` — يتحكم في الأقسام غير المعتمدة على API.

**سلوك المكونات مع الأعلام:**

- القسم الذي يعتمد على حقل API يعرض **Skeleton** (هيكل تحميل) حتى يصبح علمه `true`.
- في الوضع الحالي (dummy) الأعلام `true` فوراً، لذا لا تظهر الـ Skeletons.

---

## 3. ملفات البيانات التجريبية (`src/dummydata/`)

13 ملفاً، كل ملف يعرّف وحدة محتوى:

| الملف | شكل البيانات (البنية الأساسية) |
|---|---|
| `hero.js` | `{ tagline, headline, subheadline, cta:{primary,secondary}, stats[] }` |
| `about.js` | `{ title, paragraphs[], pillars[], principles[] }` |
| `services.js` | `[ { id, icon, title, description } ]` (4 خدمات) |
| `architecture.js` | `{ title, subtitle, cloudLayer, applications, protocols, edgeLayer, fieldLayer, cybersecurity, partners }` |
| `methodology.js` | `{ title, stages[ { number, title, description } ] }` |
| `roadmap.js` | `{ title, phases[ { label, steps[] } ] }` |
| `projects.js` | `[ { client, sector, location, title, summary, tags[], logo?, mark? } ]` (14 مشروعاً) |
| `sectors.js` | `[ { id, label, color, projects[ { client, location, scope } ] } ]` (5 قطاعات) |
| `capabilities.js` | `{ title, columns[], rows[ { client, capabilities[] } ] }` |
| `partners.js` | `{ technology[], ecosystem[] }` (كل عنصر: `{ name, logo, color }`) |
| `contact.js` | `{ headline, website, email, phones[] }` |
| `industries.js` | `[ { icon, title, kicker, text, clients[], scope[] } ]` (5 صناعات) |
| `timeline.js` | `[ { year, title, text } ]` (5 محطات) |

> التفاصيل الكاملة (الحقول + القيم) موجودة في `backend data requirements.md`.

---

## 4. تخزين الثيم (`localStorage`)

**الملف:** `src/composables/useTheme.js`

```js
const STORAGE_KEY = 'sis-theme'
const theme = ref(localStorage.getItem(STORAGE_KEY) || 'system')
```

- الخيارات: `light` | `dark` | `system`.
- يُحفظ الاختيار في `localStorage` بمفتاح `sis-theme` ويستمر بعد إغلاق المتصفح.
- عند اختيار `system` يتابع تغييرات نظام المستخدم (`prefers-color-scheme`) عبر `matchMedia`.
- `applyTheme(resolved)` يضيف/يزيل كلاس `dark` على `document.documentElement`.

> ملاحظة: `useTheme` يُنشئ حالة على مستوى الوحدة (module-level state)، لذا كل المكونات تشارك نفس الثيم. `ThemeToggle.vue` هو من يستدعي `setTheme(t)`.

---

## 5. حالة التنقل (`navigationState.js`)

```js
import { ref } from 'vue'
export const navigating = ref(false)
```

- ليست Pinia Store — مجرد `ref` مصدَّرة.
- تقرأها `GlobalLoader.vue` لتُظهر/تُخفي شريط التحميل.

---

## 6. من أين تقرأ المكونات البيانات؟

تستخدم المكونات **`storeToRefs`** من Pinia لجعل الحقول تفاعلية (Reactive):

```js
import { storeToRefs } from "pinia";
import { useSiteStore } from "@/stores/useSiteStore";

const siteStore = useSiteStore();
const { services, servicesLoaded } = storeToRefs(siteStore);
```

- `storeToRefs` يجعل الوصول إلى الحالة مفصولاً عن المرجع التفاعلي (loses reactivity if destructured without it).
- بعض المكونات تستخدم `computed` لتجهيز البيانات للعرض (مثل `filteredProjects` في `ProjectsSection.vue`).
