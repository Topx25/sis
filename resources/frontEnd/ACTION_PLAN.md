# SiS Web Application — Implementation Action Plan

This document outlines the root causes, architecture analysis, and exact step-by-step tasks required to resolve image upload errors, implement missing `/industries` endpoints, transition `/solution-pages` into a modal pop-up architecture, and verify the stability of the `/projects` endpoints across backend and frontend.

---

## Table of Contents
1. [Task 1: Solve Server Errors on Image Uploads](#task-1-solve-server-errors-on-image-uploads)
2. [Task 2: Add Update & Delete Endpoints for `/industries`](#task-2-add-update--delete-endpoints-for-industries)
3. [Task 3: Display & Edit Solution Pages in a Pop-up Modal](#task-3-display--edit-solution-pages-in-a-pop-up-modal)
4. [Task 4: Comprehensive Audit & Stabilization of Projects Endpoints](#task-4-comprehensive-audit--stabilization-of-projects-endpoints)
5. [Execution Checklist & Verification Matrix](#execution-checklist--verification-matrix)

---

## Task 1: Solve Server Errors on Image Uploads

### 1.1 Root Cause Diagnosis
When uploading images during updates (e.g. projects, solution pages, collections), 405 Method Not Allowed or 500 Internal Server Errors occur due to several compounding factors:

1. **PHP Core Limitation on HTTP `PUT` with `multipart/form-data`**:
   - PHP's built-in Request/SAPI parser only populates `$_FILES` and `$_POST` for HTTP `POST` requests.
   - When the frontend sends a native HTTP `PUT` request with `multipart/form-data`, PHP leaves `$_FILES` completely empty and does not parse multipart body boundaries into `$request->all()` or `$request->file()`.
   - As a result, validation fails (`The cover image must be an image`), files are not recognized, or PHP aborts with an unhandled exception.

2. **Missing `POST` Routes for Method Spoofing in `routes/api.php`**:
   - Standard Laravel handles this via method spoofing (sending a `POST` request with `_method: "PUT"` in the `FormData`).
   - While `routes/api.php` has `Route::post('/{resource}/{id}', ...)` for generic collections and `Route::post('/section/{section}/{id}', ...)`, it was **missing** for:
     - `/admin/projects/{project}` (only `Route::put` was defined).
     - `/admin/solution-pages/{slug}` (only `Route::put` was defined).
   - If the client sends `POST` with `_method: "PUT"`, Laravel router evaluates the native HTTP method first and returns `405 Method Not Allowed` unless a `POST` route matches.

3. **Frontend `updateProject` Using Native `PUT`**:
   - In [adminApi.js](file:///d:/iss/sis/resources/frontEnd/src/SIS-Dashboard/api/adminApi.js), `updateProject` was sending `{ method: "PUT", body: toPayload(data) }` without appending `_method: "PUT"` or switching the fetch method to `POST`.

4. **Validation Failures on Existing Image Strings in `UpdateProjectRequest`**:
   - In [UpdateProjectRequest.php](file:///d:/iss/sis/app/Http/Requests/UpdateProjectRequest.php), `'cover_image' => ['nullable', 'image', 'max:5120']`.
   - When a user updates a project without changing the existing image, the frontend sends the existing URL string (`"https://.../storage/projects/xyz.png"`).
   - The `'image'` validation rule fails because a string URL is not an instance of `UploadedFile`, triggering a `422 Unprocessable Content` response.

5. **Partial `FormData` Serialization in Managers**:
   - In [SolutionPagesManager.vue](file:///d:/iss/sis/resources/frontEnd/src/SIS-Dashboard/pages/sections/SolutionPagesManager.vue), if `hasNewImage` was true, the code created a `new FormData()` and appended **only** `hero_image`, discarding all textual content (`title`, `subtitle`, `badges`, etc.).

---

### 1.2 Required Fixes

#### A. Backend Route Updates (`routes/api.php`)
Register `POST` route counterparts for resources accepting file uploads so that Laravel's method spoofing works seamlessly:

```php
// routes/api.php (Admin prefix group)

// Projects
Route::put('/projects/{project}', [AdminProjectController::class, 'update']);
Route::post('/projects/{project}', [AdminProjectController::class, 'update']); // Add for FormData uploads

// Solution Pages
Route::put('/solution-pages/{slug}', [AdminSolutionPageController::class, 'update']);
Route::post('/solution-pages/{slug}', [AdminSolutionPageController::class, 'update']); // Add for FormData uploads
```

#### B. Frontend API Client (`adminApi.js`)
Update `updateProject` in [adminApi.js](file:///d:/iss/sis/resources/frontEnd/src/SIS-Dashboard/api/adminApi.js) to append `_method: "PUT"` and send via `POST` when payload contains `FormData`:

```javascript
updateProject: (id, data) => {
  const payload = toPayload(data);
  if (payload instanceof FormData) {
    payload.append("_method", "PUT");
    return request(`/admin/projects/${id}`, {
      method: "POST",
      body: payload,
    });
  }
  return request(`/admin/projects/${id}`, {
    method: "PUT",
    body: payload,
  });
},
```

#### C. Backend Validation Handling (`UpdateProjectRequest.php`)
Allow string URLs or strip string images before validation if they represent an unchanged existing URL:

```php
// app/Http/Requests/UpdateProjectRequest.php
protected function prepareForValidation(): void
{
    // If cover_image is sent as an existing URL string, remove it from validation
    // so it doesn't fail the 'image' file rule
    if ($this->has('cover_image') && is_string($this->input('cover_image'))) {
        $this->request->remove('cover_image');
    }
}

public function rules(): array
{
    return [
        'sector_id'    => ['sometimes', 'exists:sectors,id'],
        'title'        => ['sometimes', 'string', 'max:255'],
        'client_name'  => ['sometimes', 'string', 'max:255'],
        'location'     => ['nullable', 'string', 'max:255'],
        'summary'      => ['sometimes', 'string', 'max:1000'],
        'description'  => ['nullable', 'string'],
        'cover_image'  => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
        'tags'         => ['nullable', 'array'],
        'service_ids'  => ['nullable', 'array'],
        'service_ids.*'=> ['exists:services,id'],
        'images'       => ['nullable', 'array'],
        'images.*'     => ['nullable'],
        // JSON attributes
        'metrics'      => ['nullable', 'array'],
        'scope'        => ['nullable', 'array'],
        'technologies' => ['nullable', 'array'],
        'results'      => ['nullable', 'array'],
    ];
}
```

---

## Task 2: Add Update & Delete Endpoints for `/industries`

### 2.1 Current State & Discrepancies
1. **Public API Contract**:
   - `GET /api/v1/industries` is handled by `IndustriesPageController::index`, returning sectors mapped to industry cards.
   - The database model underlying industries is `Sector` (`App\Models\Sector`).
2. **Dashboard UI**:
   - [IndustriesManager.vue](file:///d:/iss/sis/resources/frontEnd/src/SIS-Dashboard/pages/sections/IndustriesManager.vue) is the management page labeled **"Industries | SiS Admin"**.
   - It fetches items via `adminAPI.getIndustries()` and triggers `adminAPI.updateIndustry(editingId.value, payload)`.
3. **Backend Route Gap**:
   - In [routes/api.php](file:///d:/iss/sis/routes/api.php), admin routes only contain `/admin/industries-page` (page header metadata) and generic collection `/{resource}`.
   - The collection whitelist regex was:
     `where('resource', 'services|sectors|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis')`
   - Notice that `industries` was **not included** in the whitelist regex, and `AdminCollectionController` did not map `industries` in `$collectionMap`.
   - Therefore, calling `/admin/industries/{id}` results in a **404 Route Not Found**.
   - No `deleteIndustry` method exists in `adminApi.js`.

---

### 2.2 Required Implementation

#### A. Backend Controller Mapping (`AdminCollectionController.php`)
Map `industries` directly to `Sector` in [AdminCollectionController.php](file:///d:/iss/sis/app/Http/Controllers/Api/v1/Admin/AdminCollectionController.php):

```php
// In $collectionMap:
'industries' => Sector::class,
'sectors'    => Sector::class,

// In $resourceMap:
'industries' => SectorResource::class,
'sectors'    => SectorResource::class,
```

#### B. Backend Route Whitelist (`routes/api.php`)
Update the resource regex constraint in [routes/api.php](file:///d:/iss/sis/routes/api.php) to include `industries`:

```php
->where('resource', 'services|sectors|industries|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis');
```

This automatically provides:
- `GET /admin/industries` — List all industries
- `GET /admin/industries/{id}` — Show single industry
- `POST /admin/industries` — Create industry
- `PUT /admin/industries/{id}` — Update industry (JSON)
- `POST /admin/industries/{id}` — Update industry (FormData / Image uploads)
- `DELETE /admin/industries/{id}` — Delete industry

#### C. Frontend API Integration (`adminApi.js`)
Ensure `updateIndustry` and `deleteIndustry` are properly exposed in [adminApi.js](file:///d:/iss/sis/resources/frontEnd/src/SIS-Dashboard/api/adminApi.js):

```javascript
// adminApi.js
updateIndustry: (id, data) => {
  const payload = toPayload(data);
  if (payload instanceof FormData) {
    payload.append("_method", "PUT");
    return request(`/admin/industries/${id}`, {
      method: "POST",
      body: payload,
    });
  }
  return request(`/admin/industries/${id}`, {
    method: "PUT",
    body: payload,
  });
},

deleteIndustry: (id) =>
  request(`/admin/industries/${id}`, {
    method: "DELETE",
  }),
```

#### D. Frontend Manager Updates (`IndustriesManager.vue`)
1. Add a Delete Action button with trash icon next to "Edit" on each industry card/row.
2. Integrate `ConfirmDialog` to confirm industry deletion before triggering `adminAPI.deleteIndustry(id)`.
3. Add a "Create Industry" button and modal trigger for complete CRUD parity.

---

## Task 3: Display & Edit Solution Pages in a Pop-up Modal

### 3.1 Current UX Issues in `SolutionPagesManager.vue`
Currently, [SolutionPagesManager.vue](file:///d:/iss/sis/resources/frontEnd/src/SIS-Dashboard/pages/sections/SolutionPagesManager.vue) renders:
- A flat dropdown `<select v-model="selectedSlug">` at the top.
- A 600-line inline form directly inside the page flow.
- This breaks consistency with other admin managers (`PartnersManager.vue`, `ServicesManager.vue`, `ClientsManager.vue`), which present an overview grid/table with an interactive edit modal pop-up.

---

### 3.2 Target Pop-up Architecture

```
+-------------------------------------------------------------------------------+
|  AdminPageHeader: "Services & Solutions"                                      |
|  [Search Solution Pages...]                           [+ Add Solution Page]   |
+-------------------------------------------------------------------------------+
|  Grid / Table Overview of 12 Solution Pages:                                  |
|  +--------------------+  +--------------------+  +--------------------+       |
|  | Digital Solutions  |  | Process Automation |  | Industrial AI      |       |
|  | /digital-solutions |  | /process-automation|  | /industrial-ai     |       |
|  | 4 Badges | 6 Serv. |  | 3 Badges | 5 Serv. |  | 4 Badges | 4 Serv. |       |
|  | [Edit Page] [Del]  |  | [Edit Page] [Del]  |  | [Edit Page] [Del]  |       |
|  +--------------------+  +--------------------+  +--------------------+       |
+-------------------------------------------------------------------------------+

                                       |
                           (Clicking [Edit Page] opens)
                                       v
+-------------------------------------------------------------------------------+
|  MODAL POP-UP: Edit Solution Page ("Digital Solutions")                       |
|  [Hero & Header]  [Value Badges]  [Services]  [Approach]  [Outcomes]  [FAQ]   |
|  ---------------------------------------------------------------------------  |
|  • Title, Subtitle, Slug                                                      |
|  • Hero Image Upload with Live Preview                                        |
|  • Dynamic Badges & Service Lists (with IconPicker)                           |
|  • Structured JSON Sections                                                   |
|                                                                               |
|  [Cancel]                                             [Save Changes (Button)] |
+-------------------------------------------------------------------------------+
```

### 3.3 Implementation Details
1. **Overview State**:
   - Load the list of solution pages via `adminAPI.getSolutionPages()` (`GET /admin/solution-pages`).
   - Store in `const solutionPagesList = ref([])`.
   - Display cards showing: Icon/Hero thumbnail, Name, Slug, Badge count, Services count, and status badge.
2. **Modal Pop-up State**:
   - `const modalOpen = ref(false)`.
   - `const editingSlug = ref(null)`.
   - `const form = ref(emptySolutionPageForm())`.
3. **Open Edit Function**:
   - `openEdit(page)` fetches the full record via `adminAPI.getSolutionPage(page.slug)` and loads it into `form.value`.
   - Sets `modalOpen.value = true`.
4. **Save Handler**:
   - Formats payload using `FormData` if a new `hero_image` file is present.
   - Serializes nested arrays (`badges`, `services`, `approach_steps`, etc.) using `JSON.stringify()`.
   - Calls `adminAPI.updateSolutionPage(editingSlug.value, payload)`.
   - Closes modal on success and updates local page state.

---

## Task 4: Comprehensive Audit & Stabilization of Projects Endpoints

### 4.1 Endpoints Inventory
| Endpoint | Method | Controller & Action | Status / Required Fix |
| :--- | :--- | :--- | :--- |
| `/api/v1/projects` | `GET` | `Public\ProjectController@index` | OK (Public pagination & filter) |
| `/api/v1/projects/{slug}` | `GET` | `Public\ProjectController@show` | OK (Includes sector, services, images) |
| `/api/v1/admin/projects` | `GET` | `Admin\AdminProjectController@index` | **Fix**: Eager-load `sector`, `services`, `images` |
| `/api/v1/admin/projects/{project}` | `GET` | `Admin\AdminProjectController@show` | **Fix**: Load `sector`, `services`, `images` |
| `/api/v1/admin/projects` | `POST` | `Admin\AdminProjectController@store` | OK |
| `/api/v1/admin/projects/{project}` | `PUT` | `Admin\AdminProjectController@update` | OK (Standard JSON updates) |
| `/api/v1/admin/projects/{project}` | `POST` | `Admin\AdminProjectController@update` | **Fix**: Add route for FormData / image uploads |
| `/api/v1/admin/projects/{project}` | `DELETE` | `Admin\AdminProjectController@destroy` | OK (Cleans up images & storage files) |

---

### 4.2 Detailed Fixes for Projects Endpoints

#### 1. Eager Loading in `AdminProjectController.php`
Currently, `AdminProjectController::index()` returns `Project::all()` without eager loading relationships:
```php
// BEFORE:
$projects = Project::all();

// FIX:
public function index(): JsonResponse
{
    $projects = Project::with(['sector', 'services', 'images'])->latest()->get();
    return $this->successResponse(ProjectResource::collection($projects), 'Projects retrieved successfully.');
}

public function show(Project $project): JsonResponse
{
    return $this->successResponse(
        new ProjectResource($project->load(['sector', 'services', 'images'])),
        'Project retrieved successfully.'
    );
}
```

#### 2. Resolving the `/admin/use-cases` vs `/admin/projects` Discrepancy
In [UseCasesManager.vue](file:///d:/iss/sis/resources/frontEnd/src/SIS-Dashboard/pages/sections/UseCasesManager.vue), the form calls:
- `adminAPI.createUseCase(payload)` -> sends to `/admin/use-cases`
- `adminAPI.updateUseCase(id, payload)` -> sends to `/admin/use-cases/${id}`
- `adminAPI.deleteUseCase(id)` -> sends to `/admin/use-cases/${id}`

However, in the backend:
- The database model is `Project`.
- The controller is `AdminProjectController`.
- The registered routes are `/admin/projects`.

**Fix in `adminApi.js`**:
Point `createUseCase`, `updateUseCase`, and `deleteUseCase` to `/admin/projects`:
```javascript
createUseCase: (data) => adminApi.createProject(data),
updateUseCase: (id, data) => adminApi.updateProject(id, data),
deleteUseCase: (id) => adminApi.deleteProject(id),
```
This unifies the API contracts and prevents 404 Route Not Found errors.

#### 3. Handling Gallery Images Synchronization
In `ProjectService::updateProject`:
- Existing images are filtered from `keptUrls`.
- Deleted images are purged from disk via `Storage::disk('public')->delete(...)`.
- New gallery images uploaded as `UploadedFile` are stored under `projects/gallery`.
- Ensure frontend passes newly uploaded files under `images[]` and existing URLs under the same array so the service knows which images to retain.

---

## Execution Checklist & Verification Matrix

### Phase 1: Backend Fixes
- [ ] Add `Route::post('/projects/{project}', ...)` in `routes/api.php`.
- [ ] Add `Route::post('/solution-pages/{slug}', ...)` in `routes/api.php`.
- [ ] Add `'industries' => Sector::class` and `'industries' => SectorResource::class` in `AdminCollectionController.php`.
- [ ] Add `industries` to route whitelist regex in `routes/api.php`.
- [ ] Update `UpdateProjectRequest.php` to handle existing string URLs in `cover_image`.
- [ ] Eager load `['sector', 'services', 'images']` in `AdminProjectController@index` and `show`.

### Phase 2: Frontend API Fixes
- [ ] In `adminApi.js`, ensure `updateProject` uses `POST` with `_method: "PUT"` when body is `FormData`.
- [ ] In `adminApi.js`, add `deleteIndustry(id)`.
- [ ] In `adminApi.js`, update `updateIndustry(id, data)` to handle `FormData` and method spoofing.
- [ ] In `adminApi.js`, alias `createUseCase`, `updateUseCase`, and `deleteUseCase` to project endpoints.

### Phase 3: Frontend UI Components
- [ ] Refactor `SolutionPagesManager.vue`:
  - [ ] Add list grid displaying all 12 solution pages.
  - [ ] Add interactive Pop-up Modal dialog (`modalOpen`) for editing.
  - [ ] Support image uploads with preview and complete JSON form serialization.
- [ ] Update `IndustriesManager.vue`:
  - [ ] Add Delete button with `ConfirmDialog` calling `adminAPI.deleteIndustry(id)`.
  - [ ] Support creating new industries via modal dialog.
- [ ] Verify `UseCasesManager.vue`:
  - [ ] Test project creation with cover image and gallery images.
  - [ ] Test project update with replaced cover image and retained gallery.
  - [ ] Test project deletion.

### Phase 4: Verification & Testing
- [ ] Test image upload with JPG, PNG, and WebP files up to 5MB.
- [ ] Test `PUT /admin/industries/{id}` and `DELETE /admin/industries/{id}` via Postman / Browser.
- [ ] Verify modal pop-up open, edit, save, and close workflow on `/dashboard/solution-pages`.
- [ ] Verify `GET /api/v1/projects` returns complete sector, service, and image relationships without missing values.
