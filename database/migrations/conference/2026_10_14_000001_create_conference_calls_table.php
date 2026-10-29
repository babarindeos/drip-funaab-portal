<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // These tables live in the separate conference database.
    protected $connection = 'conference';

    public function up(): void
    {
        Schema::create('conference_calls', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title')->unique();
            $table->text('description')->nullable();

            $table->date('open_date');
            $table->date('close_date');                 // the application deadline
            $table->date('earliest_conference_date');   // conferences before this date are not eligible
            $table->unsignedTinyInteger('max_duration_days')->default(5);

            $table->boolean('allow_teaching')->default(true);
            $table->boolean('allow_non_teaching')->default(true);

            $table->string('advert')->nullable();
            $table->text('approval_instructions')->nullable(); // shown to approved applicants (e.g. TETFund portal link)

            $table->string('status')->nullable();       // null = published, or draft / archived
            $table->unsignedBigInteger('created_by')->nullable(); // user id (users live in the main database)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_calls');
    }
};
