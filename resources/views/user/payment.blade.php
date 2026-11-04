@extends('layouts.user')
@section('title', 'Make payment')
@section('user-content')
<div class="mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">Confirm payment</h1><p class="mt-2 text-slate-500">Amount due: <strong>₱{{ number_format($rental->total_amount ?? 0, 2) }}</strong></p><form method="POST" action="{{ url('/user/rentals/'.($rental->id ?? '').'/payment') }}" class="mt-8 space-y-5">@csrf<label class="block text-sm font-medium">Payment method<select name="payment_method" class="mt-2 w-full rounded-xl border-slate-300"><option value="gcash">GCash</option><option value="card">Card</option><option value="cash">Cash</option></select></label><button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white">Pay securely (simulated)</button></form></div>
@endsection
