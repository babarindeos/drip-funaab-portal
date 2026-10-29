<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the Title Page upload column the application form and controller use.
     *
     * Safe to run even if the column was already added by hand or by an
     * earlier copy of the project: it only adds the column when it is missing.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('proposal_applications', 'proposal_title_file')) {
            Schema::table('proposal_applications', function (Blueprint $table) {
                $table->string('proposal_title_file')->nullable()->after('proposal_file');
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
