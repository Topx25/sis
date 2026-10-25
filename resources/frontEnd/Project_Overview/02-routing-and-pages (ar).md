# 02 — التوجيه والصفحات (Routing & Pages)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

---

## 1. إعدادات التوجيه (`src/router/index.js`)

- وضع التوجيه: **History Mode** (`createWebHistory(import.meta.env.BASE_URL)`).
- البنية: مسار جذر واحد `/` يستخدم **MainLayout**، وكل الصفحات أبناء (children) له.
- تحميل الصفحات كسول (Lazy Loading) عبر `() => import(...)`.

```js
const routes = [
  {
    path: "/",
    component: MainLayout,
    children: [
      { path: "", name: "home", component: HomePage, meta: { title: defaultTitle } },
      { path: "about", name: "about", component: AboutPage, meta: { title: "About Us | SiS" } },
      { path: "projects", name: "projects", component: ProjectsPage, meta: { title: "Projects | SiS" } },
      { path: "architecture", name: "architecture", component: ArchitecturePage, meta: { title: "Architecture | SiS" } },
      { path: "contact", name: "contact", component: ContactPage, meta: { title: "Contact Us | SiS" } },
    ],
  },
];
```

---

## 2. المسارات الخمسة (The 5 Routes)

| المسار (Path) | الاسم (Name) | العنوان (Title) | المكوّن (Component) |
|---|---|---|---|
| `/` | `home` | `SiS - Superior Integrated Solutions` | `HomePage.vue` |
| `/about` | `about` | `About Us | SiS` | `AboutPage.vue` |
| `/projects` | `projects` | `Projects | SiS` | `ProjectsPage.vue` |
| `/architecture` | `architecture` | `Architecture | SiS` | `ArchitecturePage.vue` |
| `/contact` | `contact` | `Contact Us | SiS` | `ContactPage.vue` |

- العنوان الافتراضي `defaultTitle` يُطبَّق تلقائياً إذا لم توجد `meta.title`.

### سلوك التوجيه (Router Hooks)

- `router.beforeEach` → يضبط `navigating.value = true` (يُظهر شريط تحميل `GlobalLoader`).
- `router.afterEach` → يضبط `document.title`، ثم `navigating.value = false`.
- `scrollBehavior`: يعود لأعلى الصفحة، أو لموضع محفوظ، أو لعنصر `#hash` بسلاسة.

---

## 3. الهيكل العام (Layout)

**`src/layouts/MainLayout.vue`** يغلّف كل الصفحات:

```html
<NavBar />
<main>
  <router-view />
</main>
<FooterSection />
```

يعني أن **NavBar** و **FooterSection** يظهران في كل الصفحات تلقائياً.

---

## 4. خريطة الصفحة ← المكونات (Page → Components Map)

### 4.1 `HomePage.vue` — الصفحة الرئيسية

تتكون من 9 أقسام بالترتيب:

1. `HeroSection` — قسم البطل
2. `PillarsSection` — الركائز
3. `IntegrationSection` — التكامل (قطاعات + قدرات + فيديو)
4. `ServicesSection` — الخدمات
5. `IntelligenceBand` — مؤشرات الأداء
6. `Workflow` — مراحل سير العمل
7. `MethodologySection` — منهجية العمل
8. `PartnersSection` — الشركاء
9. `ClientsSection` — العملاء

### 4.2 `AboutPage.vue` — من نحن

1. `AboutHero`
2. `AboutMissionVision`
3. `AboutDomains`
4. `TimelineSection` — الخط الزمني
5. `AboutITOT`
6. `AboutCoreValues`

### 4.3 `ProjectsPage.vue` — المشاريع

1. `ProjectsSection`

### 4.4 `ArchitecturePage.vue` — المعمارية

1. `ArchitectureSection`

### 4.5 `ContactPage.vue` — التواصل

1. `ContactSection`

---

## 5. التنقل والتمرير (Navigation & Scrolling)

### 5.1 روابط القائمة (`NavBar.vue`)

```js
const mainNav = [
  { label: "Home", route: "home" },
  { label: "About Us", route: "about" },
  { label: "Projects", route: "projects" },
  { label: "Architecture", route: "architecture" },
  { label: "Contact", route: "contact" },
];
```

- `navWithRoute` → روابط التنقل بين الصفحات.
- `dropdownNav` فارغ حالياً (يمكن إضافة قائمة منسدلة لاحقاً).

### 5.2 التمرير إلى الأقسام (`src/utils/scrollTo.js`)

الدالة `scrollToSection(id, router, route)`:

```js
const homeSections = new Set(['hero', 'services'])   // أقسام داخل الصفحة الرئيسية
const routeMap = {
  about: 'about',
  contact: 'contact',
  projects: 'projects',
  capabilities: 'capabilities',
  architecture: 'architecture',
}
```

المنطق:

1. إن كان `id` من أقسام الصفحة الرئيسية (`hero` أو `services`):
   - إن كنت في الصفحة الرئيسية → `scrollIntoView` ناعم.
   - وإلا → تنقّل إلى `home` مع `hash: '#hero'`.
2. إن كان `id` في `routeMap` → تنقّل إلى الصفحة المعنية.
3. تستخدمها عناصر: `FooterSection` (Home/About/Services/Contact) وروابط أخرى.

### 5.3 التمرير للحواشي (Hash) في `src/main.js`

- `smoothScrollToHash(hash)`: تمرير ناعم إلى عنصر بـ `id` مع تعويض ارتفاع الهيدر (header offset).
- إن كان المتصفح يدعم `scroll-behavior: smooth` الأصلي → يستخدمه، وإلا → محاكاة عبر `requestAnimationFrame`.
- يُستدعى من `router.afterEach` عند وجود `to.hash` (بعد تأخير 30ms لضمان اكتمال العرض).

---

## 6. حالة التنقل (Navigation State)

**`src/stores/navigationState.js`** — حالة بسيطة (غير Pinia):

```js
export const navigating = ref(false)
```

- يستخدمها `GlobalLoader.vue` لعرض شريط التحميل أعلى/أسفل/جوانب الشاشة أثناء التنقل بين الصفحات.
- تُضبط من `router.beforeEach` / `router.afterEach`.

---

## 7. ملاحظات عن مكونات قديمة مرتبطة بالتوجيه

- `AboutSection.vue` (مكوّن قديم في `components/about/`) **غير مستخدم** في `AboutPage.vue` الحالية (التي تستخدم 6 مكونات أخرى). يمكن حذفه أو تحديثه.
- `ProjectsSection-old.vue` **غير مستخدم** في أي صفحة — انظر [`10-warnings (ar).md`](./10-warnings%20%28ar%29.md).
