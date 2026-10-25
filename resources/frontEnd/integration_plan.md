# SiS Admin Dashboard — Frontend Integration Plan

Checklist of frontend fixes required to integrate the Admin Dashboard with the real Laravel backend.
Tick items off as they are completed. Backend-only changes live in `frontend_requerments.md`.

---

## Todo list

- [x] **1. Create `frontend_requerments.md`** — document the endpoints/backend changes the backend team must add.
- [x] **2. Fix Services payload in `CollectionManager.vue`** — send `title`/`slug`/`summary`/`description`/`icon` (backend `Service` model has `title`, not `name`); stop dropping `icon` and `summary`.
- [x] **3. Fix image upload for collections** — `CollectionManager`/`ImageInput` must emit a real `File` and `createItem`/`updateItem` must send `multipart/form-data`.
- [x] **4. Fix image upload for sections** — `updateSection` must send `multipart/form-data` when a file is present; `SectionEditor` image input emits a `File`.
- [x] **5. Fix Hero section editor** — `HeroSectionResource` returns CTAs nested as `cta: {…}`; flatten to `cta_primary`/`cta_secondary`/links on load so the editor binds and saves the flat columns.
- [x] **6. Wire `WorkSampleCredentials.vue`** — register route + sidebar link, add `adminApi` methods (backed by the global visitor-credentials endpoints), so the page is live, not dead.
- [x] **7. Fix Contact Messages** — show the reply locally after sending (backend does not persist it yet) and make the pagination controls actually request pages.
- [x] **8. Build & verify** — `npm run build` (or dev compile) passes with no errors.

---

## Integration issues found (context)

| # | Area | Issue |
|---|------|-------|
| 1 | Services CRUD | Frontend + Postman send `name`; backend column is `title`. `icon`/`summary` are dropped by `writePayload`. |
| 2 | Hero editor | Resource nests `cta` object; schema binds flat `cta_primary` etc. |
| 3 | Image upload | Sections/collections send `blob:` strings; only Projects upload real files. |
| 4 | Contact reply | Backend does not persist `reply`/`replied_at`; resource omits them. |
| 5 | WorkSampleCredentials | Page calls non-existent `adminApi` methods and is not routed/nav-linked. |
| 6 | Visitor gating | `AccessModal` validates hardcoded `demo`/`demo` client-side; no public verify endpoint. |
| 7 | Partner logo | `PartnerResource::logo_url` runs `str_starts_with(null,…)` when `logo_path` is null. |

> Visitor-credential **public verification** (issue 6) requires a new backend endpoint — see `frontend_requerments.md`.