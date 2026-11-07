<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorRentalController extends Controller
{
    public function index(Request $request): View
    {
        $vendor = $this->verifiedVendor($request);
        $rentals = Rental::query()
            ->whereHas('vehicle', fn ($query) => $query->whereBelongsTo($vendor))
            ->with(['customer', 'vehicle', 'payment'])
            ->latest()
            ->paginate(20);

        return view('vendor.rentals.index', compact('rentals'));
    }

    public function show(Request $request, Rental $rental): View
    {
        $this->verifiedVendor($request);
        $this->ownedRental($request, $rental)->load(['customer', 'vehicle', 'payment']);

        return view('vendor.rentals.show', compact('rental'));
    }

    public function approve(Request $request, Rental $rental): RedirectResponse
    {
        $this->verifiedVendor($request);
        $rental = $this->ownedRental($request, $rental);
        $rental->load('payment');

        DB::transaction(function () use ($rental): void {
            $lockedRental = Rental::query()->lockForUpdate()->findOrFail($rental->id);
            $lockedRental->load('payment');
            abort_unless($lockedRental->payment?->payment_status === 'paid', 422, 'The payment must be marked as paid before the rental can be approved.');
            abort_unless($lockedRental->status === 'pending', 422, 'Only pending rentals can be approved.');

            $hasConflict = Rental::query()
                ->where('vehicle_id', $lockedRental->vehicle_id)
                ->whereIn('status', ['approved', 'ongoing'])
                ->whereDate('pickup_date', '<=', $lockedRental->return_date)
                ->whereDate('return_date', '>=', $lockedRental->pickup_date)
                ->lockForUpdate()
                ->exists();

            abort_if($hasConflict, 422, 'This vehicle is already booked for the selected dates.');
            $lockedRental->update(['status' => 'approved']);
        });

        return back()->with('success', 'Rental request approved.');
    }

    public function reject(Request $request, Rental $rental): RedirectResponse
    {
        $this->verifiedVendor($request);
        $rental = $this->ownedRental($request, $rental);
        abort_unless($rental->status === 'pending', 422, 'Only pending rentals can be rejected.');
        $rental->update(['status' => 'cancelled']);

        return back()->with('success', 'Rental request rejected.');
    }

    public function start(Request $request, Rental $rental): RedirectResponse
    {
        $this->verifiedVendor($request);
        $rental = $this->ownedRental($request, $rental);
        abort_unless($rental->status === 'approved', 422, 'Only approved rentals can be started.');
        $rental->update(['status' => 'ongoing']);

        return back()->with('success', 'Rental marked as ongoing.');
    }

    public function complete(Request $request, Rental $rental): RedirectResponse
    {
        $this->verifiedVendor($request);
        $rental = $this->ownedRental($request, $rental);
        abort_unless($rental->status === 'ongoing', 422, 'Only ongoing rentals can be completed.');
        $rental->update(['status' => 'completed']);

        return back()->with('success', 'Rental marked as completed.');
    }

    public function markPaymentPaid(Request $request, Rental $rental): RedirectResponse
    {
        $this->verifiedVendor($request);
        $rental = $this->ownedRental($request, $rental);

        DB::transaction(function () use ($rental): void {
            $lockedRental = Rental::query()->lockForUpdate()->findOrFail($rental->id);
            $payment = $lockedRental->payment()->lockForUpdate()->firstOrFail();
            abort_unless($payment->payment_status === 'pending', 422, 'Only pending payments can be marked as paid.');

            $hasPaidConflict = Rental::query()
                ->where('vehicle_id', $lockedRental->vehicle_id)
                ->whereKeyNot($lockedRental->id)
                ->whereIn('status', ['approved', 'ongoing'])
                ->whereDate('pickup_date', '<=', $lockedRental->return_date)
                ->whereDate('return_date', '>=', $lockedRental->pickup_date)
                ->lockForUpdate()
                ->exists();

            abort_if($hasPaidConflict, 422, 'Another payment already reserved this vehicle for the selected dates. Refund this duplicate payment instead.');

            $payment->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'reviewed_at' => now(),
            ]);
            $lockedRental->update(['status' => 'approved']);
        });

        return back()->with('success', 'Payment marked as paid and rental reserved.');
    }

    public function refundPayment(Request $request, Rental $rental): RedirectResponse
    {
        $this->verifiedVendor($request);
        $rental = $this->ownedRental($request, $rental);

        DB::transaction(function () use ($rental): void {
            $lockedRental = Rental::query()->lockForUpdate()->findOrFail($rental->id);
            $payment = $lockedRental->payment()->lockForUpdate()->firstOrFail();
            abort_unless($payment->payment_status === 'pending', 422, 'Only pending payments can be refunded.');

            $payment->update([
                'payment_status' => 'refunded',
                'reviewed_at' => now(),
                'refunded_at' => now(),
            ]);
            $lockedRental->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Duplicate payment marked for refund and rental cancelled.');
    }

    public function earnings(Request $request): View
    {
        $vendor = $this->verifiedVendor($request);
        $rentals = Rental::query()
            ->whereHas('vehicle', fn ($query) => $query->whereBelongsTo($vendor))
            ->where('status', 'completed')
            ->with(['customer', 'vehicle'])
            ->latest()
            ->paginate(20);

        return view('vendor.earnings', [
            'rentals' => $rentals,
            'totalEarnings' => Rental::query()
                ->whereHas('vehicle', fn ($query) => $query->whereBelongsTo($vendor))
                ->where('status', 'completed')
                ->sum('vendor_amount'),
        ]);
    }

    private function verifiedVendor(Request $request): Vendor
    {
        $vendor = $request->user()->vendor;
        abort_unless($vendor && $vendor->verification_status === 'verified', 403, 'Your business must be verified first.');

        return $vendor;
    }

    private function ownedRental(Request $request, Rental $rental): Rental
    {
        abort_unless($rental->vehicle()->whereBelongsTo($request->user()->vendor)->exists(), 404);

        return $rental;
    }
}
