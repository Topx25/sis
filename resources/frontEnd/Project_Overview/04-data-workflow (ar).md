# 04 — دورة حياة البيانات: الجلب ← الاستدعاء ← العرض (Data Workflow)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

هذا الملف يجيب على: **كيف تُجلب البيانات؟ كيف تُستدعى؟ وكيف تُعرض؟** (Fetch / Call / Render)

---

## 1. المخطط الكامل (Full Flow)

```
index.html
   │
   ▼
src/main.js                          ← تهيئة التطبيق (createApp + Pinia + Router)
   │
   ▼
src/App.vue  (onMounted)
   │
   ├── siteStore.loadAll()           ← نقطة الانطلاق لتحميل كل البيانات
   │        │
   │        ├── [API_ENABLED = true]
   │        │      api.getProjects()  →  src/api/index.js  → fetch(BASE_URL + /projects)
   │        │      api.getSectors()   →  fetch(.../sectors)
   │        │      api.getServices()  →  fetch(.../services)
   │        │      api.getPartners()  →  fetch(.../partners)
   │        │      api.getTimeline()  →  fetch(.../timeline)
   │        │            │
   │        │            ▼
   │        │      normalizers.js  (normalizeProjects, normalizeSectors, ...)
   │        │            │
   │        │            ▼
   │        └── [API_ENABLED = false]  ← الوضع الحالي
   │               تعيين الـ dummydata مباشرة إلى حقل الـ Store
   │
   ▼
Store state (reactive Pinia)  ← هذا هو "التخزين" (انظر 03)
   │
   ▼
المكونات: storeToRefs(siteStore) → تعرض البيانات في القالب (template)
```

---

## 2. نقطة البداية: `App.vue`

```js
import { onMounted } from 'vue'
import { useSiteStore } from '@/stores/useSiteStore'

const siteStore = useSiteStore()

onMounted(() => {
  if (!siteStore.loaded) {
    siteStore.loadAll()       // تُستدعى مرة واحدة فقط عند أول تحميل
  }
})
```

- `loadAll()` تُستدعى مرة واحدة عند تركيب التطبيق (guarded بـ `loaded`).
- بعدها تبقى البيانات في الـ Store لكل الصفحات (لا يُعاد تحميلها عند التنقل).

---

## 3. مرحلة "الاستدعاء" (Calling) — `useSiteStore.loadAll()`

تعمل في أحد مسارين حسب `API_ENABLED`:

### 3.1 عند تفعيل الـ API (`API_ENABLED = true`)

```js
const results = await Promise.allSettled([
  api.getProjects(),
  api.getSectors(),
  api.getServices(),
  api.getPartners(),
  api.getTimeline(),
]);
```

- **5 طلبات في نفس الوقت (متوازية)**.
- `Promise.allSettled` يضمن أن فشل طلب لا يمنع الآخرين.
- كل نتيجة تُحوَّل عبر الـ normalizer الخاص بها، ثم تُخزَّن في الـ Store.
- أعلام التحميل تُضبط حسب نجاح/فشل كل طلب.

### 3.2 في الوضع الحالي (`API_ENABLED = false`)

- **لا توجد أي طلبات شبكة إطلاقاً.**
- القيم تُعيّن مباشرة من الـ `dummydata`.
- كل أعلام التحميل تُضبط `true` فوراً.

---

## 4. مرحلة "العرض" (Rendering) — كيف تعرض المكونات؟

### 4.1 القالب (Template) يقرأ من الـ Store

مثال `ServicesSection.vue`:

```html
<template v-for="(card, i) in displayItems" :key="card?.id ?? i">
  <article v-if="servicesLoaded" ...>
    <UiIcon :name="card.icon" />
    <h3>{{ card.title }}</h3>
    <p>{{ card.description }}</p>
  </article>
  <div v-else>...Skeleton...</div>
</template>
```

```js
const { services, servicesLoaded } = storeToRefs(siteStore);

const displayItems = computed(() => {
  if (servicesLoaded.value) return services.value;
  return Array.from({ length: 4 });   // عدد العناصر الوهمية للسكليتون
});
```

**المنطق العام (pattern) لكل قسم يعتمد على API:**

1. `storeToRefs` يجلب الحقل + علم التحميل الخاص به.
2. `computed` يرجع البيانات إذا كان العلم `true`، أو عدداً من العناصر الوهمية (`placeholder array`) لعرض الـ Skeletons.
3. القالب يستخدم `v-if="XXXLoaded"` لعرض المحتوى الحقيقي أو الـ Skeleton.

### 4.2 أقسام لا تعتمد على API (تعرض دائمًا)

- `HeroSection`, `ArchitectureSection`, `MethodologySection`, `PillarsSection`, `Workflow`, `IntelligenceBand`, `ClientsSection` تعرض بيانات `dummy` أو محتوى مدمج مباشرة — تعتمد على `loaded` العام أو على بيانات ثابتة.

### 4.3 التفاعل مع البيانات (Interactions)

- **`ProjectsSection.vue`**: يعرض المشاريع مع فلترة حسب القطاع.
  - `projectSectors` (المستخلصة من `projects`)، `filteredProjects` (فلترة)، و `showAllProjects` (إظهار الكل بعد أول 6).
  - `activeIndustryData` (من `industries`) يتحكم في المحتوى الجانبي.
  - `industryImages` خريطة (title → صورة) تختار صورة القطاع.
  - الشعارات: `logoSource(logo)` → إن كان `https://` يستخدمه مباشرة، وإلا يعامله كاسم ملف تحت `../images/ourClints/`.
- **`IntegrationSection.vue`**: يعرض `sectors` (مع `s.projects.length` عدد المشاريع) + `capabilities.columns`.
- **`ContactSection.vue`**: نموذج تفاعلي — انظر [`05-api-integration (ar).md`](./05-api-integration%20%28ar%29.md).
- **`MethodologySection.vue`**: `stageDetails` كائن بـ 3 مفاتيح، `activeStage` يتغير بالضغط، `activeData` computed.

---

## 5. جدول: مصدر البيانات لكل مكوّن

| المكوّن | مصدر البيانات | حقل الـ Store | العلم |
|---|---|---|---|
| `HeroSection` | `hero` + محتوى مدمج (SVG) | `hero` | `loaded` |
| `PillarsSection` | محتوى مدمج (`pillars` array) | — | — |
| `IntegrationSection` | `sectors` + `capabilities` | `sectors`, `capabilities` | `sectorsLoaded`, `loaded` |
| `ServicesSection` | `services` | `services` | `servicesLoaded` |
| `IntelligenceBand` | محتوى مدمج (86%، 99.9%، أعمدة) | — | — |
| `Workflow` | محتوى مدمج (5 مراحل + SVG) | — | — |
| `MethodologySection` | `methodology` + `stageDetails` مدمج | `methodology` | `loaded` |
| `PartnersSection` | `partners` | `partners` | `partnersLoaded` |
| `ClientsSection` | محتوى مدمج (11 عميل + صور) | — | — |
| `ProjectsSection` | `projects` + `industries` | `projects`, `industries` | `projectsLoaded`, `loaded` |
| `ArchitectureSection` | `architecture` | `architecture` | `loaded` |
| `ContactSection` | `contact` | `contact` | `loaded` |
| `TimelineSection` | `timeline` | `timeline` | `timelineLoaded` |
| about/* | محتوى مدمج (نصوص + مصفوفات) | — | — |
| `FooterSection` | محتوى مدمج (روابط + عناوين) | — | — |

---

## 6. أمثلة توضيحية مختصرة

### مثال: قسم الخدمات (Services)

1. `App.vue` → `loadAll()` → `this.services = services` (من dummydata) و `servicesLoaded = true`.
2. `ServicesSection` يقرأ `services` عبر `storeToRefs`.
3. `displayItems` يعيد المصفوفة.
4. القالب يعرض 4 بطاقات: أيقونة (`card.icon`) + عنوان (`card.title`) + وصف (`card.description`).

### مثال: قسم المشاريع (Projects) — عرض مقيد

1. `projects` = 14 مشروعاً.
2. `filteredProjects`: لو القطاع `All` و `!showAllProjects` → `slice(0, 6)` (أول 6 فقط).
3. الضغط على زر القطاع → `activeProjectSector` يتغير → الفلترة.
4. كل بطاقة مشروع تعرض: `sector` (شارة)، `location` (أيقونة موقع)، `client` (عنوان)، `summary`، `tags`.

---

## 7. ماذا يحدث عند خطأ في الـ API (لو فُعّل)؟

- الطلب الفاشل → `projectsLoaded = false` → القسم يعرض **Skeleton** (وليس بيانات dummy وليس صفحة فارغة).
- هذا سلوك مقصود للتأكد من أن البيانات جاءت من الخادم فعلًا.
- `Promise.allSettled` يمنع فشل طلب واحد من إيقاف باقي الطلبات.
