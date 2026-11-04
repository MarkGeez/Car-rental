@extends('layouts.admin')
@section('title', 'All vehicles')
@section('admin-content')
<div class="space-y-6"><h1 class="text-3xl font-bold">All vehicles</h1><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@forelse (($vehicles ?? []) as $vehicle) @include('partials.car-card', ['vehicle' => $vehicle]) @empty<p class="col-span-full text-center text-slate-500">No vehicles found.</p>@endforelse</div></div>
@endsection
