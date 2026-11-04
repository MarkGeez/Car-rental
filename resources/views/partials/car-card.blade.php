@php($vehicle = $vehicle ?? null)
<article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="flex h-44 items-center justify-center bg-slate-100 text-slate-400">
        @if (isset($vehicle?->images) && $vehicle->images->isNotEmpty())
            <img src="{{ asset('storage/'.$vehicle->images->first()->image_path) }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="h-full w-full object-cover">
        @else
            <span class="text-sm">Vehicle image</span>
        @endif
    </div>
    <div class="space-y-3 p-5">
        <div class="flex items-start justify-between gap-3">
            <div><h3 class="font-semibold text-slate-900">{{ $vehicle->brand ?? 'Premium' }} {{ $vehicle->model ?? 'Vehicle' }}</h3><p class="text-sm text-slate-500">{{ $vehicle->year ?? '2025' }} · {{ $vehicle->vehicle_type ?? 'Car' }}</p></div>
            @include('partials.status-badge', ['status' => $vehicle->status ?? 'available'])
        </div>
        <div class="flex items-center justify-between"><p class="text-lg font-bold text-indigo-700">₱{{ number_format($vehicle->daily_price ?? 0, 2) }} <span class="text-xs font-normal text-slate-500">/ day</span></p><a href="{{ url('/cars/'.($vehicle->id ?? '')) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">View details</a></div>
    </div>
</article>
