<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conference';

    /**
     * One row per TETFund Conference Attendance Nomination Form.
     *
     * Almost every form field is nullable here ON PURPOSE: an applicant can save
     * a draft and come back, so completeness is enforced when the application
     * is submitted, not by the database.
     */
    public function up(): void
    {
        Schema::create('conference_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('conference_call_id')->constrained('conference_calls')->restrictOnDelete();

            // Users live in the main database, so there is no foreign key.
            $table->unsignedBigInteger('user_id')->index();

            $table->string('status', 20)->default('draft')->index(); // draft | submitted | approved | not_approved
            $table->timestamp('submitted_at')->nullable();

            // snapshot of who applied, so reports never depend on the other database
            $table->string('account_email')->nullable();
            $table->string('funaab_email')->nullable();

            // Form items I - vii
            $table->string('institution')->nullable();
            $table->string('candidate_name')->nullable();
            $table->string('gender', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('present_position')->nullable();
            $table->string('staff_category', 20)->nullable();   // teaching | non_teaching
            $table->string('phone', 30)->nullable();
            $table->string('passport_photo')->nullable();

            // Items 1 - 7 : candidate's data / institutional record
            $table->string('department')->nullable();
            $table->string('highest_qualification')->nullable();
            $table->string('area_of_specialisation')->nullable();
            $table->string('staff_rank')->nullable();
            $table->string('grade_level', 50)->nullable();
            $table->date('first_appointment_date')->nullable();
            $table->decimal('work_experience_years', 4, 1)->nullable();
            $table->decimal('years_in_institution', 4, 1)->nullable();

            // Item 8 : conference to be attended
            $table->string('conference_organiser')->nullable();
            $table->string('conference_title')->nullable();
            $table->string('conference_venue')->nullable();
            $table->string('conference_scope', 10)->nullable();      // local | overseas
            $table->string('conference_mode', 10)->nullable();       // in_person | online
            $table->string('presentation_type', 10)->nullable();     // oral | poster
            $table->string('conference_website')->nullable();
            $table->string('conference_email')->nullable();
            $table->date('conference_start_date')->nullable();
            $table->date('conference_end_date')->nullable();
            $table->unsignedTinyInteger('duration_days')->nullable();

            $table->string('cost_currency', 3)->default('NGN');
            $table->decimal('registration_fee', 14, 2)->nullable();
            $table->decimal('flight_cost', 14, 2)->nullable();
            $table->decimal('accommodation_cost', 14, 2)->nullable();
            $table->decimal('feeding_cost', 14, 2)->nullable();
            $table->decimal('excursion_cost', 14, 2)->nullable();

            // Item 9 : conference attendance history (last TETFund-sponsored international conference)
            $table->boolean('previously_sponsored')->default(false);
            $table->string('last_conference_title')->nullable();
            $table->date('last_conference_date')->nullable();
            $table->string('last_conference_venue')->nullable();
            $table->text('last_conference_publication')->nullable();
            $table->text('publication_evidence_note')->nullable();   // the Q1 / Q2 articles being offered as evidence

            // Item 10 : salary account details. Account number and sort code are
            // stored ENCRYPTED (the model casts them), which is why they are text.
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('account_name')->nullable();
            $table->text('account_number')->nullable();
            $table->text('sort_code')->nullable();

            // declaration and the committee's decision
            $table->boolean('declaration_accepted')->default(false);
            $table->timestamp('declared_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->unsignedBigInteger('decided_by')->nullable();
            $table->text('decision_remark')->nullable();

            $table->timestamps();

            // one application per person per call
            $table->unique(['conference_call_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_applications');
    }
};
