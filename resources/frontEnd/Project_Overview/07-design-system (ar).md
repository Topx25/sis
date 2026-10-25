# 07 — نظام التصميم (Design System)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

---

## 1. المنهج العام

الموقع يجمع بين:
- **Tailwind CSS 3** للـ utilities (تنسيقات سريعة في القوالب).
- **متغيرات CSS مخصصة (CSS Variables)** معرّفة في `src/index.css` للـ design tokens (ألوان، خلفيات، ظلال).
- **الوضع الداكن (Dark Mode)** عبر كلاس `.dark` على `<html>` + تنسيقات `dark:` من Tailwind.

> ملاحظة: بعض الـ tokens مكرّرة بين `index.css` و `tailwind.config.js`. عند التعديل غيّر في الموضعين معاً (انظر [`10-warnings (ar).md`](./10-warnings%20%28ar%29.md)).

---

## 2. متغيرات CSS الأساسية (`src/index.css`)

### 2.1 ألوان العلامة (Brand Colors)

| المتغير | القيمة | الاستخدام |
|---|---|---|
| `--sis-primary` | `#1b3a6b` | الأزرق الداكن |
| `--sis-accent` | `#2f7ec7` | الروابط والأزرار (CTA) |
| `--sis-light` | `#4a9fd4` | تدرج أفتح |
| `--sis-panel` | `#f5f5f5` | خلفية الصفحة الفاتحة |
| `--sis-text` | `#2d2d2d` | النص الأساسي |
| `--sis-muted` | `#7a7a7a` | النص الثانوي |

### 2.2 ألوان الدارك (Dark)

| المتغير | القيمة |
|---|---|
| `--dark-bg` | `#0a1120` |
| `--dark-bg-alt` | `#070d1a` |
| `--dark-bg-deep` | `#0b1326` |
| `--dark-bg-card` | `#101b31` |
| `--dark-text` | `#eaf1fb` |
| `--dark-accent-blue` | `#37b6ff` |
| `--dark-accent-teal` | `#5fe0c4` |

### 2.3 أكسنت ومعامل RGB

```css
--accent-blue: #4a9fd4;   --accent-blue-r: 74; --accent-blue-g: 159; --accent-blue-b: 212;
--accent-teal: #4fc4a8;   --accent-teal-r: 79; --accent-teal-g: 196; --accent-teal-b: 168;
```

- تُستخدم قيم RGB لبناء `rgba(var(--accent-blue-r), ...)` مع شفافية مختلفة في كثير من الأماكن.
- في الوضع الداكن تتبدل هذه القيم (أزرق ساطع #37b6ff / تركواز #5fe0c4).

### 2.4 خلفيات الأقسام والكروت

```css
--bg-section-1 / --bg-section-2    /* تدرجات خلفية متناوبة بين الأقسام */
--bg-card-1 / --bg-card-2 / --bg-card-3  /* تدرجات الكروت */
--bg-glass / --bg-glass-card        /* خلفية زجاجية (Glassmorphism) */
--border-glass                      /* حدود زجاجية */
--overlay-bg                        /* تغطية الفيديو */
--dot-mesh                          /* نقاط شبكية خلفية */
```

- كل هذه القيم تُعرف مرتين: للفاتح في `:root` وللداكن في `:root.dark`.

### 2.5 متغيرات الـ SVG (مخططات Workflow / Hero)

```css
--svg-text-primary / --svg-text-secondary / --svg-text-label / --svg-text-annotation
--svg-node-fill / --svg-node-stroke / --svg-grid / --svg-flow
```

تُستخدم في مخططات `Workflow.vue` و `HeroSection.vue` و `ArchitectureSection.vue` لتلوين العقد والخطوط تلقائياً حسب الثيم.

### 2.6 حدود وشفافيات مقياسية

`--border-20/30/40/50`, `--text-muted-40..90`, `--bg-accent-5..70`, `--ring-*`, `--shadow-*`, `--border-accent-*`.

---

## 3. تكوين Tailwind (`tailwind.config.js`)

- **darkMode: `'class'`** — الوضع الداكن يعتمد على كلاس `dark`.
- `content`: `./index.html` + `./src/**/*.{vue,js}`.
- ألوان مخصصة: `sis.*` (مرتبطة بمتغيرات CSS) + `dark.*` + مجموعة ألوان Material (مثل `primary`, `surface`...).

### 3.1 الخطوط (Fonts)

| الخط | الدور | المصدر |
|---|---|---|
| **Inter** | نص الجسم (Body) | Google Fonts |
| **Plus Jakarta Sans** | العناوين (Headings/Display) | Google Fonts |
| **Cascadia Code / Consolas** | النصوص الأحادية (mono، شارات، أرقام) | مدمج |

- تُحمّل Inter و Plus Jakarta Sans في `index.html` عبر `fonts.googleapis.com`.
- توجد أيضاً مقياس خطوط مخصص في Tailwind: `headline-lg/md/sm`, `body-lg/md`, `label-sm`, `label-caps`.

### 3.2 ألوان Tailwind الشائعة في القوالب

- `text-sis-text`, `text-sis-muted`, `text-sis-accent`, `bg-sis-panel`, `bg-sis-panel` ...
- للداكن: `dark:text-[#eaf1fb]`, `dark:bg-[#0e1728]`, `dark:border-[rgba(126,165,224,0.14)]`, `dark:text-[var(--accent-blue)]`.

---

## 4. الأنماط البصرية (Visual Patterns)

| النمط | الوصف |
|---|---|
| **Glassmorphism** | كلاسا `.card-glass` + `.card-glass-border` في `index.css` (blur خلفي 20px + حدود شفافة) |
| **Skeleton shimmer** | كلاس `.skeleton` — تدرج متحرك (يتلألأ)؛ تعريف للفاتح والداكن |
| **Scrollbar** | تخصيص شامل لشريط التمرير بلون `--sis-accent` |
| **Focus** | `:focus-visible` بإطار أزرق `#2f7ec7` |
| **الكروت** | حدود `--border-30`, خلفية `--bg-card-1/2`, hover مع رفع (`-translate-y-1`) |
| **الأقسام** | خلفيات متناوبة `--bg-section-1/2` + تدرج عبر `flex flex-col items-center justify-center` (راجع التحذير في `10`) |

---

## 5. الوضع الداكن (Dark Mode)

- يتم تفعيله بإضافة كلاس `dark` إلى `<html>` عبر `applyTheme()` في `src/composables/useTheme.js`.
- الخيارات: `light | dark | system` (يُحفظ في `localStorage['sis-theme']`).
- في القوالب: تُستخدم صيغة `dark:` (مثل `dark:bg-[#0b1326]`) إضافةً إلى القيم العادية.
- بعض المكونات تستخدم `inject("isDark")` (مثل `AboutDomains`) لتبديل الأنماط برمجياً.

> تنبيه: خيار "system" يحتاج `matchMedia('(prefers-color-scheme: dark)')` — عند تغيير نظام المستخدم يحدّث الوضع تلقائياً.

---

## 6. أساسيات الصفحة (`src/index.css`)

```css
@layer base {
  html { scroll-behavior: smooth; }
  body { font-family: "Inter"; background-color: var(--sis-panel); color: var(--sis-text); }
  section:not(#hero) { display:flex; flex-direction:column; justify-content:center; }
}
```

> قاعدة `section:not(#hero)` تفرض كل الأقسام (عدا hero) أن تكون `flex column center` — قد تؤثر على أي قسم جديد تضيفه. راجع [`10-warnings (ar).md`](./10-warnings%20%28ar%29.md).

---

## 7. SEO والميتا (`index.html`)

- العنوان والوصف يُحقنان عبر `%VITE_SITE_NAME%` و `%VITE_SITE_DESCRIPTION%` من `.env`.
- Open Graph / Twitter tags مع صورة `/SIS-Logo-solid.png`.
- `main.js` يحدّث `og:url` و `og:image` ديناميكياً عبر `data-runtime-url` و `data-runtime-image`.
- الخطوط + Material Symbols تُحمَّل من Google Fonts.
