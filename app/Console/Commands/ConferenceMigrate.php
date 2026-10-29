<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConferenceMigrate extends Command
{
    protected $signature = 'conference:migrate
                            {--create-database : Create the conference database first if it does not exist}
                            {--force : Run in production without asking}';

    protected $description = 'Create the Conference Grant tables in the separate conference database';

    public function handle(): int
    {
        $connection = config('conference.connection', 'conference');
        $settings = config("database.connections.{$connection}");

        if (! $settings) {
            $this->error("The '{$connection}' database connection is not defined yet.");
            $this->line('Add it to config/database.php (see INTEGRATION.md), then run this again.');
            return 1;
        }

        if ($settings['database'] === config('database.connections.'.config('database.default').'.database')) {
            $this->error("CONF_DB_DATABASE ('{$settings['database']}') is the same database as the main portal.");
            $this->line('Set CONF_DB_DATABASE in .env to a different name (for example drip_conference), run `php artisan config:clear`, then try again.');
            return 1;
        }

        if ($this->option('create-database') && ! $this->createDatabase($settings)) {
            return 1;
        }

        // Always pass --database and --path, so the main portal database is never touched.
        return $this->call('migrate', [
            '--database' => $connection,
            '--path' => 'database/migrations/conference',
            '--force' => (bool) $this->option('force'),
        ]);
    }

    private function createDatabase(array $settings): bool
    {
        $name = $settings['database'];

        if (! preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            $this->error("'{$name}' is not a safe database name (use letters, numbers and underscores only).");
            return false;
        }

        try {
            $dsn = 'mysql:host='.$settings['host'].';port='.$settings['port'];
            $pdo = new \PDO($dsn, $settings['username'], $settings['password'], [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->info("Database '{$name}' is ready.");

            return true;
        } catch (\Throwable $e) {
            $this->error('Could not create the database: '.$e->getMessage());
            $this->line("Create it yourself in MySQL instead:  CREATE DATABASE {$name} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            return false;
        }
    }
}
