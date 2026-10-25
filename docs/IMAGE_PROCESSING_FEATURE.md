# توثيق ميزة معالجة وضغط الصور في الـ Backend
### باستخدام Intervention Image v3 في مشروع SiS

---

## 1. نظرة عامة (Overview)

### المشكلة السابقة:
* عند محاولة رفع صور ذات أحجام أو أبعاد كبيرة من لوحة التحكم أو الـ Frontend، كان السيرفر يواجه استهلاكاً عالياً للذاكرة ووقت تنفيذ زائد أو تجاوز حدود الرفع، مما يتسبب في ظهور خطأ **`500 Internal Server Error`**.
* كانت الصور تُحفظ بأحجامها الأصلية الضخمة دون أي تصغير أو ضغط، مما يهدر مساحة التخزين على السيرفر ويبطئ تحميل الصفحات للزوار.
* تشتت وتكرار منطق رفع الصور داخل الـ Controllers والـ Services دون مرجع موحد أو حذف تلقائي للملفات القديمة.

### الحل المعتمد:
إضافة نظام مركزي لمعالجة وضغط الصور قبل حفظها على القرص باستخدام مكتبة **Intervention Image v3** ومحرك **GD**:
1. **الحد الأقصى للأبعاد:** `1200×1200px` مع الحفاظ التام على النسبة والتناسب (Aspect Ratio).
2. **عدم التكبير (No Upscaling):** الصور الأصغر من الأبعاد المحددة لا يتم تكبيرها لتجنب تشويه الجودة.
3. **الضغط الذكي (Compression):** ضغط جودة الصور بنسبة `80%` لتقليل الحجم بنسبة تصل إلى 70% بنفس الجودة البصرية.
4. **دعم الصيغ:** السماح بصيغ `jpg, jpeg, png, webp` ودعم تلقائي لملفات الـ `svg`.
5. **رفع سقف الحجم المسموح:** رفع حد الرفع إلى `10MB` للملف.
6. **إعدادات مركزية:** إدارة الأبعاد والجودة من ملف واحد `config/image.php`.
7. **الحفاظ على المسارات:** الحفاظ الكامل على مسارات الـ API والتخزين الحالية دون أي كسر.

---

## 2. مخطط تدفق البيانات (Workflow)

```
[ Frontend / Postman ]
        │ (رفع صورة بدقة عالية حتى 10MB)
        ▼
[ Form Request Validation ]
        │ (التحقق من الصيغ jpg, jpeg, png, webp والحد الأقصى 10240KB)
        ▼
[ ImageTrait::uploadImage ]
        │
        ├─► التحقق من نوع الملف: هل هو SVG؟ ──► حفظ مباشر دون معالجة GD
        │
        ├─► حذف الصورة القديمة $oldPath (إن وُجدت) لتنظيف مساحة القرص
        │
        ├─► قراءة الصورة بواسطة ImageManager (Driver: GD)
        │
        ├─► تطبيق scaleDown(1200, 1200) (حفظ النسبة ومنع تكبير الصغير)
        │
        └─► الحفظ بضغط الجودة save($path, quality: 80)
        ▼
[ Storage: storage/app/public/... ]
        ▼
[ Database & API Response ] (إرجاع نفس الرابط المعتاد /storage/...)
```

---

## 3. جدول الملفات المعدلة والمنشأة

| # | مسار الملف | نوع التغيير | الوظيفة |
|---|---|---|---|
| 1 | `composer.json` | تعديل | تثبيت حزمة `intervention/image: ^3.11` |
| 2 | `config/image.php` | **جديد** | ملف إعدادات مركزي (الأبعاد، الجودة، حدود الرفع) |
| 3 | `app/Traits/ImageTrait.php` | **جديد** | التريت الأساسي المسؤول عن المعالجة والرفع والحذف |
| 4 | `app/Http/Requests/StoreProjectRequest.php` | تعديل | رفع الحد الأقصى إلى 10MB واعتماد الصيغ المحددة |
| 5 | `app/Http/Requests/UpdateProjectRequest.php` | تعديل | رفع الحد الأقصى إلى 10MB واعتماد الصيغ المحددة |
| 6 | `app/Services/ProjectService.php` | تعديل | تطبيق `ImageTrait` لغلاف المشروع وصور المعرض المتعددة |
| 7 | `app/Http/Controllers/Api/v1/Admin/AdminSolutionPageController.php` | تعديل | معالجة صورة الـ `hero_image` لصفحات الحلول |
| 8 | `app/Http/Controllers/Api/v1/Admin/AdminSectionController.php` | تعديل | معالجة صور وشعارات أقسام الموقع والصفحة الرئيسية |
| 9 | `app/Http/Controllers/Api/v1/Admin/AdminCollectionController.php` | تعديل | معالجة صور وشعارات الـ Collections (خدمات، قطاعات، شركاء، عملاء) |
| 10 | `tests/Feature/ImageProcessingTest.php` | **جديد** | اختبارات آلية للتحقق من تقليل الأبعاد وعدم تكبير الصغير والصور المتعددة |

---

## 4. التفاصيل البرمجية لكل ملف

### 1) الحزمة في `composer.json`
تم تثبيت الإصدار الثالث المتوافق مع PHP 8.3:
```json
"require": {
    "intervention/image": "^3.11"
}
```

---

### 2) ملف الإعدادات المركزي `config/image.php`
يسمح بتغيير حدود الأبعاد ونسبة الضغط لكل النظام مستقبلاً من مكان واحد:
```php
<?php

return [
    'driver' => env('IMAGE_DRIVER', 'gd'),

    'resize' => [
        'width' => (int) env('IMAGE_MAX_WIDTH', 1200),
        'height' => (int) env('IMAGE_MAX_HEIGHT', 1200),
    ],

    'quality' => (int) env('IMAGE_QUALITY', 80),

    'max_size_kb' => (int) env('IMAGE_MAX_SIZE_KB', 10240), // 10MB
    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'],
    'allowed_extensions' => ['jpeg', 'jpg', 'png', 'webp'],
];
```

---

### 3) التريت الموحد `app/Traits/ImageTrait.php`
يحتوي على الدوال الأساسية المستخدمة عبر كافة الكنترولرز والخدمات:
* **`uploadImage($file, $folder, $oldPath, $id, $prefix)`**:
  * حذف الصورة السابقة لتفادي تراكم الملفات المهملة.
  * التعامل مع ملفات `svg` بحفظها مباشرة دون تمريرها لمحرك GD.
  * استخدام دالة `scaleDown(width, height)` من مكتبة Intervention Image v3 لتقليص الأبعاد الأكبر فقط دون المساس بالأصغر.
  * حفظ الصورة مع تطبيق نسبة الجودة `save($path, quality: $quality)`.
* **`uploadMultiImages($files, $folder, $oldPaths)`**:
  * استقبال مصفوفة من الصور وتمرير كل واحدة للمنطق الموحد وإرجاع مصفوفة بالمسارات النسبية.
* **`deleteImage($path)` و `deleteMultiImages($paths)`**:
  * حذف آمن للملفات من `Storage::disk('public')`.

---

### 4) نماذج التحقق `StoreProjectRequest.php` و `UpdateProjectRequest.php`
تم تحديث فحص حقل صورة الغلاف ليعتمد الإعدادات المركزية:
```php
// استبدال: 'cover_image' => ['nullable', 'image', 'max:5120']
'cover_image' => [
    'nullable', 
    'file', 
    'mimes:jpeg,png,jpg,webp', 
    'max:' . config('image.max_size_kb', 10240)
],
```

---

### 5) خدمة المشاريع `app/Services/ProjectService.php`
* تم إدراج التريت: `use ImageTrait;`.
* **في `createProject`:**
  * استبدال `store('projects', 'public')` بـ `$this->uploadImage($data['cover_image'], 'projects')`.
  * استبدال حفظ صور المعرض المتعددة بـ `$this->uploadImage($image, 'projects/gallery')`.
* **في `updateProject`:**
  * استبدال الغلاف وتمرير الصورة القديمة `$project->cover_image` ليتم حذفها تلقائياً.
  * عند إزالة صور من المعرض، يتم استدعاء `$this->deleteImage($oldImage->image_path)`.
* **في `deleteProject`:**
  * حذف الغلاف وكافة صور المعرض من مسار التخزين عند مسح المشروع.

---

### 6) كنترولر صفحات الحلول `AdminSolutionPageController.php`
* تم إدراج التريت: `use ImageTrait;`.
* تم تحديث معالجة حقل `hero_image`:
```php
if ($request->hasFile('hero_image')) {
    $request->validate([
        'hero_image' => ['file', 'mimes:jpeg,png,jpg,webp,svg', 'max:' . config('image.max_size_kb', 10240)],
    ]);
    $data['hero_image'] = $this->uploadImage($request->file('hero_image'), 'solution-pages');
}
```

---

### 7) كنترولر الأقسام `AdminSectionController.php`
* تم إدراج التريت: `use ImageTrait;`.
* تمرير الحقول (`image`, `image_path`, `logo`, `logo_path`) إلى `$this->uploadImage($file, 'sections', $oldFilePath)`.

---

### 8) كنترولر المجموعات `AdminCollectionController.php`
* تم إدراج التريت: `use ImageTrait;`.
* توحيد دالة `handleFileUploads`:
```php
$oldPath = $currentItem ? ($currentItem->{$field} ?? null) : null;
$filePath = $this->uploadImage($request->file($field), "collections/{$resource}", $oldPath);
```
* إضافة تنظيف وحذف الملفات داخل دالة الحذف `destroy`:
```php
$fileFields = ['logo_path', 'image', 'image_path', 'logo', 'cover_image', 'src'];
foreach ($fileFields as $f) {
    if (!empty($item->{$f})) {
        $this->deleteImage($item->{$f});
    }
}
```

---

## 5. الاختبار والتحقق الآلي (Testing)

تم إنشاء ملف اختبار Feature Test متكامل في:  
`tests/Feature/ImageProcessingTest.php`

ويختبر الحالات التالية:
1. **`test_uploaded_oversized_image_is_downscaled_to_max_dimensions`**:
   * رفع صورة تجريبية أبعادها `1800×1200px`.
   * التأكد من حفظها بأبعاد `1200×800px` (مع الحفاظ على نسبة العرض إلى الارتفاع 3:2).
2. **`test_uploaded_small_image_is_not_upsized`**:
   * رفع صورة تجريبية أبعادها `400×300px`.
   * التأكد من بقائها `400×300px` تماماً دون تكبير.
3. **`test_multiple_gallery_images_are_all_processed`**:
   * رفع صور متعددة في وقت واحد والتأكد من معالجة وحفظ كل صورة منها بشكل مستقل.

### لتشغيل الاختبارات:
```bash
php artisan test tests/Feature/ImageProcessingTest.php
```

### نتيجة تشغيل كامل اختبارات المشروع:
```bash
php artisan test
# Result: 24 passed (317 assertions)
```

---

## 6. ضبط بيئة العمل في السيرفر المحلي (Laragon / PHP)

لتفادي أي أخطاء 500 ناتجة عن إعدادات الـ PHP الافتراضية قبل وصول الملف للسكربت:

افتح ملف `php.ini` وتأكد من القيم التالية:
```ini
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 256M
extension=gd
```
ثم أعد تشغيل السيرفر (Reload / Restart All في Laragon).
