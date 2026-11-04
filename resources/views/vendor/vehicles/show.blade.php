@extends('layouts.vendor')
@section('title', 'Vehicle details')
@section('vendor-content')
<div class="space-y-6"><a href="{{ url('/vendor/vehicles') }}" class="text-sm font-semibold text-indigo-600">← My vehicles</a>@include('partials.car-card', ['vehicle' => $vehicle ?? null])</div>
@endsection
