@extends('layouts.app')

@section('title', 'Rent a car with confidence')
@section('content')
<section class="bg-[#171717] text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
        <div class="space-y-6"><p class="font-semibold uppercase tracking-widest text-[#93C5FD]">Move freely</p><h1 class="text-4xl font-bold tracking-tight sm:text-6xl">Your next journey starts here.</h1><p class="max-w-xl text-lg text-[#D4D4D4]">Find reliable vehicles from trusted local vendors and book the right car for every trip.</p><div class="flex flex-wrap gap-3"><a href="{{ url('/cars') }}" class="rounded-xl bg-[#2563EB] px-5 py-3 font-semibold shadow-sm hover:bg-[#1D4ED8]">Browse cars</a><a href="{{ url('/register') }}" class="rounded-xl border border-[#525252] px-5 py-3 font-semibold hover:bg-[#292929]">Join RentaGo</a></div></div>
        <div class="rounded-3xl bg-[#2563EB] p-1 shadow-2xl">
            <div class="relative flex min-h-72 items-end overflow-hidden rounded-[1.4rem] bg-cover bg-center p-8"
                style="background-image: url('{{ asset('images/better-cars-better-trips.jpg') }}')">
                <div class="absolute inset-0 bg-slate-950/35"></div>
                <p class="relative z-10 text-2xl font-semibold">Better cars. Better trips.</p>
            </div>
        </div>
    </div>
</section>
<section class="mx-auto max-w-7xl space-y-8 px-4 py-16 sm:px-6 lg:px-8"><div class="flex items-end justify-between gap-4"><div><p class="font-semibold text-indigo-600">Featured fleet</p><h2 class="mt-1 text-3xl font-bold">Ready when you are</h2></div><a href="{{ url('/cars') }}" class="text-sm font-semibold text-indigo-600">View all cars →</a></div><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">@forelse (($featuredVehicles ?? []) as $vehicle) @include('partials.car-card', ['vehicle' => $vehicle]) @empty @for ($i = 0; $i < 3; $i++) @include('partials.car-card', ['vehicle' => null]) @endfor @endforelse</div></section>
@endsection