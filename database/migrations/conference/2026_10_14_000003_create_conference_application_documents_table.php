<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'conference';

    public function up(): void
    {
        // An earlier version of this migration failed halfway (MySQL does not roll
        // back table creation) and could leave an empty table behind. Nothing has
        // been stored in it at this stage, so clearing it is safe.
        Schema::dropIfExists('conference_application_documents');

        Schema::create('conference_application_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conference_application_id');
            $table->string('type', 40);            // see config/conference.php -> document_types
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();

            // explicit short names: MySQL limits identifiers to 64 characters and the
            // automatic name for this foreign key would be 66
            $table->foreign('conference_application_id', 'conf_app_docs_application_fk')
                  ->references('id')->on('conference_applications')->cascadeOnDelete();

            // one file per document type; uploading again replaces it
            $table->unique(['conference_application_id', 'type'], 'conf_app_doc_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_application_documents');
    }
};
