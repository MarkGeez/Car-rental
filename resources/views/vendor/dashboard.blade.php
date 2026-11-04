@extends('layouts.vendor')
@section('title', 'Vendor dashboard')
@section('vendor-content')
<div class="space-y-8"><div><h1 class="text-3xl font-bold">Vendor dashboard</h1><p class="mt-2 text-slate-500">Manage your fleet and rental requests.</p></div><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach ([['Cars listed', $vehicleCount ?? 0], ['Pending requests', $pendingRentals ?? 0], ['Ongoing rentals', $ongoingRentals ?? 0], ['Total earnings', '₱'.number_format($totalEarnings ?? 0, 2)]] as [$label, $value])<div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold">{{ $value }}</p></div>@endforeach</div></div>
@endsection
