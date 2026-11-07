<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RentalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $customerRentals = $request->user()->rentals();
        $today = now()->startOfDay();

        return view('user.dashboard', [
            'activeRentals' => (clone $customerRentals)
                ->whereIn('status', ['approved', 'ongoing'])
                ->count(),
            'upcomingBookings' => (clone $customerRentals)
                ->whereIn('status', ['pending', 'approved'])
                ->whereDate('pickup_date', '>=', $today)
                ->count(),
            'completedRentals' => (clone $customerRentals)
                ->where('status', 'completed')
                ->count(),
            'recentRentals' => (clone $customerRentals)
                ->with('vehicle')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $rentals = $request->user()
            ->rentals()
            ->with(['vehicle', 'payment', 'review'])
            ->latest()
            ->paginate(20);

        return view('user.rentals.index', compact('rentals'));
    }

    public function show(Request $request, Rental $rental): View
    {
        abort_unless($rental->customer_id === $request->user()->id, 404);

        $rental->load(['vehicle', 'payment', 'review']);

        return view('user.rentals.show', compact('rental'));
    }

    public function store(Request $request, Vehicle $vehicle): RedirectResponse
    {
        abort_unless(
            $vehicle->status === 'available'
                && $vehicle->vendor()->where('verification_status', 'verified')->exists(),
            404,
        );

        $data = $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:pickup_date'],
        ]);

        $pickupDate = Carbon::parse($data['pickup_date']);
        $returnDate = Carbon::parse($data['return_date']);
        $rentalDays = $pickupDate->diffInDays($returnDate) + 1;

        $hasConflict = $vehicle->rentals()
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->whereDate('pickup_date', '<=', $data['return_date'])
            ->whereDate('return_date', '>=', $data['pickup_date'])
            ->exists();

        if ($hasConflict) {
            return back()
                ->withInput()
                ->withErrors(['pickup_date' => 'This vehicle is already booked for the selected dates.']);
        }

        $rental = Rental::create([
            'customer_id' => $request->user()->id,
            'vehicle_id' => $vehicle->id,
            'pickup_date' => $data['pickup_date'],
            'return_date' => $data['return_date'],
            'rental_days' => $rentalDays,
            'daily_price' => $vehicle->daily_price,
            'total_amount' => $vehicle->daily_price * $rentalDays,
            'commission_amount' => $vehicle->daily_price * $rentalDays * 0.1,
            'vendor_amount' => $vehicle->daily_price * $rentalDays * 0.9,
            'status' => 'pending',
        ]);

        return redirect("/user/rentals/{$rental->id}")
            ->with('success', 'Your booking request has been submitted.');
    }
}
