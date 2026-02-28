<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const REQUIRED_MODULES = [
        ['name' => 'JOBS WALE BHAIYA', 'type' => App::TYPE_WORDPRESS],
        ['name' => 'JobShorts', 'type' => App::TYPE_ANDROID],
        ['name' => 'BPSC Teacher News', 'type' => App::TYPE_WORDPRESS],
        ['name' => 'ADSDELHI', 'type' => App::TYPE_WORDPRESS],
    ];

    public function index(): View
    {
        foreach (self::REQUIRED_MODULES as $module) {
            App::updateOrCreate(
                ['name' => $module['name']],
                [
                    'type' => $module['type'],
                    'status' => 'active',
                ]
            );
        }

        $moduleNames = array_column(self::REQUIRED_MODULES, 'name');

        $apps = App::query()
            ->whereIn('name', $moduleNames)
            ->active()
            ->orderByRaw("CASE name
                WHEN 'JOBS WALE BHAIYA' THEN 1
                WHEN 'JobShorts' THEN 2
                WHEN 'BPSC Teacher News' THEN 3
                WHEN 'ADSDELHI' THEN 4
                ELSE 5
            END")
            ->get();

        return view('dashboard', compact('apps'));
    }
}
