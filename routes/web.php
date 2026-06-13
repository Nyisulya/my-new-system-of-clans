<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MemberController;
use App\Http\Controllers\Web\ClanController;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
});

// Authentication routes
Auth::routes();

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/members', [DashboardController::class, 'getCategoryMembers'])->name('dashboard.members');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    
    // Member Management
    Route::get('members/{member}/dashboard', [MemberController::class, 'dashboard'])->name('members.dashboard');
    Route::get('parents', [MemberController::class, 'parents'])->name('parents.index');
    Route::resource('members', MemberController::class);
    
    // Clan Management
    Route::resource('clans', ClanController::class);
    
    // Family Management
    Route::get('families/{family}/parents', [\App\Http\Controllers\Web\FamilyController::class, 'parents'])->name('families.parents');
    Route::get('families/{family}/tree', [\App\Http\Controllers\Web\FamilyController::class, 'tree'])->name('families.tree');
    Route::get('families/{family}/founder', [\App\Http\Controllers\Web\FamilyController::class, 'founder'])->name('families.founder');
    Route::get('families/{family}/tree-dynamic', [\App\Http\Controllers\Web\FamilyController::class, 'dynamicTree'])->name('families.tree_dynamic');
    Route::resource('families', \App\Http\Controllers\Web\FamilyController::class);

    
    // Branch Management
    Route::resource('branches', \App\Http\Controllers\Web\BranchController::class);

    // Relationship Calculator
    Route::get('relationships', [\App\Http\Controllers\Web\RelationshipController::class, 'index'])->name('relationships.index');
    Route::post('relationships/calculate', [\App\Http\Controllers\Web\RelationshipController::class, 'calculate'])->name('relationships.calculate');

    // Timeline
    Route::get('timeline', [\App\Http\Controllers\Web\TimelineController::class, 'index'])->name('timeline.index');

    // Map
    Route::get('map', [\App\Http\Controllers\Web\MapController::class, 'index'])->name('maps.index');

    // GEDCOM
    Route::get('gedcom', [\App\Http\Controllers\Web\GedcomController::class, 'index'])->name('gedcom.index');
    Route::post('gedcom/export', [\App\Http\Controllers\Web\GedcomController::class, 'export'])->name('gedcom.export');
    Route::post('gedcom/import', [\App\Http\Controllers\Web\GedcomController::class, 'import'])->name('gedcom.import');

    // Calendar
    Route::get('calendar', [\App\Http\Controllers\Web\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/export', [\App\Http\Controllers\Web\CalendarController::class, 'export'])->name('calendar.export');

    // Announcements
    Route::get('announcements/feed', [\App\Http\Controllers\Web\AnnouncementController::class, 'feed'])->name('announcements.feed');
    Route::resource('announcements', \App\Http\Controllers\Web\AnnouncementController::class);

    // Contributions
    Route::post('contributions/pay', [\App\Http\Controllers\Web\ContributionController::class, 'pay'])->name('contributions.pay');
    Route::resource('campaigns', \App\Http\Controllers\Web\CampaignController::class);
    Route::resource('contributions', \App\Http\Controllers\Web\ContributionController::class)->only(['create', 'store']);

    // Notifications
    Route::get('notifications', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}/mark-read', [\App\Http\Controllers\Web\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::get('notifications/mark-all-read', [\App\Http\Controllers\Web\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Galleries
    Route::resource('galleries', \App\Http\Controllers\Web\GalleryController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('galleries/{gallery}/upload-photos', [\App\Http\Controllers\Web\GalleryController::class, 'uploadPhotos'])->name('galleries.upload-photos');
    Route::delete('galleries/photos/{id}', [\App\Http\Controllers\Web\GalleryController::class, 'deletePhoto'])->name('galleries.delete-photo');

    // Language
    Route::get('language/{locale}', [\App\Http\Controllers\Web\LanguageController::class, 'switch'])->name('language.switch');
    Route::get('test-language', function() {
        return view('test-language');
    })->name('test.language');
});
