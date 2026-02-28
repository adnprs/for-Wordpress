@php
    $title = 'Dashboard Overview';
    $subtitle = 'Centralized control for apps, prompts, team operations, and enterprise settings.';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-6">
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Sites/Apps</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $appsCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Active Modules</p>
            <p class="mt-2 text-3xl font-bold text-indigo-700">{{ $activeAppsCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Category Prompts</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $promptsCount }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Team Members</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $teamCount }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <a href="{{ route('admin.apps.index') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs uppercase tracking-wide text-slate-500">Apps</p>
            <h3 class="mt-2 text-lg font-semibold text-slate-900">Manage Sites/Apps</h3>
            <p class="mt-2 text-sm text-slate-600">Create, update, and control active/inactive status of all connected modules.</p>
        </a>

        <a href="{{ route('admin.prompts.index') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs uppercase tracking-wide text-slate-500">Prompts</p>
            <h3 class="mt-2 text-lg font-semibold text-slate-900">Categories &amp; Hidden Prompts</h3>
            <p class="mt-2 text-sm text-slate-600">Map prompts per app category to maintain predictable AI generation quality.</p>
        </a>

        <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs uppercase tracking-wide text-slate-500">Users</p>
            <h3 class="mt-2 text-lg font-semibold text-slate-900">Team Management</h3>
            <p class="mt-2 text-sm text-slate-600">Invite and manage Admin, Editor, and Operator accounts with role-based access.</p>
        </a>
    </div>

    <section id="global-settings" class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-900">⚙️ Global Settings (API Keys)</h3>
        <p class="mt-2 text-sm text-slate-600">
            Global enterprise-level API key management can be configured here in the next phase.
            This panel is reserved for secure Gemini/WordPress/Firebase credentials governance.
        </p>
    </section>
    </div>
@endsection