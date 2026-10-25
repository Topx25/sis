# 09 — المحتوى والأصول (Content & Assets)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

هذا الملف يلخص: **أين يوجد كل محتوى الموقع؟** وما الأصول (صور/فيديو) الموجودة؟ وأيها مستخدم وأيها قديم؟

> للتفاصيل الكاملة (كل حقل وكل قيمة) انظر `backend data requirements.md` — هو المرجع الكامل للمحتوى.

---

## 1. مصادر المحتوى (Content Sources)

هناك نوعان:

### 1.1 البيانات التجريبية (`src/dummydata/`) — مخزنة في الـ Store

| الملف | المحتوى |
|---|---|
| `hero.js` | الشعار، العنوان، النص التعريفي، أزرار CTA، 4 إحصائيات |
| `about.js` | العنوان + 3 فقرات + 3 ركائز (Mission/Vision/Values) + 3 مبادئ |
| `services.js` | 4 خدمات (أيقونة + عنوان + وصف) |
| `architecture.js` | عنوان + 9 بروتوكولات + طبقات (Cloud/Edge/Field) + أمان + شركاء |
| `methodology.js` | 3 مراحل منهجية |
| `roadmap.js` | خطتا تحوّل رقمي (غير معروض حالياً) |
| `projects.js` | 14 مشروعاً (عميل/قطاع/موقع/عنوان/ملخص/وسوم/شعار) |
| `sectors.js` | 5 قطاعات (لون + قائمة مشاريع قصيرة) |
| `capabilities.js` | مصفوفة قدرات (5 أعمدة × 7 عملاء) |
| `partners.js` | شركاء تقنيون (6) + نظام بيئي (4) — بأسماء وصور وألوان |
| `contact.js` | عنوان + موقع + بريد + هاتفان |
| `industries.js` | 5 صناعات (أيقونة/عنوان/كيكر/نص/عملاء/نطاق) |
| `timeline.js` | 5 محطات زمنية |

### 1.2 المحتوى المدمج داخل المكونات (Component-scoped) — يُعدَّل مباشرة في `.vue`

| المكوّن | المحتوى المدمج |
|---|---|
| `NavBar.vue` | روابط القائمة الخمسة |
| `FooterSection.vue` | وصف العلامة، روابط الفوتر، `© 2026`، روابط LinkedIn/WhatsApp |
| `HeroSection.vue` | عناوين SVG (IIoT/ERP/SCADA/GATEWAY...)، سطر الحالة "Operational since 2020", أزرار |
| `PillarsSection.vue` | 3 ركائز (`pillars` array) |
| `IntelligenceBand.vue` | OEE 86%، Availability 99.9%، أعمدة Throughput `[26,118,48,92,34,108,62,76]` |
| `Workflow.vue` | 5 بطاقات مراحل (Mind/AI/PC/Factory/Human) + عناوين SVG |
| `MethodologySection.vue` | `stageDetails` (3 مراحل بنصوص تفصيلية) |
| `ClientsSection.vue` | 11 عميلاً (أسماء + شعارات) |
| `IntegrationSection.vue` | العناوين + `videoId` (يوتيوب) + نص قسم الفيديو |
| `ArchitectureSection.vue` | قائمة الـ Apps والأمان والبنجر والنصوص الثابتة |
| `ProjectsSection.vue` | العناوين + خريطة `industryImages` (5 صناعات → صور) |
| `about/AboutHero.vue` | العنوان + الوصف + CTA |
| `about/AboutMissionVision.vue` | كارتا Mission/Vision + بانر "Why Choose SiS?" |
| `about/AboutDomains.vue` | 5 مجالات (`sectors` array) |
| `about/AboutITOT.vue` | `otTags` + `itTags` |
| `about/AboutCoreValues.vue` | 3 كروت قيم (`cards` array) |
| `ContactSection.vue` | تسميات النموذج + placeholders + نصوص الإشعارات + كارت "Ready to Transform" |

---

## 2. الأصول (Assets)

### 2.1 مجلد `src/images/` — الصور العامة

| الصورة | الاستخدام |
|---|---|
| `SIS-Logo-solid.png` / `.webp` | الشعار (فاتح/داكن) — NavBar/Footer/SEO |
| `SIS-Logo-white.png` / `.webp` | شعار أبيض (وضع داكن) |
| `factory.webp` / `factory.jpg` | خلفية الـ Hero |
| `about-hero.webp` / `.png` | صورة صفحة About |
| `about-image.webp` / `.png` | (قديمة — راجع الاستخدام) |
| `control-room.webp`, `factory-floor.webp`, `controlling-*.webp/jpg`, `slider-1-1.webp/jpg` | صور إضافية |
| `industry-water/food/chemical/oil-gas/heavy.webp` | صور القطاعات في `ProjectsSection` |
| `cards/` (g1-final, g2, g3, slider-1-1) | صور كروت (مستخدمة سابقاً) |
| `sis-showcase.mp4` | فيديو قسم التكامل |

### 2.2 `src/images/ourClints/` — شعارات العملاء

مستخدمة في `ProjectsSection` (عبر `logoUrl(filename)` → `../images/ourClints/...`) وفي `ClientsSection` (شريط العملاء):

```
Al-Takamol-Cement.webp  BariQ.webp  delta.webp  emitsal.webp  energeya.webp
Gas-Cool.webp  gcf.webp  giza_systems.webp  henkel.webp  khalad-Petroleum.webp
omis.webp  orascom_const.webp  plast_company.webp  Qaliobia-Waste-Water.webp
Saudi-Aramco.webp  SWCC.webp  tabreed.webp  TAK-Foods-Factory.webp  uniha.webp
```

> ملاحظة: ملفات `.jpg` في نفس المجلد (مثل `delta.jpg`) **غير مستخدمة حالياً** — الكود يستخدم `.webp`. أسماء شعارات المشاريع في `projects.js` مثل `khalad-Petroleum.webp`, `emitsal.webp`, `henkel.webp`, `gcf.webp`, `uniha.webp` (لاحظ الاختلاف الإملائي في بعضها مثل `khalad`).

### 2.3 `src/images/partners/` — شعارات الشركاء

مستوردة مباشرة في `dummydata/partners.js`:

```
Siemens.png  Rockwell_Automation_Logo.png  ABB.png  Mitsubishi_Electric.png
Huawei.png  Egypt_4_Industry.jpg  odoo.svg  schnider.jpg  aveva.jpg  PTC.png
```

### 2.4 `src/videos/` — الفيديوهات

| الملف | الاستخدام |
|---|---|
| `increased-numbers.mp4` | خلفية `ArchitectureSection` |
| `src/images/sis-showcase.mp4` | فيديو `IntegrationSection` |

### 2.5 مجلدات قديمة (Legacy) — لمعرض `ProjectsSection-old.vue` (غير مستخدم)

```
images/SCADA/SCADA-1..8-150x150.jpg|.webp
images/AR/{6,7,18,AR-2}-150x150.jpg|.webp
images/Panel/Panel-Soultions-{1,2,4,5}-1-150x150.jpg|.webp
```

---

## 3. المحتوى النصي الثابت (Static Copy) — خلاصة

- **عناوين الصفحات (Page titles):** في `src/router/index.js` (انظر [`02`](./02-routing-and-pages%20%28ar%29.md)).
- **SEO:** `VITE_SITE_NAME` / `VITE_SITE_DESCRIPTION` في `.env` + `index.html`.
- **أزرار CTA** داخل المكونات ترتبط بمسارات: `projects`, `contact`, `architecture` (عبر `router.push`).

---

## 4. كيف تعدّل المحتوى؟

1. **بيانات الأقسام (خدمات/مشاريع/شركاء/قطاعات...):** عدّل ملف `dummydata/*.js` المقابل (المحتوى سيظهر فوراً بدون إعادة بناء في وضع dev).
2. **محتوى مدمج بمكوّن (نص بطاقة، روابط، عناوين):** عدّل الملف `.vue` مباشرة.
3. **صور:** أضف الملف في المجلد المناسب وحدّث الـ import/المرجع (أو استخدم `logoUrl('filename.webp')` في `ProjectsSection`).
4. **إعادة بناء الإنتاج:** بعد التعديل شغّل `npm run build`.
