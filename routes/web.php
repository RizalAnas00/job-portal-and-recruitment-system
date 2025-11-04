<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobSeekerSkillController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// ------------------------- LANDING PAGE ------------------------- //
Route::get('/', [LandingPageController::class, 'index'])->name('landing');
// ------------------------- LANDING PAGE ------------------------- //

Route::get('/test-cache', function () {
    $name = ['first' => 'aa', 'last' => 'bb'];

    return Cache::rememberForever('testtt', function () use ($name) {
        return $name;
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Main Group)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
 
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Admin Dashboard
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        // Route for Role Management
        Route::prefix('role')->name('role.')->group(function () {

            // Menampilkan semua role (Read)
            Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('permission:role.read');

            // Menampilkan form tambah role (Create)
            Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('permission:role.create');

            // Menyimpan data role baru (Create)
            Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('permission:role.create');

            // Menampilkan detail satu role (Read)
            Route::get('/{role}', [RoleController::class, 'show'])->name('show')->middleware('permission:role.read');

            // Menampilkan form edit role (Update)
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('permission:role.update');

            // Mengupdate data role (Update)
            Route::put('/{role}', [RoleController::class, 'update'])->name('update')->middleware('permission:role.update');

            // Menghapus data role (Delete)
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('permission:role.delete');
        });

        // Admin - Job Postings
        Route::match(['put', 'patch'], '/job-postings/{job_posting}', [JobPostingController::class, 'update'])->name('job-postings.update');
        Route::patch('/job-postings/{job_posting}/status', [JobPostingController::class, 'updateStatus'])->name('job-postings.update-status')->middleware('permission:job_posting.update_status');
        Route::delete('/job-postings/{job_posting}', [JobPostingController::class, 'destroy'])->name('job-postings.destroy');

        // Admin - Applications
        Route::resource('applications', ApplicationController::class)->only(['index', 'destroy']);
    });


    /*
    |--------------------------------------------------------------------------
    | Company Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:company')->prefix('company')->name('company.')->group(function () {

        // Company Profile create
        Route::get('/create-profile', [CompanyController::class, 'create'])->name('profile.create');
        Route::post('/create-profile', [CompanyController::class, 'store'])->name('profile.store');

        // Company Dashboard
        Route::get('/dashboard', fn() => view('company.dashboard'))->name('dashboard');

        // Company Profile edit
        Route::get('/profile/{company}/edit', [CompanyController::class, 'edit'])->name('profile.edit');
        Route::match(['put', 'patch'], '/profile/{company}', [CompanyController::class, 'update'])->name('profile.update');

        // Job Postings (Company Only)
        Route::resource('job-postings', JobPostingController::class)->except(['show']);
        Route::patch('/job-postings/{job_posting}/status', [JobPostingController::class, 'updateStatus'])->name('job-postings.update-status');

        // Applications (Company Only)
        Route::resource('applications', ApplicationController::class)->only(['index', 'edit', 'update']);
        Route::get('/job-postings/{job_posting}/applications', [ApplicationController::class, 'indexByJobPosting'])->name('job-postings.applications.index')->middleware('permission:application.read.own');
        Route::get('/job-postings/{job_posting}/applications/filter', [ApplicationController::class, 'filterByStatus'])->name('job-postings.applications.filter')->middleware('permission:application.filter');

        // Payment & Subscription (Company Only)
        Route::get('/payment/history', [PaymentTransactionController::class, 'index'])->name('payment.index');
        Route::post('/payment/process/{subscription}', [PaymentTransactionController::class, 'processPayment'])->name('payment.process');
        Route::get('/payment/waiting/{payment}', [PaymentTransactionController::class, 'waitingPayment'])->name('payment.waiting');
        Route::get('/payment/success/{paymentTransaction}', fn($paymentTransaction) => "Payment Successful!". $paymentTransaction)->name('payment.success');
        Route::get('/payment/failure', fn() => "Payment Failed!")->name('payment.failure');
        Route::post('/payment/cancel/{payment}', [PaymentTransactionController::class, 'cancelPayment'])->name('payment.cancel');
        Route::get('payment/check-status/{payment}', [PaymentTransactionController::class, 'checkPaymentStatus'])->name('payment.check-status');

        // Subscription Pages
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{plan}', [SubscriptionController::class, 'confirmationOrder'])->name('subscriptions.confirm');
        Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::post('/subscriptions/cancel/{subscription}', [SubscriptionController::class, 'cancelSubscription'])->name('subscriptions.cancel')->withTrashed();
    });


    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {

        // User Dashboard
        Route::get('/dashboard', fn() => view('user.dashboard'))->name('dashboard');

        // Applications (User)
        Route::prefix('applications')->name('applications.')->group(function () {
            Route::get('/', [ApplicationController::class, 'index'])->name('index');
            Route::get('/apply/{jobPosting}', [ApplicationController::class, 'create'])->name('create');
            Route::post('/apply/{jobPosting}', [ApplicationController::class, 'store'])->name('store');
            Route::get('/{application}/edit', [ApplicationController::class, 'edit'])->name('edit');
            Route::match(['put', 'patch'], '/{application}', [ApplicationController::class, 'update'])->name('update');
            Route::delete('/{application}', [ApplicationController::class, 'destroy'])->name('destroy');
        });

        // Job Seeker Skills
        Route::get('/my-skills', [JobSeekerSkillController::class, 'index'])->name('skills.index');
        Route::post('/my-skills', [JobSeekerSkillController::class, 'store'])->name('skills.store');
        Route::delete('/my-skills/{skill}', [JobSeekerSkillController::class, 'destroy'])->name('skills.destroy');
    });


    /*
    |--------------------------------------------------------------------------
    | Shared Auth Routes (All Roles)
    |--------------------------------------------------------------------------
    */
    // Job Postings (semua auth user)
    Route::get('/job-postings', [JobPostingController::class, 'index'])->name('job-postings.index');

    // Interviews
    Route::prefix('interviews')->name('interviews.')->group(function () {
        Route::get('/', [InterviewController::class, 'index'])->name('index');
        Route::get('/{interview}', [InterviewController::class, 'show'])->name('show');

        // Only company
        Route::middleware('role:company')->group(function () {
            Route::get('/applications/{application}/create', [InterviewController::class, 'create'])->name('create');
            Route::post('/', [InterviewController::class, 'store'])->name('store');
        });

        // Company or Admin
        Route::middleware('role:company,admin')->group(function () {
            Route::get('/{interview}/edit', [InterviewController::class, 'edit'])->name('edit');
            Route::put('/{interview}', [InterviewController::class, 'update'])->name('update');
            Route::delete('/{interview}', [InterviewController::class, 'destroy'])->name('destroy');
        });
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::put('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
});


/*
|--------------------------------------------------------------------------
| Other Global Routes (Public / Mixed)
|--------------------------------------------------------------------------
*/

// Route Companies
Route::resource('companies', CompanyController::class);

// Public view untuk daftar & detail (autentikasi sudah ada jika perlu)
Route::middleware('auth')->group(function () {
    Route::get('/job-postings', [JobPostingController::class, 'index'])->name('job-postings.index');
    Route::get('/job-postings/{job_posting}', [JobPostingController::class, 'show'])->name('job-postings.show');
    Route::get('/job-postings/create', [JobPostingController::class, 'create'])->name('job-postings.create');
});

// Company management (create/store/edit/update/destroy) - requires company role
Route::middleware(['auth', 'role:company'])->group(function () {
    Route::get('/job-postings/create', [JobPostingController::class, 'create'])->name('job-postings.create');
    Route::post('/job-postings', [JobPostingController::class, 'store'])->name('job-postings.store');
    Route::get('/job-postings/{job_posting}/edit', [JobPostingController::class, 'edit'])->name('job-postings.edit');
    Route::put('/job-postings/{job_posting}', [JobPostingController::class, 'update'])->name('job-postings.update');
    Route::delete('/job-postings/{job_posting}', [JobPostingController::class, 'destroy'])->name('job-postings.destroy');
});

// Route Resumes
Route::resource('resumes', ResumeController::class)->except(['show']);

// Route Subscription Plans (Admin Only)
Route::resource('subscription-plans', SubscriptionPlanController::class);

// Webhook Route for Payment Gateway (No Auth)
Route::post('/webhook/payment', [WebhookController::class, 'handlePayment'])->name('webhook.payment');

require __DIR__.'/auth.php';
