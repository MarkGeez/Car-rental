<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-4 py-3.5 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-xl font-bold tracking-tight text-slate-950"><span class="grid h-9 w-9 place-items-center rounded-xl bg-indigo-600 text-sm text-white shadow-lg shadow-indigo-200">E</span>{{ config('app.name', 'RentaGo') }}</a>
        <div class="hidden items-center gap-7 text-sm font-medium text-slate-600 md:flex">
            <a href="{{ url('/cars') }}" class="transition hover:text-indigo-700">Browse cars</a>
            @auth
                @php($dashboardPath = auth()->user()->role === 'vendor' ? 'vendor/dashboard' : (auth()->user()->role === 'admin' ? 'admin/dashboard' : 'user/dashboard'))
                <a href="{{ url('/'.$dashboardPath) }}" class="transition hover:text-indigo-700">Dashboard</a>
                <span class="h-5 w-px bg-slate-200"></span>
                <span class="max-w-32 truncate text-slate-500">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ url('/logout') }}">@csrf<button class="rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-950" type="submit">Log out</button></form>
            @else
                <a href="{{ url('/login') }}" class="transition hover:text-indigo-700">Log in</a>
                <a href="{{ url('/register') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-white shadow-lg shadow-indigo-200 transition hover:bg-indigo-700">Get started</a>
            @endauth
        </div>
        <details class="relative md:hidden">
            <summary class="list-none cursor-pointer rounded-xl border border-slate-200 p-2.5 text-slate-600 [&::-webkit-details-marker]:hidden">☰</summary>
            <div class="absolute right-0 top-14 w-52 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                <a href="{{ url('/cars') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Browse cars</a>
                @auth
                    <a href="{{ url('/'.$dashboardPath) }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Dashboard</a>
                    <form method="POST" action="{{ url('/logout') }}">@csrf<button class="w-full rounded-xl px-3 py-2.5 text-left text-sm font-medium text-slate-700 hover:bg-slate-50" type="submit">Log out</button></form>
                @else
                    <a href="{{ url('/login') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Log in</a>
                    <a href="{{ url('/register') }}" class="mt-1 block rounded-xl bg-indigo-600 px-3 py-2.5 text-center text-sm font-semibold text-white">Get started</a>
                @endauth
            </div>
        </details>
    </nav>
</header>
