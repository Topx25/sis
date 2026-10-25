# 06 — المكونات (Components)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

جرد كامل للمكونات مصنّفاً حسب الدور والاستخدام. كل المكونات تستخدم **Composition API** مع `<script setup>`.

---

## 1. مكونات الهيكل العام (Layout)

| المكوّن | الملف | الوظيفة |
|---|---|---|
| `NavBar` | `src/components/Layout/NavBar.vue` | الهيدر الثابت + روابط التنقل (Home/About/Projects/Architecture/Contact) + مبدّل الثيم + قائمة الجوال |
| `FooterSection` | `src/components/Layout/FooterSection.vue` | الفوتر: الشعار + وصف العلامة + روابط + حقوق النشر + أيقونات LinkedIn/WhatsApp |
| `GlobalLoader` | `src/components/GlobalLoader.vue` | أشرطة تحميل متحركة حول الشاشة عند التنقل (`navigating`) |

---

## 2. مكونات الصفحة الرئيسية (Home Sections)

| المكوّن | الملف | المحتوى |
|---|---|---|
| `HeroSection` | `HeroSection.vue` | قسم البطل: شعار/عنوان/نص/زران + إحصائيات + صورة مصنع + شبكة OT–IT مرسومة بـ SVG متحرك |
| `PillarsSection` | `PillarsSection.vue` | 3 ركائز (Connect / Monitor / Optimize) بأيقونات مخصصة |
| `IntegrationSection` | `IntegrationSection.vue` | قائمة القطاعات (من store) + قائمة القدرات (من store) + فيديو `sis-showcase.mp4` + رابط يوتيوب |
| `ServicesSection` | `ServicesSection.vue` | 4 بطاقات خدمات (أيقونة + عنوان + وصف) مع حد علوي متحرك |
| `IntelligenceBand` | `IntelligenceBand.vue` | مؤشرات OEE (86%) + Availability (99.9%) + أعمدة Throughput + موجات Canvas |
| `Workflow` | `Workflow.vue` | مخطط SVG لمعمارية الـ IT/OT + 5 بطاقات مراحل (Mind/AI/PC/Factory/Human) + توهج يتبع الماوس |
| `MethodologySection` | `MethodologySection.vue` | 3 مراحل تفاعلية (تغيير عند الضغط) + نقاط توضيحية |
| `PartnersSection` | `PartnersSection.vue` | شبكة الشركاء (Technology + Ecosystem) مع تأثير tilt عند التحويم + بديل بالحروف الأولى |
| `ClientsSection` | `ClientsSection.vue` | شريط عملاء متحرك (Swiper marquee) — 11 عميلاً بشعارات |

---

## 3. مكونات الصفحات الأخرى

| المكوّن | الملف | الصفحة |
|---|---|---|
| `ProjectsSection` | `ProjectsSection.vue` | صفحة المشاريع: تبويبات قطاعات (من `industries`) + قائمة مشاريع مع فلترة + صورة القطاع + شارات |
| `ArchitectureSection` | `ArchitectureSection.vue` | صفحة المعمارية: فيديو خلفية + مخطط طبقات (Cloud/Edge/Field) + قائمة بروتوكولات + كارت أمان |
| `ContactSection` | `ContactSection.vue` | صفحة التواصل: معلومات الاتصال + نموذج إرسال + Toast |

---

## 4. مكونات صفحة About (`components/about/`)

| المكوّن | المحتوى |
|---|---|
| `AboutHero` | قسم البطل: عنوان "Architects of the 4th Industrial Revolution" + صورة |
| `AboutMissionVision` | كارت الرسالة (Mission) + كارت الرؤية (Vision) + بانر "Why Choose SiS?" |
| `AboutDomains` | 5 مجالات خبرة (قطاعات) بأيقونات + خلفية نجوم متحركة (Canvas) في الوضع الداكن |
| `TimelineSection` | الخط الزمني للشركة (5 محطات من `timeline` بالـ store) |
| `AboutITOT` | شرح الفرق بين OT و IT + وسوم (tags) لكل منهما |
| `AboutCoreValues` | 3 بطاقات قيم (Precision / Innovation / Reliability) |
| `AboutSection` | ⚠️ مكوّن قديم — **غير مستخدم** في الصفحة الحالية (يقرأ `about` من الـ store) |

---

## 5. المكونات المساعدة (Utility Components)

### 5.1 `UiIcon` — الأيقونات

**الملف:** `src/components/common/UiIcon.vue`

- إذا كان الاسم ضمن `KNOWN_ICONS` → يرسم **SVG مخصص**.
- وإلا → يعرض أيقونة **Material Symbols** بالاسم نفسه.

```js
const KNOWN_ICONS = [
  'monitor', 'predictive', 'cube', 'layers', 'energy', 'database',
  'connect', 'pulse', 'robot-arm', 'cpu', 'settings', 'arrow',
  'chevron-down', 'water', 'factory', 'science', 'oil',
  'engineering', 'shield',
]
```

- الاستخدام: `<UiIcon name="robot-arm" :size="24" />`
- أي اسم خارج القائمة (مثل `local_gas_station`, `science`, `handyman`, `water_drop`, `restaurant`) يُعرض كـ Material Symbols.

### 5.2 `Skeleton`

**الملف:** `src/components/Skeleton.vue` — عنصر تحميل متلألئ (شimmer).

```html
<div class="skeleton" v-bind="$attrs" />
```

- يُمرَّر `class` لضبط الأبعاد (مثل `h-5 w-48 rounded-full`).
- يُستخدم في كل الأقسام أثناء انتظار البيانات.

### 5.3 `ToastNotification`

**الملف:** `src/components/ToastNotification.vue` — إشعار منبثق.

- Props: `title`, `message`, `duration` (افتراضي 5000ms), `variant` (`success`|`error`).
- `Teleport to="body"` + حركة انزلاق.
- يُصدر `close` عند الإغلاق.

### 5.4 `AnimatedCounter`

**الملف:** `src/components/common/AnimatedCounter.vue` — عدّاد تصاعدي بـ GSAP عند الوصول للعنصر.

- Props: `value` (Number), `suffix` (String).

### 5.5 `ThemeToggle`

**الملف:** `src/components/ThemeToggle.vue` — قائمة منسدلة (Light/Dark/System).

- يستخدم `useTheme` من `src/composables/useTheme.js`.
- يُغلق عند الضغط خارجه.

### 5.6 `SectionWrapper`

**الملف:** `src/components/common/SectionWrapper.vue` — غلاف قسم يضيف حركة Reveal بـ GSAP.

- يبحث عن `[data-reveal]` داخل `slot` ويحرّكها بستاجات (stagger).
- Props: `wrapperClass`.

### 5.7 `LazyLoadSection`

**الملف:** `src/components/LazyLoadSection.vue` — تحميل كسول لمكوّن عند الاقتراب.

- Props: `loader` (دالة تعيد Promise بمكوّن), `skeletonClass`.
- يستخدم `useIntersectionObserver` من VueUse.
- ⚠️ حالياً **لا يُستخدم** في أي صفحة نشطة.

---

## 6. العلاقات بين المكونات (Component Relationships)

```
MainLayout
├── NavBar ────────────── ThemeToggle
├── <router-view> ─────── (الصفحات الخمس)
└── FooterSection

HomePage
├── HeroSection, PillarsSection, IntegrationSection,
├── ServicesSection (UiIcon + Skeleton), IntelligenceBand,
├── Workflow, MethodologySection, PartnersSection (Skeleton),
└── ClientsSection (Swiper + Skeleton)

AboutPage
├── AboutHero, AboutMissionVision, AboutDomains,
├── TimelineSection (Skeleton), AboutITOT, AboutCoreValues

ContactPage → ContactSection (ToastNotification + Skeleton)

App.vue → GlobalLoader
```
