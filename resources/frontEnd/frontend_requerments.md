# SiS Backend — Frontend Requirements (Missing Endpoints / Changes)

Endpoints or backend changes the frontend needs in order to fully integrate the Admin Dashboard
and the public site. These are **not yet implemented** (or currently broken) in `sis-api/`.

Reference collection: `SiS - Admin Dashboard API Only.postman_collection.json`

---

## 1. Fix the Services create/update contract (`title`, not `name`)

- **Current:** `AdminCollectionController` + Postman "6.1 Create Service" use `name`.
- **Backend model `Service` has `title`** (`title`, `slug`, `summary`, `description`, `icon`).
- **Action:** send `title` when creating/updating services. Postman collection must be updated too
  (`"name": …` → `"title": …`). The frontend has already been fixed to send `title`.

## 2. Collections — file upload field names must match model columns

- `AdminCollectionController::store/update` only handles upload fields named `image` / `logo`,
  but the models use `logo_path` (Partner, Client) and `image` / `image_path` (WorkSample).
- **Exact field names the frontend sends files under (must be read verbatim):**
  - `logo_path` → Partner / Client logo (`hasFile('logo_path')`), stored to `logo_path`.
  - `image` → WorkSample image and Hero / AboutHero section image, stored to `image`.
  - `cover_image` → Project cover (already handled by `AdminProjectController`).
- **Round-trip note:** when an item already has a logo/image and the admin does **not** upload a
  new file, the frontend sends back the existing absolute URL string under the same field name
  (e.g. `logo_path: "https://…/storage/…"`). The backend must **not** re-upload/store that as a
  file — only act when `hasFile()` is true; otherwise keep the existing column value.
- **Action:** map the uploaded file to the correct column. Same for sections (`AdminSectionController`).

## 3. Persist contact replies and expose `reply` / `replied_at`

- **Current:** `ContactService::replyToRequest` only emails and sets `status = read`; the
  `contact_requests` table has no reply columns and `ContactRequestResource` omits `reply` / `replied_at`.
- **Frontend expects** (see `ContactMessages.vue`): each contact request can carry
  `reply` and `replied_at`, and the reply endpoint should return them.
- **Action:**
  - Add `reply` (text, nullable) + `replied_at` (timestamp, nullable) columns to `contact_requests`.
  - Add `reply` and `replied_at` to `ContactRequest::$fillable`.
  - Persist the reply text + timestamp in `replyToRequest`.
  - Include `reply` and `replied_at` in `ContactRequestResource`.

## 4. Public endpoint to verify visitor credentials (gates work samples)

- **Current:** visitor credentials can be created in the admin, but the public work-samples
  `AccessModal` validates hardcoded `demo`/`demo` client-side. Nothing validates against `visitor_credentials`.
- **Needed endpoint:** `POST /api/v1/visitor-access/verify`
  - **Body:** `{ "username": "…", "password": "…" }`
  - **Behaviour:** return 200 when an active, non-expired credential matches; 422/401 otherwise.
  - **Response:** `{ "success": true, "data": { "valid": true, "expires_at": "…" } }`
- **Note:** do not return `password` in public responses; only the admin list may.

## 5. Add `days_until_expiry` / `is_active` / `expires_at` on the visitor-credential admin list

- **Current:** `AdminVisitorCredentialController::index` returns raw models — the frontend
  normalizes `is_active`, `expires_at`, `created_at` already. This is fine as-is; keep the
  snake_case field names (`is_active`, `expires_at`, `created_at`). No change strictly required.

## 6. Fix `PartnerResource::logo_url` null-safety

- **Current:** `str_starts_with($this->logo_path, 'http')` throws/fails when `logo_path` is null.
- **Action:** guard with `$this->logo_path ? … : null`.

## 7. Postman collection gaps (documentation only)

- Add `PUT`/`DELETE` for `services`, `sectors`, `partners`, `clients`.
- Add CRUD examples for `timeline`, `pillars`, `work-flow`, `work-samples`.
- Add section updates for `about`, `about-hero`, `core-values`, `domains`.
- Add `GET /admin/projects`? *(public read `/projects` already covers listing — optional.)*

## 8. Admin store/update responses should use the same resources as the public reads

- **Current:** `AdminCollectionController::store/update` returns the raw Eloquent model
  (snake_case columns). The public endpoints return camelCase resource objects.
- **Why it matters:** after create/update the dashboard re-fetches from the public endpoints, so
  today it works — but the raw response breaks any code that relies on the response shape and
  leaks columns like `password` if ever returned from an admin collection (see note under item 4).
- **Action:** wrap admin store/update responses in the same `*Resource` used by the public GET
  endpoints (e.g. `ServiceResource`, `SectorResource`, `PartnerResource`, `ClientResource`,
  `WorkSampleResource`). At minimum, never return a raw model containing `password`/`password_hash`.

---

## Endpoint summary of what the frontend calls

| Method | Path | Status |
|---|---|---|
| POST | `/admin/login` | ✅ exists |
| POST | `/admin/logout` | ✅ exists |
| GET | `/admin/me` | ✅ exists |
| GET | `/hero`, `/vision`, `/about-hero`, `/it-ot`, `/core-values`, `/domains` | ✅ exists (public) |
| PUT | `/admin/{resource}/{id}` (hero\|vision\|about\|about-hero\|it-ot\|core-values\|domains) | ✅ exists |
| GET/POST/PUT/DELETE | `/admin/{resource}[/{id}]` (services, sectors, partners, clients, timeline, pillars, work-flow, work-samples) | ✅ exists |
| GET | `/admin/contact-requests` | ✅ exists |
| PATCH | `/admin/contact-requests/{id}/status` | ✅ exists |
| POST | `/admin/contact-requests/{id}/reply` | ⚠️ exists but needs persistence (item 3) |
| GET/POST/PUT/DELETE | `/admin/visitor-credentials[/{id}]` | ✅ exists |
| POST | `/visitor-access/verify` | ❌ **needed** (item 4) |