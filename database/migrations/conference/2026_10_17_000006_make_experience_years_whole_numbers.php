<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'conference';

    /**
     * "Duration of entire work experience" and "Number of years spent in the
     * institution" are whole numbers of years. Existing decimals (e.g. 12.5)
     * are rounded. Safe to run again: a column that is already a whole number
     * is left alone.
     */
    public function up(): void
    {
        $db = DB::connection($this->connection);

        foreach (['work_experience_years', 'years_in_institution'] as $column) {
            $current = $db->selectOne(
                'SELECT DATA_TYPE AS type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['conference_applications', $column]
            );

            if ($current && strtolower($current->type) !== 'tinyint') {
                $db->statement("UPDATE conference_applications SET {$column} = ROUND({$column}) WHERE {$column} IS NOT NULL");
                $db->statement("ALTER TABLE conference_applications MODIFY {$column} TINYINT UNSIGNED NULL");
            }
        }
    }

    public function down(): void
    {
        // intentionally empty: never change data on rollback
    }
};
