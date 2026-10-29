<?php

use App\Http\Controllers\Conference\Admin\ConferenceApplicationReviewController;
use App\Http\Controllers\Conference\Admin\ConferenceCallController;
use App\Http\Controllers\Conference\Admin\ConferenceReportController;
use App\Http\Controllers\Conference\ConferenceFileController;
use App\Http\Controllers\Conference\Staff\ConferenceApplicationController as StaffConferenceController;
use App\Http\Controllers\Conference\Staff\ConferenceUploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Conference Grant routes
|--------------------------------------------------------------------------
|
| Loaded from routes/web.php with one line:
|
|     require __DIR__.'/conference.php';
|
*/

// the document types allowed in a URL: passport_photo|invitation_letter|...
// 'signed_form' is a legacy type: files uploaded before it was retired can still be opened,
// but the upload route refuses it because it is no longer in config/conference.php
$document_types = implode('|', array_merge(array_keys(config('conference.document_types')), ['signed_form']));

// ---- Admin: manage conference calls --------------------------------------
Route::prefix('admin/conference')
    ->middleware(['auth', 'admin'])
    ->name('admin.conference.')
    ->group(function () {
        Route::get('calls', [ConferenceCallController::class, 'index'])->name('calls.index');
        Route::get('calls/create', [ConferenceCallController::class, 'create'])->name('calls.create');
        Route::post('calls', [ConferenceCallController::class, 'store'])->name('calls.store');
        Route::get('calls/{call}/edit', [ConferenceCallController::class, 'edit'])->name('calls.edit');
        Route::put('calls/{call}', [ConferenceCallController::class, 'update'])->name('calls.update');
        Route::delete('calls/{call}', [ConferenceCallController::class, 'destroy'])->name('calls.destroy');

        // review and decide
        Route::get('calls/{call}/applications', [ConferenceApplicationReviewController::class, 'index'])->name('applications.index');
        Route::get('applications/{application:uuid}', [ConferenceApplicationReviewController::class, 'show'])->name('applications.show');
        Route::post('applications/{application:uuid}/decision', [ConferenceApplicationReviewController::class, 'decide'])->name('applications.decide');
        Route::post('applications/{application:uuid}/reopen', [ConferenceApplicationReviewController::class, 'reopen'])->name('applications.reopen');

        // analytics and exports
        Route::get('analytics', [ConferenceReportController::class, 'analytics'])->name('analytics');
        Route::get('calls/{call}/applications/export/csv', [ConferenceReportController::class, 'csv'])->name('applications.csv');
        Route::get('calls/{call}/applications/export/pdf', [ConferenceReportController::class, 'pdf'])->name('applications.pdf');
    });


// ---- Staff: browse calls and fill in the nomination form -----------------
Route::prefix('staff/conference')
    ->middleware(['auth', 'staff'])
    ->name('staff.conference.')
    ->group(function () use ($document_types) {
        Route::get('calls', [StaffConferenceController::class, 'index'])->name('calls.index');
        Route::get('calls/{call:uuid}', [StaffConferenceController::class, 'show'])->name('calls.show');
        Route::post('calls/{call:uuid}/apply', [StaffConferenceController::class, 'start'])->name('calls.apply');
        Route::get('applications/{application:uuid}/edit', [StaffConferenceController::class, 'edit'])->name('applications.edit');
        Route::put('applications/{application:uuid}', [StaffConferenceController::class, 'update'])->name('applications.update');
        Route::put('applications/{application:uuid}/eligibility', [StaffConferenceController::class, 'eligibility'])->name('applications.eligibility');

        Route::get('applications/{application:uuid}', [StaffConferenceController::class, 'application'])->name('applications.show');
        Route::get('applications/{application:uuid}/documents', [StaffConferenceController::class, 'documents'])->name('applications.documents');
        Route::post('applications/{application:uuid}/submit', [StaffConferenceController::class, 'submit'])->name('applications.submit');

        Route::post('applications/{application:uuid}/documents/{type}', [ConferenceUploadController::class, 'store'])
            ->where('type', $document_types)->name('documents.store');
        Route::delete('applications/{application:uuid}/documents/{type}', [ConferenceUploadController::class, 'destroy'])
            ->where('type', $document_types)->name('documents.destroy');
    });

// ---- Private files: the applicant and admins only ------------------------
Route::prefix('conference/applications')
    ->middleware('auth')
    ->name('conference.files.')
    ->group(function () use ($document_types) {
        Route::get('{application:uuid}/files/{type}', [ConferenceFileController::class, 'document'])
            ->where('type', $document_types)->name('document');
        Route::get('{application:uuid}/nomination-form.pdf', [ConferenceFileController::class, 'form'])->name('form');
    });
