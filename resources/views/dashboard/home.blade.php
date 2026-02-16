@extends('dashboard.layout')

@section('title', 'Dashboard Home')
@section('header', 'Connected Apps Overview')

@section('content')
    <section>
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Connected Applications</h3>
                <p class="text-sm text-slate-500">Choose an app workspace to use its tools.</p>
            </div>
            <span class="rounded-lg bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">
                {{ $apps->count() }} Apps Connected
            </span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($apps as $app)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-base font-semibold text-slate-900">{{ $app->name }}</h4>
                            <p class="mt-1 text-sm text-slate-500">Type: {{ ucfirst($app->type) }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700">
                            Active
                        </span>
                    </div>

                    <a
                        href="{{ route('dashboard.workspace', $app->id) }}"
                        class="mt-5 inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-indigo-700"
                    >
                        Open Workspace
                    </a>
                </article>
            @endforeach
        </div>
    </section>
@endsection