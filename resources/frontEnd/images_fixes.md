# SiS Backend — Image Serving Fixes

Why client/project/partner/work-sample images return 404 (or nothing) on the deployed site, and exactly what to fix in `sis-api/`.

## 1. Root cause

Two infrastructure problems make **every** image 404, independent of any code:

1. `storage/app/public/` is empty — the image files referenced by the seeders were never placed
   there (only a `.gitignore` exists).
2. `public/storage` symlink does not exist — so even a present file is unreachable via
   `asset('storage/…')`.

The public resources all build image URLs as `asset('storage/' . $path)` (or handle a leading
slash), so the fix is: **create the symlink and place the files at the exact relative paths the
seeders store.**

---

## 2. Setup fixes

### 2.1 Create the storage symlink

```bash
cd /home/sis/www/vue/sis-api
php artisan storage:link
```

Expected: a symlink `public/storage` → `storage/app/public`.

### 2.2 Place the seeded image files

Copy the images bundled with the frontend repo (`/home/sis/www/vue/public/images/`) into
`storage/app/public` at the relative paths the seeders reference. After this step the directory
should look like:

```
storage/app/public/
├── partners/
├── ourClints/
├── work-samples/
└── images/about-hero.webp
```

### File mapping table (source → destination)

| Source (frontend `public/images/`) | Destination (`storage/app/public/`) | Notes |
|---|---|---|
| `partners/Siemens.png` | `partners/Siemens.png` | |
| `partners/Rockwell_Automation_Logo.png` | `partners/Rockwell_Automation_Logo.png` | |
| `partners/schnider.jpg` | `partners/schnider.jpg` | |
| `partners/ABB.png` | `partners/ABB.png` | |
| `partners/Mitsubishi_Electric.png` | `partners/Mitsubishi_Electric.png` | |
| `partners/Huawei.png` | `partners/Huawei.png` | |
| `partners/aveva.jpg` | `partners/aveva.jpg` | |
| `partners/PTC.png` | `partners/PTC.png` | |
| `partners/Egypt_4_Industry.jpg` | `partners/Egypt_4_Industry.jpg` | |
| `partners/odoo.svg` | `partners/odoo.svg` | |
| `ourClints/khalad-Petroleum.webp` | `ourClints/khalad-Petroleum.webp` | project cover |
| `ourClints/Saudi-Aramco.webp` | `ourClints/Saudi-Aramco.webp` | project cover |
| `ourClints/SWCC.webp` | `ourClints/SWCC.webp` | project cover |
| `ourClints/Qaliobia-Waste-Water.webp` | `ourClints/Qaliobia-Waste-Water.webp` | project cover |
| `ourClints/uniha.webp` | `ourClints/uniha.webp` | project cover |
| `ourClints/Gas-Cool.webp` | `ourClints/Gas-Cool.webp` | project cover |
| `ourClints/emitsal.webp` | `ourClints/emitsal.webp` | project cover |
| `ourClints/henkel.webp` | `ourClints/henkel.webp` | project cover |
| `ourClints/BariQ.webp` | `ourClints/BariQ.webp` | project cover |
| `ourClints/TAK-Foods-Factory.webp` | `ourClints/TAK-Foods-Factory.webp` | project cover |
| `ourClints/Al-Takamol-Cement.webp` | `ourClints/Al-Takamol-Cement.webp` | project cover |
| `ourClints/gcf.webp` | `ourClints/gcf.webp` | project cover |
| `ourClints/delta.jpg` | `ourClints/delta.jpg` | client logo |
| `ourClints/emitsal.jpg` | `ourClints/emitsal.jpg` | client logo |
| `ourClints/energeya.jpg` | `ourClints/energeya.jpg` | client logo |
| `ourClints/gcf.jpg` | `ourClints/gcf.jpg` | client logo |
| `ourClints/giza_systems.jpg` | `ourClints/giza_systems.jpg` | client logo |
| `ourClints/henkel.jpg` | `ourClints/henkel.jpg` | client logo |
| `ourClints/omis.jpg` | `ourClints/omis.jpg` | client logo |
| `ourClints/orascom_const.jpg` | `ourClints/orascom_const.jpg` | client logo |
| `ourClints/plast_company.jpg` | `ourClints/plast_company.jpg` | client logo |
| `ourClints/tabreed.jpg` | `ourClints/tabreed.jpg` | client logo |
| `ourClints/uniha.jpg` | `ourClints/uniha.jpg` | client logo |
| `work-samples-images/cement-process-automation.png` | `work-samples/cement-process-automation.png` | ⚠️ renamed folder |
| `work-samples-images/ems-energy-management.png` | `work-samples/ems-energy-management.png` | ⚠️ renamed folder |
| `work-samples-images/bms-building-management.png` | `work-samples/bms-building-management.png` | ⚠️ renamed folder |
| `work-samples-images/cmms-maintenance.png` | `work-samples/cmms-maintenance.png` | ⚠️ renamed folder |
| `work-samples-images/ai-analysis.png` | `work-samples/ai-analysis.png` | ⚠️ renamed folder |
| `work-samples-images/process-automation.png` | `work-samples/process-automation.png` | ⚠️ renamed folder |
| `about-hero.webp` | `images/about-hero.webp` | AboutHero seed stores `images/about-hero.webp` |

> **⚠️ Work-samples path discrepancy:** the seeders store work-sample images as
> `work-samples/*.png` (`WorkSampleSeeder`), but the frontend bundles them under
> `work-samples-images/`. Copy them **into** the `work-samples/` folder on the backend so the
> seeded `work-samples/*.png` paths resolve.

> **⚠️ `.gitignore`:** `storage/app/public/.gitignore` ignores `*`, so these files are **not**
> committed to git. They must be copied on each server/deploy — see §3 for an automated command.

---

## 3. Optional: automate asset install with an artisan command

Add a console command so deployment copies the assets automatically:

`app/Console/Commands/InstallSiteAssets.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallSiteAssets extends Command
{
    protected $signature = 'sis:install-assets
        {--source= : Path to the frontend public/images directory}';

    protected $description = 'Copy bundled site images into storage/app/public at seed-expected paths';

    public function handle(): int
    {
        $source = $this->option('source')
            ?? realpath(base_path('../vue/public/images'));

        if (!$source || !File::isDirectory($source)) {
            $this->error("Source directory not found: {$source}");
            return self::FAILURE;
        }

        // from <source> => to storage_path('app/public/<to>')
        $mapping = [
            'partners'            => 'partners',
            'ourClints'           => 'ourClints',
            'work-samples-images' => 'work-samples', // seed expects work-samples/*.png
            'about-hero.webp'     => 'images/about-hero.webp',
        ];

        $copied = 0;
        foreach ($mapping as $from => $to) {
            $fromPath = rtrim($source, '/') . '/' . $from;
            if (!File::exists($fromPath)) {
                $this->warn("Missing source entry: {$fromPath}");
                continue;
            }

            $dest = storage_path('app/public/' . $to);

            if (File::isFile($fromPath)) {
                File::ensureDirectoryExists(dirname($dest));
                File::copy($fromPath, $dest);
                $copied++;
                continue;
            }

            File::ensureDirectoryExists($dest);
            $count = 0;
            foreach (File::allFiles($fromPath) as $file) {
                File::copy($file->getPathname(), $dest . '/' . $file->getFilename());
                $count++;
            }
            $copied += $count;
        }

        $this->info("Copied {$copied} file(s) into storage/app/public.");

        if (!File::exists(public_path('storage'))) {
            $this->warn('Run `php artisan storage:link` if the public/storage symlink is missing.');
        }

        return self::SUCCESS;
    }
}
```

Usage:

```bash
cd /home/sis/www/vue/sis-api
php artisan storage:link
php artisan sis:install-assets --source=/home/sis/www/vue/public/images
```

---

## 4. Code fix — `PartnerResource::logo_url` null-safety

`app/Http/Resources/PartnerResource.php` calls `str_starts_with($this->logo_path, 'http')`
unconditionally, which throws when `logo_path` is `null` (e.g. a partner created without a logo).

Current:

```php
'logo_url' => str_starts_with($this->logo_path, 'http')
    ? $this->logo_path
    : asset('storage/' . $this->logo_path),
```

Fixed (match the pattern used by `ClientResource`/`WorkSampleResource`):

```php
'logo_url' => $this->logo_path
    ? (str_starts_with($this->logo_path, 'http')
        ? $this->logo_path
        : asset('storage/' . $this->logo_path))
    : null,
```

---

## 5. Code fix — `AdminCollectionController` uploads

`app/Http/Controllers/Api/v1/Admin/AdminCollectionController.php` currently:

1. Only inspects `hasFile('image')` / `hasFile('logo')` and writes to `$data['image']` /
   `$data['logo']` — but **Partner/Client use the `logo_path` column**, so uploaded logos are
   silently dropped (only `image`-style rows like WorkSample would persist).
2. Stores the path as `"/storage/" . $path` (leading slash). Downstream resources then build
   `asset('storage/' . '/storage/…')` → a doubled `storage/storage` URL.
3. On **update without a new upload**, the frontend sends the existing URL string back under the
   same field key; the current code would overwrite the DB column with that URL string.

### Field-name contract (what the frontend sends)

| Resource | File field name | Column to write |
|---|---|---|
| `partners`, `clients` | `logo_path` | `logo_path` |
| `work-samples` | `image` | `image` |
| other collections | — | — |

### Recommended changes

In both `store()` and `update()`, replace the two `hasFile()` blocks with a shared helper that:

```php
private function handleFile(Request $request, string $field): ?string
{
    if (!$request->hasFile($field)) {
        return null;
    }

    return $request->file($field)->store(
        "collections/{$request->route('resource')}",
        'public'
    ); // store() returns a RELATIVE path, e.g. collections/partners/123_x.png
}
```

Then:

```php
// store()
if ($request->hasFile('logo_path')) {
    $data['logo_path'] = $this->handleFile($request, 'logo_path');
}
if ($request->hasFile('image')) {
    $data['image'] = $this->handleFile($request, 'image');
}

// update()
if ($request->hasFile('logo_path')) {
    $data['logo_path'] = $this->handleFile($request, 'logo_path');
} else {
    unset($data['logo_path']); // keep existing column on round-trip
}

if ($request->hasFile('image')) {
    $data['image'] = $this->handleFile($request, 'image');
} else {
    unset($data['image']); // keep existing column on round-trip
}
```

Rules to follow:

- **Store relative paths only** (`collections/{resource}/{file}`), never `/storage/…`, so the
  existing `asset('storage/' . $path)` in the resources produces a correct URL.
- **`unset()` the file key on update when no file was uploaded** — the frontend echoes the
  current URL back under the same key; writing it back is harmless but pollutes the column, and
  it can break `hasFile`-style checks later.
- Consider deleting the previous file on replace (mirror `ProjectService::updateProject`).

---

## 6. Code fix — `AdminSectionController` uploads

`app/Http/Controllers/Api/v1/Admin/AdminSectionController.php` stores `/storage/sections/…`
(leading slash). The hero/about-hero resources already tolerate a leading `/`, so this works —
but apply the same conventions for consistency and clean round-trips:

```php
// replace the hasFile('image') block
if ($request->hasFile('image')) {
    $data['image'] = $request->file('image')->store('sections', 'public');
} else {
    unset($data['image']); // keep existing on round-trip
}
```

---

## 7. No change needed — projects

`ProjectService::createProject/updateProject` already:

- stores `cover_image` via `$file->store('projects', 'public')` → relative path
  (`projects/{file}`),
- deletes the old file on replace and on delete,
- `ProjectResource` correctly null-guards `cover_image`.

Leave `AdminProjectController` / `ProjectService` as-is.

---

## 8. Verification checklist

```bash
cd /home/sis/www/vue/sis-api

# 1. Symlink exists
ls -la public/storage          # -> storage/app/public

# 2. Files present at seed paths
ls storage/app/public/partners/      # 10 partner logos
ls storage/app/public/ourClints/     # project covers + client logos
ls storage/app/public/work-samples/  # 6 work-sample images
ls storage/app/public/images/        # about-hero.webp

# 3. Re-run seeders (updateOrCreate makes this idempotent)
php artisan db:seed

# 4. Public endpoints return reachable URLs (no double /storage, no null crash)
php artisan serve &
curl -s http://127.0.0.1:8000/api/v1/partners   | jq '.data[] | .logo_url'
curl -s http://127.0.0.1:8000/api/v1/clients    | jq '.data[] | .src'
curl -s http://127.0.0.1:8000/api/v1/projects   | jq '.projects[0] | .cover_image'
curl -s http://127.0.0.1:8000/api/v1/work-samples | jq '.data[] | .image_url'

# 5. Admin upload round-trip: create a partner with logo_path file,
#    then update it WITHOUT a new file -> logo_path column unchanged,
#    then create a partner with NO logo -> /partners must return logo_url: null (no 500).
```

Pass criteria:

- Every `logo_url` / `src` / `cover_image` / `image_url` above loads in a browser (HTTP 200).
- A partner with a `null` logo returns `"logo_url": null`, not a 500.
- Admin-uploaded images resolve under `/storage/collections/…` and `/storage/sections/…`.
