@extends('dashboard.layout')

@section('title', 'Workspace - ' . $selectedApp->name)
@section('header', 'Workspace: ' . $selectedApp->name)

@section('content')
    <section class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">{{ $selectedApp->name }}</h3>
                    <p class="mt-1 text-sm text-slate-500">App Type: {{ ucfirst($selectedApp->type) }} • Workspace tools are tailored for this app.</p>
                </div>

                <a
                    href="{{ route('dashboard.editor', $selectedApp->id) }}"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700"
                >
                    Open Editor Interface
                </a>
            </div>
        </div>

        <div>
            <h4 class="mb-3 text-base font-semibold text-slate-900">Available Tools</h4>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($tools as $tool)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h5 class="text-base font-semibold text-slate-900">{{ $tool }}</h5>
                        <p class="mt-2 text-sm text-slate-500">Ready to run in {{ $selectedApp->name }} workspace.</p>
                        <button class="mt-4 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Launch
                        </button>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection