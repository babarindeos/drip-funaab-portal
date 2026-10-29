<?php

namespace App\Console\Commands;

use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceApplicationDocument;
use App\Models\Conference\ConferenceApplicationEvent;
use App\Models\Conference\ConferenceCall;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConferenceCheck extends Command
{
    protected $signature = 'conference:check';

    protected $description = 'Check that the Conference Grant database is connected and set up correctly';

    public function handle(): int
    {
        $connection = config('conference.connection', 'conference');

        if (! config("database.connections.{$connection}")) {
            $this->error("The '{$connection}' connection is not defined in config/database.php (see INTEGRATION.md).");
            return 1;
        }

        try {
            $name = DB::connection($connection)->getDatabaseName();
            DB::connection($connection)->getPdo();
        } catch (\Throwable $e) {
            $this->error('Could not connect to the conference database: '.$e->getMessage());
            $this->line('Check CONF_DB_DATABASE in .env, that the database exists, then run `php artisan config:clear`.');
            $this->line('To create it: php artisan conference:migrate --create-database');
            return 1;
        }

        $this->info("Connected to the conference database '{$name}'.");

        $main = DB::connection()->getDatabaseName();
        if ($name === $main) {
            $this->warn("It is the SAME database as the main portal ('{$main}'). Set CONF_DB_DATABASE to a different name.");
        } else {
            $this->line("Main portal database: '{$main}' (separate, as intended).");
        }

        $tables = [
            'conference_calls' => ConferenceCall::class,
            'conference_applications' => ConferenceApplication::class,
            'conference_application_documents' => ConferenceApplicationDocument::class,
            'conference_application_events' => ConferenceApplicationEvent::class,
        ];

        $missing = 0;

        foreach ($tables as $table => $model) {
            if (Schema::connection($connection)->hasTable($table)) {
                $this->line(sprintf('  [ok]      %-36s %d rows', $table, $model::count()));
            } else {
                $this->line(sprintf('  [MISSING] %s', $table));
                $missing++;
            }
        }

        if ($missing > 0) {
            $this->warn('Some tables are missing. Run: php artisan conference:migrate');
            return 1;
        }

        $this->info('Everything looks good.');

        return 0;
    }
}
