@php
    $title = 'Team Management';
    $subtitle = 'Invite and manage enterprise team members with role-based access.';
@endphp

@extends('layouts.admin')

@section('content')
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Invite / Create Team Member</h3>

            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="role" class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                    <select id="role" name="role" required class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="editor" @selected(old('role', 'editor') === 'editor')>Editor</option>
                        <option value="operator" @selected(old('role') === 'operator')>Operator</option>
                    </select>
                    @error('role')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Create Team Member
                </button>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-900">Existing Team Members</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($users as $member)
                        <tr>
                            <td class="px-4 py-4 text-sm font-semibold text-slate-900">{{ $member->name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $member->email }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ ucfirst($member->role) }}</td>
                            <td class="px-4 py-4 text-sm">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $member->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                                    {{ ucfirst($member->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-700">
                                <div class="space-y-2">
                                    <form method="POST" action="{{ route('admin.users.update', $member) }}" class="grid gap-2 md:grid-cols-5">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $member->name }}" class="rounded-md border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <input type="email" name="email" value="{{ $member->email }}" class="rounded-md border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <select name="role" class="rounded-md border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <option value="admin" @selected($member->role === 'admin')>Admin</option>
                                            <option value="editor" @selected($member->role === 'editor')>Editor</option>
                                            <option value="operator" @selected($member->role === 'operator')>Operator</option>
                                        </select>
                                        <select name="status" class="rounded-md border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                            <option value="active" @selected($member->status === 'active')>Active</option>
                                            <option value="inactive" @selected($member->status === 'inactive')>Inactive</option>
                                        </select>
                                        <button type="submit" class="rounded-md bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600">Update</button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.destroy', $member) }}" onsubmit="return confirm('Remove this team member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No team members found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection