@extends('layouts.admin')
@section('title', 'All rentals')
@section('admin-content')
<div class="space-y-6"><h1 class="text-3xl font-bold">All rentals</h1><div class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><div class="divide-y divide-slate-100">@forelse (($rentals ?? []) as $rental)<a href="{{ url('/admin/rentals/'.$rental->id) }}" class="flex items-center justify-between gap-4 p-5"><span>Rental #{{ $rental->id }} · {{ $rental->customer->name ?? 'Customer' }}</span>@include('partials.status-badge', ['status' => $rental->status])</a>@empty<p class="p-10 text-center text-slate-500">No rentals found.</p>@endforelse</div></div></div>
@endsection
