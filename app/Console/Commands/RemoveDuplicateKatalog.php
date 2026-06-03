<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateKatalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'katalog:dedup {--dry-run : Show duplicates without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate rows from katalog table keeping the smallest id_katalog per id_layanan+varian+harga';

    public function handle()
    {
        $this->info('Looking for duplicate katalog rows (grouped by id_layanan, varian, harga)...');

        $groups = DB::table('katalog')
            ->selectRaw('id_layanan, COALESCE(varian, "") as varian, COALESCE(harga,0) as harga, GROUP_CONCAT(id_katalog ORDER BY id_katalog ASC) as ids, COUNT(*) as cnt')
            ->groupBy('id_layanan', 'varian', 'harga')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($groups->isEmpty()) {
            $this->info('No duplicates found.');
            return 0;
        }

        $totalToDelete = 0;
        foreach ($groups as $g) {
            $ids = explode(',', $g->ids);
            // keep first (smallest id_katalog), delete rest
            $toDelete = array_slice($ids, 1);
            $totalToDelete += count($toDelete);
            $this->line("Group id_layanan={$g->id_layanan} varian='{$g->varian}' harga={$g->harga} => keeping {$ids[0]}, deleting: " . implode(',', $toDelete));
        }

        $this->line("Total rows to delete: {$totalToDelete}");

        if ($this->option('dry-run')) {
            $this->info('Dry run enabled — no rows were deleted.');
            return 0;
        }

        // Perform deletion inside transaction
        DB::transaction(function () use ($groups) {
            foreach ($groups as $g) {
                $ids = explode(',', $g->ids);
                $toDelete = array_slice($ids, 1);
                if (!empty($toDelete)) {
                    DB::table('katalog')->whereIn('id_katalog', $toDelete)->delete();
                }
            }
        });

        $this->info('Duplicate katalog rows removed.');
        return 0;
    }
}
