<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conference';

    /**
     * Eligibility to apply: is the applicant a confirmed member of staff, and
     * since when. (null = not answered yet.) Each column is only added if it is missing.
     */
    public function up(): void
    {
        Schema::table('conference_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('conference_applications', 'is_confirmed_staff')) {
                $table->boolean('is_confirmed_staff')->nullable()->after('years_in_institution');
            }
            if (! Schema::hasColumn('conference_applications', 'confirmation_date')) {
                $table->date('confirmation_date')->nullable()->after('is_confirmed_staff');
            }
        });
    }

    public function down(): void
    {
        // intentionally empty: never drop data on rollback
    }
};
