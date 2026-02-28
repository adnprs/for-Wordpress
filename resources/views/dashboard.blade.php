<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight">
            Blogging Hub Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Workspace Modules</h3>
                            <p class="mt-1 text-sm text-slate-500">Manage your connected blogging and app platforms from one place.</p>
                        </div>

                        <div class="flex items-center gap-3">
                            @if(auth()->user()->role === 'admin')
                                <a
                                    href="{{ route('admin.dashboard') }}"
                                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800"
                                >
                                    ⚙️ Enterprise Admin Panel
                                </a>
                            @endif

                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">
                                {{ $apps->count() }} Modules
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($apps as $app)
                            <article class="rounded-xl border border-slate-200 bg-slate-50/70 p-6 shadow-sm">
                                <div>
                                    <h4 class="text-2xl font-extrabold tracking-tight text-slate-900">{{ $app->name }}</h4>
                                    <p class="mt-2 text-sm font-medium text-slate-600">{{ $app->module_badge }}</p>
                                </div>

                                <a
                                    href="{{ route('workspace.show', $app->id) }}"
                                    class="mt-8 inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                                >
                                    Open Workspace
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
