# 10 — تنبيهات ونقاط انتباه (Warnings & Gotchas)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

هذه قائمة بالأمور التي يجب أن يعرفها المطوّر الجديد قبل التعديل، لتجنّب المفاجآت.

---

## 1. الـ API معطّل عمداً (API_ENABLED = false)

- الوضع الحالي: **لا توجد طلبات شبكة**، كل المحتوى من `dummydata` ومن النصوص المدمجة.
- كود الـ API موجود بالكامل خلف `if (API_ENABLED)`.
- **لا تحذف كود الـ API** — ستحتاجه لاحقاً. للتفعيل انظر [`05-api-integration (ar).md`](./05-api-integration%20%28ar%29.md).
- عند التفعيل: إذا فشل طلب ما، القسم يعرض **Skeleton** (وليس بيانات dummy). هذا مقصود.

---

## 2. `ProjectsSection-old.vue` — مكوّن قديم غير مستخدم

- لا تستورده أي صفحة (كلاهما يستخدم `ProjectsSection.vue`).
- يحتوي على: إحصائيات، معرض صور (galleryCategories + galleryImages)، دالة `iconForSector`، Lightbox.
- لا يُبنى ولا يُعرض. **لا تعدّله** إلا لو كان مخططاً لإعادته.
- الصور المرتبطة به (SCADA/AR/Panel) **قديمة** وليست مستخدمة حالياً.

---

## 3. `AboutSection.vue` و `SectionWrapper.vue` و `LazyLoadSection.vue` — قديمة/غير مستخدمة

- `AboutSection.vue` (في `components/about/`) **غير مستخدم** في `AboutPage.vue` الحالية.
- `LazyLoadSection.vue` **غير مستخدم** في أي صفحة نشطة.
- `SectionWrapper.vue` يستخدمه فقط `AboutSection.vue` القديم (أي عملياً غير مُفعّل).
- لا تحذفها إن كانت قد تُستفاد لاحقاً، لكن لا تعتمد عليها.

---

## 4. قاعدة CSS `section:not(#hero)` في `index.css`

```css
section:not(#hero) {
  display: flex !important;
  flex-direction: column !important;
  justify-content: center !important;
}
```

- تفرض أن **كل** قسم (عدا `#hero`) هو `flex column center`.
- قد تكسر أقساماً جديدة تضيفها (خاصة التي تعتمد على `grid` أو `row`). انتبه عند بناء أقسام جديدة.

---

## 5. مكرر الألوان بين `index.css` و `tailwind.config.js`

- نفس الـ tokens معرّفة في مكانين: متغيرات CSS في `src/index.css` وقيم Tailwind في `tailwind.config.js` (كلاسات مثل `text-sis-accent`, `bg-sis-panel`).
- عند تغيير لون: **عدّل الاثنين معاً**، وإلا سيظهر عدم اتساق (بعض الأماكن تقرأ المتغير، وأخرى تقرأ تكوين Tailwind).
- أيضاً أسماء `sis.*` في Tailwind تستخدم `var(--sis-*, fallback)` — تغيير المتغيرات في `index.css` كافٍ غالباً، لكن `dark.*` في Tailwind قيم ثابتة.

---

## 6. أيقونة `chart` في `PillarsSection`

- `PillarsSection.vue` يستخدم `icon: "chart"` في الركيزة الثالثة، و `chart` **غير موجود** في `KNOWN_ICONS` في `UiIcon.vue`.
- النتيجة: تُعرض كاسم **Material Symbols** (`chart`) — تحقق أنها تظهر بشكل مقبول، أو أضف أيقونة `chart` المخصصة في `UiIcon.vue`.

---

## 7. الوثيقة القديمة `WORK_FLOW.MD` غير محدّثة

- تصف وضعاً قديماً: **Hash-based routing** و **12 صفحة** و `dummydata` بدون API.
- الوضع الحالي: **History routing** و **5 صفحات** و مفتاح `API_ENABLED`.
- **لا تعتمد عليها** — اعتمد على مجلد `Project_Overview/` والوثائق المرتبطة.

---

## 8. مشكلة ngrok (إن فُعّل الـ API)

- الـ ngrok المجاني يحجب طلبات المتصفح (لا هيدر CORS) → حل مؤقت في `src/api/index.js` عبر رأس `ngrok-skip-browser-warning: true`.
- الصور على ngrok تُحظر أيضاً → تعرض المكونات الحروف الأولى (initials) كبديل.
- **الحل الدائم:** دومين حقيقي للـ Backend.

---

## 9. أسماء ملفات الشعارات غير متناسقة

- أسماء شعارات المشاريع في `projects.js` مكتوبة كما هي في أسماء الملفات (مثل `khalad-Petroleum.webp`، `emitsal.webp`، `gcf.webp`) — وهي مختلفة إملائياً عن الاسم المعروض للعميل.
- عند إضافة مشروع أو شعار جديد: تأكد من تطابق `logo` (اسم الملف) مع الملف الفعلي في `src/images/ourClints/`.
- في `ClientsSection` تُستخدم أسماء وشعارات مختلفة قليلاً (نفس المجلد).
- ملفات `.jpg` في `ourClints/` غير مستخدمة (الكود يستخدم `.webp`).

---

## 10. `VITE_BASE_PATH` والمنافذ

- المشروع يُبنى تحت مسار فرعي `/vue/dist/` في بيئة العمل الحالية.
- خادم التطوير يعمل على منفذ **4174** حالياً (وليست 4173 الافتراضية).
- عند النشر: تأكد من `VITE_BASE_PATH` يتطابق مع مسار الاستضافة، ووجود SPA fallback للـ History routing.

---

## 11. الوضع الداكن (Dark Mode) يعتمد على كلاس `.dark`

- بعض المكونات تقرأ `inject("isDark")` (مثل `AboutDomains`) — تُمرَّر عبر Provide/Inject من أعلى الشجرة.
- أي مكوّن جديد يريد معرفة الثيم يجب أن يتعامل مع `useTheme()` (أو `inject`) — لا تعتمد على `matchMedia` مباشرة داخل المكونات لاتساق السلوك.

---

## 12. تحميل الخطوط والأيقونات من Google Fonts

- `index.html` يحمّل Inter + Plus Jakarta Sans + Material Symbols من Google Fonts.
- إذا كان الموقع سيُعرض في بيئة **غير متصلة بالإنترنت** أو بلوك هذه الدومينات → ستفقد الخطوط والأيقونات.
- الخطوط الأحادية مثل Cascadia Code هي خيارات نظام (fallback) وليست محمّلة.

---

## 13. اختبار سريع قبل التسليم

- [ ] `npm run build` ينجح بدون أخطاء.
- [ ] الصفحات الخمس تفتح وتعمل.
- [ ] لا أخطاء في وحدة التحكم (Console).
- [ ] لا توجد طلبات شبكة (في الوضع الحالي) — أو 5 طلبات تعمل (إن فُعّلت).
- [ ] الوضع الفاتح والداكن يبدوان سليمين في الأقسام الرئيسية.
- [ ] القائمة والفوتر يعملان على الجوال (Responsive).
