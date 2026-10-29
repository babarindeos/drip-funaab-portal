<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the reviewer's general comment column.
     *
     * Safe to run even if the column was already added by hand or by an
     * earlier copy of the project: it only adds the column when it is missing.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('scoring_sheets', 'comment')) {
            Schema::table('scoring_sheets', function (Blueprint $table) {
                $table->text('comment')->nullable()->after('scoring_guide_19');
            });
        }
    }

    /**
     * Intentionally empty: this column may already have existed before this
     * migration ran, so rolling back must not drop data it did not create.
     */
    public function down(): void
    {
        //
    }
};
