<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'vendorCount' => Vendor::count(),
            'vehicleCount' => Vehicle::count(),
            'commissionEarnings' => Rental::sum('commission_amount'),
        ]);
    }

    public function vendors(Request $request): View
    {
        $status = $request->query('status');

        abort_unless($status === null || in_array($status, ['pending', 'verified', 'rejected'], true), 404);

        $vendors = Vendor::query()
            ->with('user')
            ->when($status, fn ($query) => $query->where('verification_status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.vendors.index', compact('vendors'));
    }

    public function showVendor(Vendor $vendor): View
    {
        return view('admin.vendors.show', compact('vendor'));
    }

    public function verifyVendor(Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);

        return back()->with('success', "{$vendor->business_name} has been verified.");
    }

    public function rejectVendor(Vendor $vendor): RedirectResponse
    {
        $vendor->update([
            'verification_status' => 'rejected',
            'verified_at' => null,
        ]);

        return back()->with('success', "{$vendor->business_name} has been rejected.");
    }

    public function users(Request $request): View
    {
        $users = User::query()
            ->when($request->query('role'), fn ($query, $role) => $query->where('role', $role))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function updateUserStatus(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        if ($user->is($request->user()) && $data['status'] === 'suspended') {
            return back()->withErrors(['status' => 'You cannot suspend your own admin account.']);
        }

        $user->update(['status' => $data['status']]);

        return back()->with('success', "{$user->name}'s account is now {$data['status']}.");
    }

    public function vehicles(): View
    {
        $vehicles = Vehicle::query()
            ->with(['images', 'vendor'])
            ->latest()
            ->paginate(18);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function rentals(): View
    {
        $rentals = Rental::query()
            ->with(['customer', 'vehicle', 'payment'])
            ->latest()
            ->paginate(20);

        return view('admin.rentals.index', compact('rentals'));
    }

    public function showRental(Rental $rental): View
    {
        $rental->load(['customer', 'vehicle.vendor', 'payment']);

        return view('admin.rentals.show', compact('rental'));
    }
}
