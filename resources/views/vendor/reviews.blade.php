@extends('layouts.vendor')
@section('title', 'Reviews')
@section('vendor-content')
<div class="space-y-6"><h1 class="text-3xl font-bold">Reviews on your cars</h1><div class="space-y-4">@forelse (($reviews ?? []) as $review)<div class="rounded-2xl border border-slate-200 bg-white p-5"><p class="font-semibold">{{ $review->rating ?? 0 }}/5 · {{ $review->rental->vehicle->brand ?? 'Vehicle' }}</p><p class="mt-2 text-slate-600">{{ $review->comment }}</p></div>@empty<p class="text-slate-500">No reviews yet.</p>@endforelse</div></div>
@endsection
