# SiS Full-Stack Refactoring & Dead Code Elimination Plan

## 1. Objective & Scope

The objective of this refactoring plan is to safely eliminate verified dead code, orphaned components, obsolete data structures, and unreferenced assets across the SiS frontend and backend repositories without breaking:
- Any active public user workflows or routing (`/`, `/about`, `/services`, `/projects`, `/partner-ecosystem`, `/contact`, `/use-cases`, `/solution-pages/:slug`).
- Any admin dashboard capabilities, authentication, or CMS management endpoints.
- Any backend database integrity, migrations, historical schemas, or production data.
- Any offline development capabilities (such as dashboard mock fallback tooling).

---

## 2. Safety Rules & Strict Exclusions

1. **Database Immutability:** No database migrations, schemas, table definitions (e.g. `use_cases`, `pillars`), or production data may be dropped, renamed, or modified.
2. **External Compatibility:** No public backend API routes (e.g., `/v1/digital`, `/v1/ai`, `/v1/pillars`) may be removed without explicit business/external consumer approval.
3. **No Destructive Git Commands:** No `git reset --hard`, `git clean -fd`, or force reverts. All changes are small, incremental, and tracked.
4. **Preserve User Work:** Pre-existing uncommitted work or branch state must never be overwritten.
5. **Pre-Validation Baseline Check:** Every cleanup step must be validated against the established baseline before moving to the next step.

---

## 3. Pre-Refactoring Baseline Record (Established 2026-10-20)

- **Git Status:** Working tree clean on branch `main` (`nothing to commit, working tree clean`).
- **Frontend Build (`npm run build` in `resources/frontEnd`):**
  - Result: **SUCCESS (Exit Code 0)**.
  - Duration: 18.11s.
  - Output Assets: 49 chunks generated into `public/build/assets/`.
- **Backend Tests (`php artisan test` in project root):**
  - Result: 64 tests total, **63 PASSED**, **1 PRE-EXISTING FAILURE**.
  - Pre-existing failure: `Tests\Feature\ClientTest::test_can_fetch_clients_list` (Line 12: `Failed asserting that an array has the key 'id'`).
  - All other 63 tests in 17 feature suites pass cleanly.

---

## 4. Phased Execution Roadmap

### Legend:
- `[TODO]` Pending review / awaiting turn
- `[IN_PROGRESS]` Currently undergoing modification and validation
- `[DONE]` Completed and validated with zero regressions
- `[BLOCKED]` Paused pending investigation or external confirmation

---

### Phase A — Confirmed Frontend Dead Code (Isolated & Zero-Risk)

- [DONE] **Step A1:** Remove dead import statements in [`resources/frontEnd/src/router/index.js`](resources/frontEnd/src/router/index.js#L17-L40).
  - *Status:* Completed & validated.
  - *Details:* Removed 12 unrouted lazy imports. Vite transformed modules decreased from 190 to 177.

- [DONE] **Step A2:** Delete 12 obsolete, unrouted static solution page components.
  - *Status:* Completed & validated.
  - *Details:* Deleted 12 unreferenced `.vue` files (241.6 KB, 5,803 lines) from `resources/frontEnd/src/pages/services&solutions/`. Dynamic routing via `BasicServicesPage.vue` verified.

- [DONE] **Step A3:** Delete orphaned component [`ServiceDetailsModal.vue`](resources/frontEnd/src/components/common/ServiceDetailsModal.vue).
  - *Status:* Completed & validated.
  - *Details:* Deleted component (3.7 KB, 66 lines). Inline modal in `BasicServicesPage.vue` verified.

- [DONE] **Step A4:** Delete orphaned component [`AboutSection.vue`](resources/frontEnd/src/components/about/AboutSection.vue) and [`SectionWrapper.vue`](resources/frontEnd/src/components/common/SectionWrapper.vue).
  - *Status:* Completed & validated.
  - *Details:* Deleted unreferenced components (4.0 KB, 131 lines).

- [DONE] **Step A5:** Delete orphaned component [`AnimatedCounter.vue`](resources/frontEnd/src/components/common/AnimatedCounter.vue).
  - *Status:* Completed & validated.
  - *Details:* Deleted component (1.2 KB, 48 lines).

- [DONE] **Step A6:** Delete abandoned store [`stores/pillers.js`](resources/frontEnd/src/stores/pillers.js) and re-export [`stores/index.js`](resources/frontEnd/src/stores/index.js).
  - *Status:* Completed & validated.
  - *Details:* Deleted broken syntax store and its single re-export (1.8 KB).

- [DONE] **Step A7:** Delete unreferenced static data [`data/services.js`](resources/frontEnd/src/data/services.js).
  - *Status:* Completed & validated.
  - *Details:* Deleted dead static mock data file and removed empty `src/data` directory (54.9 KB, 1,327 lines).

- [DONE] **Step A8:** Delete raw scrape document [`dummydata/sis_holding_extracted_content.md`](resources/frontEnd/src/dummydata/sis_holding_extracted_content.md).
  - *Status:* Completed & validated.
  - *Details:* Deleted scrape markdown file (29.5 KB, 610 lines).

- [DONE] **Step A9:** Delete unreferenced video [`src/videos/increased-numbers.mp4`](resources/frontEnd/src/videos/increased-numbers.mp4).
  - *Status:* Completed & validated.
  - *Details:* Deleted unreferenced video file and removed empty `src/videos` directory (3.91 MB).

---

### Phase B — Frontend Optimization & Preserved Tooling

- [DONE] **Step B1:** Deactivate background Swiper execution in [`HeroSection.vue`](resources/frontEnd/src/components/Home/HeroSection.vue#L129-L205).
  - *Status:* Completed & validated.
  - *Details:* Added `v-if="false"` to the hidden carousel container. Vue skips compilation and execution of the duplicate Swiper carousel, reducing `HomePage.js` chunk size from 149.94 kB to 138.26 kB (saved 11.68 kB) while keeping the template markup safely preserved.

- [DONE] **Step B2:** Move offline presentation assets out of source tree.
  - *Status:* Completed & validated.
  - *Details:* Moved `page_1.png`–`page_5.png` and `SIS Presentation.pdf` (11.4 MB) from `resources/frontEnd/` to `docs/design-assets/` to preserve historical design context while removing clutter from the frontend source tree. Build verified.

- [DONE] **Step B3:** Preserve Dashboard Mock API for offline development.
  - *Status:* Completed & validated.
  - *Details:* Preserved `mockApi.js`, `db.js`, `seed.js` intact so developers can toggle `USE_MOCK = true` for offline frontend testing.

---

### Phase C — Backend Cleanup (Safe Candidates)

- [DONE] **Step C1:** Delete orphaned stub seeder [`database/seeders/SolutionPageSeeder.php`](database/seeders/SolutionPageSeeder.php).
  - *Status:* Completed & validated.
  - *Details:* Deleted unused 4KB stub seeder superseded by `SolutionPagesSeeder.php` (55KB). Tested with `php artisan test` (63/64 passing, baseline verified).

- [DONE] **Step 1 (C2.1):** Delete empty model stub `app/Models/UseCase.php`.
  - *Status:* DONE.
  - *Details:* Deleted `app/Models/UseCase.php`. It had zero imports, zero relations, zero model/controller/route references, and was completely decoupled from `UseCaseResource.php`.
  - *Validation:*
    - `php artisan test`: PASS (63 passed, 1 pre-existing failure in `ClientTest`, 0 new failures).
    - Repository reference search: PASS (zero active references to `App\Models\UseCase` or `UseCase::class`).
    - No new failures or regressions introduced.

- [DONE] **Step 2 (C2.2):** Clean up `app/Http/Resources/UseCaseResource.php` and `AdminCollectionController.php` (Line 64).
  - *Status:* DONE.
  - *Investigation Evidence:*
    - 14-point audit confirmed `UseCaseResource` was never reachable via any active route.
    - `AdminCollectionController::$collectionMap` does not contain `use-cases`.
    - `{resource}` route regex excludes `use-cases`.
    - Route `/api/v1/admin/use-cases` is handled exclusively by `AdminProjectController` using `ProjectResource`.
    - 0 tests, 0 models, and 0 Postman requests reference `UseCaseResource`.
  - *Decision:* Outcome A (Proven Safe).
  - *Exact Changes:*
    - Removed stale line 64 (`'use-cases' => \App\Http\Resources\UseCaseResource::class,`) from `app/Http/Controllers/Api/v1/Admin/AdminCollectionController.php`.
    - Deleted orphaned resource `app/Http/Resources/UseCaseResource.php`.
  - *Validation:*
    - `php artisan test`: PASS (63 passed, 1 pre-existing failure in `ClientTest`, 0 new failures).
    - `php artisan route:list --path=v1/admin`: PASS (all 81 admin routes compiled with 0 errors).
    - Repository search for `UseCaseResource` and `App\Http\Resources\UseCaseResource`: 0 active code references.
  - *Risk Assessment:* Zero risk. No new failures introduced.

---

### Phase D — Items Requiring Explicit Product Approval (Not Executed Without Sign-off)

- [PENDING APPROVAL] **Step 3 (D2): Legacy Solution Aliases:** (`/v1/digital`, `/v1/ai`, `/v1/process`, `/v1/industrial`, `/v1/general-services`).
  - *Objective:* Evaluate whether legacy solution aliases in `routes/api.php` and `SolutionPageController.php` can safely be retired.
  - *Investigation & Supporting Evidence:*
    - Traced route declarations in `routes/api.php` lines 81–85 and implementations in `SolutionPageController.php` lines 76–99.
    - Verified direct external references in `SiS_Translation_Postman_Collection.json` (Arabic & English test suites rely on `{{base_url}}/digital`, `{{base_url}}/ai`, etc.).
    - Verified internal mechanics: lightweight 3-line methods proxying `$this->show('<slug>')`, consuming zero database footprint and sharing the same cache tags.
    - Verified frontend usage: The active Vue frontend strictly uses `/v1/solution-pages/{slug}`.
  - *Decision:* **KEPT FOR COMPATIBILITY (Status: PENDING APPROVAL)**.
    - Preserved 100% intact. Hard deletion introduces severe external breaking change risk (HTTP 404 for Postman collections, bookmarks, and external crawlers) for minimal code reduction.
    - Alternative future option: 301 Permanent Redirects if business formally approves deprecation.
  - *Files Changed:* None (preservation decision).
  - *Validation:*
    - `php artisan test`: PASS (63 passed, 1 pre-existing baseline failure in `ClientTest`, 0 new failures).
    - `php artisan route:list --path=v1/digital`: PASS (route resolves to `SolutionPageController@digital`).
    - `npm run build`: PASS (177 modules transformed, built in 21.42s).
  - *Remaining Risks:* Zero risk while preserved. High risk if deleted without deprecation window.
  - *Next Recommended Step:* **Step 4 (D1 & D4)** — Pillars & Workflow Backend & commented-out UI sections investigation.

- [DONE] **Step 5 (Frontend Inactive Home Sections Cleanup):**
  - *Objective:* Delete confirmed inactive, unrouted Home section components and their exclusive dead helper functions from the frontend source tree.
  - *Files Deleted:*
    - `resources/frontEnd/src/components/Home/HowItWorkSection.vue` (94 lines, 3.98 KB)
    - `resources/frontEnd/src/components/Home/IntegrationSection.vue` (305 lines, 11.03 KB)
    - `resources/frontEnd/src/components/Home/Workflow.vue` (923 lines, 32.63 KB)
    - `resources/frontEnd/src/components/Home/MethodologySection.vue` (204 lines, 10.51 KB)
  - *Files Cleaned / Modified:*
    - `resources/frontEnd/src/pages/Home/HomePage.vue`: Removed obsolete commented-out template tags and imports.
    - `resources/frontEnd/src/api/normalizers.js`: Removed orphaned `normalizePillars()` function (33 lines).
  - *Preserved Elements:*
    - `WorkSamples.vue` & `WorkSamplesGrid.vue`: Preserved intact because `WorkSamplesGrid` is actively rendered on `ServicesPage.vue`.
    - Backend Pillars and Architecture/Workflow controllers, models, and routes: Preserved intact to protect Admin CMS, seeders, and API contracts.
  - *Validation:*
    - `npm run build`: PASS (177 modules transformed in 21.84s, exit code 0; `index.css` bundle decreased by 6.21 kB from 193.58 kB to 187.37 kB).
    - `git diff --check`: PASS (0 warnings).
    - Repository reference searches: 0 active code references to deleted components or symbols.
    - Active UI and page appearance: 100% identical and unchanged.
  - *Risk Assessment:* Zero risk. No new failures introduced.

- [BLOCKED] **D3. Database Migration Cleanup:** Table `use_cases`.
  - *Reason:* Database migrations and production tables are strictly out of scope.

---

## 5. Rollback Instructions

If any step fails validation or causes an unexpected regression:
1. Identify the files modified in that step.
2. Revert only those files via Git:
   ```bash
   git checkout -- <file-path>
   # For deleted files:
   git checkout HEAD -- <file-path>
   ```
3. Re-run `npm run build` or `php artisan test` to confirm return to baseline.
4. Mark the step `BLOCKED` with documented error logs.

---

## 6. Discovered Issues & Decisions Log

- **2026-10-20 (Phase 0):** Baseline test confirmed 63/64 passing tests on backend. `Tests\Feature\ClientTest::test_can_fetch_clients_list` has a pre-existing failure (`Failed asserting that an array has the key 'id'`). This is cataloged so it is not mistaken for a regression.
- **2026-10-20 (Phase 0):** Baseline frontend production build completed in 18.11s with exit code 0.
- **2026-10-20 (Phase A Execution):** Steps A1 through A9 executed and validated.
  - Eliminated 20 dead files (12 static solution pages, 4 unused components, 2 broken/re-export store files, 1 unused 55KB services data file, 1 scrape doc, and 1 unreferenced 3.9MB video).
  - Cleaned up empty directories `src/data` and `src/videos`.
  - Frontend production build (`npm run build`) exited with code 0; module transform count decreased from 190 to 177.
  - Backend tests (`php artisan test`) confirmed 63/64 tests passing, perfectly matching the baseline with zero regressions.
- **2026-10-20 (Step 5 Execution — Inactive Home Sections):**
  - Removed 4 commented-out Home sections: `HowItWorkSection.vue` (93 lines), `IntegrationSection.vue` (304 lines), `Workflow.vue` (922 lines), `MethodologySection.vue` (203 lines).
  - Cleaned up commented imports/tags in `HomePage.vue`.
  - Removed orphaned normalizer `normalizePillars` (31 lines) from `src/api/normalizers.js`.
  - Frontend production build (`npm run build`) exited with code 0.
- **2026-10-20 (Step 6 Execution — Dead Components & Dormant Dummy Data):**
  - Removed 5 verified dead files:
    1. `src/components/global/LazyLoadSection.vue` (37 lines, 0 references anywhere in project).
    2. `src/pages/services/servicesAndSolutionsPage.vue` (0 bytes, unreferenced empty file).
    3. `src/dummydata/architecture.js` (14 lines, obsolete schema, route removed).
    4. `src/dummydata/methodology.js` (8 lines, consuming component removed).
    5. `src/dummydata/roadmap.js` (21 lines, unread state property).
  - Cleaned up dead imports (`architecture`, `methodology`, `roadmap`, `normalizePartners`, `normalizeClients`) and unread assignments/state (`roadmap: null`, `this.architecture`, `this.methodology`, `this.roadmap`) in `src/stores/useSiteStore.js`.
  - Frontend production build (`npm run build`) exited with code 0; module transform count decreased from 177 to 174 modules.
  - Backend tests (`php artisan test`) confirmed 63/64 tests passing (1 pre-existing failure in `ClientTest`), 0 regressions.
- **2026-10-20 (Step 7 Execution — Public Asset Cleanup & Safety Audit):**
  - Audited all files in `resources/frontEnd/public/images/` across frontend code, backend code, SQLite database records, seeders, and production CMS backups.
  - Removed 9 verified dead public asset files totaling ~10.66 MB:
    1. `images/controlling-2.jpg` (6.16 MB raw duplicate; hero uses `controlling-2.webp`).
    2. `images/controlling.png` (2.01 MB unreferenced image).
    3. `images/controlling.webp` (130 KB unreferenced image).
    4. `images/about-hero.png` (407 KB duplicate; `about-hero.webp` is active in seeders/code).
    5. `images/training.PNG` (903 KB unreferenced duplicate; `training.webp` is mapped dynamically).
    6. `images/slider-1-1.jpg` (148 KB obsolete card image).
    7. `images/slider-1-1.webp` (125 KB obsolete card image).
    8. `images/sis-logo.png` (749 KB identical duplicate of root `public/sis-logo.png`).
    9. `images/sis-logo-removebg-preview.png` (25 KB unreferenced preview asset).
  - Explicitly preserved `page111.jfif`, `services-automation.PNG`, seeder/backup solution page hero images (`General-services.PNG`, `digital_solutions.jfif`, `indust_ai.jfif`, etc.), client logos (`ourClints/*.jpg` and `*.webp`), and partner logos (`partners/*`).
  - Frontend build (`npm run build`) passed cleanly in 25.75s (174 modules); backend tests confirmed 63/64 passing with 0 regressions.
- **2026-10-20 (Step 8 Execution — Navbar Promo Image Reference Fix):**
  - Resolved broken reference `${import.meta.env.BASE_URL}images/page111.webp` in `NavBar.vue` (line 665).
  - Validated source asset `public/images/page111.jfif` (848x1259 sRGB JPEG).
  - Generated `public/images/page111.webp` (62.8 KB) using pre-existing `sharp` utility at quality 80, preserving original `page111.jfif` untouched.
  - Zero new dependencies added. Staged `page111.webp`.
  - Frontend build (`npm run build`) passed with exit code 0 in 21.43s.
- **2026-10-20 (Step 9 Execution — Unused Component, Composable, and Dummy Data Removal):**
  - Removed 5 verified dead files:
    1. `src/components/global/ThemeToggle.vue` (60 lines, 2.36 KB; 0 active imports, was commented out in `NavBar.vue`).
    2. `src/composables/useTheme.js` (42 lines, 935 B; only imported by deleted `ThemeToggle.vue`).
    3. `src/dummydata/hero.js` (13 lines, 616 B; 0 readers across project).
    4. `src/dummydata/about.js` (19 lines, 1.57 KB; 0 readers across project).
    5. `src/dummydata/capabilities.js` (16 lines, 732 B; 0 readers across project).
  - Cleaned up dead imports and assignments in `src/stores/useSiteStore.js`.
  - Removed commented-out ThemeToggle template tag and import from `src/components/Layout/NavBar.vue`.
  - Cleaned up unused variables (`factoryImage`, `factoryLoaded`) and dead commented slides array from `src/components/Home/HeroSection.vue`.
  - Removed obsolete route mappings (`capabilities`, `architecture`) from `src/utils/scrollTo.js`.
  - Frontend production build (`npm run build`) transformed modules decreased from 174 to 171; bundle sizes decreased (`index.js` -2.6 kB, `index.css` -550 B).
  - Backend tests (`php artisan test`) confirmed 63/64 tests passing, 0 regressions.

---

## 7. Backend Dependency Safety Audit (Deep Dive Findings)

### Candidate Classification Summary

| Candidate | Classification | Confidence | Risk | Recommended Action |
| :--- | :--- | :--- | :--- | :--- |
| `app/Models/UseCase.php` | **Confirmed Dead Code (Deleted in Step 1)** | 100% | Very Low | **Deleted independently** of `UseCaseResource.php`. Zero runtime or framework references. |
| `app/Http/Resources/UseCaseResource.php` | **Confirmed Dead Code (Deleted in Step 2)** | 100% | Zero | **Deleted concurrently** with removal of stale mapping in `AdminCollectionController.php` (line 64). |
| Legacy Solution Aliases (`/v1/digital`, `/v1/ai`, `/v1/process`, `/v1/industrial`, `/v1/general-services`) | **Potential External API Contract / Legacy Reachable** | 90% (Internal), 30% (External) | High | **Retain as lightweight proxies** or convert to HTTP 301 redirects to `/api/v1/solution-pages/{slug}`. |
| `PillarController.php`, `Pillar.php`, `PillarResource.php`, `/v1/pillars` | **Internally Referenced & Potential External Contract** | 85% | High | **Retain intact.** Wired into `AdminCollectionController`, `DatabaseSeeder`, and Postman API contract. |
| `ArchitectureController.php`, `ArchitectureStage.php`, `ArchitectureStageResource.php`, `/v1/work-flow` | **Internally Referenced & Potential External Contract** | 85% | High | **Retain intact.** Wired into `AdminCollectionController`, `DatabaseSeeder`, and Postman API contract. |

---

### Detailed Candidate Audits

#### 1. `app/Models/UseCase.php`
- **Current Usage Path:** None. Empty Eloquent model class stub created via artisan generator.
- **Direct References:** 0. Never imported (`use App\Models\UseCase;`) or mentioned in `app/`, `config/`, `routes/`, `tests/`, or `database/seeders/`.
- **Indirect References:** 0. `AdminCollectionController::$collectionMap` does not contain `use-cases`. `AdminCollectionController` has no dynamic class string concatenation, convention fallbacks, or reflection mechanisms to resolve `UseCase.php`.
- **Route References:** None. `/api/v1/admin/use-cases` is an explicit route group routed to `AdminProjectController.php`, which operates exclusively on `App\Models\Project`.
- **Controller/Resource/Model Relationships:** Completely disconnected. `UseCaseResource.php` does not reference or import `UseCase.php`.
- **Database Dependency:** An empty migration table `use_cases` exists with only `id` and `timestamps` (`database/migrations/2026_09_10_140813_create_use_cases_table.php`), but no business logic reads from or writes to this table.
- **Test References:** 0. (Tests in `UseCasesCacheTest.php` reference `UseCasesPage`, an unrelated model for page hero/stats).
- **External-Consumer Risk:** None. The model is an unreferenced internal PHP class.
- **Confidence:** 100% dead.
- **Risk:** Very Low.
- **Independent Deletion Analysis:** **YES.** `UseCase.php` can be safely removed completely independently of `UseCaseResource.php`. Nothing in PHP or Laravel relies on its existence.

#### 2. `app/Http/Resources/UseCaseResource.php`
- **Current Usage Path:** None actively called at runtime.
- **Direct References:** Exactly 1 direct compile-time reference in `app/Http/Controllers/Api/v1/Admin/AdminCollectionController.php` (Line 64):
  ```php
  'use-cases' => \App\Http\Resources\UseCaseResource::class,
  ```
- **Indirect References / Dynamic Resolution:**
  - `AdminCollectionController` has a `$collectionMap` defining supported resources (`services`, `sectors`, `partners`, `clients`, `timeline`, `pillars`, `work-flow`, `work-samples`, etc.).
  - `use-cases` is **NOT** present in `$collectionMap`.
  - In `AdminCollectionController::index()`, the first line is:
    ```php
    $modelClass = $this->getModelClass($resource);
    if (!$modelClass) {
        return $this->errorResponse("Unknown collection resource: {$resource}", 404);
    }
    ```
  - Therefore, any request to `AdminCollectionController` with `$resource = 'use-cases'` returns a 404 error before ever accessing `$resourceMap['use-cases']`.
  - Furthermore, `routes/api.php` line 202 strictly constrains `{resource}` using a regex whitelist that excludes `use-cases`.
  - However, in PHP, referencing `\App\Http\Resources\UseCaseResource::class` inside the class property `$resourceMap` requires the class to exist during autoloading.
- **Route References:** None.
- **Test References:** 0.
- **External-Consumer Risk:** None.
- **Confidence:** 100% dead logic, but 100% syntactically coupled to `AdminCollectionController.php`.
- **Risk:** **High** if deleted without modifying `AdminCollectionController.php` (causes fatal `Class "App\Http\Resources\UseCaseResource" not found`). **Zero** risk if removed simultaneously with line 64 of `AdminCollectionController.php`.
- **Recommended Action:** Preserve until backend changes are approved. When ready, remove line 64 in `AdminCollectionController.php` and delete `UseCaseResource.php` in a single coordinated edit.

#### 3. Legacy Solution Aliases (`/v1/digital`, `/v1/ai`, `/v1/process`, `/v1/industrial`, `/v1/general-services`)
- **Current Usage Path:** `GET /api/v1/{alias}` -> `SolutionPageController::{method}()` -> proxies `$this->show('{canonical-slug}')`.
- **Direct References:** `routes/api.php` lines 81–85; `SolutionPageController.php` lines 76–99.
- **Indirect References:** Documented in `SiS_Translation_Postman_Collection.json` (e.g. `{{base_url}}/digital`, `{{base_url}}/ai`).
- **Frontend Usage:** None. The active Vue application queries `/api/v1/solution-pages/{slug}` dynamically via `api.getSolutionPage(slug)`.
- **Test References:** 0 in automated tests (tests only cover `/api/v1/solution-pages/{slug}` and `/api/v1/admin/solution-pages`).
- **External-Consumer Risk:** **High.** These endpoints were historical primary entry points. External systems, partner integrations, mobile apps, marketing links, or cached bookmarks may still target `/api/v1/digital` or `/api/v1/ai`.
- **Confidence:** 90% internal obsolescence; low confidence regarding third-party absence.
- **Risk:** High (immediate 404 breaking change for external clients).
- **Safe Options Evaluated:**
  1. *Hard Deletion:* High risk; not recommended.
  2. *HTTP 301 Permanent Redirect:* Excellent for SEO and HTTP clients following redirects (`Route::redirect('/digital', '/api/v1/solution-pages/digital-solutions', 301)`).
  3. *Retain as Lightweight Proxies (Current State):* 5 tiny 3-line controller methods with zero database footprint and 100% backward compatibility.
- **Recommended Action:** **Retain as-is** or convert to explicit 301 redirects. Do not delete without external API deprecation notice.

#### 4. `PillarController.php`, `Pillar.php`, `PillarResource.php`, `/v1/pillars`
- **Current Usage Path:**
  - Public: `GET /api/v1/pillars` -> `PillarController@index` (returns `PillarResource::collection(Pillar::orderBy('sort_order')->get())`).
  - Admin: `api/v1/admin/{resource}` (where resource = `pillars`) -> `AdminCollectionController` (full CRUD).
- **Direct References:**
  - `routes/api.php` lines 34, 76, 202.
  - `AdminCollectionController.php` lines 9, 17, 41 (`'pillars' => Pillar::class`), 58 (`'pillars' => PillarResource::class`).
  - `DatabaseSeeder.php` lines 11, 337 (seeds 3 default pillars on setup).
  - Postman collections: `SiS_Complete_API_Postman_Collection.json` ("2.15 Get Pillars"), `SiS_Dashboard_Admin_API.json` (full CRUD folder "Pillars").
- **Indirect References:** `HowItWorkSection.vue` had a call to `api.getPillars()`, but that component is currently commented out in `HomePage.vue`.
- **Database Dependency:** Active database table `pillars` (`2026_08_12_090943_create_pillars_table.php`).
- **Test References:** 0 direct feature tests.
- **External-Consumer Risk:** Medium-High. Fully documented and operational in Postman and Admin API collections.
- **Confidence:** Feature is dormant on public frontend, but fully operational on backend and admin dashboard.
- **Risk:** High if deleted (causes missing class errors in `AdminCollectionController` and breaks `DatabaseSeeder`).
- **Recommended Action:** **Keep intact.** Requires explicit business decision to retire the Pillars feature. If approved, deletion must coordinate removing routes, controller, resource, seeder entry, and `AdminCollectionController` mappings simultaneously.

#### 5. `ArchitectureController.php`, `ArchitectureStage.php`, `ArchitectureStageResource.php`, `/v1/work-flow`
- **Current Usage Path:**
  - Public: `GET /api/v1/work-flow` -> `ArchitectureController@index` (returns `ArchitectureStageResource::collection(ArchitectureStage::orderBy('sort_order')->get())`).
  - Admin: `api/v1/admin/{resource}` (where resource = `work-flow`) -> `AdminCollectionController` (full CRUD).
- **Direct References:**
  - `routes/api.php` lines 35, 77, 202.
  - `AdminCollectionController.php` lines 6, 14, 42 (`'work-flow' => ArchitectureStage::class`), 59 (`'work-flow' => ArchitectureStageResource::class`).
  - `DatabaseSeeder.php` lines 12, 385 (seeds 5 architecture stages on setup).
  - Postman collections: `SiS_Complete_API_Postman_Collection.json` ("Get Work Flow").
- **Indirect References:** Documented in project architectural notes (`resources/frontEnd/src/Weekly-Reports/weekly-progres.md`).
- **Database Dependency:** Active database table `architecture_stages` (`2026_08_12_090956_create_architecture_stages_table.php`).
- **Test References:** 0 direct feature tests.
- **External-Consumer Risk:** Medium-High. Published in Postman collections.
- **Confidence:** Feature is dormant on public frontend, but fully operational on backend and admin dashboard.
- **Risk:** High if deleted (breaks `AdminCollectionController` and `DatabaseSeeder`).
- **Recommended Action:** **Keep intact.** Retain until business explicitly confirms permanent decommissioning of the Methodology / Architecture section.

---

## 8. Frontend Cleanup Batch 2 (Step 6: Dead Components, Dormant Dummy Data, and Unused Store Properties)

### Overview
Executed a safe, targeted cleanup of confirmed dead components, unreferenced files, obsolete dummy data files, and unused Pinia store properties/imports in `resources/frontEnd/src`.

### Candidates Removed

| Candidate File / Symbol | Classification | Evidence & Verification | Risk Assessment |
| :--- | :--- | :--- | :--- |
| `src/components/global/LazyLoadSection.vue` | **Confirmed Dead Component** | 0 imports or template references across entire repository. Documentation explicitly confirmed component is unused. | **Zero**. No runtime consumer. |
| `src/pages/services/servicesAndSolutionsPage.vue` | **Abandoned Empty Stub** | 0-byte file left over from early routing development. 0 imports across entire repository. | **Zero**. File was empty and unreferenced. |
| `src/dummydata/architecture.js` | **Orphaned Dummy Data** | Only imported in `useSiteStore.js`. The architecture section was removed from router and UI. Property `this.architecture` was never read by any component. | **Zero**. No runtime reader. |
| `src/dummydata/methodology.js` | **Orphaned Dummy Data** | Only imported in `useSiteStore.js`. Consuming component `MethodologySection.vue` was removed in Step 5. Property `this.methodology` was never read anywhere. | **Zero**. Consuming component decommissioned. |
| `src/dummydata/roadmap.js` | **Orphaned Dummy Data** | Only imported in `useSiteStore.js`. Property `roadmap: null` was stored in state, but never accessed by any component or page. | **Zero**. No active consumer. |
| `src/stores/useSiteStore.js` (Dead Imports & State) | **Unused Code Cleanup** | Removed unused imports `architecture`, `methodology`, `roadmap`, `normalizePartners`, `normalizeClients`. Removed `roadmap: null` state and unread assignments. | **Zero**. Cleaned syntax and dead memory allocation without touching active API logic. |

### Verification & Validation Results
- **Frontend Production Build:** `npm run build` completed in 28.12s with exit code 0.
  - Module transform count decreased from 177 to 174 modules.
  - Zero compiler errors or missing import warnings.
- **Git Diff Hygiene:** `git diff --check` passed cleanly with 0 whitespace or formatting issues.
- **Symbol Exhaustion Check:** Full search across `resources/frontEnd/src` for `LazyLoadSection`, `servicesAndSolutionsPage`, `dummydata/architecture`, `dummydata/methodology`, and `dummydata/roadmap` returned 0 matches.
- **Backend Regression Check:** `php artisan test` passed 63/64 tests with 0 new failures (exactly matching the baseline 1 pre-existing failure in `ClientTest`).

---

## 9. Frontend Cleanup Batch 3 (Step 7: Public Assets Audit & Safe Deletion)

### Overview
Conducted an exhaustive multi-layer audit of `resources/frontEnd/public/images/` across all frontend code, backend code, database seeders, live SQLite database records (`database/database.sqlite`), and production CMS data dumps (`backup_prod_solution_pages_20261011_144620.json`).

### Candidates Removed (9 Files — Total: 10,657,753 Bytes / ~10.66 MB)

| File Removed | Size | Evidence of Zero Use & Replacement | Risk Assessment |
| :--- | :--- | :--- | :--- |
| `public/images/controlling-2.jpg` | 6,163,526 B (6.16 MB) | Raw uncompressed JPEG original. The active hero section and production CMS use `images/controlling-2.webp` (92.8 KB) in `src/api/normalizers.js` (line 389) and in the production CMS backup. **0** references exist for `.jpg`. | **Zero**. Active code uses `.webp`. |
| `public/images/controlling.png` | 2,006,220 B (2.01 MB) | 2 MB uncompressed image. **0** references anywhere in frontend code, backend seeders, database, or backups. | **Zero**. |
| `public/images/controlling.webp` | 130,402 B (130 KB) | Unused webp export (hero uses `controlling-2.webp`, NOT `controlling.webp`). **0** references anywhere. | **Zero**. |
| `public/images/about-hero.png` | 407,235 B (407 KB) | Pre-WebP duplicate. The active code, `AboutHero.vue`, `HomeSectionsSeeder.php`, `DatabaseSeeder.php`, and `create_about_heroes_table` migration all reference `images/about-hero.webp` (180 KB). **0** references exist for `.png`. | **Zero**. Active code uses `.webp`. |
| `public/images/training.PNG` | 902,860 B (903 KB) | Duplicate image. `src/utils/imageUrl.js` (line 76) explicitly and dynamically rewrites any `training-consulting.jpg` requests to `images/training.webp` (227 KB). **0** references exist for `training.PNG`. | **Zero**. Active code uses `training.webp`. |
| `public/images/slider-1-1.jpg` | 147,670 B (148 KB) | Obsolete card image documented in project notes as previously used. **0** active references in any code, template, seeder, or database table. | **Zero**. |
| `public/images/slider-1-1.webp` | 124,990 B (125 KB) | Obsolete card image. **0** active references in any code, template, seeder, or database table. | **Zero**. |
| `public/images/sis-logo.png` | 749,478 B (749 KB) | Byte-for-byte identical duplicate (SHA256: `4D8745829B8297614476561838EB2733C8B80264E9AA2752F9C50EADC92223F2`) of root `public/sis-logo.png`. Every HTML tag, SEO service, and backend model targets `/sis-logo.png`. `images/sis-logo.png` had **0** references. | **Zero**. Root `public/sis-logo.png` is preserved. |
| `public/images/sis-logo-removebg-preview.png` | 25,372 B (25 KB) | Temporary watermarked preview asset from third-party tool. **0** references anywhere in project. | **Zero**. |

### Preserved Assets & Documented Safety Decisions

| Asset Group / File | Evidence Found | Reason for Preservation |
| :--- | :--- | :--- |
| `images/page111.jfif` | `NavBar.vue` line 665 references `${import.meta.env.BASE_URL}images/page111.webp` (which does not exist). | **Preserved**. `page111.jfif` is the original source for the navbar services promotion preview. Deleting it would destroy the asset source. |
| `images/services-automation.PNG` | `backup_prod_solution_pages_20261011_144620.json` references `images/services-automation.PNG` as hero image for Industrial Automation. | **Preserved**. Used by production CMS database records. |
| `images/General-services.PNG`, `digital_solutions.jfif`, `indust_ai.jfif`, `infraSolutions.PNG`, `engineeringServices.PNG`, `cyberSecurity.PNG`, `predictiveMaintain.PNG`, `smartManu.PNG`, `maintainOperations.PNG` | Explicitly referenced in `database/seeders/SolutionPagesSeeder.php`. | **Preserved**. Backend seeders require these exact file paths. |
| `images/General-services.webp`, `digital_solutions.webp`, `indust_ai.webp`, `infraSolutions.webp`, `engineeringServices.webp`, `cyberSecurity.webp`, `predictiveMaintain.webp`, `smartManu.webp`, `maintainOperations.webp` | Referenced in `backup_prod_solution_pages_20261011_144620.json` and production API responses. | **Preserved**. Production CMS data uses WebP solution hero images. |
| `images/ourClints/*.jpg` AND `images/ourClints/*.webp` | `DatabaseSeeder.php` seeds `.jpg` paths; live SQLite database and `ClientSeeder.php` store `.webp` paths. | **Preserved**. Both formats are required across seeder and live database environments. |
| `images/partners/*.png` AND `images/partners/*.webp` | Seeders and `normalizers.js` reference `.png`/`.jpg` (and `.webp` for Aveva/Schneider). Unreferenced `.webp` files preserved for consistency with active PNG assets. | **Preserved**. |

### Verification & Validation Results
- **Frontend Production Build:** `npm run build` completed cleanly in 25.75s with exit code 0 (174 modules).
- **Git Diff Hygiene:** `git diff --check` passed with 0 errors.
- **Reference Exhaustion Search:** Confirmed 0 broken references to the 9 deleted files across all code, templates, database dumps, and seeders.
- **Backend Tests:** `php artisan test` passed 63/64 tests with 0 new regressions.

---

## 10. Navbar Promo Image Reference Resolution (Step 8)

### Problem
`resources/frontEnd/src/components/Layout/NavBar.vue` (line 665) referenced:
```javascript
const servicesPromoImage = `${import.meta.env.BASE_URL}images/page111.webp`;
```
The image is rendered in the Services megamenu desktop promo card (`<img :src="servicesPromoImage" alt="Smart Solutions" ... />`).
However, `images/page111.webp` did not exist in `public/images/`. The only asset present was `images/page111.jfif` (595.8 KB).

### Solution Executed
1. **Source Asset Validation:** Confirmed `public/images/page111.jfif` is a valid 848x1259 sRGB JPEG image.
2. **Zero New Dependencies:** Utilized the pre-existing project dependency `sharp` (already present and used by `resources/frontEnd/scripts/compress-images.cjs`).
3. **Lossless/Web-Standard Conversion:** Converted `public/images/page111.jfif` into `public/images/page111.webp` at quality 80:
   - Output size: 62,766 bytes (~62.8 KB, an 89.5% size reduction).
   - Preserved `public/images/page111.jfif` completely intact without overwriting or deleting it.
4. **Validation:**
   - Referenced asset `public/images/page111.webp` now exists and matches the URL constructed in `NavBar.vue`.
   - `npm run build` completed with exit code 0 in 21.43s.
   - `git diff --check` passed with 0 errors.
   - Staged `resources/frontEnd/public/images/page111.webp` for review.

---

## 11. Frontend Cleanup Batch 4 (Step 9: Dead Component, Composable, and Stale Dummy Data Removal)

### Overview
Executed a safe cleanup of unused components, obsolete composables, dormant dummy data files, and dead code/imports in `resources/frontEnd/src`.

### Candidates Removed (5 Files)

| File Removed | Size / Lines | Verification Evidence | Risk Assessment |
| :--- | :--- | :--- | :--- |
| `src/components/global/ThemeToggle.vue` | 2,365 B (60 lines) | The entire project has transitioned to dark mode. The component was never rendered; its template tag and import were commented out in `NavBar.vue`. **0** active references in any active template or route. | **Zero**. Component was dead code. |
| `src/composables/useTheme.js` | 935 B (42 lines) | Only imported by `ThemeToggle.vue`. Never imported in `App.vue`, `main.js`, or any page. | **Zero**. |
| `src/dummydata/hero.js` | 616 B (13 lines) | Only imported in `useSiteStore.js`. The active hero section queries `api.getHero()` and falls back to `normalizeHero()` (which contains its own inline slides). `this.hero` was never defined in `state()` and never read anywhere. | **Zero**. |
| `src/dummydata/about.js` | 1,567 B (19 lines) | Legacy mock data for the decommissioned `AboutSection.vue`. The current modular About page sections query their respective API endpoints and normalizers. `this.about` was never read anywhere. | **Zero**. |
| `src/dummydata/capabilities.js` | 732 B (16 lines) | Only imported in `useSiteStore.js`. The active capabilities matrix is defined inline in `normalizers.js` and `mockApi.js`. `this.capabilities` was never read anywhere. | **Zero**. |

### Code & Dead Import Cleanups
1. **`src/stores/useSiteStore.js`:**
   - Removed imports for `hero`, `about`, `capabilities`.
   - Removed runtime assignments `this.hero = hero;`, `this.about = about;`, `this.capabilities = capabilities;`.
2. **`src/components/Layout/NavBar.vue`:**
   - Removed commented-out template tag `<!-- <ThemeToggle /> -->`.
   - Removed commented-out import `//import ThemeToggle from "@/components/global/ThemeToggle.vue";`.
3. **`src/components/Home/HeroSection.vue`:**
   - Removed unused `const factoryImage = ...;`.
   - Removed dead commented-out array of slide objects (lines 524–550).
   - Removed dead commented-out `// const hero = ref(null);`.
   - Removed unused `const factoryLoaded = ref(false);`.
4. **`src/utils/scrollTo.js`:**
   - Removed dead route mappings `capabilities: 'capabilities'` and `architecture: 'architecture'` which referenced nonexistent routes.

### Verification & Validation Results
- **Frontend Production Build:** `npm run build` completed cleanly in 25.16s with exit code 0.
  - Module transform count decreased from **174 to 171 modules**.
  - `index.js` bundle size decreased from **267.82 kB to 265.22 kB** (-2.6 kB).
  - `index.css` bundle size decreased from **187.37 kB to 186.82 kB** (-550 B).
- **Git Diff Hygiene:** `git diff --check` passed with 0 whitespace or formatting errors.
- **Reference Exhaustion Search:** Global searches confirmed 0 broken references to `ThemeToggle`, `useTheme`, `dummydata/hero`, `dummydata/about`, or `dummydata/capabilities`.
- **Preserved Work Check:** Confirmed `resources/frontEnd/public/images/page111.webp` remains staged and completely intact.
- **Backend Tests:** `php artisan test` passed 63/64 tests with 0 new regressions.

---

## 12. Frontend Cleanup Batch 5 (Unused Assets, CSS, Utilities, and Obsolete Store Residue)

### Overview
Executed a targeted, verified cleanup across `resources/frontEnd/` focused on obsolete CSS rules/selectors/variables, unused mock data files, orphaned normalizer functions, and dead store state/network requests.

### Candidates Removed / Cleaned

#### 1. Deleted Obsolete Files (2 Files — Total: 2,677 Bytes)
| File Deleted | Size / Lines | Verification Evidence | Risk Assessment |
| :--- | :--- | :--- | :--- |
| `src/dummydata/workFlow.js` | 1,180 B (42 lines) | Only imported by `useSiteStore.js`. The component `Workflow.vue` was decommissioned in Step 5. No page, template, or test consumes `workFlow`. **0** references remain. | **Zero**. |
| `src/dummydata/partners.js` | 1,497 B (28 lines) | Only imported by `useSiteStore.js`. The actual partner components (`partnerEcosystem.vue` and `PartnersSection.vue`) query `api.getPartners()` directly and normalize via `normalizeEcosystem()`. **0** references remain. | **Zero**. |

#### 2. Cleaned Obsolete Normalizers & Constants (`src/api/normalizers.js`)
- **`normalizeWorkFlow`:** Removed 15 lines. Was only imported by `useSiteStore.js`. 0 active callers.
- **`normalizePartners`:** Removed 22 lines. Was uncalled across the entire codebase (`partnerEcosystem.vue` uses `normalizeEcosystem`).
- **`SECTOR_COLORS`:** Removed 7 lines. Completely unused constant.
- **`PARTNER_COLORS`:** Removed 1 line. Was only used internally by `normalizePartners`.

#### 3. Cleaned Dead Store State, Misaligned Endpoints & Leaks (`src/stores/useSiteStore.js`)
- Removed dead imports for `workFlow`, `partners`, and `normalizeWorkFlow`.
- Removed dead state `workFlowLoaded: false`.
- Removed wasted parallel network endpoints in `Promise.allSettled`:
  - `api.getPartners()`: was skipped at index 3 in destructuring.
  - `api.getClients()`: was erroneously captured as `workFlowRes` at index 5 and passed to `normalizeWorkFlow`.
  - `api.getAboutHero()`: was skipped at index 6 in destructuring.
- Aligned destructuring indices cleanly across active endpoints (`projects`, `sectors`, `services`, `workSamples`, `contact`, `footer`, `header`, `timeline`).
- Removed dead assignments `this.workFlow`, `this.partners`, `this.workFlowLoaded`, `this.partnersLoaded`.
- Ensured `this.workSamplesLoaded = true` is set upon successful API resolution.

#### 4. Cleaned Obsolete CSS, Selectors, and Dead Variables (`src/index.css`)
- **Dead Vue Transitions (31 lines):**
  - `.mth-fade-*`: Methodology transition from deleted `MethodologySection.vue`. 0 callers.
  - `.fade-tab-*`: Sector tab transition from obsolete `ProjectsSection-old`. 0 callers.
- **Dead Utilities (10 lines):**
  - `.card-glass` and `.card-glass-border`: 0 usages across all templates and components.
- **Dead CSS Variables (9 lines):**
  - `--svg-text-primary`, `--svg-text-secondary`, `--svg-text-label`, `--svg-text-annotation`, `--svg-node-fill`, `--svg-node-stroke`, `--svg-grid`, `--svg-flow`: created exclusively for the interactive SVG in deleted `Workflow.vue`. 0 usages in active code.
  - `--text-border-40`: 0 usages in any file.
- **Obsolete Commented-out Dead Blocks (74 lines):**
  - Commented dark mode root color variables (`--dark-bg`, `--dark-text`, etc.).
  - Commented `:root.dark { ... }` block (36 lines).
  - Commented `.dark .service-feature-grid` block (18 lines).
  - Commented `:root.dark .skeleton` block (9 lines).
  - Commented section padding overrides (2 lines).

### Preserved Assets Verification
- `public/images/page111.webp` remains staged and completely intact.
- Source asset `public/images/page111.jfif` preserved.
- Both `factory.webp` and `factory-floor.webp` preserved (despite matching SHA256 hashes, distinct references exist in `NavBar.vue` / `normalizers.js` vs `BasicServicesPage.vue` / seeders).
- Seeder images (`services-automation.PNG`, `General-services.PNG`, `digital_solutions.jfif`, `indust_ai.jfif`, etc.) preserved.
- Client and partner logos (`ourClints/`, `partners/`) preserved.

### Verification & Validation Results
- **Frontend Production Build:** `npm run build` completed cleanly in 24.06s with exit code 0.
  - Module transform count dropped from **171 to 169 modules**.
  - `index.js` bundle size dropped from **265.22 kB to 262.93 kB** (-2.29 kB reduction).
  - `index.css` bundle size dropped from **186.82 kB to 185.93 kB** (-890 B reduction).
- **Git Diff Hygiene:** `git diff --check` passed with 0 errors.
- **Symbol Exhaustion Check:** Zero broken references to deleted functions, files, or CSS selectors across the codebase.
- **Preserved Work Check:** `resources/frontEnd/public/images/page111.webp` confirmed staged as `A`.
- **Backend Tests:** `php artisan test` passed 63/64 tests with 0 regressions (1 pre-existing failure in `ClientTest`).

---

## 13. Frontend Cleanup Batch 6 (Safe Asset Audit and Dead CSS Removal)

### Overview
Conducted a comprehensive audit of all static media assets across `resources/frontEnd/public/images/` and dead/duplicate CSS rules and variables across `resources/frontEnd/src/`.

### Public Asset Audit Findings & Preservation Rationale
An exhaustive multi-layer audit examined frontend source templates, CSS URLs, JavaScript dynamic paths, backend seeders, live SQLite database records, and production CMS backups. Every remaining asset in `resources/frontEnd/public/images/` was verified:

| Asset / Asset Group | Usage & Evidence Found | Decision & Rationale |
| :--- | :--- | :--- |
| `public/images/page111.webp` (62.8 KB) | Fixes desktop Navbar Services mega-menu promo card preview. | **STAGED (`A`) & PRESERVED**. Must not be touched. |
| `public/images/page111.jfif` (595.8 KB) | Original high-res source asset for `page111.webp`. | **PRESERVED**. Prevents destruction of source artwork. |
| `public/images/factory.webp` & `public/images/factory-floor.webp` | `factory.webp` referenced in `NavBar.vue` and `normalizers.js`. `factory-floor.webp` referenced in `BasicServicesPage.vue`, `SolutionPagesSeeder.php`, and production CMS backups. | **PRESERVED**. Required by active routing and seeders. |
| Solution Hero Images (`General-services.*`, `digital_solutions.*`, `indust_ai.*`, `infraSolutions.*`, `maintainOperations.*`, `predictiveMaintain.*`, `smartManu.*`, `cyberSecurity.*`, `engineeringServices.*`, `services-automation.PNG`) | Explicitly seeded by `database/seeders/SolutionPagesSeeder.php` and referenced in `backup_prod_solution_pages_*.json`. | **PRESERVED**. Removing any would break backend database seeding or production CMS restoration. |
| `public/images/about-image.png` (1.77 MB) & `about-image.webp` (67.1 KB) | `about-image.png` is hardcoded in `database/seeders/HomeSectionsSeeder.php` (line 22) and `src/api/normalizers.js` (line 335). | **PRESERVED**. Modifying normalizers is prohibited in Batch 6; seeder integrity preserved. |
| `public/images/controlling-2.webp` (92.8 KB) | Referenced in `src/api/normalizers.js` (line 389) and production CMS backup for Process Automation. | **PRESERVED**. Actively used by fallback and production CMS. |
| `public/images/training.webp` (226.9 KB) & `services/training-consulting.jpg` (305 KB) | `SolutionPagesSeeder.php` (line 538) seeds `images/services/training-consulting.jpg`; `imageUrl.js` dynamically maps requests to `training.webp`. | **PRESERVED**. Required for seeder and dynamic URL resolver. |
| `public/images/sis-panel-img.jpeg` (81.0 KB) | Used as default fallback logo in `NavBar.vue`, `FooterSection.vue`, `AdminLayout.vue`, `HeaderManager.vue`, and `FooterManager.vue`. | **PRESERVED**. Actively rendered across user and admin layouts. |
| `public/images/industry-*.webp` (5 files) | Used for industry sector cards in `ProjectsSection.vue`, `NavBar.vue`, `UseCasesPage.vue`, and `useCasesProjects.js`. | **PRESERVED**. Actively rendered across multiple navigation and sector views. |
| `public/images/control-room.webp` (130.4 KB) | Referenced in `dummydata/useCasesProjects.js` and `seed_images.php`. | **PRESERVED**. Active fallback for use case projects. |
| `public/images/ourClints/*.jpg` & `*.webp` (30 files) | Dual-format client logos: `DatabaseSeeder.php` seeds `.jpg`, while `ClientSeeder.php` and live SQLite database store `.webp`. | **PRESERVED**. Both formats required across environments. |
| `public/images/partners/*.png` & `*.webp` (19 files) | Seeders and `partnerEcosystem.vue` reference partner logos. | **PRESERVED**. Actively rendered on Partner Ecosystem page. |
| `public/images/work-samples-images/*` (6 files) | Referenced by `DatabaseSeeder.php`, `workSamples.js`, and `WorkSamplesGrid.vue`. | **PRESERVED**. Actively rendered on Services page. |
| `public/sis-logo.png` (749.5 KB) | Primary favicon/brand icon in `index.html` and OpenGraph metadata. | **PRESERVED**. Core branding asset. |

**Asset Removal Decision:** Concluded that zero asset deletions are justified in this batch. Every asset has active references in templates, seeders, or production CMS backups. Deleting files simply because they look older would violate the strict constraint against breaking seeders or CMS data.

### Dead CSS Selectors and Rules Removed

1. **Dead Duplicate Mega-Menu Transition (`NavBar.vue`):**
   - Removed lines 1052–1069 (`.mega-menu-enter-active`, `.mega-menu-leave-active`, `.mega-menu-enter-from`, etc.).
   - This 25-line block was completely overridden by a second, identical-selector declaration at lines 1117–1132 with cubic-bezier timing and translateY(-12px).
2. **Redundant Global Transition (`src/index.css`):**
   - Removed 14 lines defining global `.content-fade-enter-active`, `.content-fade-leave-active`, `.content-fade-enter-from`, `.content-fade-leave-to`.
   - Originally created for the deleted `ThemeToggle` dropdown; `NavBar.vue` has its own scoped `.content-fade-*` implementation for the mobile menu.
3. **Dead CSS Custom Properties / Variables (`src/index.css`):**
   - Removed 9 unused variables in `:root`:
     - `--bg-panel-60` (0 usages)
     - `--bg-panel-80` (0 usages)
     - `--text-90` (0 usages)
     - `--bg-card-3` (0 usages)
     - `--bg-glass` (0 usages)
     - `--bg-glass-card` (0 usages)
     - `--section-bg` (0 usages)
     - `--overlay-bg` (0 usages; obsolete leftover from deleted video)
     - `--dot-mesh` (0 usages; obsolete leftover from deleted Workflow SVG)

### Verification & Validation Results
- **Frontend Production Build:** `npm run build` completed cleanly in 30.66s with exit code 0.
  - Module transform count remained stable at **169 modules**.
  - `index.css` bundle size decreased from **185.93 kB to 185.16 kB** (-770 B reduction).
  - `index.js` bundle size remained stable at **262.93 kB**.
- **Git Diff Hygiene:** `git diff --check` passed cleanly with 0 whitespace or formatting errors.
- **Reference Exhaustion Search:** Global searches confirmed zero broken references to removed CSS variables or transition classes.
- **Preserved Work Check:** `resources/frontEnd/public/images/page111.webp` confirmed staged as `A` and completely intact.
- **Backend / Store / Normalizers Protection:** `useSiteStore.js`, `normalizers.js`, backend controllers, models, routes, migrations, and database records remained 100% untouched.


