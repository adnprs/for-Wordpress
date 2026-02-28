<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-900">Select Your Workspace Mode</h1>
        <p class="mt-2 text-sm text-slate-600">Choose how you want to continue in Dashboard Hub.</p>
    </div>

    <form method="POST" action="{{ route('role.set') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="mode" :value="__('Workspace Mode')" />
            <select
                id="mode"
                name="mode"
                required
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="" disabled {{ old('mode', $activeMode) ? '' : 'selected' }}>Select mode</option>
                <option value="admin" {{ old('mode', $activeMode) === 'admin' ? 'selected' : '' }}>
                    Super Admin (Manage Sites, Team, Prompts)
                </option>
                <option value="user" {{ old('mode', $activeMode) === 'user' ? 'selected' : '' }}>
                    User Workspace (Create Posts &amp; Content)
                </option>
            </select>
            <x-input-error :messages="$errors->get('mode')" class="mt-2" />
        </div>

        <button
            type="submit"
            class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            Continue
        </button>
    </form>
</x-guest-layout>