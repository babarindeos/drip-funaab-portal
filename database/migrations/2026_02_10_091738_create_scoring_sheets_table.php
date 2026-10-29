<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scoring_sheets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('proposal_reviewers_id');
            $table->foreign('proposal_reviewers_id')->references('id')->on('proposal_reviewers')->onDelete('cascade');
            $table->uuid('proposal_reviewers_uuid');
            $table->uuid('proposal_application_uuid');
            $table->uuid('reviewer_uuid');
            $table->unsignedBigInteger('reviewer_id');
            $table->foreign('reviewer_id')->references('id')->on('reviewers')->onDelete('cascade');
            $table->string('scoring_guide_1');
            $table->string('scoring_guide_2');
            $table->string('scoring_guide_3');
            $table->string('scoring_guide_4');
            $table->string('scoring_guide_5');  
            $table->string('scoring_guide_6');
            $table->string('scoring_guide_7');
            $table->string('scoring_guide_8');
            $table->string('scoring_guide_9');
            $table->string('scoring_guide_10');
            $table->string('scoring_guide_11');
            $table->string('scoring_guide_12');
            $table->string('scoring_guide_13');
            $table->string('scoring_guide_14');
            $table->string('scoring_guide_15');
            $table->string('scoring_guide_16');
            $table->string('scoring_guide_17');
            $table->string('scoring_guide_18');
            $table->string('scoring_guide_19');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scoring_sheets');
    }
};
