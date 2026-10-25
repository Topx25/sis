# توثيق مشروع موقع SiS — Superior Integrated Solutions

> **وثيقة التسليم (Handover) لمطوّر الواجهة الأمامية (Frontend Developer) الجديد**
>
> هذه الوثيقة مكتوبة باللغة العربية مع إبقاء المصطلحات التقنية (Technical Terms) بالإنجليزية بين قوسين، ويبقى الكود والأسماء الفنية كما هي باللغة الإنجليزية.

---

## الفهرس العام (Table of Contents)

| الملف | المحتوى |
|---|---|
| [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md) | هذه الصفحة: الفهرس + نظرة عامة + التقنيات + هيكل المشروع |
| [`01-setup-and-deployment (ar).md`](./01-setup-and-deployment%20%28ar%29.md) | التثبيت والتشغيل والنشر |
| [`02-routing-and-pages (ar).md`](./02-routing-and-pages%20%28ar%29.md) | التوجيه (Routing) والصفحات |
| [`03-storing-state (ar).md`](./03-storing-state%20%28ar%29.md) | التخزين (Storing) وإدارة الحالة (State Management) |
| [`04-data-workflow (ar).md`](./04-data-workflow%20%28ar%29.md) | دورة حياة البيانات: الجلب ← الاستدعاء ← العرض (Data Workflow) |
| [`05-api-integration (ar).md`](./05-api-integration%20%28ar%29.md) | التكامل (Integration) مع الـ API |
| [`06-components (ar).md`](./06-components%20%28ar%29.md) | المكونات (Components) |
| [`07-design-system (ar).md`](./07-design-system%20%28ar%29.md) | نظام التصميم (Design System) |
| [`08-animations (ar).md`](./08-animations%20%28ar%29.md) | الحركة والتفاعلات (Animations) |
| [`09-content-and-assets (ar).md`](./09-content-and-assets%20%28ar%29.md) | المحتوى والأصول (Content & Assets) |
| [`10-warnings (ar).md`](./10-warnings%20%28ar%29.md) | تنبيهات ونقاط انتباه (Warnings & Gotchas) |

> وثائق مرتبطة أخرى في جذر المشروع:
> - `backend data requirements.md` — متطلبات الـ Backend لكل بيانات الموقع (تُرسل لفريق الـ Backend).
> - `integration documentation.md` — توثيق تكامل الـ API (العميل، نقاط النهاية، كيف تصل البيانات للـ Store).

---

## 1. نظرة عامة على المشروع (Project Overview)

**SiS** موقع تعريفي لشركة **Superior Integrated Solutions (SiS)** — شركة متخصصة في أتمتة المصانع (Industrial Automation) وتكامل الـ OT مع الـ IT، وخدمات الـ IIoT عبر منطقة الشرق الأوسط وشمال أفريقيا (MENA).

الموقع هو **صفحة واحدة لعدة أقسام (Single Page App)** يحتوي على:

- **صفحة رئيسية (Home)** تتكوّن من عدة أقسام متتالية (Hero، Pillars، Integration، Services، Intelligence Band، Workflow، Methodology، Partners، Clients).
- **صفحة من نحن (About)** مع خط زمني للشركة.
- **صفحة المشاريع (Projects)** مع فلترة حسب القطاع.
- **صفحة المعمارية (Architecture)** شرح للمعمارية الصناعية.
- **صفحة تواصل (Contact)** مع نموذج إرسال رسالة.

### 1.1 الحالة الحالية (Current State) — مهم جدًا

- الموقع يعمل حالياً في وضع **"البيانات التجريبية فقط" (Dummy Data Only)**.
- جميع استدعاءات الـ API معطّلة عبر المفتاح `API_ENABLED = false` الموجود في `src/stores/useSiteStore.js`.
- كل أقسام الموقع تعرض بيانات من ملفات `src/dummydata/*.js` ومن محتوى مدمج داخل المكونات نفسها.
- لا توجد أي طلبات شبكة (Network Requests) تخرج من المتصفح حالياً.
- كود الـ API موجود بالكامل ولم يُحذف — فقط معطّل خلف المفتاح، ويمكن تفعيله لاحقاً (انظر [`05-api-integration (ar).md`](./05-api-integration%20%28ar%29.md)).

---

## 2. التقنيات والمكدس (Tech Stack)

| الطبقة (Layer) | التقنية (Technology) | الإصدار |
|---|---|---|
| الإطار (Framework) | **Vue 3** — Composition API مع `<script setup>` | `^3.4.0` |
| إدارة الحالة (State) | **Pinia** | `^3.0.4` |
| التوجيه (Router) | **Vue Router 4** — وضع الـ History (`createWebHistory`) | `^4.3.3` |
| التنسيق (Styling) | **Tailwind CSS 3** + متغيرات CSS مخصصة (CSS Variables) | `^3.4.4` |
| الحركة (Animation) | **GSAP 3** + `IntersectionObserver` الأصلي | `^3.12.2` |
| أدوات مساعدة (Utilities) | **VueUse** (`@vueuse/core`) | `^14.3.0` |
| الكاروسيل (Carousel) | **Swiper** (لقسم العملاء) | `^14.0.5` |
| البناء (Build) | **Vite 5** | `^5.4.1` |
| الأيقونات (Icons) | **Material Symbols Outlined** (من Google Fonts) + أيقونات SVG مخصصة في `UiIcon.vue` | — |
| تحويل الصور (Image) | **sharp** (أداة Dev فقط) | `^0.35.3` |

> ملاحظة: الوثيقة القديمة `WORK_FLOW.MD` في جذر المشروع **غير محدّثة** — تصف وضعاً قديماً (Hash Routing و 12 صفحة). اعتمد على هذه الوثائق الجديدة. انظر [`10-warnings (ar).md`](./10-warnings%20%28ar%29.md).

---

## 3. هيكل المشروع (Project Structure)

```
vue/
├── index.html                     # نقطة دخول الـ SPA: الخطوط + SEO meta + جبل التطبيق
├── vite.config.js                 # إعدادات Vite: base path + alias @ + ports
├── tailwind.config.js             # ألوان مخصصة + مقياس خطوط + ظلال + رسوم
├── postcss.config.js              # Tailwind + Autoprefixer
├── package.json                   # الاعتماديات (deps) وأوامر npm
├── .env                           # متغيرات بيئة التطوير (VITE_BASE_PATH, VITE_API_BASE_URL...)
├── .env.production                # متغيرات بيئة الإنتاج
│
├── src/
│   ├── main.js                    # تهيئة التطبيق + تحديث SEO + تمرير ناعم للحواشي (#)
│   ├── App.vue                    # المكوّن الجذر: GlobalLoader + router-view + مراقب reveal
│   ├── index.css                  # متغيرات CSS + Tailwind + حركات + skeleton + scrollbar
│   │
│   ├── router/
│   │   └── index.js               # التوجيه: 5 مسارات + عناوين الصفحات + scrollBehavior
│   │
│   ├── layouts/
│   │   └── MainLayout.vue         # الهيكل العام: NavBar + <router-view> + FooterSection
│   │
│   ├── pages/                     # مكونات الصفحات (5)
│   │   ├── HomePage.vue
│   │   ├── AboutPage.vue
│   │   ├── ProjectsPage.vue
│   │   ├── ArchitecturePage.vue
│   │   └── ContactPage.vue
│   │
│   ├── components/                # مكونات الأقسام (Sections) والمكونات المساعدة
│   │   ├── NavBar.vue             # الهيدر + التنقل (سطح المكتب + الجوال)
│   │   ├── FooterSection.vue      # الفوتر + روابط التواصل
│   │   ├── HeroSection.vue        # قسم البطل + شبكة OT–IT المرسومة بـ SVG
│   │   ├── PillarsSection.vue     # 3 ركائز (Connect / Monitor / Optimize)
│   │   ├── IntegrationSection.vue # القطاعات + القدرات + الفيديو
│   │   ├── ServicesSection.vue    # الخدمات (4 بطاقات)
│   │   ├── IntelligenceBand.vue   # مؤشرات OEE/Availability/Throughput
│   │   ├── Workflow.vue           # مخطط SVG + 5 بطاقات مراحل
│   │   ├── MethodologySection.vue # منهجية العمل (3 مراحل تفاعلية)
│   │   ├── PartnersSection.vue    # الشركاء (تقني + نظام بيئي) + تأثير tilt
│   │   ├── ClientsSection.vue     # عملاء متحركون (Swiper marquee)
│   │   ├── ProjectsSection.vue    # قسم المشاريع مع تبويبات القطاعات
│   │   ├── ProjectsSection-old.vue# نسخة قديمة — غير مستخدمة (Legacy)
│   │   ├── ArchitectureSection.vue# معمارية OT–IT + فيديو خلفية
│   │   ├── ContactSection.vue     # نموذج التواصل + بيانات الاتصال + Toast
│   │   ├── TimelineSection.vue    # الخط الزمني للشركة (صفحة About)
│   │   ├── SectionWrapper.vue     # غلاف حركة GSAP
│   │   ├── LazyLoadSection.vue    # تحميل كسول عبر IntersectionObserver
│   │   ├── AnimatedCounter.vue    # عدّاد GSAP
│   │   ├── Skeleton.vue           # هيكل التحميل (Loading placeholder)
│   │   ├── GlobalLoader.vue       # شريط التحميل عند التنقل
│   │   ├── ThemeToggle.vue        # مبدّل الثيم (Light/Dark/System)
│   │   ├── ToastNotification.vue  # إشعار النجاح/الخطأ (Teleport to body)
│   │   ├── UiIcon.vue             # أيقونات SVG مخصصة + Material Symbols
│   │   └── about/                 # مكونات صفحة About
│   │       ├── AboutHero.vue
│   │       ├── AboutMissionVision.vue
│   │       ├── AboutDomains.vue
│   │       ├── AboutITOT.vue
│   │       ├── AboutCoreValues.vue
│   │       └── AboutSection.vue   # (مكوّن قديم/غير مستخدم في الصفحة الحالية)
│   │
│   ├── stores/
│   │   ├── useSiteStore.js        # Store الرئيسي (Pinia) + مفتاح API_ENABLED
│   │   └── navigationState.js     # حالة التنقل (ref بسيطة للـ Loader)
│   │
│   ├── composables/
│   │   └── useTheme.js            # إدارة الثيم (Light/Dark/System + localStorage)
│   │
│   ├── api/
│   │   ├── index.js               # عميل الـ API (fetch wrapper + unwrap + ngrok header)
│   │   └── normalizers.js         # محوّل البيانات (API shape → component shape)
│   │
│   ├── dummydata/                 # 13 وحدة بيانات تجريبية (مصدر المحتوى الحالي)
│   │   ├── hero.js  about.js  services.js  architecture.js
│   │   ├── methodology.js  roadmap.js  projects.js  sectors.js
│   │   ├── capabilities.js  partners.js  timeline.js  contact.js
│   │   └── industries.js
│   │
│   ├── images/                    # الصور (Logos, Industries, ourClints, partners...)
│   ├── videos/                    # الفيديوهات (increased-numbers.mp4)
│   └── utils/
│       └── scrollTo.js            # التمرير إلى الأقسام (Home/About/Contact...)
│
├── public/                        # ملفات عامة (تُنسخ كما هي)
├── dist/                          # ناتج البناء (Build output)
└── Project_Overview/              # هذه المجموعة التوثيقية
```

---

## 4. نقاط البداية للمطوّر الجديد

1. اقرأ الملفات بالترتيب: `01` (التشغيل) ثم `02` (التوجيه) ثم `03` (التخزين) ثم `04` (دورة البيانات) ثم `05` (التكامل) ثم الباقي.
2. شغّل المشروع كما في [`01-setup-and-deployment (ar).md`](./01-setup-and-deployment%20%28ar%29.md).
3. افحص `src/stores/useSiteStore.js` أولاً — هو مركز البيانات (Data Hub).
4. عند تعديل المحتوى: مصدر المحتوى الحالي هو `src/dummydata/*.js` + المحتوى المدمج في المكونات (انظر [`09-content-and-assets (ar).md`](./09-content-and-assets%20%28ar%29.md)).
5. عند تفعيل الـ API: اتبع [`05-api-integration (ar).md`](./05-api-integration%20%28ar%29.md).
