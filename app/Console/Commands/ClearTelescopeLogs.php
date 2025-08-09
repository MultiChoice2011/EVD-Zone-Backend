<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearTelescopeLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telescope:clear-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Telescope logs from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear Telescope entries
        $connection = DB::connection(config('telescope.storage.database.connection'));
        $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
        $connection->table('telescope_entries')->truncate();
        $connection->table('telescope_entries_tags')->truncate();
        $connection->table('telescope_monitoring')->truncate();
        $connection->statement('SET FOREIGN_KEY_CHECKS=1;');

        Log::info('Telescope logs have been cleared.');
    }
}
