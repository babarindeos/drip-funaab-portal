<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conference';

    /**
     * The committee's remark is internal unless the admin chooses to share it
     * with the applicant. This records that choice.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('conference_applications', 'decision_remark_shared')) {
            Schema::table('conference_applications', function (Blueprint $table) {
                $table->boolean('decision_remark_shared')->default(false)->after('decision_remark');
            });
        }
    }

    public function down(): void
    {
        // intentionally empty: never drop data on rollback
    }
};
