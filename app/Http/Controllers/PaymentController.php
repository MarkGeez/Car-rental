<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, Rental $rental): View
    {
        $this->ownedRental($request, $rental);
        $rental->load(['vehicle.vendor', 'payment']);

        return view('user.payment', compact('rental'));
    }

    public function store(Request $request, Rental $rental): RedirectResponse
    {
        $this->ownedRental($request, $rental);
        $rental->load('vehicle.vendor');

        abort_if($rental->status === 'cancelled', 422, 'Cancelled rentals cannot be paid.');
        abort_unless($rental->vehicle->vendor->payment_qr_code, 422, 'This vendor has not uploaded a payment QR code yet.');

        $data = $request->validate([
            'payment_method' => ['required', 'in:gcash'],
            'proof' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $payment = $rental->payment()->firstOrNew();
        abort_if($payment->payment_status === 'paid', 422, 'This rental has already been paid.');

        if ($payment->proof_path) {
            Storage::disk('public')->delete($payment->proof_path);
        }

        $payment->fill([
            'amount' => $rental->total_amount,
            'payment_method' => $data['payment_method'],
            'transaction_reference' => $payment->transaction_reference ?: (string) Str::uuid(),
            'proof_path' => $request->file('proof')->store('payment-proofs', 'public'),
            'payment_status' => 'pending',
            'paid_at' => null,
            'reviewed_at' => null,
            'refunded_at' => null,
        ])->save();

        return redirect()->route('user.rentals.show', $rental)->with('success', 'Payment proof submitted for vendor review.');
    }

    private function ownedRental(Request $request, Rental $rental): Rental
    {
        abort_unless($rental->customer_id === $request->user()->id, 404);

        return $rental;
    }
}
