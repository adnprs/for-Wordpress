<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                Generated Content - {{ $app->name }}
            </h2>

            <a
                href="{{ route('workspace.show', $app->id) }}"
                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Back to Workspace
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('workspace.publish', $app->id) }}" x-data="{ isPublishing: false }" @submit="isPublishing = true">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium mt-4 mb-1 text-gray-700" for="title">
                            Post Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Enter SEO Title for WordPress"
                        >
                    </div>

                    <label for="generated_html" class="mb-2 block text-sm font-semibold text-slate-700">
                        Generated HTML
                    </label>

                    <textarea
                        id="generated_html"
                        name="content"
                        rows="20"
                        class="w-full rounded-lg border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old('content', $generatedHtml) }}</textarea>

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <button type="submit" :disabled="isPublishing" class="mt-6 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-md shadow-sm transition-all flex justify-center items-center">
                            <span x-show="!isPublishing">Publish to WordPress</span>
                            <span x-show="isPublishing" class="flex items-center" x-cloak>
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Processing...
                            </span>
                        </button>

                        <a
                            href="{{ route('workspace.show', $app->id) }}"
                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>