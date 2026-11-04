@extends('layouts.user')
@section('title', 'Leave a review')
@section('user-content')
<div class="mx-auto max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">Review your rental</h1><form method="POST" action="{{ url('/user/rentals/'.($rental->id ?? '').'/review') }}" class="mt-8 space-y-5">@csrf<label class="block text-sm font-medium">Rating<select name="rating" required class="mt-2 w-full rounded-xl border-slate-300">@for ($rating = 5; $rating >= 1; $rating--)<option value="{{ $rating }}">{{ $rating }} / 5</option>@endfor</select></label><label class="block text-sm font-medium">Comment<textarea name="comment" rows="5" class="mt-2 w-full rounded-xl border-slate-300">{{ old('comment') }}</textarea></label><button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-semibold text-white">Submit review</button></form></div>
@endsection
