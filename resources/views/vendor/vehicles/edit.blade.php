@extends('layouts.vendor')
@section('title', 'Edit vehicle')
@section('vendor-content')
<div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">Edit vehicle</h1><form method="POST" action="{{ url('/vendor/vehicles/'.($vehicle->id ?? '')) }}" enctype="multipart/form-data" class="mt-8 space-y-5">@csrf @method('PATCH') @include('vendor.vehicles._form')<button class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white">Save vehicle</button></form></div>
@endsection
