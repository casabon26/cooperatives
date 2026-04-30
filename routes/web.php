<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\CooperativeController;
use App\Http\Controllers\SimpleAdminAuthController;
use App\Http\Controllers\PublicAuthController;
use App\Http\Controllers\SimpleAdminTrainingController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\StoreLocationController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\Api\VideoProgressController;

Route::get('/', [PublicController::class, 'home']);

// temporary debug route for phpinfo
Route::get('/phpinfo', function () {
    phpinfo();
});


// Design preview for the new 2026-style theme
Route::view('/design-preview', 'demo.design_preview')->name('design.preview');

// Simple static pages (placeholders to be filled later)
Route::view('/about', 'pages.about')->name('about');
Route::view('/faqs', 'pages.faqs')->name('faqs');
Route::get('/livelihood', [PublicController::class, 'livelihood'])->name('livelihood');
// Enterprise Portal page
Route::view('/enterprise-portal', 'enterprise')->name('enterprise.portal');
// AJAX list of enterprises by category (placeholder)
Route::get('/enterprise-portal/enterprises', [\App\Http\Controllers\Admin\EnterpriseController::class, 'list'])->name('enterprise.list');

// Public enterprise detail page
Route::get('/enterprises/{enterprise}', function(\App\Models\Enterprise $enterprise){
    return view('enterprises.show', compact('enterprise'));
})->name('enterprises.show');
// Store locations page
Route::view('/store-locations', 'pages.store_locations')->name('store.locations');

// Public news and videos (dynamic)
Route::get('/news', [PublicController::class, 'news'])->name('news.index');
Route::get('/news/{news}', [PublicController::class, 'newsShow'])->name('news.show');
Route::get('/videos', [PublicController::class, 'videos'])->name('videos.index');
Route::get('/videos/{video}', [PublicController::class, 'videoShow'])->name('videos.show');
// Public training listing (separate)
Route::get('/training', [\App\Http\Controllers\TrainingController::class, 'index'])->name('training.index');
Route::get('/training/{video}', [\App\Http\Controllers\TrainingController::class, 'show'])->name('training.show');
Route::post('/training/{video}/complete', [\App\Http\Controllers\TrainingController::class, 'complete'])->middleware('auth')->name('training.complete');
Route::get('/training/{video}/certificate', [\App\Http\Controllers\TrainingController::class, 'certificate'])->middleware('auth')->name('training.certificate');

// User profile (authenticated)
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');

// User account settings (authenticated)
Route::get('/settings', [UserSettingsController::class, 'show'])->middleware('auth')->name('settings.show');
Route::post('/settings', [UserSettingsController::class, 'update'])->middleware('auth')->name('settings.update');
// Memorandum circulars
Route::get('/memorandums', [\App\Http\Controllers\MemorandumController::class, 'index'])->name('memorandums.index');
Route::get('/memorandums/{memorandum}', [\App\Http\Controllers\MemorandumController::class, 'show'])->name('memorandums.show');
Route::get('/memorandums/{memorandum}/file', [\App\Http\Controllers\MemorandumController::class, 'file'])->name('memorandums.file');

// Simple hardcoded admin login (separate from full auth)
// Admin/simple auth routes
Route::get('/admin/login', [SimpleAdminAuthController::class, 'showLogin']);
Route::post('/admin/login', [SimpleAdminAuthController::class, 'login']);
Route::post('/admin/logout', [SimpleAdminAuthController::class, 'logout']);
Route::get('/admin/panel', [SimpleAdminAuthController::class, 'panel']);

// Admin profile routes
Route::get('/admin/profile', [SimpleAdminAuthController::class, 'showProfile'])->name('admin.profile.show');
Route::get('/admin/profile/edit', [SimpleAdminAuthController::class, 'editProfile'])->name('admin.profile.edit');
Route::post('/admin/profile/update', [SimpleAdminAuthController::class, 'updateProfile'])->name('admin.profile.update');
Route::get('/admin/profile/change-password', [SimpleAdminAuthController::class, 'showChangePassword'])->name('admin.profile.change-password');
Route::post('/admin/profile/update-password', [SimpleAdminAuthController::class, 'updatePassword'])->name('admin.profile.update-password');

// Provide a generic named `login` route so Laravel's `auth` middleware can redirect unauthenticated users.
Route::get('/login', [SimpleAdminAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [SimpleAdminAuthController::class, 'login']);

// Public/user authentication (separate from simple admin session)
Route::get('/user/login', [PublicAuthController::class, 'showLogin']);
Route::post('/user/login', [PublicAuthController::class, 'login']);
Route::get('/register', [PublicAuthController::class, 'showRegister']);
Route::post('/register', [PublicAuthController::class, 'register']);
Route::post('/logout', [PublicAuthController::class, 'logout']);

// Simple admin news CRUD (session-based admin)
use App\Http\Controllers\SimpleAdminNewsController;
Route::prefix('admin')->group(function(){
    Route::get('manage-news', [SimpleAdminNewsController::class,'index']);
    Route::post('manage-news', [SimpleAdminNewsController::class,'store']);
    Route::get('manage-news/{news}/edit', [SimpleAdminNewsController::class,'edit']);
    Route::post('manage-news/{news}', [SimpleAdminNewsController::class,'update']);
    Route::post('manage-news/{news}/delete', [SimpleAdminNewsController::class,'destroy']);
    // Simple admin video management (session-based)
    Route::get('manage-videos', [\App\Http\Controllers\SimpleAdminVideosController::class,'index']);
    Route::get('manage-videos/create', [\App\Http\Controllers\SimpleAdminVideosController::class,'create']);
    Route::post('manage-videos', [\App\Http\Controllers\SimpleAdminVideosController::class,'store']);
    Route::get('manage-videos/{video}/edit', [\App\Http\Controllers\SimpleAdminVideosController::class,'edit']);
    Route::post('manage-videos/{video}', [\App\Http\Controllers\SimpleAdminVideosController::class,'update']);
    Route::post('manage-videos/{video}/delete', [\App\Http\Controllers\SimpleAdminVideosController::class,'destroy']);

    // Training management (separate CRUD)
    Route::get('manage-training', [SimpleAdminTrainingController::class,'index']);
    Route::get('manage-training/create', [SimpleAdminTrainingController::class,'create']);
    Route::post('manage-training', [SimpleAdminTrainingController::class,'store']);
    Route::get('manage-training/{video}/edit', [SimpleAdminTrainingController::class,'edit']);
    Route::post('manage-training/{video}', [SimpleAdminTrainingController::class,'update']);
    Route::post('manage-training/{video}/delete', [SimpleAdminTrainingController::class,'destroy']);
    // Simple admin cooperative resources (session-based)
    Route::get('manage-cooperative-resources', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'index']);
    Route::get('manage-cooperative-resources/create', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'create']);
    Route::post('manage-cooperative-resources', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'store']);
    Route::get('manage-cooperative-resources/{resource}/edit', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'edit']);
    Route::post('manage-cooperative-resources/{resource}', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'update']);
    Route::post('manage-cooperative-resources/{resource}/delete', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'destroy']);
});

Route::get('/cooperatives', [PublicController::class,'directory'])->name('cooperatives.directory');
// Public listing pages for Accomplishment Reports and Cooperative Resources
Route::get('/accomplishment-reports', [PublicController::class,'accomplishmentReports'])->name('accomplishment-reports.index');
Route::get('/cooperative-resources', [PublicController::class,'cooperativeResources'])->name('cooperative-resources.index');
Route::get('/accomplishment-reports/{report}/file', [PublicController::class,'accomplishmentReportFile'])->name('accomplishment-reports.file');
Route::get('/accomplishment-reports/{report}', [PublicController::class,'accomplishmentReportShow'])->name('accomplishment-reports.show');
// Cooperatives modal content (AJAX)
Route::get('/cooperatives/{cooperative}/modal', [PublicController::class,'profileModal'])->name('cooperatives.profile.modal');
// SLPA modal content (AJAX)
Route::get('/slpas/{slpa}/modal', [PublicController::class,'slpaModal'])->name('slpas.modal');
// Gallery listing and modal (public)
Route::get('/gallery', [PublicController::class, 'galleryIndex'])->name('gallery.index');
Route::get('/galleries/{gallery}/modal', [PublicController::class, 'galleryModal'])->name('galleries.modal');
// SLPA full profile page
Route::get('/slpas/{slpa}', [PublicController::class,'slpaShow'])->name('slpas.show');
Route::get('/cooperatives/search', [PublicController::class,'search'])->name('cooperatives.search');
Route::get('/cooperatives/{cooperative}', [PublicController::class,'profile'])->name('cooperatives.profile');

// Public access to cooperative resources (streamed via controller to avoid direct storage access issues)
Route::get('/cooperative-resources/{resource}/file', [PublicController::class, 'cooperativeResourceFile'])->name('cooperative-resources.file');
Route::get('/cooperative-resources/{resource}', [PublicController::class, 'cooperativeResourceShow'])->name('cooperative-resources.show');

// Public document delete (authenticated users with permission)
Route::post('/documents/{document}/delete', [\App\Http\Controllers\DocumentController::class, 'destroy'])->middleware('auth')->name('documents.delete');

// Video progress API endpoints (simple JSON endpoints used by training page)
Route::middleware('api')->group(function(){
    Route::get('/api/get-video-progress', [VideoProgressController::class, 'get']);
    Route::post('/api/save-video-progress', [VideoProgressController::class, 'save']);
    Route::post('/api/mark-video-complete', [VideoProgressController::class, 'complete']);
    Route::post('/api/send-completion-email', [VideoProgressController::class, 'sendCompletionEmail']);

    // Serve persistent store markers (from DB)
    Route::get('/api/store-locations', [StoreLocationController::class, 'apiList']);
});

Route::middleware(['auth','can:access-admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/', [AdminController::class,'dashboard'])->name('dashboard');
    // Allow full resourceful CRUD for cooperatives in the admin panel
    // AJAX search for cooperatives (real-time letter-by-letter search)
    Route::get('cooperatives/search', [CooperativeController::class, 'search'])->name('cooperatives.search');

    Route::resource('cooperatives', CooperativeController::class)->except(['show']);
    // Trashed cooperatives management
    Route::get('cooperatives/trashed', [CooperativeController::class, 'trashed'])->name('cooperatives.trashed');
    Route::post('cooperatives/{id}/restore', [CooperativeController::class, 'restore'])->name('cooperatives.restore');
    Route::delete('cooperatives/{id}/force', [CooperativeController::class, 'forceDelete'])->name('cooperatives.force_delete');
    // Delete a single gallery image from a cooperative profile
    Route::delete('cooperatives/{cooperative}/gallery', [CooperativeController::class, 'deleteGalleryImage'])->name('cooperatives.gallery.delete');
    // Enterprise resourceful CRUD (placeholder controller)
    Route::resource('enterprises', \App\Http\Controllers\Admin\EnterpriseController::class)->except(['show']);
    // Read-only overview for admins (separate from manage CRUD)
    Route::get('cooperatives/view', [CooperativeController::class, 'overview'])->name('cooperatives.view');
    Route::post('cooperatives/import-default', [CooperativeController::class,'importDefault'])->name('cooperatives.import_default');
    // (removed) Update cooperative-specific directory card content from profile (admin)
    // Document upload route for admins and cooperative admins
    Route::post('documents', [\App\Http\Controllers\DocumentController::class,'store'])->name('documents.store');
    Route::get('documents/{document}/download', [\App\Http\Controllers\DocumentController::class,'download'])->name('documents.download');
    // Cooperative membership management
    Route::post('cooperatives/{cooperative}/members', [\App\Http\Controllers\CooperativeMemberController::class,'store'])->name('cooperatives.members.store');
    Route::delete('cooperatives/{cooperative}/members/{user}', [\App\Http\Controllers\CooperativeMemberController::class,'destroy'])->name('cooperatives.members.destroy');
    // News management
    Route::get('news', [\App\Http\Controllers\NewsController::class,'index'])->name('news.index');
    Route::post('news', [\App\Http\Controllers\NewsController::class,'store'])->name('news.store');
    Route::delete('news/{news}', [\App\Http\Controllers\NewsController::class,'destroy'])->name('news.destroy');

    // User management
    Route::get('users', [\App\Http\Controllers\UserController::class,'index'])->name('users.index');
    // Create / store new admin users
    Route::get('users/create', [\App\Http\Controllers\UserController::class,'create'])->name('users.create');
    Route::post('users', [\App\Http\Controllers\UserController::class,'store'])->name('users.store');
    Route::post('users/{user}/role', [\App\Http\Controllers\UserController::class,'updateRole'])->name('users.updateRole');

    // Admin memorandum CRUD
    Route::resource('memorandums', \App\Http\Controllers\Admin\MemorandumController::class)->except(['show']);
    // Import enterprises (temporary CSV importer)
    Route::get('enterprises/import', [\App\Http\Controllers\Admin\EnterpriseController::class,'importForm'])->name('enterprises.import');
    Route::post('enterprises/import', [\App\Http\Controllers\Admin\EnterpriseController::class,'importProcess'])->name('enterprises.import.process');
    // Bulk delete enterprises (server-side)
    Route::post('enterprises/bulk-delete', [\App\Http\Controllers\Admin\EnterpriseController::class,'bulkDelete'])->name('enterprises.bulk_delete');
    // Admin accomplishment reports CRUD
    Route::resource('accomplishment-reports', \App\Http\Controllers\Admin\AccomplishmentReportController::class)->except(['show']);
    // Cooperative resources (PPT/PDF) management
    Route::resource('cooperative-resources', \App\Http\Controllers\Admin\CooperativeResourceController::class)->except(['show']);
    // Livelihood cards management (admin)
    Route::resource('livelihood', \App\Http\Controllers\Admin\LivelihoodController::class)->except(['show']);
    // Gallery management (admin)
    Route::resource('galleries', \App\Http\Controllers\Admin\GalleryController::class)->except(['show']);
    // Select list items (dropdown options) management
    Route::resource('select_lists', \App\Http\Controllers\Admin\SelectListController::class)->except(['show']);
    // Per-cooperative resource management (create/edit/update/destroy bound to a cooperative)
    Route::post('cooperatives/{cooperative}/resources', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'storeForCooperative'])->name('cooperatives.resources.store');
    Route::get('cooperatives/{cooperative}/resources/{resource}/edit', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'editForCooperative'])->name('cooperatives.resources.edit');
    Route::put('cooperatives/{cooperative}/resources/{resource}', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'updateForCooperative'])->name('cooperatives.resources.update');
    Route::delete('cooperatives/{cooperative}/resources/{resource}', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'destroyForCooperative'])->name('cooperatives.resources.destroy');
    // Store locations admin CRUD
    // Hidden CABS MAIN Excel import (not linked anywhere; visit URL directly)
    Route::get('store_locations/cabs-main-import', [StoreLocationController::class, 'cabsImportForm'])->name('store_locations.cabs_import_form');
    Route::post('store_locations/cabs-main-import', [StoreLocationController::class, 'cabsImportProcess'])->name('store_locations.cabs_import');
    Route::resource('store_locations', \App\Http\Controllers\StoreLocationController::class)->except(['show']);
    // Manage store categories items (simple JSON-backed)
    Route::get('store-categories/create', [StoreCategoryController::class, 'create'])->name('store_categories.create');
    Route::post('store-categories', [StoreCategoryController::class, 'storeCategory'])->name('store_categories.store');
    Route::post('store-categories/delete', [StoreCategoryController::class, 'deleteCategory'])->name('store_categories.delete');
    Route::post('store-categories/items', [StoreCategoryController::class, 'storeItem'])->name('store_categories.items.store');
    Route::post('store-categories/items/delete', [StoreCategoryController::class, 'deleteItem'])->name('store_categories.items.delete');
    Route::post('store-categories/items/update', [StoreCategoryController::class, 'updateItem'])->name('store_categories.items.update');
    Route::post('store-categories/update', [StoreCategoryController::class, 'updateCategory'])->name('store_categories.update');
});
