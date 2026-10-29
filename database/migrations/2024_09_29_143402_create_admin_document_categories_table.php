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
        Schema::create('admin_document_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_category_type_id');
            $table->foreign('admin_category_type_id')->references('id')->on('admin_category_types')->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_document_categories');
    }
};
