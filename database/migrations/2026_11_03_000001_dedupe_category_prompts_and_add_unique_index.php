<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const BACKUP_TABLE = 'category_prompts_backup_20261103';

    private const UNIQUE_INDEX = 'category_prompts_app_id_category_name_unique';

    public function up(): void
    {
        // 1. Back up first. Never overwritten, never dropped by down().
        if (! Schema::hasTable(self::BACKUP_TABLE)) {
            DB::statement('CREATE TABLE '.self::BACKUP_TABLE.' AS SELECT * FROM category_prompts');
        }

        // 2. Merge duplicates (same app + case/whitespace-insensitive name).
        DB::transaction(fn () => $this->mergeDuplicates());

        // 3. Make it impossible to recreate them.
        if (! Schema::hasIndex('category_prompts', self::UNIQUE_INDEX)) {
            Schema::table('category_prompts', function (Blueprint $table) {
                $table->unique(['app_id', 'category_name'], self::UNIQUE_INDEX);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('category_prompts', self::UNIQUE_INDEX)) {
            Schema::table('category_prompts', function (Blueprint $table) {
                $table->dropUnique(self::UNIQUE_INDEX);
            });
        }
    }

    private function mergeDuplicates(): void
    {
        $references = $this->referencingColumns();

        $groups = DB::table('category_prompts')
            ->orderBy('id')
            ->get(['id', 'app_id', 'category_name'])
            ->groupBy(fn ($row) => $row->app_id.'|'.mb_strtolower(trim($row->category_name)))
            ->filter(fn ($rows) => $rows->count() > 1);

        foreach ($groups as $rows) {
            $ids = $rows->pluck('id')->all();

            // Keep the most-referenced row; ties (including "none referenced") go to the lowest id.
            $keeper = collect($ids)
                ->sortBy(fn ($id) => [-$this->countReferences($references, $id), $id])
                ->first();

            $losers = array_values(array_diff($ids, [$keeper]));

            foreach ($references as [$table, $column]) {
                DB::table($table)->whereIn($column, $losers)->update([$column => $keeper]);
            }

            DB::table('category_prompts')->whereIn('id', $losers)->delete();
        }
    }

    /** @return array<int, array{0: string, 1: string}> [table, column] pairs holding a category_prompts id. */
    private function referencingColumns(): array
    {
        $found = [];

        foreach (Schema::getTables() as $table) {
            $name = $table['name'];

            if ($name === 'category_prompts' || $name === self::BACKUP_TABLE) {
                continue;
            }

            foreach (Schema::getForeignKeys($name) as $fk) {
                if ($fk['foreign_table'] === 'category_prompts' && count($fk['columns']) === 1) {
                    $found[] = [$name, $fk['columns'][0]];
                }
            }
        }

        return $found;
    }

    private function countReferences(array $references, int $id): int
    {
        return array_sum(array_map(
            fn ($ref) => DB::table($ref[0])->where($ref[1], $id)->count(),
            $references,
        ));
    }
};
