<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                Admin: Category Prompts
            </h2>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Add New Category Prompt</h3>

                <form method="POST" action="{{ route('admin.prompts.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="app_id" class="mb-2 block text-sm font-semibold text-slate-700">Select App</label>
                        <select
                            id="app_id"
                            name="app_id"
                            class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Choose app</option>
                            @foreach($apps as $app)
                                <option value="{{ $app->id }}" @selected(old('app_id') == $app->id)>
                                    {{ $app->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('app_id')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_name" class="mb-2 block text-sm font-semibold text-slate-700">Category Name</label>
                        <input
                            id="category_name"
                            name="category_name"
                            type="text"
                            value="{{ old('category_name') }}"
                            placeholder="Latest Job"
                            class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                        @error('category_name')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prompt_template" class="mb-2 block text-sm font-semibold text-slate-700">Prompt Template</label>
                        <textarea
                            id="prompt_template"
                            name="prompt_template"
                            rows="6"
                            placeholder="Write a blog about {keyword}..."
                            class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >{{ old('prompt_template') }}</textarea>
                        @error('prompt_template')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        Save Prompt
                    </button>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">Existing Category Prompts</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">App</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Prompt Template</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($groupedPrompts as $appId => $appPrompts)
                                @foreach($appPrompts as $prompt)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $prompt->app?->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-700">{{ $prompt->category_name }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-700">
                                            <div class="max-w-xl truncate" title="{{ $prompt->prompt_template }}">
                                                {{ $prompt->prompt_template }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-700">
                                            <div class="flex items-center gap-2">
                                                <button type="button" class="rounded-md border border-amber-300 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                                                    Edit
                                                </button>
                                                <button type="button" class="rounded-md border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No category prompts found yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
