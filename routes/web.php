<?php

use App\Http\Controllers\Admin\Admin_AdminCategoryController;
use App\Http\Controllers\Admin\Admin_AdminCategoryTypeController;
use App\Http\Controllers\Admin\Admin_AdminDocumentController;
use App\Http\Controllers\Admin\Admin_AnalyticsController;
use App\Http\Controllers\Admin\Admin_AuthController;
use App\Http\Controllers\Admin\Admin_BranchController;
use App\Http\Controllers\Admin\Admin_CallForProposalController;
use App\Http\Controllers\Admin\Admin_CollegeController;
use App\Http\Controllers\Admin\Admin_DashboardController;
use App\Http\Controllers\Admin\Admin_DeanController;
use App\Http\Controllers\Admin\Admin_DepartmentController;
use App\Http\Controllers\Admin\Admin_DirectorateController;
use App\Http\Controllers\Admin\Admin_DivisionController;
use App\Http\Controllers\Admin\Admin_MaintenanceController;
use App\Http\Controllers\Admin\Admin_MinistryController;
use App\Http\Controllers\Admin\Admin_DocumentController;
use App\Http\Controllers\Admin\Admin_OfficeController;
use App\Http\Controllers\Admin\Admin_ProfileController;
use App\Http\Controllers\Admin\Admin_ProposalReviewerController;
use App\Http\Controllers\Admin\Admin_ReviewerController;
use App\Http\Controllers\Admin\Admin_SectionController;
use App\Http\Controllers\Admin\Admin_StaffController;
use App\Http\Controllers\Admin\Admin_TrackerController;
use App\Http\Controllers\Admin\Admin_UnitController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Guest\Guest_CallForProposalController;
use App\Http\Controllers\Guest\Guest_WelcomeController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\Staff_AdminDocumentController;
use App\Http\Controllers\Staff\Staff_AuthController;
use App\Http\Controllers\Staff\Staff_CategoryController;
use App\Http\Controllers\Staff\Staff_DashboardController;
use App\Http\Controllers\Staff\Staff_DocumentController;
use App\Http\Controllers\Staff\Staff_GeneralMessageController;
use App\Http\Controllers\Staff\Staff_PrivateMessageController;
use App\Http\Controllers\Staff\Staff_ProfileController;
use App\Http\Controllers\Staff\Staff_ProposalApplicationController;
use App\Http\Controllers\Staff\Staff_WorkflowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//             return view('welcome');
// })->name('welcome');

Route::get('/force_logout', [Guest_WelcomeController::class, 'force_logout'])->name('force_logout');
Route::get('/check_auth', [Guest_WelcomeController::class, 'check_auth'])->name('check_auth');

Route::middleware(['guest'])->group(function () {
    Route::get('/', [Guest_WelcomeController::class, 'index'])->name('welcome');
    Route::get('/call_for_proposals', [Guest_WelcomeController::class, 'call_for_proposals'])->name('guest.call_for_proposals.index');
    Route::get('/conference_calls', [Guest_WelcomeController::class, 'conference_calls'])->name('guest.conference_calls.index');
    Route::post('/guest_logout', [Guest_WelcomeController::class, 'logout'])->name('guest.auth.logout');

    Route::get('/admin', [Admin_AuthController::class, 'index'])->name('admin.auth.index');
    Route::post('/admin', [Admin_AuthController::class, 'login'])->name('admin.auth.login');

    Route::post('/', [Staff_AuthController::class, 'login'])->name('staff.auth.login');

    Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);

    Route::get('register', [Guest_WelcomeController::class, 'register'])->name('guest.auth.register');
    Route::post('register', [Guest_WelcomeController::class, 'store'])->name('guest.auth.register.store');

    // Forgotten password: an emailed link (valid 60 minutes) opens a page to choose a new one.
    // Works for staff and admin accounts. Requests are limited to 6 a minute per address.
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.store');

});

// External reviewers have no account, so their review link is public. It is
// deliberately OUTSIDE the 'guest' middleware group above: that group bounces
// any logged-in user to the dashboard, which would stop an admin (or a staff
// member) from opening a review link while signed in.
Route::get('call_for_proposals/{call_for_proposal}/proposal_applications/{proposal_application}/reviewers/{reviewer}/reviews/{review}/review', [Guest_CallForProposalController::class, 'get_review'])->name('guests.call_for_proposals.proposal_applications.review');
Route::post('call_for_proposals/{call_for_proposal}/proposal_applications/{proposal_application}/reviewers/{reviewer}/reviews/{review}/review', [Guest_CallForProposalController::class, 'post_review'])->name('guests.call_for_proposals.proposal_applications.review.store');

// Proposal documents are served through the portal so access can be checked.
// Admins and the applicant (logged in) ...
Route::get('documents/applications/{uuid}/{type}', [DocumentController::class, 'application'])
    ->middleware('auth')
    ->whereIn('type', ['title', 'proposal', 'college_review'])
    ->name('documents.application');
// ... and external reviewers, via their review link (proposal document only).
Route::get('documents/reviews/{review}/{type}', [DocumentController::class, 'review'])
    ->whereIn('type', ['proposal'])
    ->name('documents.review');

Route::prefix('staff')->middleware(['auth', 'staff'])->group(function () {
    Route::get('/dashboard', [Staff_DashboardController::class, 'index'])->name('staff.dashboard.index');
    Route::post('/logout', [Staff_AuthController::class, 'logout'])->name('staff.auth.logout');

    Route::get('/documents', [Staff_DocumentController::class, 'index'])->name('staff.document.index');
    Route::get('/documents/create', [Staff_DocumentController::class, 'create'])->name('staff.documents.create');
    Route::post('/documents/store', [Staff_DocumentController::class, 'store'])->name('staff.documents.store');

    Route::get('/documents/{document}/show', [Staff_DocumentController::class, 'show'])->name('staff.documents.show');
    Route::get('/documents/mydocuments', [Staff_DocumentController::class, 'mydocuments'])->name('staff.documents.mydocuments');

    // Admin Document
    Route::get('/admin_documents', [Staff_AdminDocumentController::class, 'index'])->name('staff.admin_documents.index');
    Route::get('/admin_documents/create', [Staff_AdminDocumentController::class, 'create'])->name('staff.admin_documents.create');
    Route::get('/admin_documents/{category_type}/get_category', [Staff_AdminDocumentController::class,
        'get_category_by_category_type'])->name('staff.admin_documents.get_category');
    Route::post('/admin_documents/store', [Staff_AdminDocumentController::class, 'store'])->name('staff.admin_documents.store');

    Route::get('/workflows/{document}/flow', [Staff_WorkflowController::class, 'flow'])->name('staff.workflows.flow');
    Route::get('/workflows/{document}/add_contributor', [Staff_WorkflowController::class, 'add_contributor'])->name('staff.workflows.add_contributor');
    Route::post('/workflows/{document}/post_add_contributor', [Staff_WorkflowController::class, 'post_add_contributor'])->name('staff.workflows.post_add_contributor');

    Route::post('/workflows/{document}/search_staff', [Staff_WorkflowController::class, 'search_staff'])->name('staff.workflows.search_staff');
    Route::post('/workflows/{document}/forward_document', [Staff_WorkflowController::class, 'forward_document'])->name('staff.workflows.forward_document');

    Route::get('/workflows/{workflow}/notification_update', [Staff_WorkflowController::class, 'notification_update'])->name('staff.workflows.notification_update');

    Route::get('/workflows/{document}/general_message', [Staff_GeneralMessageController::class, 'index'])->name('staff.workflows.general_message');
    Route::post('/workflows/{document}/general_message', [Staff_GeneralMessageController::class, 'store'])->name('staff.workflows.general_message.store');

    Route::get('/workflows/{document}/private_messages/{recipient}/my_private_messages', [Staff_PrivateMessageController::class, 'my_private_messages'])->name('staff.workflows.private_messages.my_private_messages');
    Route::get('/workflows/{document}/private_message/{recipient}', [Staff_PrivateMessageController::class, 'index'])->name('staff.workflows.private_message.index');
    Route::get('/workflows/{document}/private_message/{sender}/{recipient}/{chat_uuid}/chat', [Staff_PrivateMessageController::class, 'chat'])->name('staff.workflows.private_message.chat');

    Route::post('/workflows/{document}/private_message/{sender}/{recipient}/{chat_uuid}/chat', [Staff_PrivateMessageController::class, 'store'])->name('staff.workflows.private_message.store');

    Route::get('/profile/create', [Staff_ProfileController::class, 'create'])->name('staff.profile.create');
    Route::post('/profile/create', [Staff_ProfileController::class, 'store'])->name('staff.profile.store');
    Route::post('/profile/upload_avatar', [Staff_ProfileController::class, 'upload_avatar'])->name('staff.profile.upload_avatar');

    Route::get('/profile/myprofile', [Staff_ProfileController::class, 'myprofile'])->name('staff.profile.myprofile');

    Route::get('/profile/myprofile/edit', [Staff_ProfileController::class, 'edit'])->name('staff.profile.myprofile.edit');
    Route::post('/profile/myprofile/update', [Staff_ProfileController::class, 'update'])->name('staff.profile.myprofile.update');

    Route::post('/profile/myprofile/update_avatar', [Staff_ProfileController::class, 'update_avatar'])->name('staff.profile.myprofile.update_avatar');

    Route::get('/profile/user/{fileno}', [Staff_ProfileController::class, 'user_profile'])->name('staff.profile.user_profile');
    Route::get('/profile/user/{email}/user_profile', [Staff_ProfileController::class, 'email_user_profile'])->name('staff.profile.email_user_profile');

    Route::get('/profile/change_password', [Staff_ProfileController::class, 'change_password'])->name('staff.profile.change_password');
    Route::post('/profile/update_password', [Staff_ProfileController::class, 'update_password'])->name('staff.profile.update_password');

    Route::get('/profile/my_signature', [Staff_ProfileController::class, 'my_signature'])->name('staff.profile.my_signature');
    Route::post('/profile/my_signature', [Staff_ProfileController::class, 'upload_signature'])->name('staff.profile.upload_signature');
    Route::post('/profile/update_signature', [Staff_ProfileController::class, 'update_signature'])->name('staff.profile.update_signature');

    // Categories
    Route::get('/categories/create', [Staff_CategoryController::class, 'create'])->name('staff.categories.create');
    Route::post('/categories/store', [Staff_CategoryController::class, 'store'])->name('staff.categories.store');

    Route::get('/call_for_proposals', [Staff_ProposalApplicationController::class, 'index'])->name('staff.call_for_proposals.index');
    Route::get('/call_for_proposals/{uuid}/application', [Staff_ProposalApplicationController::class, 'application'])->name('staff.call_for_proposals.application');
    Route::post('/call_for_proposals/{uuid}/application', [Staff_ProposalApplicationController::class, 'store_application'])->name('staff.call_for_proposals.application.store');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard', [Admin_DashboardController::class, 'index'])->name('admin.dashboard.index');

    // Clear the caches from the browser (servers without SSH). Admins only; the commands run on POST only.
    Route::get('/maintenance', [Admin_MaintenanceController::class, 'index'])->name('admin.maintenance.index');
    Route::post('/maintenance', [Admin_MaintenanceController::class, 'run'])->middleware('throttle:10,1')->name('admin.maintenance.run');
    Route::post('/logout', [Admin_AuthController::class, 'logout'])->name('admin.auth.logout');

    //college
    Route::get('/colleges', [Admin_CollegeController::class, 'index'])->name('admin.colleges.index');
    Route::get('/colleges/create', [Admin_CollegeController::class, 'create'])->name('admin.colleges.create');
    Route::post('colleges/store', [Admin_CollegeController::class, 'store'])->name('admin.colleges.store');

    Route::get('colleges/{college}/edit', [Admin_CollegeController::class, 'edit'])->name('admin.colleges.edit');
    Route::post('colleges/{college}/update', [Admin_CollegeController::class, 'update'])->name('admin.colleges.update');
    Route::delete('college/{college}/destroy', [Admin_CollegeController::class, 'destroy'])->name('admin.colleges.destroy');

    // Ofice
    Route::get('/offices', [Admin_OfficeController::class, 'index'])->name('admin.offices.index');
    Route::get('/offices/create', [Admin_OfficeController::class, 'create'])->name('admin.offices.create');
    Route::post('/offices/store', [Admin_OfficeController::class, 'store'])->name('admin.offices.store');

    Route::get('/offices/{office}/show', [Admin_OfficeController::class, 'show'])->name('admin.offices.show');
    Route::get('/offices/{office}/edit', [Admin_OfficeController::class, 'edit'])->name('admin.offices.edit');
    Route::post('/offices/{office}/update', [Admin_OfficeController::class, 'update'])->name('admin.offices.update');

    Route::get('/offices/{office}/confirm_delete', [Admin_OfficeController::class, 'confirm_delete'])->name('admin.offices.confirm_delete');
    Route::post('/offices/{office}/destroy', [Admin_OfficeController::class, 'destroy'])->name('admin.offices.destroy');

    // directorates
    Route::get('/directorates', [Admin_DirectorateController::class, 'index'])->name('admin.directorates.index');
    Route::get('/directorates/create', [Admin_DirectorateController::class, 'create'])->name('admin.directorates.create');
    Route::post('/directorates/store', [Admin_DirectorateController::class, 'store'])->name('admin.directorates.store');

    Route::get('/directorates/{directorate}/show', [Admin_DirectorateController::class, 'show'])->name('admin.directorates.show');
    Route::get('/directorates/{directorate}/edit', [Admin_DirectorateController::class, 'edit'])->name('admin.directorates.edit');
    Route::post('/directorates/{directorate}/update', [Admin_DirectorateController::class, 'update'])->name('admin.directorates.update');

    Route::get('/directorates/{directorate}/confirm_delete', [Admin_DirectorateController::class, 'confirm_delete'])->name('admin.directorates.confirm_delete');
    Route::post('/directorates/{directorate}/destroy', [Admin_DirectorateController::class, 'destroy'])->name('admin.directorates.destroy');

    // ministry
    Route::get('/ministries', [Admin_MinistryController::class, 'index'])->name('admin.ministries.index');
    Route::get('/ministries/create', [Admin_MinistryController::class, 'create'])->name('admin.ministries.create');
    Route::post('/ministries/store', [Admin_MinistryController::class, 'store'])->name('admin.ministries.store');

    Route::get('/ministries/{ministry}/show', [Admin_MinistryController::class, 'show'])->name('admin.ministries.show');
    Route::get('/ministries/{ministry}/edit', [Admin_MinistryController::class, 'edit'])->name('admin.ministries.edit');
    Route::post('/ministries/{ministry}/update', [Admin_MinistryController::class, 'update'])->name('admin.ministries.update');

    Route::get('/ministries/{ministry}/destroy', [Admin_MinistryController::class, 'destroy'])->name('admin.ministries.destroy');
    Route::post('/ministries/{ministry}/confirm_delete', [Admin_MinistryController::class, 'confirm_delete'])->name('admin.ministries.confirm_delete');

    // Department
    Route::get('/departments', [Admin_DepartmentController::class, 'index'])->name('admin.departments.index');
    Route::get('departments/create', [Admin_DepartmentController::class, 'create'])->name('admin.departments.create');
    Route::post('departments/store', [Admin_DepartmentController::class, 'store'])->name('admin.departments.store');

    Route::get('departments/{department}/edit', [Admin_DepartmentController::class, 'edit'])->name('admin.departments.edit');
    Route::post('departments/{department}/update', [Admin_DepartmentController::class, 'update'])->name('admin.departments.update');

    Route::get('departments/{department}/confirm_delete', [Admin_DepartmentController::class, 'confirm_delete'])->name('admin.departments.confirm_delete');
    Route::post('/departments/{department}/destroy', [Admin_DepartmentController::class, 'destroy'])->name('admin.departments.destroy');

    // Division
    Route::get('/divisions', [Admin_DivisionController::class, 'index'])->name('admin.divisions.index');
    Route::get('divisions/create', [Admin_DivisionController::class, 'create'])->name('admin.divisions.create');
    Route::post('divisions/store', [Admin_DivisionController::class, 'store'])->name('admin.divisions.store');

    Route::get('divisions/{division}/show', [Admin_DivisionController::class, 'show'])->name('admin.divisions.show');
    Route::get('divisions/{division}/edit', [Admin_DivisionController::class, 'edit'])->name('admin.divisions.edit');
    Route::post('divisions/{division}/update', [Admin_DivisionController::class, 'update'])->name('admin.divisions.update');

    Route::get('divisions/{division}/confirm_delete', [Admin_DivisionController::class, 'confirm_delete'])->name('admin.divisions.confirm_delete');
    Route::post('/divisions/{division}/destroy', [Admin_DivisionController::class, 'destroy'])->name('admin.divisions.destroy');

    // Branch
    Route::get('/branches', [Admin_BranchController::class, 'index'])->name('admin.branches.index');
    Route::get('branches/create', [Admin_BranchController::class, 'create'])->name('admin.branches.create');
    Route::post('branches/store', [Admin_BranchController::class, 'store'])->name('admin.branches.store');

    Route::get('branches/{branch}/show', [Admin_BranchController::class, 'show'])->name('admin.branches.show');
    Route::get('branches/{branch}/edit', [Admin_BranchController::class, 'edit'])->name('admin.branches.edit');
    Route::post('branches/{branch}/update', [Admin_BranchController::class, 'update'])->name('admin.branches.update');

    Route::get('branches/{branch}/confirm_delete', [Admin_BranchController::class, 'confirm_delete'])->name('admin.branches.confirm_delete');
    Route::post('/branches/{branch}/destroy', [Admin_BranchController::class, 'destroy'])->name('admin.branches.destroy');

    // Section
    Route::get('/sections', [Admin_SectionController::class, 'index'])->name('admin.sections.index');
    Route::get('sections/create', [Admin_SectionController::class, 'create'])->name('admin.sections.create');
    Route::post('sections/store', [Admin_SectionController::class, 'store'])->name('admin.sections.store');

    Route::get('sections/{section}/show', [Admin_SectionController::class, 'show'])->name('admin.sections.show');
    Route::get('sections/{section}/edit', [Admin_SectionController::class, 'edit'])->name('admin.sections.edit');
    Route::post('sections/{section}/update', [Admin_SectionController::class, 'update'])->name('admin.sections.update');

    Route::get('sections/{section}/confirm_delete', [Admin_SectionController::class, 'confirm_delete'])->name('admin.sections.confirm_delete');
    Route::post('/sections/{section}/confirm_destroy', [Admin_SectionController::class, 'destroy'])->name('admin.sections.destroy');

    // Unit
    Route::get('/units', [Admin_UnitController::class, 'index'])->name('admin.units.index');
    Route::get('units/create', [Admin_UnitController::class, 'create'])->name('admin.units.create');
    Route::post('units/store', [Admin_UnitController::class, 'store'])->name('admin.units.store');

    Route::get('units/{unit}/show', [Admin_UnitController::class, 'show'])->name('admin.units.show');
    Route::get('units/{unit}/edit', [Admin_UnitController::class, 'edit'])->name('admin.units.edit');
    Route::post('units/{unit}/update', [Admin_UnitController::class, 'update'])->name('admin.units.update');

    Route::get('units/{unit}/confirm_delete', [Admin_UnitController::class, 'confirm_delete'])->name('admin.units.confirm_delete');
    Route::post('/units/{unit}/destroy', [Admin_UnitController::class, 'destroy'])->name('admin.units.destroy');

    // Staff
    Route::get('staff', [Admin_StaffController::class, 'index'])->name('admin.staff.index');
    Route::post('staff/select_organ', [Admin_StaffController::class, 'select_organ'])->name('admin.staff.select_organ');
    Route::get('staff/create', [Admin_StaffController::class, 'create'])->name('admin.staff.create');
    Route::post('staff/store', [Admin_StaffController::class, 'store'])->name('admin.staff.store');

    Route::get('staff/{staff}/edit', [Admin_StaffController::class, 'edit'])->name('admin.staff.edit');
    Route::post('staff/{staff}/update', [Admin_StaffController::class, 'update'])->name('admin.staff.update');

    Route::get('staff/organs/fetch_organ', [Admin_StaffController::class, 'fetch_organ'])->name('admin.staff.organs.fetch_organ');

    // Admin Document Category Type
    Route::get('admin_category_types', [Admin_AdminCategoryTypeController::class, 'index'])->name('admin.admin_category_types.index');
    Route::get('admin_category_types/create', [Admin_AdminCategoryTypeController::class, 'create'])->name('admin.admin_category_types.create');
    Route::post('admin_category_types/store', [Admin_AdminCategoryTypeController::class, 'store'])->name('admin.admin_category_types.store');
    Route::get('admin_category_types/{admin_category_type}/edit', [Admin_AdminCategoryTypeController::class, 'edit'])->name('admin.admin_category_types.edit');
    Route::post('admin_category_types/{admin_category_type}/update', [Admin_AdminCategoryTypeController::class, 'update'])->name('admin.admin_category_types.update');
    Route::get('admin_category_types/{admin_category_type}/show', [Admin_AdminCategoryTypeController::class, 'show'])->name('admin.admin_category_types.show');

    // Admin Categories
    Route::get('admin_categories', [Admin_AdminCategoryController::class, 'index'])->name('admin.admin_categories.index');
    Route::get('admin_categories/create', [Admin_AdminCategoryController::class, 'create'])->name('admin.admin_categories.create');
    Route::post('admin_categories/store', [Admin_AdminCategoryController::class, 'store'])->name('admin.admin_categories.store');
    Route::get('admin_categories/{admin_category}/edit', [Admin_AdminCategoryController::class, 'edit'])->name('admin.admin_categories.edit');
    Route::post('admin_categories/{admin_category}/update', [Admin_AdminCategoryController::class, 'update'])->name('admin.admin_categories.update');

    //  Admin Documents
    Route::get('admin_documents', [Admin_AdminDocumentController::class, 'index'])->name('admin.admin_documents.index');

    // Document
    Route::get('documents', [Admin_DocumentController::class, 'index'])->name('admin.documents.index');
    Route::get('document_details/{document}', [Admin_DocumentController::class, 'show'])->name('admin.documents.show');

    // User Profile
    Route::get('/profile/user/{email}', [Admin_ProfileController::class, 'user_profile'])->name('admin.profile.user_profile');

    // Tracker
    Route::get('tracker', [Admin_TrackerController::class, 'index'])->name('admin.tracker.index');
    Route::get('analytics', [Admin_AnalyticsController::class, 'index'])->name('admin.analytics.index');
    //Route::post('tracker', [Admin_TrackerController::class, 'index'])->name('admin.tracker.index');

    // Deans
    Route::get('deans', [Admin_DeanController::class, 'index'])->name('admin.deans.index');
    Route::get('dean/create', [Admin_DeanController::class, 'create'])->name('admin.deans.create');
    Route::post('dean/get_assigned_dean', [Admin_DeanController::class, 'get_assigned_dean'])->name('admin.deans.get_assigned_dean');

    Route::get('dean/assign_dean', [Admin_DeanController::class, 'assign_dean'])->name('admin.deans.assign_dean');
    Route::post('dean/assign_dean', [Admin_DeanController::class, 'store_assign_dean'])->name('admin.deans.store_assign_dean');

    Route::get('call_for_proposals', [Admin_CallForProposalController::class, 'index'])->name('admin.call_for_proposals.index');
    Route::get('call_for_proposals/create', [Admin_CallForProposalController::class, 'create'])->name('admin.call_for_proposals.create');
    Route::post('call_for_proposals/store', [Admin_CallForProposalController::class, 'store'])->name('admin.call_for_proposals.store');
    Route::get('call_for_proposals/{call_for_proposal}/show', [Admin_CallForProposalController::class, 'show'])->name('admin.call_for_proposals.show');

    Route::get('call_for_proposals/{call_for_proposal}/edit', [Admin_CallForProposalController::class, 'edit'])->name('admin.call_for_proposals.edit');
    Route::post('call_for_proposals/{call_for_proposal}/update', [Admin_CallForProposalController::class, 'update'])->name('admin.call_for_proposals.update');
    Route::delete('call_for_proposals/{call_for_proposal}/destroy', [Admin_CallForProposalController::class, 'destroy'])->name('admin.call_for_proposals.destroy');

    Route::get('call_for_proposals/{call_for_proposal}/submissions', [Admin_CallForProposalController::class, 'submissions'])->name('admin.call_for_proposals.submissions');
    Route::get('call_for_proposals/{call_for_proposal}/results', [Admin_CallForProposalController::class, 'results'])->name('admin.call_for_proposals.results');
    Route::get('call_for_proposals/{call_for_proposal}/results/export/csv', [Admin_CallForProposalController::class, 'export_results_csv'])->name('admin.call_for_proposals.results.csv');
    Route::get('call_for_proposals/{call_for_proposal}/results/export/pdf', [Admin_CallForProposalController::class, 'export_results_pdf'])->name('admin.call_for_proposals.results.pdf');
    Route::get('call_for_proposals/{call_for_proposal}/proposal_applications/{proposal_application}/proposal_application', [Admin_CallForProposalController::class, 'proposal_application'])->name('admin.call_for_proposals.proposal_application');

    Route::post('call_for_proposals/proposal_applications/{proposal_application}/proposal_application/status_update', [Admin_CallForProposalController::class, 'status_update'])->name('admin.call_for_proposals.proposal_application.status_update');

    Route::get('reviewers', [Admin_ReviewerController::class, 'index'])->name('admin.reviewers.index');
    Route::get('reviewers/create', [Admin_ReviewerController::class, 'create'])->name('admin.reviewers.create');
    Route::post('reviewers/store', [Admin_ReviewerController::class, 'store'])->name('admin.reviewers.store');
    Route::get('reviewers/fetch_reviewer', [Admin_ReviewerController::class, 'fetch_reviewer'])->name('admin.reviewers.fetch_reviewer');

    Route::get('reviewers/{reviewer}/edit', [Admin_ReviewerController::class, 'edit'])->name('admin.reviewers.edit');
    Route::post('reviewers/{reviewer}/update', [Admin_ReviewerController::class, 'update'])->name('admin.reviewers.update');

    Route::get('reviewers/{reviewer}/confirm_delete', [Admin_ReviewerController::class, 'confirm_delete'])->name('admin.reviewers.confirm_delete');
    Route::post('reviewers/{reviewer}/destroy', [Admin_ReviewerController::class, 'destroy'])->name('admin.reviewers.destroy');

    Route::get('reviewers/generate_uuid', [Admin_ReviewerController::class, 'generate_uuid'])->name('admin.reviewers.generate_uuid');

    Route::get('call_for_proposals/proposal_applications/{proposal_application}/proposal_application/send_to_reviewer', [Admin_ProposalReviewerController::class, 'send_to_reviewer'])->name('admin.call_for_proposals.proposal_application.send_to_reviewer');
    Route::post('call_for_proposals/proposal_applications/{proposal_application}/proposal_application/send_to_reviewer', [Admin_ProposalReviewerController::class, 'post_send_to_reviewer'])->name('admin.call_for_proposals.proposal_application.send_to_reviewer.store');

    Route::delete('call_for_proposals/proposal_applications/{proposal_reviewer}/delete', [Admin_ProposalReviewerController::class, 'destroy'])->name('admin.call_for_proposals.proposal_application.proposal_reviewer.destroy');
    Route::post('call_for_proposals/proposal_applications/{proposal_reviewer}/resend', [Admin_ProposalReviewerController::class, 'resend'])->name('admin.call_for_proposals.proposal_application.proposal_reviewer.resend');

});

require __DIR__.'/conference.php';

/* Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
}); */
