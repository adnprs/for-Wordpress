<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_modules_match_required_blog_hub_configuration(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('apps', 4);

        $this->assertDatabaseHas('apps', ['name' => 'JOBS WALE BHAIYA', 'type' => App::TYPE_WORDPRESS]);
        $this->assertDatabaseHas('apps', ['name' => 'JobShorts', 'type' => App::TYPE_ANDROID]);
        $this->assertDatabaseHas('apps', ['name' => 'BPSC Teacher News', 'type' => App::TYPE_WORDPRESS]);
        $this->assertDatabaseHas('apps', ['name' => 'ADSDELHI', 'type' => App::TYPE_WORDPRESS]);
    }

    public function test_admin_can_login_and_see_all_module_cards_on_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@test.com')->firstOrFail();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('JOBS WALE BHAIYA');
        $response->assertSee('JobShorts');
        $response->assertSee('BPSC Teacher News');
        $response->assertSee('ADSDELHI');
        $response->assertSee('WordPress Site');
        $response->assertSee('Android App');
        $response->assertSee('Open Workspace');
    }

    public function test_open_workspace_navigates_to_specific_module_area(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@test.com')->firstOrFail();
        $app = App::where('name', 'ADSDELHI')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('dashboard.workspace', $app->id));

        $response->assertOk();
        $response->assertSee('Workspace: ADSDELHI');
    }
}
