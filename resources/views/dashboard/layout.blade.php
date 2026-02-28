<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Workspace Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="w-full border-b border-slate-200 bg-white lg:min-h-screen lg:w-72 lg:border-b-0 lg:border-r">
            <div class="border-b border-slate-200 px-6 py-5">
                <h1 class="text-xl font-bold tracking-tight">Workspace Console</h1>
                <p class="mt-1 text-sm text-slate-500">Team publishing control panel</p>
            </div>

            @php
                $navItems = [
                    ['label' => 'Keyword Briefs', 'route' => 'dashboard.seo-generator', 'key' => 'seo-generator'],
                    ['label' => 'PDF Upload', 'route' => 'dashboard.pdf-upload', 'key' => 'pdf-upload'],
                    ['label' => 'JobShorts Firebase', 'route' => 'dashboard.jobshorts-firebase', 'key' => 'jobshorts-firebase'],
                    ['label' => 'App Settings', 'route' => 'dashboard.app-settings', 'key' => 'app-settings'],
                ];
            @endphp

            <nav class="space-y-2 p-6" aria-label="Dashboard Modules">
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Navigation</h2>

                @foreach($navItems as $item)
                    @php
                        $isActive = ($activeModule ?? null) === $item['key'];
                    @endphp

                    <a
                        href="{{ route($item['route']) }}"
                        class="block rounded-lg border px-3 py-2 text-sm font-medium transition {{ $isActive ? 'border-indigo-200 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-700 hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1">
            <header class="border-b border-slate-200 bg-white px-6 py-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Dashboard</p>
                        <h2 class="text-lg font-semibold text-slate-900">@yield('header', 'Overview')</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Drafts Generated</p>
                            <p class="mt-1 text-2xl font-bold text-indigo-600">{{ number_format($stats['drafts_generated'] ?? 0) }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">API Calls</p>
                            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ number_format($stats['api_calls'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
