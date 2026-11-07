@extends('layouts.user')

@section('title', 'My profile')

@section('user-content')
<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-bold text-slate-900">Profile settings</h1>

    <form method="POST" action="{{ url('/user/profile') }}" class="mt-8 space-y-5">
        @csrf
        @method('PATCH')

        <label class="block text-sm font-medium text-slate-700">
            Name
            <input 
                type="text"
                name="name" 
                value="{{ old('name', auth()->user()->name ?? '') }}" 
                required 
                class="mt-2 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </label>

        <label class="block text-sm font-medium text-slate-700">
            Contact number
            <input 
                type="text"
                name="contact_number" 
                value="{{ old('contact_number', auth()->user()->contact_number ?? '') }}" 
                class="mt-2 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </label>

        <label class="block text-sm font-medium text-slate-700">
            New password
            <input 
                type="password" 
                name="password" 
                class="mt-2 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </label>

        <label class="block text-sm font-medium text-slate-700">
            Confirm password
            <input 
                type="password" 
                name="password_confirmation" 
                class="mt-2 w-full rounded-xl border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </label>

        <div>
            <button 
                type="submit" 
                class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white hover:bg-indigo-500 transition-colors"
            >
                Save changes
            </button>
        </div>
    </form>
</div>
@endsection