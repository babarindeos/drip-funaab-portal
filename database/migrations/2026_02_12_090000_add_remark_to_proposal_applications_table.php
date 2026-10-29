<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the admin decision remark column.
     *
     * Safe to run even if the column was already added by hand or by an
     * earlier copy of the project: it only adds the column when it is missing.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('proposal_applications', 'remark')) {
            Schema::table('proposal_applications', function (Blueprint $table) {
                $table->text('remark')->nullable()->after('status');
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
