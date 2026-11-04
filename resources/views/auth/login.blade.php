@extends('layouts.app')
@section('title', 'Log in')
@section('content')
<div class="grid min-h-[calc(100vh-9rem)] lg:grid-cols-2">
    <div class="relative hidden overflow-hidden bg-slate-950 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-indigo-500/30 blur-3xl"></div>
        <div class="relative"><a href="{{ url('/') }}" class="flex items-center gap-2.5 text-xl font-bold"><span class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-500 text-sm">E</span>{{ config('app.name', 'RentaGo') }}</a></div>
        <div class="relative max-w-md"><p class="mb-5 text-sm font-semibold uppercase tracking-[0.2em] text-indigo-300">Welcome back</p><h1 class="text-5xl font-bold leading-tight">Your next drive is closer than you think.</h1><p class="mt-6 text-lg leading-8 text-slate-300">Access your bookings, manage your trips, and keep moving with confidence.</p></div>
        <p class="relative text-sm text-slate-400">Trusted rides for every kind of journey.</p>
    </div>
    <div class="flex items-center justify-center px-4 py-12 sm:px-6 lg:px-12">
        <div class="w-full max-w-md">
            <div class="mb-8 lg:hidden"><a href="{{ url('/') }}" class="flex items-center gap-2.5 text-xl font-bold text-slate-950"><span class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-600 text-sm text-white">E</span>{{ config('app.name', 'RentaGo') }}</a></div>
            <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60 sm:p-9">
                <p class="text-sm font-semibold text-indigo-600">Member login</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Welcome back</h1><p class="mt-2 text-slate-500">Log in to manage your RentaGo account.</p>
                <form method="POST" action="{{ url('/login') }}" class="mt-8 space-y-5">@csrf
                    <label class="block text-sm font-semibold text-slate-700">Email<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></label>
                    <label class="block text-sm font-semibold text-slate-700">Password<input name="password" type="password" autocomplete="current-password" required class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></label>
                    <button class="w-full rounded-xl bg-indigo-600 px-4 py-3.5 font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Log in</button>
                </form>
                <p class="mt-7 text-center text-sm text-slate-500">No account yet? <a href="{{ url('/register') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Create one</a></p>
            </div>
        </div>
    </div>
</div>
@endsection