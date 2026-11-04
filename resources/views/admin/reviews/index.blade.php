@extends('layouts.admin')
@section('title', 'Moderate reviews')
@section('admin-content')
<div class="space-y-6"><h1 class="text-3xl font-bold">Reviews</h1><div class="space-y-4">@forelse (($reviews ?? []) as $review)<div class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5"><div><p class="font-semibold">{{ $review->rating }}/5</p><p class="mt-2 text-slate-600">{{ $review->comment }}</p></div><form method="POST" action="{{ url('/admin/reviews/'.$review->id) }}">@csrf @method('DELETE')<button class="text-sm font-semibold text-red-600">Delete</button></form></div>@empty<p class="text-slate-500">No reviews found.</p>@endforelse</div></div>
@endsection
