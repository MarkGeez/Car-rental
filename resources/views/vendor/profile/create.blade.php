@extends('layouts.vendor')

@section('title', 'Create business profile')

@section('vendor-content')
<div class="max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h1 class="text-2xl font-bold">Set up your business</h1>

    <form method="POST" action="{{ url('/vendor/profile') }}" enctype="multipart/form-data" class="mt-8 space-y-5">
        @csrf
        @include('vendor.profile._form', ['permitRequired' => true])

        <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white">
            Submit for verification
        </button>
    </form>
</div>
@endsection