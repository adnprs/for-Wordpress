@php
    $title = 'Manage Sites/Apps';
    $subtitle = 'Control all enterprise modules across WordPress sites and Android apps.';
@endphp

@extends('layouts.admin')

@section('content')
    <div x-data="{ selectedApp: 'new' }" class="mx-auto w-full max-w-7xl space-y-6">
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Select &amp; Edit Workspace Module</h3>
            <p class="mt-1 text-sm text-gray-500">Choose whether you want to add a new site/app or edit an existing one.</p>

            <div class="mt-5">
                <label for="app_selector" class="mb-2 block text-sm font-medium text-gray-700">Workspace Module</label>
                <select id="app_selector" x-model="selectedApp" class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="new">+ Add New Site/App</option>
                    @foreach($apps as $app)
                        <option value="{{ $app->id }}">{{ $app->name }}</option>
                    @endforeach
                </select>
            </div>
        </section>

        <section x-show="selectedApp === 'new'" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="text-xl font-semibold text-gray-900">Add New Workspace Module</h3>

            <form method="POST" action="{{ route('admin.apps.store') }}" class="mt-6 space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. JobShorts" />
                        @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="type" class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select type</option>
                            <option value="wordpress" @selected(old('type') === 'wordpress')>WordPress</option>
                            <option value="android" @selected(old('type') === 'android')>Android</option>
                        </select>
                        @error('type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                        </select>
                        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="wp_url" class="mb-1 block text-sm font-medium text-gray-700">WP URL</label>
                        <input id="wp_url" name="wp_url" type="url" value="{{ old('wp_url') }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://example.com" />
                        @error('wp_url')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="wp_username" class="mb-1 block text-sm font-medium text-gray-700">WP Username</label>
                        <input id="wp_username" name="wp_username" type="text" value="{{ old('wp_username') }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="WordPress username" />
                        @error('wp_username')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="wp_password" class="mb-1 block text-sm font-medium text-gray-700">WP App Password</label>
                        <input id="wp_password" name="wp_password" type="text" value="{{ old('wp_password') }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="xxxx xxxx xxxx xxxx" />
                        @error('wp_password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 md:w-auto">
                    Add Site/App
                </button>
            </form>
        </section>

        @foreach($apps as $app)
            <section x-show="selectedApp === '{{ $app->id }}'" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-xl font-semibold text-gray-900">Edit Workspace Module</h3>
                <p class="mt-1 text-sm text-gray-500">Update configuration for <span class="font-medium text-gray-700">{{ $app->name }}</span>.</p>

                <form method="POST" action="{{ route('admin.apps.update', $app->id) }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="name_{{ $app->id }}" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                            <input id="name_{{ $app->id }}" name="name" type="text" value="{{ $app->name }}" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label for="type_{{ $app->id }}" class="mb-1 block text-sm font-medium text-gray-700">Type</label>
                            <select id="type_{{ $app->id }}" name="type" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="wordpress" @selected($app->type === 'wordpress')>WordPress</option>
                                <option value="android" @selected($app->type === 'android')>Android</option>
                            </select>
                        </div>

                        <div>
                            <label for="status_{{ $app->id }}" class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                            <select id="status_{{ $app->id }}" name="status" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active" @selected($app->status === 'active')>Active</option>
                                <option value="inactive" @selected($app->status === 'inactive')>Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label for="wp_url_{{ $app->id }}" class="mb-1 block text-sm font-medium text-gray-700">WP URL</label>
                            <input id="wp_url_{{ $app->id }}" name="wp_url" type="url" value="{{ $app->wp_url }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://example.com" />
                        </div>

                        <div>
                            <label for="wp_username_{{ $app->id }}" class="mb-1 block text-sm font-medium text-gray-700">WP Username</label>
                            <input id="wp_username_{{ $app->id }}" name="wp_username" type="text" value="{{ $app->wp_username }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="WordPress username" />
                        </div>

                        <div>
                            <label for="wp_password_{{ $app->id }}" class="mb-1 block text-sm font-medium text-gray-700">WP App Password</label>
                            <input id="wp_password_{{ $app->id }}" name="wp_password" type="text" value="{{ $app->wp_password }}" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="xxxx xxxx xxxx xxxx" />
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 sm:w-auto">
                            Save Changes
                        </button>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.apps.destroy', $app->id) }}" class="mt-3" onsubmit="return confirm('Delete this site/app?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
                        Delete
                    </button>
                </form>
            </section>
        @endforeach

        @if($apps->isEmpty())
            <div class="rounded-xl border border-gray-200 bg-white p-6 text-sm text-gray-500 shadow-sm">
                No sites/apps found. Use the form above to add your first workspace module.
            </div>
        @endif
    </div>
@endsection