@extends('layouts.vendor')
@section('title', 'Edit business profile')
@section('vendor-content')
<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex flex-wrap items-center justify-between gap-3"><h1 class="text-2xl font-bold">Business profile</h1>@include('partials.status-badge', ['status' => $vendor->verification_status])</div>@if (($vendor->verification_status ?? '') === 'rejected')<div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">Your profile was rejected. Update your information and submit it again for review.</div>@endif<form method="POST" action="{{ url('/vendor/profile') }}" enctype="multipart/form-data" class="mt-8 space-y-5">@csrf @method('PATCH') @include('vendor.profile._form', ['permitRequired' => false])<button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white">Save changes</button></form></div>
@endsection
