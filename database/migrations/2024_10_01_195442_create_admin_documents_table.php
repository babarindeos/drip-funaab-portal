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
        Schema::create('admin_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('title');
            $table->unsignedBigInteger('admin_category_id');
            $table->foreign('admin_category_id')->references('id')->on('admin_document_categories')->onDelete('cascade');
            $table->string('document');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('uploader');
            $table->foreign('uploader')->references('id')->on('users')->onDelete('cascade');
            $table->string('filesize');
            $table->string('filetype');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_documents');
    }
};
