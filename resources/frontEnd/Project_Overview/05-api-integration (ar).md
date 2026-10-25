# 05 — التكامل مع الـ API (API Integration)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

هذا الملف يجيب على: **كيف يتكامل الموقع مع الـ Backend؟** (التكامل / Integration)

> ⚠️ **حالياً الـ API معطّل** (`API_ENABLED = false`). هذا الملف يشرح الكود الموجود وكيفية تفعيله لاحقاً.

---

## 1. عميل الـ API (`src/api/index.js`)

كلبوس (Wrapper) واحد حول `fetch` تستخدمه كل الواجهة.

### 1.1 الميزات

| الخاصية | الوصف |
|---|---|
| `request(path, options)` | `fetch(BASE_URL + path)` مع `Accept: application/json`، وتفكيك الغلاف (unwrap) للاستجابة: يعيد `json.data` مباشرة |
| `httpsUrl(url)` | يرفع أي `http://` إلى `https://` (للصور والشعارات) |
| رأس ngrok | عند وجود `.ngrok` في الـ host يضيف `ngrok-skip-browser-warning: true` |
| الأخطاء | عند استجابة غير 2xx يرمي `Error` مع `.status` و `.errors` |

```js
const BASE_URL = import.meta.env.VITE_API_BASE_URL;  // من .env
const IS_NGROK = BASE_URL.includes(".ngrok");
```

### 1.2 نقطة النهاية المقدَّمة (Exported API)

| الدالة | نقطة النهاية (نسبة إلى base URL) | ملاحظات |
|---|---|---|
| `api.getProjects(params)` | `GET /projects` | `per_page=100` افتراضياً + `sector`/`search` اختيارية |
| `api.getProject(slug)` | `GET /projects/{slug}` | |
| `api.getServices()` | `GET /services` | |
| `api.getSectors()` | `GET /sectors` | |
| `api.getPartners()` | `GET /partners` | |
| `api.getTimeline()` | `GET /timeline` | |
| `api.submitContact(payload)` | `POST /contact` | body: `{name, email, phone, company_name, message}` |

**قاعدة URL الحالية:** `https://fried-robbing-professed.ngrok-free.dev/api/v1`

---

## 2. غلاف الاستجابة (Response Envelope)

- الـ Backend يرد بـ `{ success, message, data }`.
- الـ client يفك الغلاف ويعيد `json.data`.
- أمثلة ما يُفك:
  - `GET /projects` → `data.projects` (مصفوفة)
  - `GET /sectors` → `data` (مصفوفة)

---

## 3. محوّلات البيانات (`src/api/normalizers.js`)

تحويل شكل الـ API إلى الشكل الذي تنتظره المكونات.

| الدالة | مصدر الحقول |
|---|---|
| `normalizeProjects(result)` | `result.projects[]` → `{ id, client, sector, location, title, summary, tags, logo, mark }` |
| `normalizeSectors(result, projects)` | → `{ id, slug, label, icon, description, color, projects }` (يَحسب عدد مشاريع القطاع من `projects`) |
| `normalizeServices(result)` | → `{ id, icon, title, description }` |
| `normalizePartners(result)` | → `{ technology: [...], ecosystem: [] }` (يوزّع ألواناً افتراضية) |
| `normalizeTimeline(result)` | → `{ id, year, title, text, icon }` |

### 3.1 حقول مشتقة (Derived Fields) — الـ Frontend يحسبها

- `project.mark` / `project.logo` fallback: إن لم توجد صورة شعار → يعرض الحروف الأولى (initials) من `client_name` عبر `initials()`.
- `sector.color`: يُشتق من `SECTOR_COLORS[slug]` (خريطة slug→لون)، وإلا افتراضي `#2F7EC7`.
- `partner.color`: يُشتق من `PARTNER_COLORS[index % 4]`.
- `sector.projects`: عدّ المشاريع من قائمة المشاريع (Array بمقدار العدد — تُعرض كـ "N projects").

### 3.2 ألوان القطاعات (`SECTOR_COLORS`)

```js
const SECTOR_COLORS = {
  "oil-gas": "#1B3A6B",
  "water-waste-water": "#2F7EC7",
  "chemical-industries": "#4A9FD4",
  "food-beverage-fmcg": "#1B3A6B",
  "heavy-industries": "#2F7EC7",
}
```

> يجب أن يطابق الـ Backend هذه الـ slugs حتى تُطبق الألوان الصحيحة.

---

## 4. نقطة `POST /contact` (نموذج التواصل)

**الملف:** `src/components/ContactSection.vue` — دالة `handleSubmit()`

### 4.1 السلوك حسب `API_ENABLED`

```js
async function handleSubmit() {
  submitting.value = true;
  try {
    if (API_ENABLED) {
      await api.submitContact({
        name: form.name,
        email: form.email,
        phone: form.phone,
        company_name: form.company_name,
        message: form.message,
      });
    } else {
      await new Promise((resolve) => setTimeout(resolve, 800)); // نجاح وهمي
    }
    // نجاح: Toast + إعادة تعيين النموذج
  } catch (error) {
    // خطأ: Toast خطأ مع رسالة error.message
  } finally {
    submitting.value = false;
    showToast.value = true;
  }
}
```

- **الوضع الحالي:** محاكاة نجاح بعد 800ms (لا يوجد اتصال فعلي).
- **الوضع المُفعّل:** `POST /contact` حقيقي.
- بيانات النموذج (Reactive): `form = reactive({ name, email, phone, company_name, message })`.
- `ToastNotification` يعرض النجاح (أخضر) أو الخطأ (أحمر).

---

## 5. مفتاح التفعيل `API_ENABLED` — خطوات إعادة التفعيل

1. **غيّر الرابط:** في `.env` و `.env.production` اجعل `VITE_API_BASE_URL` يشير للـ Backend الحقيقي.
2. **فعّل المفتاح:** في `src/stores/useSiteStore.js`:
   ```js
   export const API_ENABLED = true;
   ```
3. **أعد البناء/التشغيل:** `npm run build` أو `npm run dev`.
4. **افحص:** افتح DevTools → Network وتأكد من ظهور 5 طلبات GET تعمل (انظر [`04-data-workflow (ar).md`](./04-data-workflow%20%28ar%29.md) §7 عن سلوك الفشل).
5. **حدّث توثيق الحالة:** ملاحظة الحالة في `integration documentation.md`.

> ملاحظة: عند التفعيل يجب أن يطابق الـ Backend كل الحقول في [`backend data requirements.md`](../backend%20data%20requirements.md) (وثيقة تُرسل لفريق الـ Backend).

---

## 6. مشكلة ngrok المعروفة

- الـ ngrok المجاني يعترض طلبات المتصفح (لا يعيد هيدر CORS) → يعرض صفحة تحذير `ERR_NGROK_6024`.
- الحل المطبّق في الواجهة: رأس `ngrok-skip-browser-warning: true` يُرسل فقط عندما يكون الـ host `.ngrok`.
- مشكلة إضافية: الصور المستضافة على ngrok تُحظر أيضاً، ولا تستطيع `<img>` إرسال الهيدر — لذلك تعرض المكونات **الحروف الأولى (initials)** كبديل عند فشل تحميل الصور.
- **الحل الدائم:** نشر الـ Backend على دومين حقيقي (وليس ngrok مجاني).

---

## 7. خلاصة سريعة للتكامل

```
component → api.getX() → fetch → response {success,message,data}
                                          ↓ unwrap json.data
                              normalizers.normalizeX()  →  store state → render
```

- كل التكامل معزول في `src/api/` و `src/stores/useSiteStore.js`.
- المكونات لا تتصل بالشبكة مباشرة — تتعامل مع الـ Store فقط.
