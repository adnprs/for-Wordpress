<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\CategoryPrompt;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const REQUIRED_MODULES = [
        ['name' => 'JOBS WALE BHAIYA', 'type' => App::TYPE_WORDPRESS],
        ['name' => 'JobShorts', 'type' => App::TYPE_ANDROID],
        ['name' => 'BPSC Teacher News', 'type' => App::TYPE_WORDPRESS],
        ['name' => 'ADSDELHI', 'type' => App::TYPE_WORDPRESS],
    ];

    public function dashboard(): View
    {
        $this->ensureRequiredApps();

        return view('admin.dashboard', [
            'appsCount' => App::query()->count(),
            'promptsCount' => CategoryPrompt::query()->count(),
            'teamCount' => User::query()->count(),
            'activeAppsCount' => App::query()->where('status', 'active')->count(),
        ]);
    }

    public function appsIndex(): View
    {
        $this->ensureRequiredApps();

        return view('admin.apps.index', [
            'apps' => App::query()->orderBy('name')->get(),
        ]);
    }

    public function appsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([App::TYPE_WORDPRESS, App::TYPE_ANDROID])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'wp_url' => ['nullable', 'url', 'max:255'],
            'wp_username' => ['nullable', 'string', 'max:255'],
            'wp_password' => ['nullable', 'string', 'max:255'],
        ]);

        App::create($validated);

        return back()->with('status', 'Site/App created successfully.');
    }

    public function appsUpdate(Request $request, App $app): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in([App::TYPE_WORDPRESS, App::TYPE_ANDROID])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'wp_url' => ['nullable', 'url', 'max:255'],
            'wp_username' => ['nullable', 'string', 'max:255'],
            'wp_password' => ['nullable', 'string', 'max:255'],
        ]);

        $app->update($validated);

        return back()->with('status', 'Site/App updated successfully.');
    }

    public function appsDestroy(App $app): RedirectResponse
    {
        $app->delete();

        return back()->with('status', 'Site/App deleted successfully.');
    }

    public function promptsIndex(): View
    {
        $this->ensureRequiredApps();

        $apps = App::query()->orderBy('name')->get();

        $prompts = CategoryPrompt::query()
            ->with('app')
            ->orderBy('app_id')
            ->orderBy('category_name')
            ->get();

        return view('admin.prompts.index', compact('apps', 'prompts'));
    }

    public function promptsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_id' => ['required', 'exists:apps,id'],
            'category_name' => ['required', 'string', 'max:255'],
            'prompt_template' => ['required', 'string'],
        ]);

        CategoryPrompt::create($validated);

        return back()->with('status', 'Category prompt saved successfully.');
    }

    public function promptsUpdate(Request $request, CategoryPrompt $prompt): RedirectResponse
    {
        $validated = $request->validate([
            'app_id' => ['required', 'exists:apps,id'],
            'category_name' => ['required', 'string', 'max:255'],
            'prompt_template' => ['required', 'string'],
        ]);

        $prompt->update($validated);

        return back()->with('success', 'Category prompt updated successfully.');
    }

    public function promptsDestroy(CategoryPrompt $prompt): RedirectResponse
    {
        $prompt->delete();

        return back()->with('success', 'Category prompt deleted successfully.');
    }

    public function usersIndex(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->latest()->get(),
        ]);
    }

    public function usersStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'editor', 'operator'])],
        ]);

        User::create([
            ...$validated,
            'status' => 'active',
        ]);

        return back()->with('status', 'Team member created successfully.');
    }

    public function usersUpdate(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'editor', 'operator'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('status', 'Team member updated successfully.');
    }

    public function usersDestroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return back()->with('status', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('status', 'Team member removed successfully.');
    }

    private function ensureRequiredApps(): void
    {
        foreach (self::REQUIRED_MODULES as $module) {
            App::query()->updateOrCreate(
                ['name' => $module['name']],
                [
                    'type' => $module['type'],
                    'status' => 'active',
                ]
            );
        }
    }
}
