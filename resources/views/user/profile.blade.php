@extends('layouts.user')
@section('title', 'My profile')
@section('user-content')
<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">Profile settings</h1><form method="POST" action="{{ url('/user/profile') }}" class="mt-8 space-y-5">@csrf @method('PATCH')<label class="block text-sm font-medium">Name<input name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required class="mt-2 w-full rounded-xl border-slate-300"></label><label class="block text-sm font-medium">Contact number<input name="contact_number" value="{{ old('contact_number', auth()->user()->contact_number ?? '') }}" class="mt-2 w-full rounded-xl border-slate-300"></label><label class="block text-sm font-medium">New password<input name="password" type="password" class="mt-2 w-full rounded-xl border-slate-300"></label><button class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white">Save changes</button></form></div>
@endsection
