@extends('dashboard.layout')

@section('title', 'Keyword Brief Builder')
@section('header', 'Keyword Brief Builder')

@section('content')
    <section class="max-w-3xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Generate Keyword Brief</h3>
            <p class="mt-1 text-sm text-slate-500">Fill the form and generate structured content inputs.</p>

            <form class="mt-6 space-y-5" method="POST" action="#">
                @csrf

                <div>
                    <label for="keyword" class="mb-2 block text-sm font-medium text-slate-700">Keyword</label>
                    <input
                        id="keyword"
                        name="keyword"
                        type="text"
                        placeholder="e.g. best interview tips"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                    >
                </div>

                <div>
                    <label for="tone" class="mb-2 block text-sm font-medium text-slate-700">Tone</label>
                    <select
                        id="tone"
                        name="tone"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                    >
                        <option value="">Select tone</option>
                        <option value="professional">Professional</option>
                        <option value="friendly">Friendly</option>
                        <option value="persuasive">Persuasive</option>
                        <option value="technical">Technical</option>
                    </select>
                </div>

                <div>
                    <label for="word_count" class="mb-2 block text-sm font-medium text-slate-700">Word Count</label>
                    <input
                        id="word_count"
                        name="word_count"
                        type="number"
                        min="100"
                        step="50"
                        placeholder="e.g. 1200"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                    >
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                >
                    Generate Draft
                </button>
            </form>
        </div>
    </section>
@endsection