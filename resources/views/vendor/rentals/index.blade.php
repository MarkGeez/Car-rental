@extends('layouts.vendor')
@section('title', 'Rental requests')
@section('vendor-content')
<div class="space-y-6"><h1 class="text-3xl font-bold">Rental requests</h1><div class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><div class="divide-y divide-slate-100">@forelse (($rentals ?? []) as $rental)<a href="{{ url('/vendor/rentals/'.$rental->id) }}" class="flex flex-col gap-2 p-5 sm:flex-row sm:items-center sm:justify-between"><span><strong>{{ $rental->customer->name ?? 'Customer' }}</strong><span class="ml-2 text-slate-500">{{ $rental->vehicle->brand ?? 'Vehicle' }} {{ $rental->vehicle->model ?? '' }}</span></span>@include('partials.status-badge', ['status' => $rental->status])</a>@empty<p class="p-10 text-center text-slate-500">No rental requests.</p>@endforelse</div></div></div>
@endsection
