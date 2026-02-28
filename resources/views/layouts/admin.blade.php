<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} • Enterprise Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
<div class="fixed right-4 top-4 z-50 space-y-3 sm:right-6 sm:top-6">
    @if (session('success') || session('status'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.duration.300ms
            x-init="setTimeout(() => show = false, 5000)"
            class="w-80 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-lg"
        >
            {{ session('success') ?? session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.duration.300ms
            x-init="setTimeout(() => show = false, 5000)"
            class="w-80 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 shadow-lg"
        >
            {{ session('error') }}
        </div>
    @endif
</div>

<div class="min-h-screen">
    @include('layouts.top-navbar')

    <div class="lg:flex">
        <aside class="w-full border-b border-gray-200 bg-white text-gray-900 lg:min-h-[calc(100vh-4rem)] lg:w-72 lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
            <div>
                <p class="text-xs uppercase tracking-widest text-indigo-300">Enterprise</p>
                <h1 class="text-lg font-bold">Super Admin Panel</h1>
            </div>
            </div>

            <nav class="space-y-1 px-4 py-5">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }} flex items-center rounded-r-lg px-4 py-2.5 text-sm transition">
                📊 Dashboard Overview
            </a>
            <a href="{{ route('admin.apps.index') }}" class="{{ request()->routeIs('admin.apps.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }} flex items-center rounded-r-lg px-4 py-2.5 text-sm transition">
                🌐 Manage Sites/Apps
            </a>
            <a href="{{ route('admin.prompts.index') }}" class="{{ request()->routeIs('admin.prompts.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }} flex items-center rounded-r-lg px-4 py-2.5 text-sm transition">
                📂 Categories &amp; Prompts
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 font-semibold' : 'text-gray-600 hover:bg-gray-50' }} flex items-center rounded-r-lg px-4 py-2.5 text-sm transition">
                👥 Team Management
            </a>
            <a href="{{ route('admin.dashboard') }}#global-settings" class="text-gray-600 hover:bg-gray-50 flex items-center rounded-r-lg px-4 py-2.5 text-sm transition">
                ⚙️ Global Settings (API Keys)
            </a>
            </nav>

            <div class="mx-4 mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4 text-xs text-gray-600">
                <p class="font-semibold text-gray-800">Admin Workspace</p>
                <p class="mt-1 truncate">Manage sites, team, and prompts.</p>
            </div>
        </aside>

        <div class="flex-1">
            <header class="border-b border-slate-200 bg-white px-6 py-5 sm:px-8">
                <h2 class="text-xl font-semibold text-slate-900">{{ $title ?? 'Enterprise Admin' }}</h2>
                @isset($subtitle)
                    <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                @endisset
            </header>

            <main class="p-6 sm:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</div>
</body>
</html>