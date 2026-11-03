<?php

namespace Tests\Feature;

use App\Models\App;
use App\Models\CategoryPrompt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryPromptDuplicatesTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION_PATH = 'database/migrations/2026_11_03_000001_dedupe_category_prompts_and_add_unique_index.php';

    private User $admin;

    private App $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->site = App::create(['name' => 'Jobs Wale Bhaiya', 'type' => 'wordpress', 'status' => 'active']);
    }

    private function asAdmin(): static
    {
        return $this->actingAs($this->admin)->withSession(['active_mode' => 'admin']);
    }

    /** Roll the dedupe migration back so duplicate rows can be inserted, as they exist in production. */
    private function withoutUniqueIndex(): void
    {
        Artisan::call('migrate:rollback', ['--path' => self::MIGRATION_PATH, '--realpath' => false]);
    }

    /** State of production before this migration: duplicates present, no backup table yet. */
    private function asProductionBeforeMigration(): void
    {
        $this->withoutUniqueIndex();
        Schema::dropIfExists('category_prompts_backup_20261103');
    }

    private function runDedupeMigration(): void
    {
        Artisan::call('migrate', ['--path' => self::MIGRATION_PATH, '--realpath' => false]);
    }

    private function insertPrompt(int $id, string $name, string $text, ?int $appId = null): void
    {
        DB::table('category_prompts')->insert([
            'id' => $id,
            'app_id' => $appId ?? $this->site->id,
            'category_name' => $name,
            'prompt_template' => $text,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_creating_a_duplicate_category_for_the_same_app_is_rejected_with_a_validation_error(): void
    {
        CategoryPrompt::create(['app_id' => $this->site->id, 'category_name' => 'Result', 'prompt_template' => 'first']);

        $response = $this->asAdmin()->post(route('admin.prompts.store'), [
            'app_id' => $this->site->id,
            'category_name' => 'Result',
            'prompt_template' => 'second',
        ]);

        $response->assertSessionHasErrors('category_name');
        $this->assertSame(1, CategoryPrompt::where('category_name', 'Result')->count());
    }

    public function test_same_category_name_is_allowed_for_a_different_app(): void
    {
        $other = App::create(['name' => 'Other Site', 'type' => 'wordpress', 'status' => 'active']);
        CategoryPrompt::create(['app_id' => $this->site->id, 'category_name' => 'Result', 'prompt_template' => 'first']);

        $this->asAdmin()->post(route('admin.prompts.store'), [
            'app_id' => $other->id,
            'category_name' => 'Result',
            'prompt_template' => 'second',
        ])->assertSessionHasNoErrors();

        $this->assertSame(2, CategoryPrompt::where('category_name', 'Result')->count());
    }

    public function test_renaming_a_prompt_to_an_existing_category_is_rejected_but_keeping_its_own_name_is_fine(): void
    {
        $a = CategoryPrompt::create(['app_id' => $this->site->id, 'category_name' => 'Result', 'prompt_template' => 'a']);
        CategoryPrompt::create(['app_id' => $this->site->id, 'category_name' => 'Admit Card', 'prompt_template' => 'b']);

        $this->asAdmin()->put(route('admin.prompts.update', $a), [
            'category_name' => 'Admit Card',
            'prompt_template' => 'a',
        ])->assertSessionHasErrors('category_name');

        $this->asAdmin()->put(route('admin.prompts.update', $a), [
            'category_name' => 'Result',
            'prompt_template' => 'a edited',
        ])->assertSessionHasNoErrors();
    }

    public function test_migration_merges_duplicates_keeping_lowest_id_when_neither_is_referenced(): void
    {
        $this->withoutUniqueIndex();
        $this->insertPrompt(2, 'Admit Card', 'same text');
        $this->insertPrompt(13, 'Admit Card', 'same text');
        $this->insertPrompt(3, 'Result', 'result text');

        $this->runDedupeMigration();

        $this->assertSame([2, 3], CategoryPrompt::orderBy('id')->pluck('id')->all());
    }

    public function test_migration_keeps_the_referenced_row_and_repoints_references_before_deleting_others(): void
    {
        Schema::create('prompt_refs', function ($table) {
            $table->id();
            $table->foreignId('category_prompt_id')->constrained('category_prompts');
        });

        $this->withoutUniqueIndex();
        $this->insertPrompt(1, 'Latest Jobs', 'text');
        $this->insertPrompt(12, 'Latest Jobs', 'text');
        $this->insertPrompt(5, 'Admission', 'text');
        $this->insertPrompt(16, 'Admission', 'text');
        DB::table('prompt_refs')->insert([
            ['category_prompt_id' => 12],
            ['category_prompt_id' => 12],
            ['category_prompt_id' => 5],
        ]);

        $this->runDedupeMigration();

        $this->assertSame([5, 12], CategoryPrompt::orderBy('id')->pluck('id')->all());
        $this->assertSame(3, DB::table('prompt_refs')->whereIn('category_prompt_id', [5, 12])->count());
    }

    public function test_migration_repoints_references_from_losing_rows_when_both_are_referenced(): void
    {
        Schema::create('prompt_refs', function ($table) {
            $table->id();
            $table->foreignId('category_prompt_id')->constrained('category_prompts');
        });

        $this->withoutUniqueIndex();
        $this->insertPrompt(6, 'Scholarship', 'text');
        $this->insertPrompt(17, 'Scholarship', 'text');
        DB::table('prompt_refs')->insert([
            ['category_prompt_id' => 6],
            ['category_prompt_id' => 17],
            ['category_prompt_id' => 17],
        ]);

        $this->runDedupeMigration();

        $this->assertSame([17], CategoryPrompt::pluck('id')->all(), 'row with most references wins');
        $this->assertSame(3, DB::table('prompt_refs')->where('category_prompt_id', 17)->count());
    }

    public function test_migration_backs_up_table_first_and_adds_unique_index(): void
    {
        $this->asProductionBeforeMigration();
        $this->insertPrompt(1, 'Result', 'x');
        $this->insertPrompt(12, 'Result', 'x');

        $this->runDedupeMigration();

        $this->assertTrue(Schema::hasTable('category_prompts_backup_20261103'));
        $this->assertSame(2, DB::table('category_prompts_backup_20261103')->count(), 'backup holds the pre-merge rows');

        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->insertPrompt(99, 'Result', 'y');
    }

    public function test_migration_is_idempotent(): void
    {
        $this->asProductionBeforeMigration();
        $this->insertPrompt(1, 'Result', 'x');
        $this->insertPrompt(12, 'Result', 'x');

        $this->runDedupeMigration();
        $this->withoutUniqueIndex();
        $this->runDedupeMigration();

        $this->assertSame([1], CategoryPrompt::pluck('id')->all());
        $this->assertSame(2, DB::table('category_prompts_backup_20261103')->count(), 'backup is not overwritten');
    }

    public function test_latest_jobs_prompt_text_is_preserved_exactly_after_merge(): void
    {
        $text = str_repeat("Line with unicode \u{2713} à¤¹à¤¿à¤‚à¤¦à¥€, {keyword} & <b>tags</b>\r\n", 190);
        $text = substr($text, 0, 10974) ?: $text;
        $this->assertGreaterThan(10000, strlen($text));

        $this->withoutUniqueIndex();
        $this->insertPrompt(1, 'Latest Jobs', $text);
        $this->insertPrompt(12, 'Latest Jobs', $text);

        $this->runDedupeMigration();

        $kept = CategoryPrompt::where('category_name', 'Latest Jobs')->get();
        $this->assertCount(1, $kept);
        $this->assertSame($text, $kept->first()->prompt_template);
        $this->assertSame(md5($text), md5(DB::table('category_prompts')->where('id', 1)->value('prompt_template')));
    }

    public function test_content_generator_lists_each_category_once_after_merge(): void
    {
        $this->withoutUniqueIndex();
        foreach ([[1, 'Latest Jobs'], [2, 'Admit Card'], [3, 'Result']] as [$id, $name]) {
            $this->insertPrompt($id, $name, 'text');
            $this->insertPrompt($id + 10, $name, 'text');
        }

        $this->runDedupeMigration();

        $response = $this->asAdmin()->get(route('workspace.show', $this->site->id));

        $response->assertOk();
        $categories = $response->viewData('categories');
        $this->assertSame(['Admit Card', 'Latest Jobs', 'Result'], $categories->sort()->values()->all());
    }

    public function test_put_updates_name_and_template_without_app_id_and_page_shows_new_text(): void
    {
        $prompt = CategoryPrompt::create(['app_id' => $this->site->id, 'category_name' => 'Old Name', 'prompt_template' => 'old template']);

        $this->asAdmin()->put(route('admin.prompts.update', $prompt), [
            'category_name' => 'New Name',
            'prompt_template' => 'brand new template text',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('category_prompts', [
            'id' => $prompt->id,
            'app_id' => $this->site->id,
            'category_name' => 'New Name',
            'prompt_template' => 'brand new template text',
        ]);

        $this->asAdmin()->get(route('admin.prompts.index'))
            ->assertOk()
            ->assertSee('New Name')
            ->assertSee('brand new template text')
            ->assertDontSee('old template');
    }

    public function test_edit_button_opens_a_prefilled_form_that_submits_to_the_update_route(): void
    {
        $prompt = CategoryPrompt::create([
            'app_id' => $this->site->id,
            'category_name' => 'Answer Key',
            'prompt_template' => "Line one\nLine <two> & {keyword}",
        ]);

        $html = $this->asAdmin()->get(route('admin.prompts.index'))->assertOk()->getContent();

        // The Edit button is wired to something (previously it had no handler at all).
        $this->assertMatchesRegularExpression('/<button[^>]*@click="[^"]*editing\s*=\s*' . $prompt->id . '[^"]*"[^>]*>\s*Edit/s', $html);

        // A form posting PUT to the update route exists, prefilled with the saved values.
        $this->assertStringContainsString('action="' . route('admin.prompts.update', $prompt) . '"', $html);
        $this->assertStringContainsString('name="_method" value="PUT"', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*name="category_name"[^>]*value="Answer Key"/', $html);
        $this->assertMatchesRegularExpression('/<textarea[^>]*name="prompt_template"[^>]*>\s*Line one\nLine &lt;two&gt; &amp; \{keyword\}\s*<\/textarea>/', $html);
    }
}
