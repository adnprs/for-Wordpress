@php
    $title = 'Categories & Prompts';
    $subtitle = 'Define site-specific category names and their hidden AI prompts.';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Add Category Prompt</h3>

            <form method="POST" action="{{ route('admin.prompts.store') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="app_id" class="mb-1 block text-sm font-medium text-slate-700">Select Site/App</label>
                    <select id="app_id" name="app_id" required class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose module</option>
                        @foreach($apps as $app)
                            <option value="{{ $app->id }}" @selected(old('app_id') == $app->id)>{{ $app->name }}</option>
                        @endforeach
                    </select>
                    @error('app_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category_name" class="mb-1 block text-sm font-medium text-slate-700">Category Name</label>
                    <input id="category_name" name="category_name" type="text" value="{{ old('category_name') }}" required placeholder="e.g. Latest Jobs" class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('category_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="prompt_template" class="mb-1 block text-sm font-medium text-slate-700">Hidden AI Prompt</label>
                    <textarea id="prompt_template" name="prompt_template" rows="7" required placeholder="Write the hidden prompt template used by AI content generation..." class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('prompt_template') }}</textarea>
                    @error('prompt_template')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Save Category Prompt
                </button>
            </form>
        </section>

        <section class="space-y-4"
                 x-data="{ editing: {{ (int) old('_editing', 0) ?: 'null' }} }"
                 x-init="$watch('editing', id => document.body.classList.toggle('overflow-hidden', id !== null)); if (editing !== null) document.body.classList.add('overflow-hidden')"
                 @keydown.escape.window="editing = null">
            <div class="px-1">
                <h3 class="text-lg font-semibold text-slate-900">Existing Category Prompts</h3>
            </div>

            <div class="w-full overflow-x-auto md:overflow-x-visible">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
                    <table class="table-fixed w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="w-2/12 px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Site/App</th>
                        <th class="w-2/12 px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="w-6/12 px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hidden AI Prompt</th>
                        <th class="w-2/12 px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($prompts as $prompt)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900">{{ $prompt->app?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">{{ $prompt->category_name }}</td>
                            <td class="px-6 py-4 whitespace-normal break-words text-sm text-gray-600">
                                <div>{{ $prompt->prompt_template }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                <button type="button" @click="editing = {{ $prompt->id }}" aria-haspopup="dialog" class="inline-flex min-h-11 items-center text-indigo-600 hover:text-indigo-900 font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.prompts.destroy', $prompt) }}" onsubmit="return confirm('Delete this category prompt?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-500">No prompts configured yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            @foreach($prompts as $prompt)
                @php
                    $isEditedWithErrors = (int) old('_editing') === $prompt->id;
                    $nameValue = $isEditedWithErrors ? old('category_name') : $prompt->category_name;
                    $templateValue = $isEditedWithErrors ? old('prompt_template') : $prompt->prompt_template;
                @endphp
                <div x-show="editing === {{ $prompt->id }}" style="display: none;"
                     class="fixed inset-0 z-50 flex items-stretch justify-center bg-slate-900/50 sm:items-center sm:p-6"
                     @click.self="editing = null">
                    <div role="dialog" aria-modal="true" aria-labelledby="edit-title-{{ $prompt->id }}"
                         class="flex h-full w-full flex-col bg-white sm:h-auto sm:max-h-[90vh] sm:max-w-2xl sm:rounded-2xl sm:shadow-xl">
                        <form method="POST" action="{{ route('admin.prompts.update', $prompt) }}" class="flex min-h-0 flex-1 flex-col">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_editing" value="{{ $prompt->id }}">

                            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-6">
                                <h3 id="edit-title-{{ $prompt->id }}" class="text-lg font-semibold text-slate-900">Edit Category Prompt</h3>
                                <button type="button" @click="editing = null" aria-label="Close" class="-mr-2 inline-flex h-11 w-11 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><path d="M5 5l10 10M15 5L5 15"/></svg>
                                </button>
                            </div>

                            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-4 py-4 sm:px-6">
                                <p class="text-sm text-slate-500">{{ $prompt->app?->name ?? 'N/A' }}</p>

                                <div>
                                    <label for="edit-name-{{ $prompt->id }}" class="mb-1 block text-sm font-medium text-slate-700">Category Name</label>
                                    <input id="edit-name-{{ $prompt->id }}" name="category_name" type="text" value="{{ $nameValue }}" required maxlength="255" class="min-h-11 w-full rounded-lg border-slate-300 text-base shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                    @if($isEditedWithErrors)@error('category_name')<p class="mt-1 text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror @endif
                                </div>

                                <div>
                                    <label for="edit-template-{{ $prompt->id }}" class="mb-1 block text-sm font-medium text-slate-700">Hidden AI Prompt</label>
                                    <textarea id="edit-template-{{ $prompt->id }}" name="prompt_template" rows="12" required class="w-full rounded-lg border-slate-300 text-base shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $templateValue }}</textarea>
                                    @if($isEditedWithErrors)@error('prompt_template')<p class="mt-1 text-sm text-rose-600" role="alert">{{ $message }}</p>@enderror @endif
                                </div>
                            </div>

                            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 px-4 py-3 sm:flex-row sm:justify-end sm:px-6">
                                <button type="button" @click="editing = null" class="min-h-11 rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                    Cancel
                                </button>
                                <button type="submit" class="min-h-11 rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </section>
    </div>
@endsection