<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function dashboard(Request $request): View
    {
        $vendor = $request->user()->vendor;
        abort_unless($vendor, 404);

        $vehicleQuery = $vendor->vehicles();
        $rentalQuery = Rental::whereHas('vehicle', fn ($query) => $query->whereBelongsTo($vendor));

        return view('vendor.dashboard', [
            'vehicleCount' => $vehicleQuery->count(),
            'pendingRentals' => (clone $rentalQuery)->where('status', 'pending')->count(),
            'ongoingRentals' => (clone $rentalQuery)->where('status', 'ongoing')->count(),
            'totalEarnings' => (clone $rentalQuery)->where('status', 'completed')->sum('vendor_amount'),
        ]);
    }

    public function show(Request $request): View
    {
        $vendor = $request->user()->vendor;

        if (! $vendor) {
            return view('vendor.profile.create');
        }

        if ($vendor->verification_status === 'pending') {
            return view('vendor.profile.pending', compact('vendor'));
        }

        return view('vendor.profile.edit', compact('vendor'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->vendor, 409, 'A business profile already exists.');

        $data = $this->validatedProfile($request, true);
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('business_permit')) {
            $data['business_permit'] = $request->file('business_permit')->store('business-permits', 'public');
        }
        if ($request->hasFile('payment_qr_code')) {
            $data['payment_qr_code'] = $request->file('payment_qr_code')->store('payment-qr-codes', 'public');
        }

        Vendor::create($data);

        return redirect()->route('vendor.me')->with('success', 'Business profile submitted for verification.');
    }

    public function update(Request $request): RedirectResponse
    {
        $vendor = $request->user()->vendor;
        abort_if(! $vendor, 404);

        $data = $this->validatedProfile($request);
        if ($request->hasFile('business_permit')) {
            if ($vendor->business_permit) {
                Storage::disk('public')->delete($vendor->business_permit);
            }
            $data['business_permit'] = $request->file('business_permit')->store('business-permits', 'public');
        }
        if ($request->hasFile('payment_qr_code')) {
            if ($vendor->payment_qr_code) {
                Storage::disk('public')->delete($vendor->payment_qr_code);
            }
            $data['payment_qr_code'] = $request->file('payment_qr_code')->store('payment-qr-codes', 'public');
        }

        if ($vendor->verification_status === 'rejected') {
            $data['verification_status'] = 'pending';
            $data['verified_at'] = null;
        }

        $vendor->update($data);

        return redirect()->route('vendor.me')->with('success', 'Business profile updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedProfile(Request $request, bool $permitRequired = false): array
    {
        return $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'business_permit' => [
                Rule::requiredIf($permitRequired),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:10240',
            ],
            'payment_qr_code' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'business_permit.uploaded' => 'The business permit upload was not received. Please select a JPG, JPEG, or PNG image smaller than 10 MB and try again.',
            'business_permit.image' => 'The business permit must be a valid JPG, JPEG, or PNG image.',
            'business_permit.mimes' => 'The business permit must be a JPG, JPEG, or PNG image.',
        ]);
    }
}
