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
        Schema::create('proposal_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('call_for_proposal_id');
            $table->foreign('call_for_proposal_id')->references('id')->on('call_for_proposals')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('principal_investigator');
            $table->string('proposal_title');   
            $table->string('college_review');
            $table->text('proposal_description');
            $table->string('proposal_file')->nullable();
            $table->enum('status', ['pending', 'acknowledged', 'accepted', 'rejected'])->default('pending');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_applications');
    }
};
