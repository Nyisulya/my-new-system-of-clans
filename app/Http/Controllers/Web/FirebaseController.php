<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FirebaseController extends Controller
{
    /**
     * Update the FCM token for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
        ]);

        try {
            $user = auth()->user();
            $user->fcm_token = $validated['fcm_token'];
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'FCM Token updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('FCM Token update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update FCM Token.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
