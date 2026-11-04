@extends('layouts.app')
@section('title', 'Create an account')
@section('content')
<div class="grid min-h-[calc(100vh-9rem)] lg:grid-cols-2">
    <div class="flex items-center justify-center px-4 py-12 sm:px-6 lg:order-2 lg:px-12">
        <div class="w-full max-w-md">
            <div class="mb-8 lg:hidden"><a href="{{ url('/') }}" class="flex items-center gap-2.5 text-xl font-bold text-slate-950"><span class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-600 text-sm text-white">E</span>{{ config('app.name', 'RentaGo') }}</a></div>
            <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-xl shadow-slate-200/60 sm:p-9">
                <p class="text-sm font-semibold text-indigo-600">Get started</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Create your account</h1><p class="mt-2 text-slate-500">Join a better way to rent and share cars.</p>
                <form method="POST" action="{{ url('/register') }}" class="mt-8 space-y-5">@csrf
                    <label class="block text-sm font-semibold text-slate-700">Full name<input name="name" value="{{ old('name') }}" autocomplete="name" required class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></label>
                    <label class="block text-sm font-semibold text-slate-700">Email address<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></label>
                    <label class="block text-sm font-semibold text-slate-700">Password<input name="password" type="password" autocomplete="new-password" required class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"><span class="mt-1 block text-xs font-normal text-slate-400">Use at least 8 characters.</span></label>
                    <label class="block text-sm font-semibold text-slate-700">Confirm password<input name="password_confirmation" type="password" autocomplete="new-password" required class="mt-2 w-full rounded-xl border-slate-300 px-4 py-3 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"></label>
                    <fieldset><legend class="text-sm font-semibold text-slate-700">How will you use RentaGo?</legend><div class="mt-3 grid gap-3 sm:grid-cols-2"><label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"><input class="accent-indigo-600" type="radio" name="role" value="customer" checked><span class="ml-2 font-semibold text-slate-800">Rent a car</span><span class="mt-1 block pl-6 text-xs text-slate-500">Find a ride for your next trip.</span></label><label class="cursor-pointer rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50"><input class="accent-indigo-600" type="radio" name="role" value="vendor"><span class="ml-2 font-semibold text-slate-800">List cars</span><span class="mt-1 block pl-6 text-xs text-slate-500">Earn by sharing your vehicles.</span></label></div></fieldset>
                    <button class="w-full rounded-xl bg-indigo-600 px-4 py-3.5 font-semibold text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">Create account</button>
                </form>
                <p class="mt-7 text-center text-sm text-slate-500">Already have an account? <a href="{{ url('/login') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Log in</a></p>
            </div>
        </div>
    </div>
    <div class="relative hidden overflow-hidden bg-indigo-600 p-12 text-white lg:order-1 lg:flex lg:flex-col lg:justify-between">
        <div class="absolute -bottom-40 -left-20 h-[30rem] w-[30rem] rounded-full bg-cyan-400/30 blur-3xl"></div>
        <div class="relative"><a href="{{ url('/') }}" class="flex items-center gap-2.5 text-xl font-bold"><span class="grid h-9 w-9 place-items-center rounded-xl bg-white text-sm text-indigo-700">E</span>{{ config('app.name', 'RentaGo') }}</a></div>
        <div class="relative max-w-md"><p class="mb-5 text-sm font-semibold uppercase tracking-[0.2em] text-indigo-200">One account, more freedom</p><h1 class="text-5xl font-bold leading-tight">Make every journey worth taking.</h1><p class="mt-6 text-lg leading-8 text-indigo-100">Whether you need a car or have one to share, RentaGo connects you with the road ahead.</p></div>
        <p class="relative text-sm text-indigo-200">Simple booking. Trusted vehicles. Happier trips.</p>
    </div>
</div>
@endsection
