@extends('layouts.admin')
@section('title', 'Rental detail')
@section('admin-content')
<div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex justify-between"><h1 class="text-2xl font-bold">Rental #{{ $rental->id ?? '—' }}</h1>@include('partials.status-badge', ['status' => $rental->status ?? 'pending'])</div><dl class="mt-8 grid gap-5 sm:grid-cols-2"><div><dt class="text-sm text-slate-500">Customer</dt><dd>{{ $rental->customer->name ?? '—' }}</dd></div><div><dt class="text-sm text-slate-500">Vehicle</dt><dd>{{ $rental->vehicle->brand ?? '—' }} {{ $rental->vehicle->model ?? '' }}</dd></div><div><dt class="text-sm text-slate-500">Payment</dt><dd>{{ $rental->payment->payment_status ?? 'Unpaid' }}</dd></div><div><dt class="text-sm text-slate-500">Commission</dt><dd>₱{{ number_format($rental->commission_amount ?? 0, 2) }}</dd></div></dl></div>
@endsection
