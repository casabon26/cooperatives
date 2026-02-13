<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CooperativeController;
use App\Http\Controllers\SimpleAdminAuthController;
use App\Http\Controllers\StoreLocationController;
use App\Http\Controllers\StoreCategoryController;

Route::get('/', [PublicController::class, 'home']);

// Simple static pages (placeholders to be filled later)
Route::view('/about', 'pages.about')->name('about');
Route::view('/faqs', 'pages.faqs')->name('faqs');
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

// Provide a generic named `login` route so Laravel's `auth` middleware can redirect unauthenticated users.
Route::get('/login', [SimpleAdminAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [SimpleAdminAuthController::class, 'login']);

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
    Route::post('manage-videos', [\App\Http\Controllers\SimpleAdminVideosController::class,'store']);
    Route::get('manage-videos/{video}/edit', [\App\Http\Controllers\SimpleAdminVideosController::class,'edit']);
    Route::post('manage-videos/{video}', [\App\Http\Controllers\SimpleAdminVideosController::class,'update']);
    Route::post('manage-videos/{video}/delete', [\App\Http\Controllers\SimpleAdminVideosController::class,'destroy']);
    // Simple admin cooperative resources (session-based)
    Route::get('manage-cooperative-resources', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'index']);
    Route::get('manage-cooperative-resources/create', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'create']);
    Route::post('manage-cooperative-resources', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'store']);
    Route::get('manage-cooperative-resources/{resource}/edit', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'edit']);
    Route::post('manage-cooperative-resources/{resource}', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'update']);
    Route::post('manage-cooperative-resources/{resource}/delete', [\App\Http\Controllers\Admin\CooperativeResourceController::class,'destroy']);
});

Route::get('/cooperatives', [PublicController::class,'directory'])->name('cooperatives.directory');
// Cooperatives modal content (AJAX)
Route::get('/cooperatives/{cooperative}/modal', [PublicController::class,'profileModal'])->name('cooperatives.profile.modal');
Route::get('/cooperatives/search', [PublicController::class,'search'])->name('cooperatives.search');
Route::get('/cooperatives/{cooperative}', [PublicController::class,'profile'])->name('cooperatives.profile');

// Public access to cooperative resources (streamed via controller to avoid direct storage access issues)
Route::get('/cooperative-resources/{resource}/file', [PublicController::class, 'cooperativeResourceFile'])->name('cooperative-resources.file');
Route::get('/cooperative-resources/{resource}', [PublicController::class, 'cooperativeResourceShow'])->name('cooperative-resources.show');

// Serve persistent store markers (from DB)
Route::get('/api/store-locations', [StoreLocationController::class, 'apiList']);

Route::middleware(['auth','can:access-admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/', [AdminController::class,'dashboard'])->name('dashboard');
    // Allow full resourceful CRUD for cooperatives in the admin panel
    Route::resource('cooperatives', CooperativeController::class)->except(['show']);
    // Enterprise resourceful CRUD (placeholder controller)
    Route::resource('enterprises', \App\Http\Controllers\Admin\EnterpriseController::class)->except(['show']);
    // Read-only overview for admins (separate from manage CRUD)
    Route::get('cooperatives/view', [CooperativeController::class, 'overview'])->name('cooperatives.view');
    Route::post('cooperatives/import-default', [CooperativeController::class,'importDefault'])->name('cooperatives.import_default');
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
    Route::post('users/{user}/role', [\App\Http\Controllers\UserController::class,'updateRole'])->name('users.updateRole');

    // Admin memorandum CRUD
    Route::resource('memorandums', \App\Http\Controllers\Admin\MemorandumController::class)->except(['show']);
    // Admin accomplishment reports CRUD
    Route::resource('accomplishment-reports', \App\Http\Controllers\Admin\AccomplishmentReportController::class)->except(['show']);
    // Cooperative resources (PPT/PDF) management
    Route::resource('cooperative-resources', \App\Http\Controllers\Admin\CooperativeResourceController::class)->except(['show']);
    // Store locations admin CRUD
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
