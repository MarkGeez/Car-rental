@extends('layouts.admin')
@section('title', 'Admin dashboard')
@section('admin-content')
<div class="space-y-8"><div><h1 class="text-3xl font-bold">Admin dashboard</h1><p class="mt-2 text-slate-500">A quick overview of the marketplace.</p></div><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">@foreach ([['Users', $userCount ?? 0], ['Vendors', $vendorCount ?? 0], ['Vehicles', $vehicleCount ?? 0], ['Commission', '₱'.number_format($commissionEarnings ?? 0, 2)]] as [$label, $value])<div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-bold">{{ $value }}</p></div>@endforeach</div></div>
@endsection
