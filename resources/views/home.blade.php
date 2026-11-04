@extends('layouts.app')

@section('title', 'Rent a car with confidence')
@section('content')
<section class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
        <div class="space-y-6"><p class="font-semibold uppercase tracking-widest text-indigo-300">Move freely</p><h1 class="text-4xl font-bold tracking-tight sm:text-6xl">Your next journey starts here.</h1><p class="max-w-xl text-lg text-slate-300">Find reliable vehicles from trusted local vendors and book the right car for every trip.</p><div class="flex flex-wrap gap-3"><a href="{{ url('/cars') }}" class="rounded-xl bg-indigo-500 px-5 py-3 font-semibold hover:bg-indigo-400">Browse cars</a><a href="{{ url('/register') }}" class="rounded-xl border border-slate-600 px-5 py-3 font-semibold hover:bg-slate-800">Join RentaGo</a></div></div>
        <div class="rounded-3xl bg-gradient-to-br from-indigo-500 to-cyan-400 p-1"><div class="flex min-h-72 items-end rounded-[1.4rem] bg-slate-900/20 p-8"><p class="text-2xl font-semibold">Better cars. Better trips.</p></div></div>
    </div>
</section>
<section class="mx-auto max-w-7xl space-y-8 px-4 py-16 sm:px-6 lg:px-8"><div class="flex items-end justify-between gap-4"><div><p class="font-semibold text-indigo-600">Featured fleet</p><h2 class="mt-1 text-3xl font-bold">Ready when you are</h2></div><a href="{{ url('/cars') }}" class="text-sm font-semibold text-indigo-600">View all cars →</a></div><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@forelse (($featuredVehicles ?? []) as $vehicle) @include('partials.car-card', ['vehicle' => $vehicle]) @empty @for ($i = 0; $i < 3; $i++) @include('partials.car-card', ['vehicle' => null]) @endfor @endforelse</div></section>
@endsection