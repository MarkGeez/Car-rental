<div class="mx-auto max-w-7xl space-y-3 px-4 pt-4 sm:px-6 lg:px-8">
    @if (session('success'))
        <div class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-800 shadow-sm" role="alert"><span class="mt-0.5 font-bold">✓</span><p>{{ session('success') }}</p></div>
    @endif
    @if (session('error'))
        <div class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5 text-sm text-red-800 shadow-sm" role="alert"><span class="mt-0.5 font-bold">!</span><p>{{ session('error') }}</p></div>
    @endif
    @if ($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5 text-sm text-red-800 shadow-sm" role="alert">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
</div>
