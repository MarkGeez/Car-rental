@php
    $status = strtolower((string) ($status ?? 'pending'));
    $colors = ['approved' => 'bg-emerald-100 text-emerald-700', 'verified' => 'bg-emerald-100 text-emerald-700', 'completed' => 'bg-emerald-100 text-emerald-700', 'available' => 'bg-emerald-100 text-emerald-700', 'paid' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700', 'cancelled' => 'bg-red-100 text-red-700', 'unavailable' => 'bg-slate-100 text-slate-600', 'ongoing' => 'bg-blue-100 text-blue-700'];
@endphp
<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $colors[$status] ?? 'bg-amber-100 text-amber-700' }}">{{ str_replace('_', ' ', ucfirst($status)) }}</span>
