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

        <section class="space-y-4">
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
                                <button type="button" class="text-indigo-600 hover:text-indigo-900 font-semibold">
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
        </section>
    </div>
@endsection