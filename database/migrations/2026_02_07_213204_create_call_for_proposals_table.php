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
        Schema::create('call_for_proposals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('title')->unique();
            $table->text('description')->nullable();
            $table->text('status')->nullable();
            $table->date('open_date');
            $table->date('close_date');
            $table->string('advert')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_for_proposals');
    }
};
