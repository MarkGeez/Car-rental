<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorVehicleController extends Controller
{
    public function index(Request $request): View
    {
        $vendor = $this->verifiedVendor($request);
        $vehicles = $vendor->vehicles()->with('images')->latest()->paginate(12);

        return view('vendor.vehicles.index', compact('vehicles'));
    }

    public function create(Request $request): View
    {
        $this->verifiedVendor($request);

        return view('vendor.vehicles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $vendor = $this->verifiedVendor($request);
        $data = $this->validatedVehicle($request);
        $data['vendor_id'] = $vendor->id;
        $data['status'] = $this->requestedStatus($request);
        $vehicle = $vendor->vehicles()->create($data);

        $this->storeImages($request, $vehicle);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        $this->ownedVehicle($request, $vehicle)->load('images');

        return view('vendor.vehicles.show', compact('vehicle'));
    }

    public function edit(Request $request, Vehicle $vehicle): View
    {
        $this->verifiedVendor($request);
        $this->ownedVehicle($request, $vehicle)->load('images');

        return view('vendor.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->verifiedVendor($request);
        $vehicle = $this->ownedVehicle($request, $vehicle);
        $vehicle->update([
            ...$this->validatedVehicle($request, $vehicle),
            'status' => $this->requestedStatus($request),
        ]);
        $this->storeImages($request, $vehicle);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function updateStatus(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->verifiedVendor($request);
        $vehicle = $this->ownedVehicle($request, $vehicle);
        $data = $request->validate([
            'status' => ['required', Rule::in(['available', 'unavailable'])],
        ]);

        $vehicle->update(['status' => $data['status']]);

        return back()->with('success', 'Vehicle status updated successfully.');
    }

    public function destroy(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->verifiedVendor($request);
        $vehicle = $this->ownedVehicle($request, $vehicle);

        if ($vehicle->rentals()->whereNotIn('status', ['cancelled', 'completed'])->exists()) {
            return back()->withErrors(['vehicle' => 'Vehicles with active rentals cannot be deleted.']);
        }

        foreach ($vehicle->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }

    public function addImage(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $this->verifiedVendor($request);
        $vehicle = $this->ownedVehicle($request, $vehicle);
        $this->storeImages($request, $vehicle, true);

        return back()->with('success', 'Vehicle images added.');
    }

    public function deleteImage(Request $request, Vehicle $vehicle, VehicleImage $image): RedirectResponse
    {
        $this->verifiedVendor($request);
        $this->ownedVehicle($request, $vehicle);
        abort_unless($image->vehicle_id === $vehicle->id, 404);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Vehicle image deleted.');
    }

    private function verifiedVendor(Request $request): Vendor
    {
        $vendor = $request->user()->vendor;
        abort_unless($vendor && $vendor->verification_status === 'verified', 403, 'Your business must be verified first.');

        return $vendor;
    }

    private function ownedVehicle(Request $request, Vehicle $vehicle): Vehicle
    {
        abort_unless($vehicle->vendor_id === $request->user()->vendor?->id, 404);

        return $vehicle;
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedVehicle(Request $request, ?Vehicle $vehicle = null): array
    {
        return $request->validate([
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'plate_number' => ['required', 'string', 'max:30', Rule::unique('vehicles', 'plate_number')->ignore($vehicle?->id)],
            'vehicle_type' => ['required', 'string', 'max:100'],
            'transmission' => ['required', 'string', 'max:50'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'seats' => ['required', 'integer', 'min:1', 'max:20'],
            'daily_price' => ['required', 'numeric', 'min:0'],
            'security_deposit' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:5000'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function storeImages(Request $request, Vehicle $vehicle, bool $required = false): void
    {
        $request->validate([
            'images' => [$required ? 'required' : 'nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        foreach ($request->file('images', []) as $image) {
            $vehicle->images()->create([
                'image_path' => $image->store('vehicle-images', 'public'),
            ]);
        }
    }

    private function requestedStatus(Request $request): string
    {
        if ($request->has('status')) {
            return $request->validate([
                'status' => [Rule::in(['available', 'unavailable'])],
            ])['status'];
        }

        return $request->boolean('available') ? 'available' : 'unavailable';
    }
}
