<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\MemberController;
use App\Http\Controllers\Web\ClanController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\ProfileClaimController;
use App\Http\Controllers\Web\FirebaseController;
use Kreait\Firebase\Factory;
// Root landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/test-firebase', function () {
    try {
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));
        $messaging = $factory->createMessaging();
        return "Hongera! Firebase imeunganishwa vizuri na Laravel.";
    } catch (\Exception $e) {
        return "Kuna tatizo: " . $e->getMessage();
    }
});
// Language switcher (public so guests can switch languages on the landing page)
Route::get('language/{locale}', [\App\Http\Controllers\Web\LanguageController::class, 'switch'])->name('language.switch');

// Authentication routes
Auth::routes();

// Public Routes for SEO
Route::get('announcements', [\App\Http\Controllers\Web\AnnouncementController::class, 'index'])->name('announcements.index');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/members', [DashboardController::class, 'getCategoryMembers'])->name('dashboard.members');
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    
    // Profile Claiming
    Route::get('/profile/claim', [ProfileClaimController::class, 'showSearchForm'])->name('profile.claim.search');
    Route::get('/profile/claim/search', [ProfileClaimController::class, 'search'])->name('profile.claim.ajax_search');
    Route::post('/profile/claim', [ProfileClaimController::class, 'claim'])->name('profile.claim.submit');
    
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

    

    // Timeline
    Route::get('timeline', [\App\Http\Controllers\Web\TimelineController::class, 'index'])->name('timeline.index');

    // Map
    Route::get('map', [\App\Http\Controllers\Web\MapController::class, 'index'])->name('maps.index');

    // Relationship Calculator
    Route::get('relationships', [\App\Http\Controllers\Web\RelationshipController::class, 'index'])->name('relationships.index');
    Route::post('relationships/calculate', [\App\Http\Controllers\Web\RelationshipController::class, 'calculate'])->name('relationships.calculate');


    // Calendar
    Route::get('calendar', [\App\Http\Controllers\Web\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/export', [\App\Http\Controllers\Web\CalendarController::class, 'export'])->name('calendar.export');

    // Announcements & Posts
    Route::get('announcements/feed', [\App\Http\Controllers\Web\AnnouncementController::class, 'feed'])->name('announcements.feed');
    Route::resource('announcements', \App\Http\Controllers\Web\AnnouncementController::class)->except(['index']);

    // Posts (Timeline)
    Route::get('posts', [\App\Http\Controllers\Web\PostController::class, 'index'])->name('posts.index');
    Route::post('posts', [\App\Http\Controllers\Web\PostController::class, 'store'])->name('posts.store');
    Route::delete('posts/{post}', [\App\Http\Controllers\Web\PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('posts/{post}/like', [\App\Http\Controllers\Web\PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('posts/{post}/comment', [\App\Http\Controllers\Web\PostController::class, 'storeComment'])->name('posts.comment');

    // Contributions
    Route::post('contributions/pay', [\App\Http\Controllers\Web\ContributionController::class, 'pay'])->name('contributions.pay');
    Route::resource('campaigns', \App\Http\Controllers\Web\CampaignController::class);
    Route::resource('contributions', \App\Http\Controllers\Web\ContributionController::class)->only(['create', 'store']);

    // Notifications
    Route::post('fcm-token', [FirebaseController::class, 'updateFcmToken'])->name('fcm.token');
    Route::get('notifications', [\App\Http\Controllers\Web\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{id}/mark-read', [\App\Http\Controllers\Web\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::get('notifications/mark-all-read', [\App\Http\Controllers\Web\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Galleries
    Route::post('galleries/upload-photos', [\App\Http\Controllers\Web\GalleryController::class, 'uploadPhotosGeneral'])->name('galleries.upload-photos-general');
    Route::resource('galleries', \App\Http\Controllers\Web\GalleryController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::post('galleries/{gallery}/upload-photos', [\App\Http\Controllers\Web\GalleryController::class, 'uploadPhotos'])->name('galleries.upload-photos');
    Route::delete('galleries/photos/{id}', [\App\Http\Controllers\Web\GalleryController::class, 'deletePhoto'])->name('galleries.delete-photo');

    // Language
    Route::get('test-language', function() {
        return view('test-language');
    })->name('test.language');

    // Admin only routes
    Route::middleware(['can:admin-only'])->group(function () {
        Route::get('users', [\App\Http\Controllers\Web\UserController::class, 'index'])->name('admin.users');
        Route::delete('users/{user}', [\App\Http\Controllers\Web\UserController::class, 'destroy'])->name('admin.users.destroy');
    });
});
