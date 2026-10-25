# 08 — الحركة والتفاعلات (Animations & Interactions)

> جزء من [`project documentation (ar).md`](./project%20documentation%20%28ar%29.md)

---

## 1. نظام الظهور (Reveal System) — `App.vue`

- **المبدأ:** كل عنصر يحمل كلاس `.reveal` أو الخاصية `[data-reveal]` يبدأ مخفياً (`opacity:0; translateY(30px)`) ثم يظهر عند دخوله نافذة العرض.

```css
.reveal, [data-reveal] {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
}
.reveal.active, [data-reveal].active {
  opacity: 1;
  transform: translateY(0);
}
```

- **كيف يُضاف كلاس `active`؟** `App.vue` على `onMounted` يمر على كل العناصر ويراقبها عبر `useIntersectionObserver` (VueUse). عند دخول العنصر يضيف `active`.
- `MutationObserver` يضمن أن أي عناصر تُضاف لاحقاً (بعد التحميل) تُراقَب أيضاً.

> لإعادة التشغيل عند تعديل الحركة: `App.vue` يستدعي `observeElements()` تلقائياً عند أي تغيير في الـ DOM.

---

## 2. حركات GSAP

GSAP مستخدم في مكونين:

### 2.1 `SectionWrapper.vue` — Reveal متدرج (Stagger)

```js
import { gsap } from 'gsap'
gsap.set(children, { y: 30, opacity: 0 })          // إخفاء أولي
gsap.to(children, { y: 0, opacity: 1, duration: 0.7, ease: 'power3.out', stagger: 0.1 })
```

- يبحث داخل `slot` عن كل `[data-reveal]` ويظهرها تباعاً (stagger).
- يُستخدم حالياً في مكوّن `AboutSection.vue` (القديم غير المستخدم).

### 2.2 `AnimatedCounter.vue` — عدّاد

```js
gsap.to(displayedValue, { value: props.value, duration: 1.8, ease: 'power2.out', snap: 'value', onUpdate })
```

- يبدأ العد من 0 إلى `props.value` عندما يظهر العنصر بنسبة ≥ 60%.
- يُستخدم في `ProjectsSection-old.vue` (القديم غير المستخدم).

---

## 3. حركات محلية داخل المكونات

| المكوّن | الحركة |
|---|---|
| `PartnersSection` | تأثير **Tilt 3D** عند تحريك الماوس فوق بطاقة الشريك (`onTilt`): `perspective(800px) rotateX/rotateY` + `scale3d(1.03)` |
| `Workflow` | **توهج جوي (atmospheric glow)** يتبع الماوس (`handleMouseMove` يحرك `atmoRef`)، و بطاقات تظهر متدرجة (توقيت `i*90ms`) |
| `ServicesSection` | خط علوي (border gradient) يتمدد من اليسار عند hover (`scale-x-100`) |
| `MethodologySection` | بطاقة محتوى تبدّل البيانات عند الضغط على المرحلة (لا حركة متحركة بل تبادل محتوى) |
| `TimelineSection` | عقد دائرية تنبض (`timeline-pulse` keyframes في scoped style) |
| `NavBar` / `FooterSection` | انتقالات hover بسيطة (transition-colors، -translate-y) |
| `HeroSection` | نقاط متحركة (animateMotion) على مسارات SVG + شارة "LIVE" تومض (`blink`) |

---

## 4. مؤثرات Canvas

| المكوّن | التفاصيل |
|---|---|
| `IntelligenceBand` | رسم **موجات (waveform)** على `<canvas>` + مؤشرات منحنية (gauges) |
| `AboutDomains` | خلفية **نجوم متحركة (stars)** على `<canvas>` — تظهر فقط في الوضع الداكن، تعمل بـ `requestAnimationFrame` مع `twinkle` |

---

## 5. انتقالات Vue (Transitions)

معرّفة في `src/index.css`:

| الاسم | الاستخدام | التوقيت |
|---|---|---|
| `page-fade` | تبديل الصفحات (في `App.vue` حول `router-view`) | 0.25s opacity |
| `content-fade` | قائمة `ThemeToggle` | 0.35s |
| `fade-tab` | تبديل تبويبات القطاعات في `ProjectsSection-old` | 0.35s |
| `lightbox-fade` | الـ Lightbox في `ProjectsSection-old` | 0.3s |
| `mth-fade` | (إضافي) | 0.35s |
| `toast-slide` | `ToastNotification` (انزلاق من اليمين) | 0.4s cubic-bezier |

---

## 6. حركات الخلفية والـ Loaders

- `GlobalLoader`: 4 أشرطة (أعلى/أسفل/يمين/يسار) تتحرك بحركات `sweep-x` و `sweep-y` (keyframes في `index.css`) — تظهر أثناء التنقل.
- `PartnersSection` / أقسام أخرى: توهجات دائرية (radial gradients) مع `animate-pulse` أو `blur` ثابت.

---

## 7. نصائح عند إضافة حركة جديدة

1. للعناصر التي تظهر عند التمرير: أضف `data-reveal` (وسيتولى `App.vue` الباقي).
2. إذا احتجت توقيت/ستاجير مخصص → استخدم GSAP كما في `SectionWrapper`.
3. احرص على تجربة الوضعين الفاتح والداكن (بعض الألوان تختلف).
4. تجنب تعارض حركات مع `section:not(#hero)` flex rule (راجع [`10-warnings (ar).md`](./10-warnings%20%28ar%29.md)).
