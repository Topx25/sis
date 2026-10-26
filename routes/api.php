<?php

use App\Http\Controllers\Api\v1\Admin\AdminCollectionController;
use App\Http\Controllers\Api\v1\Admin\AdminContactController;
use App\Http\Controllers\Api\v1\Admin\AdminContactPageController;
use App\Http\Controllers\Api\v1\Admin\AdminFooterController;
use App\Http\Controllers\Api\v1\Admin\AdminHeaderController;
use App\Http\Controllers\Api\v1\Admin\AdminIndustriesPageController;
use App\Http\Controllers\Api\v1\Admin\AdminProjectController;
use App\Http\Controllers\Api\v1\Admin\AdminSectionController;
use App\Http\Controllers\Api\v1\Admin\AdminPageSeoController;
use App\Http\Controllers\Api\v1\Admin\AdminSeoSettingController;
use App\Http\Controllers\Api\v1\Admin\AdminSolutionPageController;
use App\Http\Controllers\Api\v1\Admin\AdminTrustedPartnersPageController;
use App\Http\Controllers\Api\v1\Admin\AdminUseCasesPageController;
use App\Http\Controllers\Api\v1\Admin\AdminVisitorCredentialController;
use App\Http\Controllers\Api\v1\Admin\AuthController;
use App\Http\Controllers\Api\v1\Public\AboutController;
use App\Http\Controllers\Api\v1\Public\AboutHeroController;
use App\Http\Controllers\Api\v1\Public\ArchitectureController;
use App\Http\Controllers\Api\v1\Public\ClientController;
use App\Http\Controllers\Api\v1\Public\ContactController;
use App\Http\Controllers\Api\v1\Public\ContactPageController;
use App\Http\Controllers\Api\v1\Public\FooterController;
use App\Http\Controllers\Api\v1\Public\HeaderController;
use App\Http\Controllers\Api\v1\Public\PageSeoController;
use App\Http\Controllers\Api\v1\Public\SeoSettingController;
use App\Http\Controllers\Api\v1\Public\CoreValuesController;
use App\Http\Controllers\Api\v1\Public\DomainController;
use App\Http\Controllers\Api\v1\Public\HeroController;
use App\Http\Controllers\Api\v1\Public\IndustriesPageController;
use App\Http\Controllers\Api\v1\Public\ItOtController;
use App\Http\Controllers\Api\v1\Public\PartnerController;
use App\Http\Controllers\Api\v1\Public\PillarController;
use App\Http\Controllers\Api\v1\Public\ProjectController;
use App\Http\Controllers\Api\v1\Public\SectorController;
use App\Http\Controllers\Api\v1\Public\ServiceController;
use App\Http\Controllers\Api\v1\Public\SolutionPageController;
use App\Http\Controllers\Api\v1\Public\TimelineController;
use App\Http\Controllers\Api\v1\Public\TrustedPartnersController;
use App\Http\Controllers\Api\v1\Public\UseCasesController;
use App\Http\Controllers\Api\v1\Public\VisitorAccessController;
use App\Http\Controllers\Api\v1\Public\WorkSampleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SiS (Superior Integrated Solutions)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // 🌐 PUBLIC ENDPOINTS (Vue.js Frontend)
    // ==========================================
    Route::get('/hero', [HeroController::class, 'index']);
    Route::get('/hero-home', [App\Http\Controllers\HomeHeroSlideController::class, 'index']);
    Route::get('/why-sis', [App\Http\Controllers\HomeWhySisController::class, 'index']);
    Route::get('/vision', [AboutController::class, 'index']);
    Route::get('/about-hero', [AboutHeroController::class, 'index']);
    Route::get('/it-ot', [ItOtController::class, 'index']);
    Route::get('/core-values', [CoreValuesController::class, 'index']);
    Route::get('/domains', [DomainController::class, 'index']);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{slug}', [ProjectController::class, 'show']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{slug}', [ServiceController::class, 'show']);

    Route::get('/sectors', [SectorController::class, 'index']);
    Route::get('/partners', [PartnerController::class, 'index']);
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/timeline', [TimelineController::class, 'index']);
    Route::get('/timeline-section', [TimelineController::class, 'section']);
    Route::get('/pillars', [PillarController::class, 'index']);
    Route::get('/work-flow', [ArchitectureController::class, 'index']);
    Route::get('/work-samples', [WorkSampleController::class, 'index']);

    // 🚀 New Solution & Dedicated Pages Endpoints
    Route::get('/digital', [SolutionPageController::class, 'digital']);
    Route::get('/ai', [SolutionPageController::class, 'ai']);
    Route::get('/process', [SolutionPageController::class, 'process']);
    Route::get('/industrial', [SolutionPageController::class, 'industrial']);
    Route::get('/general-services', [SolutionPageController::class, 'generalServices']);
    Route::get('/solution-pages', [SolutionPageController::class, 'index']);
    Route::get('/solution-pages/menu', [SolutionPageController::class, 'menu']);
    Route::get('/solution-pages/{slug}', [SolutionPageController::class, 'show']);

    Route::get('/use-cases', [UseCasesController::class, 'index']);
    Route::get('/partner-ecosystem', [TrustedPartnersController::class, 'index']);
    Route::get('/industries', [IndustriesPageController::class, 'index']);
    Route::get('/contact-page', [ContactPageController::class, 'show']);
    Route::get('/footer', [FooterController::class, 'index']);
    Route::get('/header', [HeaderController::class, 'index']);
    Route::get('/seo-settings', [SeoSettingController::class, 'index']);
    Route::get('/pages-seo', [PageSeoController::class, 'index']);
    Route::get('/pages-seo/{pageKey}', [PageSeoController::class, 'show']);

    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
    Route::post('/visitor-access/verify', [VisitorAccessController::class, 'verify'])->middleware('throttle:10,1');

    // ==========================================
    // 🔒 AUTHENTICATION & ADMIN ENDPOINTS
    // ==========================================
    Route::prefix('admin')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::post('/refresh-token', [AuthController::class, 'refresh'])->middleware('throttle:10,1');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);

            // Admin CRUD Projects
            Route::get('/projects', [AdminProjectController::class, 'index']);
            Route::get('/projects/{project}', [AdminProjectController::class, 'show']);
            Route::post('/projects', [AdminProjectController::class, 'store']);
            Route::put('/projects/{project}', [AdminProjectController::class, 'update']);
            Route::post('/projects/{project}', [AdminProjectController::class, 'update']); // Added to handle FormData via POST
            Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy']);

            // Admin View & Manage Inquiries
            Route::get('/contact-requests', [AdminContactController::class, 'index']);
            Route::patch('/contact-requests/{contactRequest}/status', [AdminContactController::class, 'updateStatus']);
            Route::post('/contact-requests/{contactRequest}/reply', [AdminContactController::class, 'reply']);

            // Visitor Access Credentials CRUD
            Route::get('/visitor-credentials', [AdminVisitorCredentialController::class, 'index']);
            Route::post('/visitor-credentials', [AdminVisitorCredentialController::class, 'store']);
            Route::put('/visitor-credentials/{visitorCredential}', [AdminVisitorCredentialController::class, 'update']);
            Route::delete('/visitor-credentials/{visitorCredential}', [AdminVisitorCredentialController::class, 'destroy']);

            // Admin Solution Pages Management
            Route::get('/solution-pages', [AdminSolutionPageController::class, 'index']);
            Route::post('/solution-pages', [AdminSolutionPageController::class, 'store']);
            Route::get('/solution-pages/{slug}', [AdminSolutionPageController::class, 'show']);
            Route::put('/solution-pages/{slug}', [AdminSolutionPageController::class, 'update']);
            Route::post('/solution-pages/{slug}', [AdminSolutionPageController::class, 'update']); // Added to handle FormData via POST
            Route::delete('/solution-pages/{slug}', [AdminSolutionPageController::class, 'destroy']);

            // Admin Dedicated Pages Management
            Route::get('/use-cases-page', [AdminUseCasesPageController::class, 'show']);
            Route::put('/use-cases-page', [AdminUseCasesPageController::class, 'update']);

            Route::get('/trusted-partners-page', [AdminTrustedPartnersPageController::class, 'show']);
            Route::put('/trusted-partners-page', [AdminTrustedPartnersPageController::class, 'update']);
            Route::post('/trusted-partners-page', [AdminTrustedPartnersPageController::class, 'update']); // Added to handle FormData via POST

            Route::get('/industries-page', [AdminIndustriesPageController::class, 'show']);
            Route::put('/industries-page', [AdminIndustriesPageController::class, 'update']);
            Route::post('/industries-page', [AdminIndustriesPageController::class, 'update']); // Added to handle FormData via POST
            Route::put('/industries', [AdminIndustriesPageController::class, 'update']);
            
            // Industries (Sectors) CRUD endpoints
            Route::get('/industries', [AdminCollectionController::class, 'index'])->defaults('resource', 'sectors');
            Route::post('/industries', [AdminCollectionController::class, 'store'])->defaults('resource', 'sectors');
            Route::get('/industries/{id}', [AdminCollectionController::class, 'show'])->defaults('resource', 'sectors');
            Route::put('/industries/{id}', [AdminCollectionController::class, 'update'])->defaults('resource', 'sectors');
            Route::post('/industries/{id}', [AdminCollectionController::class, 'update'])->defaults('resource', 'sectors');
            Route::delete('/industries/{id}', [AdminCollectionController::class, 'destroy'])->defaults('resource', 'sectors');
            
            Route::get('/contact-page', [AdminContactPageController::class, 'show']);
            Route::put('/contact-page', [AdminContactPageController::class, 'update']);
            Route::delete('/contact-page', [AdminContactPageController::class, 'destroy']);

            // Admin Header Management
            Route::get('/header', [AdminHeaderController::class, 'show']);
            Route::put('/header', [AdminHeaderController::class, 'update']);
            Route::post('/header', [AdminHeaderController::class, 'update']); // Added to handle FormData via POST

            // Admin Footer Management
            Route::get('/footer', [AdminFooterController::class, 'show']);
            Route::put('/footer', [AdminFooterController::class, 'update']);
            Route::post('/footer', [AdminFooterController::class, 'update']); // Added to handle FormData via POST

            // Admin Footer Links Management (reorder before {id} to avoid collision)
            Route::post('/footer/links', [AdminFooterController::class, 'storeLink']);
            Route::patch('/footer/links/reorder', [AdminFooterController::class, 'reorderLinks']);
            Route::patch('/footer/links/{id}/status', [AdminFooterController::class, 'updateLinkStatus']);
            Route::put('/footer/links/{id}', [AdminFooterController::class, 'updateLink']);
            Route::post('/footer/links/{id}', [AdminFooterController::class, 'updateLink']); // FormData via POST
            Route::delete('/footer/links/{id}', [AdminFooterController::class, 'destroyLink']);

            // Admin SEO Settings Management
            Route::get('/seo-settings', [AdminSeoSettingController::class, 'show']);
            Route::put('/seo-settings', [AdminSeoSettingController::class, 'update']);
            Route::post('/seo-settings', [AdminSeoSettingController::class, 'update']); // Added to handle FormData via POST
            Route::get('/pages-seo', [AdminPageSeoController::class, 'index']);
            Route::get('/pages-seo/{pageKey}', [AdminPageSeoController::class, 'show']);
            Route::put('/pages-seo/{pageKey}', [AdminPageSeoController::class, 'update']);
            Route::post('/pages-seo/{pageKey}', [AdminPageSeoController::class, 'update']); // FormData via POST

            // Admin Update Single Sections
            Route::get('/section/{section}', [AdminSectionController::class, 'show'])
                ->where('section', 'hero|vision|about|about-hero|it-ot|core-values|domains|home-why-sis|why-sis|services|clients|timeline');
            Route::put('/section/{section}/{id}', [AdminSectionController::class, 'update'])
                ->where('section', 'hero|vision|about|about-hero|it-ot|core-values|domains|home-why-sis|why-sis|services|clients|timeline');
            Route::post('/section/{section}/{id}', [AdminSectionController::class, 'update']) // Added to handle FormData via POST
                ->where('section', 'hero|vision|about|about-hero|it-ot|core-values|domains|home-why-sis|why-sis|services|clients|timeline');

            // Admin Collections Generic CRUD
            Route::get('/{resource}', [AdminCollectionController::class, 'index'])
                ->where('resource', 'services|sectors|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis');
            Route::get('/{resource}/{id}', [AdminCollectionController::class, 'show'])
                ->where('resource', 'services|sectors|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis');
            Route::post('/{resource}', [AdminCollectionController::class, 'store'])
                ->where('resource', 'services|sectors|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis');
            Route::put('/{resource}/{id}', [AdminCollectionController::class, 'update'])
                ->where('resource', 'services|sectors|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis');
            Route::post('/{resource}/{id}', [AdminCollectionController::class, 'update']) // Added to handle FormData correctly via POST
                ->where('resource', 'services|sectors|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis');
            Route::delete('/{resource}/{id}', [AdminCollectionController::class, 'destroy'])
                ->where('resource', 'services|sectors|partners|partner-ecosystem|clients|timeline|pillars|work-flow|work-samples|home-hero-slide|home-why-sis|why-sis');

            // Alias use-cases to manage projects
            Route::prefix('use-cases')->group(function () {
                Route::get('/', [AdminProjectController::class, 'index']);
                Route::get('/{project}', [AdminProjectController::class, 'show']);
                Route::post('/', [AdminProjectController::class, 'store']);
                Route::put('/{project}', [AdminProjectController::class, 'update']);
                Route::post('/{project}', [AdminProjectController::class, 'update']); // Handle FormData via POST
                Route::delete('/{project}', [AdminProjectController::class, 'destroy']);
            });
                
            // Explicit routes for partner/ecosystem (since {resource} doesn't match slashes)
            Route::prefix('partner/ecosystem')->group(function () {
                Route::get('/', [AdminCollectionController::class, 'index'])->defaults('resource', 'partners');
                Route::get('/{id}', [AdminCollectionController::class, 'show'])->defaults('resource', 'partners');
                Route::post('/', [AdminCollectionController::class, 'store'])->defaults('resource', 'partners');
                Route::put('/{id}', [AdminCollectionController::class, 'update'])->defaults('resource', 'partners');
                Route::post('/{id}', [AdminCollectionController::class, 'update'])->defaults('resource', 'partners');
                Route::delete('/{id}', [AdminCollectionController::class, 'destroy'])->defaults('resource', 'partners');
            });
        });
    });
});
