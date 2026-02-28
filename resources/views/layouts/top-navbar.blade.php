@php
    $user = auth()->user();
    $isAdminUser = $user?->role === 'admin';
    $isAdminPanel = request()->routeIs('admin.*');
    $switchMode = $isAdminPanel ? 'user' : 'admin';
    $switchLabel = $isAdminPanel ? 'Switch to User Workspace' : 'Switch to Super Admin';
@endphp

<nav class="h-16 border-b border-slate-200 bg-white shadow-sm">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-sm font-bold text-white">DH</span>
            <span class="text-base font-semibold text-slate-900">Dashboard Hub</span>
        </a>

        <x-dropdown align="right" width="w-56" contentClasses="py-1 bg-white">
            <x-slot name="trigger">
                <button class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                    <span class="hidden sm:inline">{{ $user?->name }}</span>
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-semibold text-slate-900">{{ $user?->name }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $user?->email }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 transition hover:bg-slate-50">
                    Profile
                </a>

                @if ($isAdminUser)
                    <form method="POST" action="{{ route('role.set') }}">
                        @csrf
                        <input type="hidden" name="mode" value="{{ $switchMode }}">
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm font-medium text-indigo-600 transition hover:bg-indigo-50">
                            {{ $switchLabel }}
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 mt-1">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-50">
                        Log Out
                    </button>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</nav>