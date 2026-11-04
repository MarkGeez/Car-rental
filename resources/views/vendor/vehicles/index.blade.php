@extends('layouts.vendor')
@section('title', 'My vehicles')
@section('vendor-content')
<div class="space-y-6"><div class="flex items-center justify-between gap-4"><div><h1 class="text-3xl font-bold">My vehicles</h1><p class="mt-2 text-slate-500">Keep your fleet up to date.</p></div><a href="{{ url('/vendor/vehicles/create') }}" class="rounded-xl bg-indigo-600 px-4 py-2 font-semibold text-white">Add vehicle</a></div><div class="grid gap-6 sm:grid-cols-2">@forelse (($vehicles ?? []) as $vehicle) @include('partials.car-card', ['vehicle' => $vehicle]) @empty<p class="col-span-full rounded-2xl border border-dashed p-10 text-center text-slate-500">No vehicles listed yet.</p>@endforelse</div></div>
@endsection
