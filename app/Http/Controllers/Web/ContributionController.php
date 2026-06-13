<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Campaign;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    public function create(Request $request)
    {
        $campaign = null;
        if ($request->has('campaign_id')) {
            $campaign = Campaign::findOrFail($request->campaign_id);
        }
        
        $campaigns = Campaign::where('status', 'active')->get();
        $members = Member::orderBy('first_name')->get();

        return view('contributions.create', compact('campaign', 'campaigns', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'member_id' => 'required|exists:members,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Contribution::create([
            'campaign_id' => $request->campaign_id,
            'member_id' => $request->member_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'method' => $request->method,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('campaigns.show', $request->campaign_id)
            ->with('success', 'Contribution recorded successfully.');
    }

    public function pay(Request $request, \App\Services\PaymentService $paymentService)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'campaign_id' => 'required|exists:campaigns,id',
            'member_id' => 'required|exists:members,id',
        ]);

        // 1. Initiate Payment (Real API)
        $result = $paymentService->initiatePayment($request->phone, $request->amount);

        if ($result['success']) {
            // 2. Create Contribution Record (Pending)
            // Note: In a real production app, you would wait for the callback.
            // For localhost, we assume success if the push was sent, but mark as "Pending Confirmation"
            Contribution::create([
                'campaign_id' => $request->campaign_id,
                'member_id' => $request->member_id,
                'amount' => $request->amount,
                'date' => now(),
                'method' => 'Mobile Money',
                'notes' => 'STK Push Sent to ' . $request->phone . '. CheckoutID: ' . $result['transaction_id'],
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request sent! Please enter your PIN to complete the transaction.',
                'redirect_url' => route('campaigns.show', $request->campaign_id)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }
}
