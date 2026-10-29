<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Keeps a record of the invitation that went to each reviewer: the review
     * link, the message text, whether the email was delivered, and the error
     * if it was not. Each column is only added if it is missing.
     */
    public function up(): void
    {
        Schema::table('proposal_reviewers', function (Blueprint $table) {
            if (! Schema::hasColumn('proposal_reviewers', 'review_link')) {
                $table->text('review_link')->nullable()->after('reviewer_uuid');
            }
            if (! Schema::hasColumn('proposal_reviewers', 'message')) {
                $table->text('message')->nullable()->after('review_link');
            }
            if (! Schema::hasColumn('proposal_reviewers', 'emailed_at')) {
                $table->timestamp('emailed_at')->nullable()->after('message');
            }
            if (! Schema::hasColumn('proposal_reviewers', 'email_error')) {
                $table->text('email_error')->nullable()->after('emailed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_reviewers', function (Blueprint $table) {
            foreach (['review_link', 'message', 'emailed_at', 'email_error'] as $column) {
                if (Schema::hasColumn('proposal_reviewers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
