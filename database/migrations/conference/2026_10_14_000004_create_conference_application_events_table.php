<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conference';

    /**
     * An audit trail: who did what to an application and when. The committee's
     * decision is final, so it must be traceable.
     */
    public function up(): void
    {
        Schema::create('conference_application_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_application_id');
            $table->unsignedBigInteger('user_id')->nullable();   // who did it (main database user)
            $table->string('event', 40);                          // created | submitted | approved | not_approved ...
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('conference_application_id', 'conf_app_events_application_fk')
                  ->references('id')->on('conference_applications')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_application_events');
    }
};
