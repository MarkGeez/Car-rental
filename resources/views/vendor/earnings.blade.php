@extends('layouts.vendor')
@section('title', 'Earnings')
@section('vendor-content')
<div class="space-y-6"><h1 class="text-3xl font-bold">Earnings</h1><div class="rounded-2xl bg-indigo-600 p-6 text-white"><p class="text-indigo-100">Total earned</p><p class="mt-2 text-4xl font-bold">₱{{ number_format($totalEarnings ?? 0, 2) }}</p></div><div class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><div class="divide-y divide-slate-100">@foreach (($rentals ?? []) as $rental)<div class="flex justify-between p-4"><span>Rental #{{ $rental->id }}</span><strong>₱{{ number_format($rental->vendor_amount ?? 0, 2) }}</strong></div>@endforeach</div></div></div>
@endsection
