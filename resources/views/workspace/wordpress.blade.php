<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ $app->name }} Workspace
            </h2>

            <a
                href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ activeTab: 'keyword' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 pt-6">
                    <nav class="flex flex-wrap gap-2" aria-label="Workspace Tabs">
                        <button
                            type="button"
                            @click="activeTab = 'keyword'"
                            :class="activeTab === 'keyword' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        >
                            Keyword Generator
                        </button>

                        <button
                            type="button"
                            @click="activeTab = 'document'"
                            :class="activeTab === 'document' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        >
                            Document (PDF/Image) Generator
                        </button>

                        <button
                            type="button"
                            @click="activeTab = 'url'"
                            :class="activeTab === 'url' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold transition"
                        >
                            URL Extractor
                        </button>
                    </nav>
                </div>

                <div class="p-6 sm:p-8">
                    <div x-show="activeTab === 'keyword'" x-cloak>
                        <form method="POST" action="{{ route('workspace.generate', $app->id) }}" class="space-y-5" x-data="{ isGenerating: false }" @submit="isGenerating = true">
                            @csrf
                            <input type="hidden" name="generator_type" value="keyword">

                            <div>
                                <label for="keyword" class="mb-2 block text-sm font-semibold text-slate-700">Keyword</label>
                                <input
                                    id="keyword"
                                    name="keyword"
                                    type="text"
                                    placeholder="Bihar Police Constable Vacancy 2026"
                                    class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                            </div>

                            <div>
                                <label for="keyword_category" class="mb-2 block text-sm font-semibold text-slate-700">Category</label>
                                <select
                                    id="keyword_category"
                                    name="category"
                                    class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select category</option>
                                    @foreach($categories as $categoryId => $categoryName)
                                        <option value="{{ $categoryId }}">{{ $categoryName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" :disabled="isGenerating" class="flex justify-center items-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-75 disabled:cursor-not-allowed">
                                <span x-show="!isGenerating">Generate Post ✨</span>
                                <span x-show="isGenerating" class="flex items-center" x-cloak>
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>

                    <div x-show="activeTab === 'document'" x-cloak>
                        <form method="POST" action="{{ route('workspace.generate', $app->id) }}" enctype="multipart/form-data" class="space-y-5" x-data="{ isGenerating: false }" @submit="isGenerating = true">
                            @csrf
                            <input type="hidden" name="generator_type" value="document">

                            <div>
                                <label for="document" class="mb-2 block text-sm font-semibold text-slate-700">Document Upload</label>
                                <input
                                    id="document"
                                    name="document"
                                    type="file"
                                    accept=".pdf,.jpg,.png"
                                    class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                                    required
                                >
                            </div>

                            <div>
                                <label for="document_category" class="mb-2 block text-sm font-semibold text-slate-700">Category</label>
                                <select
                                    id="document_category"
                                    name="category"
                                    class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select category</option>
                                    @foreach($categories as $categoryId => $categoryName)
                                        <option value="{{ $categoryId }}">{{ $categoryName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" :disabled="isGenerating" class="flex justify-center items-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-75 disabled:cursor-not-allowed">
                                <span x-show="!isGenerating">Generate Post ✨</span>
                                <span x-show="isGenerating" class="flex items-center" x-cloak>
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>

                    <div x-show="activeTab === 'url'" x-cloak>
                        <form method="POST" action="{{ route('workspace.generate', $app->id) }}" class="space-y-5" x-data="{ isGenerating: false }" @submit="isGenerating = true">
                            @csrf
                            <input type="hidden" name="generator_type" value="url">

                            <div>
                                <label for="source_url" class="mb-2 block text-sm font-semibold text-slate-700">Source URL</label>
                                <input
                                    id="source_url"
                                    name="url"
                                    type="url"
                                    placeholder="https://example.com/notification"
                                    class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                            </div>

                            <div>
                                <label for="url_category" class="mb-2 block text-sm font-semibold text-slate-700">Category</label>
                                <select
                                    id="url_category"
                                    name="category"
                                    class="block w-full rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select category</option>
                                    @foreach($categories as $categoryId => $categoryName)
                                        <option value="{{ $categoryId }}">{{ $categoryName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" :disabled="isGenerating" class="flex justify-center items-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-75 disabled:cursor-not-allowed">
                                <span x-show="!isGenerating">Generate Post ✨</span>
                                <span x-show="isGenerating" class="flex items-center" x-cloak>
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-slate-900">Post History</h3>
                    <p class="mt-1 text-sm text-slate-500">Last 10 posts published/generated by you for this workspace.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Created</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($postHistories as $history)
                            <tr>
                                <td class="px-4 py-4 text-sm font-medium text-slate-900">{{ $history->title }}</td>
                                <td class="px-4 py-4 text-sm">
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                        {{ $history->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-600">{{ $history->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-500">No post history found yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>