<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleSwitchController extends Controller
{
    public function showSelection(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        return view('auth.choose-role', [
            'activeMode' => $request->session()->get('active_mode'),
        ]);
    }

    public function setRole(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            $request->session()->put('active_mode', 'user');

            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'mode' => ['required', 'in:admin,user'],
        ]);

        $request->session()->put('active_mode', $validated['mode']);

        if ($validated['mode'] === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}