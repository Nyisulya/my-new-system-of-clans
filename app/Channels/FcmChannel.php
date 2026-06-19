<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        // Get the device token stored on the user
        $token = $notifiable->fcm_token;

        if (!$token) {
            return;
        }

        // Get the FCM payload formatted by the notification class
        if (!method_exists($notification, 'toFcm')) {
            Log::warning("Notification does not have a toFcm method: " . get_class($notification));
            return;
        }

        $fcmData = $notification->toFcm($notifiable);

        if (!$fcmData || !isset($fcmData['title']) || !isset($fcmData['body'])) {
            return;
        }

        try {
            // Locate the service account json file path from environment variables
            $credentialsPath = env('FIREBASE_CREDENTIALS');
            
            if (!$credentialsPath || !file_exists($credentialsPath)) {
                // If it is a relative path in env, check storage folder
                if ($credentialsPath && file_exists(storage_path($credentialsPath))) {
                    $credentialsPath = storage_path($credentialsPath);
                } else {
                    Log::error("Firebase credentials file not found. Path configured: " . ($credentialsPath ?? 'none'));
                    return;
                }
            }

            // Initialize the Firebase Factory
            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $messaging = $factory->createMessaging();

            // Build the FCM CloudMessage
            $message = CloudMessage::new()
                ->withToken($token)
                ->withNotification(FirebaseNotification::create(
                    $fcmData['title'],
                    $fcmData['body']
                ))
                ->withData($fcmData['data'] ?? []);

            // Send the notification via Firebase Messaging
            $messaging->send($message);
        } catch (\Exception $e) {
            Log::error('FCM dispatch failed: ' . $e->getMessage());
        }
    }
}
