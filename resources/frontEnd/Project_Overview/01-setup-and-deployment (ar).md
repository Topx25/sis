# 01 — التثبيت والتشغيل والنشر (Setup, Run & Deployment)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

---

## 1. المتطلبات (Prerequisites)

- **Node.js** إصدار حديث (يدعم Vite 5 — يُنصح بـ Node 18 أو أحدث).
- **npm** (يأتي مع Node).
- إن لم تكن الحزم مثبتة: شغّل `npm install` في جذر المشروع.

---

## 2. الأوامر الأساسية (Commands)

من جذر المشروع (`/home/sis/www/vue`):

| الأمر | الوظيفة |
|---|---|
| `npm install` | تثبيت الاعتماديات (dependencies) |
| `npm run dev` | تشغيل خادم التطوير (Development Server) |
| `npm run build` | بناء نسخة الإنتاج إلى مجلد `dist/` |
| `npm run preview` | معاينة ناتج البناء محلياً |

أوامر `package.json`:

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "preview": "vite preview"
}
```

---

## 3. إعدادات Vite (`vite.config.js`)

```js
base: env.VITE_BASE_PATH || '/',          // مسار القاعدة للنشر (يُقرأ من env)
resolve.alias: { '@': ./src }             // الاسم المختصر لاستيراد ملفات src
server: { host: true, port: 4173 }
preview: { host: true, port: 4173 }
```

### 3.1 قاعدة المسار (Base Path)

- الافتراضي `/` (نشر في جذر الدومين).
- المشروع الحالي يُنشر تحت مسار فرعي: `VITE_BASE_PATH=/vue/dist/` (انظر `.env`).
- عند النشر تحت مسار فرعي، يجب أن يكون كل من `index.html` والملفات العامة على هذا المسار، ويعتمد `createWebHistory(import.meta.env.BASE_URL)` على هذا المتغير.

### 3.2 الاسم المختصر (Alias)

كل الاستيرادات داخل المشروع تستخدم `@/`:

```js
import { useSiteStore } from "@/stores/useSiteStore";
```

يُحدَّد في `vite.config.js` على أنه `./src`.

---

## 4. ملفات البيئة (Environment Files)

| الملف | الاستخدام |
|---|---|
| `.env` | متغيرات بيئة التطوير (Development) |
| `.env.production` | متغيرات بيئة الإنتاج (Production build) |

### 4.1 المتغيرات الموجودة

```
VITE_BASE_PATH=/vue/dist/
VITE_API_BASE_URL=https://fried-robbing-professed.ngrok-free.dev/api/v1
VITE_SITE_NAME=SiS - Superior Integrated Solutions
VITE_SITE_DESCRIPTION=Superior Integrated Solutions (SiS) bridges OT and IT across the MENA region with industrial automation, IIoT, and digital transformation.
```

| المتغير | الدور |
|---|---|
| `VITE_BASE_PATH` | مسار القاعدة (يستخدمه `vite.config.js` و `router`) |
| `VITE_API_BASE_URL` | رابط قاعدة الـ API (يُستخدم في `src/api/index.js`) — **غير مستخدم حالياً** لأن `API_ENABLED=false` |
| `VITE_SITE_NAME` | اسم الموقع (يُحقن في `index.html` عبر `%VITE_SITE_NAME%`) |
| `VITE_SITE_DESCRIPTION` | وصف الموقع (يُحقن في meta description و Open Graph) |

> أي متغير يبدأ بـ `VITE_` يصبح متاحاً داخل الكود عبر `import.meta.env.VITE_...`.

### 4.2 قواعد هامة

- أي تغيير في `.env` يتطلب **إعادة تشغيل خادم التطوير** وإعادة البناء للإنتاج.
- الرابط ngrok الحالي **مؤقت** — عند جاهزية الدومين الحقيقي يُغيَّر في الملفين معاً (`.env` و `.env.production`).
- لا ترفع أسرار حقيقية في هذه الملفات إن كانت ستُتتبع في Git.

---

## 5. خوادم التشغيل (Dev Servers) والمنافذ

- المنفذ الافتراضي في الإعدادات هو **4173**.
- في بيئة العمل الحالية يعمل الخادم على منفذ **4174** ويخدم `http://localhost:4174/vue/dist/` (لأن المنفذ 4173 كان مشغولاً بتطبيق آخر).
- للتأكد من أن الخادم يخدم ناتج `dist/` (وليس المصدر)، استخدم `npm run preview` أو خادماً ثابتاً موجهاً إلى `dist/`.

---

## 6. النشر (Deployment)

1. `npm run build` → ينتج مجلد `dist/`.
2. انشر محتويات `dist/` على أي خادم ثابت (Nginx, Apache, Netlify, Vercel, GitHub Pages...).
3. بما أن التوجيه يستخدم **History Mode**، يجب إعداد إعادة التوجيه (Fallback) لكل المسارات إلى `index.html` على الخادم (SPA fallback).
4. تأكد من أن `VITE_BASE_PATH` في `.env.production` يتطابق مع مسار الاستضافة:
   - استضافة في الجذر → `/`
   - استضافة تحت `https://domain/vue/dist/` → `/vue/dist/`
5. افحص الصفحات الخمس في المتصفح (Home، About، Projects، Architecture، Contact) مع التأكد من أن روابط الـ Assets تعمل (صور، فيديوهات).

---

## 7. فحص سريع بعد التشغيل

- افتح DevTools → Network، وأعد تحميل الصفحة.
- في الوضع الحالي (dummy only) **لن ترى أي طلبات** إلى `ngrok` — هذا طبيعي وصحيح.
- افحص وحدة التحكم (Console): يجب ألا يكون هناك أخطاء CORS أو أخطاء جافا سكريبت.
