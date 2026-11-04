<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $vehicles = Vehicle::query()
            ->with(['images', 'vendor'])
            ->where('status', 'available')
            ->whereHas('vendor', fn ($query) => $query->where('verification_status', 'verified'))
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query
                        ->where('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->when($filters['vehicle_type'] ?? null, fn ($query, string $vehicleType) => $query->where('vehicle_type', $vehicleType))
            ->when($filters['max_price'] ?? null, fn ($query, float|int|string $maxPrice) => $query->where('daily_price', '<=', $maxPrice))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('cars.index', compact('vehicles'));
    }

    public function show(Vehicle $vehicle): View
    {
        abort_unless(
            $vehicle->status === 'available'
                && $vehicle->vendor()->where('verification_status', 'verified')->exists(),
            404,
        );

        $vehicle->load(['images', 'vendor']);

        return view('cars.show', [
            'vehicle' => $vehicle,
            'reviews' => collect(),
        ]);
    }

    public function availability(Request $request, Vehicle $vehicle): JsonResponse
    {
        $dates = $request->validate([
            'pickup_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:pickup_date'],
        ]);

        $isAvailable = ! $vehicle->rentals()
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->where(function ($query) use ($dates): void {
                $query
                    ->whereDate('pickup_date', '<=', $dates['return_date'])
                    ->whereDate('return_date', '>=', $dates['pickup_date']);
            })
            ->exists();

        return response()->json([
            'available' => $isAvailable,
        ]);
    }
}
