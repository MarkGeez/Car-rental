@extends('layouts.user')

@section('title', 'Rental details')

@section('user-content')
<div class="max-w-3xl space-y-6">
    <a href="{{ url('/user/rentals') }}" class="text-sm font-semibold text-indigo-600">
        ← My rentals
    </a>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">
                    {{ $rental->vehicle->brand ?? 'Vehicle' }} {{ $rental->vehicle->model ?? '' }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Rental #{{ $rental->id ?? '—' }}
                </p>
            </div>
            @include('partials.status-badge', ['status' => $rental->status ?? 'pending'])
        </div>

        <dl class="mt-8 grid gap-5 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-slate-500">Pickup</dt>
                <dd class="font-medium">{{ $rental->pickup_date ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Return</dt>
                <dd class="font-medium">{{ $rental->return_date ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Total</dt>
                <dd class="font-medium">₱{{ number_format($rental->total_amount ?? 0, 2) }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Payment</dt>
                <dd class="font-medium">{{ $rental->payment->payment_status ?? 'Unpaid' }}</dd>
            </div>
        </dl>

        <div class="mt-8 flex flex-wrap gap-3">
            @if ($rental->status === 'completed' && ! $rental->review)
                <a href="{{ route('user.rentals.review', $rental) }}" class="rounded-xl bg-indigo-600 px-4 py-2 font-semibold text-white">
                    Leave a review
                </a>
            @elseif ($rental->review)
                <span class="rounded-xl bg-slate-100 px-4 py-2 font-semibold text-slate-600">
                    Review submitted
                </span>
            @endif

            @if (($rental->payment->payment_status ?? 'unpaid') !== 'paid')
                <a href="{{ url('/user/rentals/' . ($rental->id ?? '') . '/payment') }}" class="rounded-xl bg-indigo-600 px-4 py-2 font-semibold text-white">
                    Submit payment
                </a>
            @endif

            @if (in_array($rental->status ?? '', ['pending', 'approved']))
                <form method="POST" action="{{ url('/user/rentals/' . ($rental->id ?? '') . '/cancel') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-xl border border-red-200 px-4 py-2 font-semibold text-red-600">
                        Cancel rental
                    </button>
                </form>
            @endif
        </div>

        @if (in_array($rental->status, ['approved', 'ongoing'], true))
            @php($pendingExtension = $rental->extensions->firstWhere('status', 'pending'))
            @if (! $pendingExtension)
                <form method="POST" action="{{ route('user.rentals.extend', $rental) }}" class="mt-6 flex flex-wrap items-end gap-3 border-t border-slate-200 pt-6">
                    @csrf
                    <label class="block text-sm font-medium text-slate-700">
                        New return date
                        <input
                            type="date"
                            name="return_date"
                            min="{{ $rental->return_date->copy()->addDay()->toDateString() }}"
                            value="{{ old('return_date') }}"
                            required
                            class="mt-2 block rounded-xl border-slate-300"
                        >
                    </label>
                    <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 font-semibold text-white">
                        Extend rental
                    </button>
                </form>
            @else
                <p class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Extension request #{{ $pendingExtension->id }} is pending vendor approval.
                </p>
            @endif
        @endif
    </div>
</div>
@endsection