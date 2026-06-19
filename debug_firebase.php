<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Channels\FcmChannel;
use App\Notifications\NewAnnouncementNotification;
use App\Models\Announcement;

echo "=== Firebase Cloud Messaging (FCM) Debug Tool ===\n";

// 1. Check Env Configurations
$apiKey = config('services.fcm.api_key');
$projectId = config('services.fcm.project_id');
$credentialsPath = env('FIREBASE_CREDENTIALS');

echo "FCM API Key: " . ($apiKey ? "Configured (Starts with " . substr($apiKey, 0, 5) . "...)" : "NOT CONFIGURED") . "\n";
echo "FCM Project ID: " . ($projectId ?: "NOT CONFIGURED") . "\n";
echo "FIREBASE_CREDENTIALS Path: " . ($credentialsPath ?: "NOT CONFIGURED") . "\n";

if ($credentialsPath) {
    $fullPath = file_exists($credentialsPath) ? $credentialsPath : (file_exists(storage_path($credentialsPath)) ? storage_path($credentialsPath) : null);
    if ($fullPath) {
        echo "Service Account JSON File: Found at " . $fullPath . "\n";
        $json = json_decode(file_get_contents($fullPath), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "Service Account JSON: Valid JSON format. Project ID inside JSON: " . ($json['project_id'] ?? 'N/A') . "\n";
        } else {
            echo "Service Account JSON: INVALID JSON FORMAT!\n";
        }
    } else {
        echo "Service Account JSON File: NOT FOUND on the system! Searched: " . $credentialsPath . " and " . storage_path($credentialsPath) . "\n";
    }
}

echo "\n--- Database Check ---\n";
$usersWithTokens = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->get();
echo "Total Users with FCM Tokens in Database: " . $usersWithTokens->count() . "\n";

if ($usersWithTokens->count() === 0) {
    echo "No users found with an FCM token. Please log in on your phone/browser so that the token is stored.\n";
    exit;
}

foreach ($usersWithTokens as $user) {
    echo "- User ID: {$user->id}, Name: {$user->name}, Token: " . substr($user->fcm_token, 0, 15) . "...\n";
}

echo "\n--- Sending Test Push Notification ---\n";
// Grab the first user
$testUser = $usersWithTokens->first();
echo "Attempting to send a test notification to User: {$testUser->name} (ID: {$testUser->id})\n";

// Create a dummy Announcement for testing
$dummyAnnouncement = new Announcement([
    'title' => 'Test Tangazo la Majaribio',
    'content' => 'Huu ni ujumbe wa majaribio kuhakiki kama Push Notification inafanya kazi kwenye simu.',
    'type' => 'info',
    'start_date' => now(),
]);
$dummyAnnouncement->id = 9999; // Mock ID

try {
    $channel = new FcmChannel();
    $notification = new NewAnnouncementNotification($dummyAnnouncement);
    
    echo "Calling FcmChannel::send...\n";
    $channel->send($testUser, $notification);
    echo "Process finished. Check if any errors were printed above, and check your phone!\n";
} catch (\Exception $e) {
    echo "EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . " in " . $e->getFile() . "\n";
}
