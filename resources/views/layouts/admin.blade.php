@extends('layouts.app')

@section('content')
<div class="mx-auto flex max-w-7xl gap-8 px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
    <aside class="hidden w-60 shrink-0 lg:block">
        <div class="sticky top-24 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-slate-950 px-5 py-5 text-white"><p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-300">Control center</p><p class="mt-2 text-sm text-slate-300">Keep the marketplace moving.</p></div>
            <nav class="space-y-1 p-3">
                @php($currentPath = request()->path())
                @foreach ([['Dashboard', 'admin/dashboard', '⌂'], ['Vendors', 'admin/vendors', '◆'], ['Users', 'admin/users', '○'], ['Vehicles', 'admin/vehicles', '▤'], ['Rentals', 'admin/rentals', '▣'], ['Reviews', 'admin/reviews', '★']] as [$label, $path, $icon])
                    <a href="{{ url('/'.$path) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ str_starts_with($currentPath, $path) ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950' }}"><span class="w-5 text-center text-base">{{ $icon }}</span>{{ $label }}</a>
                @endforeach
            </nav>
        </div>
    </aside>
    <section class="min-w-0 flex-1">@yield('admin-content')</section>
</div>
@endsection
