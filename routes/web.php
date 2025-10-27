<?php

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
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin-only routes
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Add more admin routes here
    // Route for Role Management
     // Route for Role Management (Versi Lengkap & Terhubung ke Controller)
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
});

Route::middleware(['auth', 'role:user'])->group(function () {
    // User-only routes
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
    // Add more user routes here
});

Route::middleware(['auth', 'role:company'])->group(function () {
    // Company-only routes
    Route::get('/company/dashboard', function () {
        return view('company.dashboard');
    })->name('company.dashboard');
    // Add more company routes here
});

// Route Job Postings
Route::middleware('auth')->group(function () {
    Route::get('/job-postings', [JobPostingController::class, 'index'])->name('index');
});

Route::middleware('role:company')->prefix('company')->name('company.')->group(function () {
    Route::resource('job-postings', JobPostingController::class)->except(['show']);
});

Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

    Route::match(['put', 'patch'], '/job-postings/{job_posting}', [JobPostingController::class, 'update'])->name('update');
    Route::delete('/job-postings/{job_posting}', [JobPostingController::class, 'destroy'])->name('job-postings.destroy');
});

// Route Applications
Route::middleware('role:user')->prefix('my-applications')->name('applications.')->group(function () {
    Route::get('/', [ApplicationController::class, 'index'])->name('index');
    Route::get('/apply/{jobPosting}', [ApplicationController::class, 'create'])->name('create');
    Route::post('/apply/{jobPosting}', [ApplicationController::class, 'store'])->name('store');
    Route::get('/{application}/edit', [ApplicationController::class, 'edit'])->name('edit');
    Route::match(['put', 'patch'], '/{application}', [ApplicationController::class, 'update'])->name('update');
    Route::delete('/{application}', [ApplicationController::class, 'destroy'])->name('destroy');
});

Route::middleware('role:company')->prefix('company')->name('company.')->group(function () {
    Route::resource('applications', ApplicationController::class)->only([
        'index', 'edit', 'update']);
});

Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('applications', ApplicationController::class)->only([
        'index', 'destroy']);
});

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
Route::resource('resumes', ResumeController::class)->except([
    'show'
]);

// Route Interviews
Route::middleware('auth')->group(function () {
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::get('/interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show');

    Route::middleware('role:company')->group(function () {
        Route::get('/applications/{application}/interviews/create', [InterviewController::class, 'create'])->name('interviews.create');
        Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    });

    Route::middleware('role:company,admin')->group(function () {
        Route::get('/interviews/{interview}/edit', [InterviewController::class, 'edit'])->name('interviews.edit');
        Route::put('/interviews/{interview}', [InterviewController::class, 'update'])->name('interviews.update');
        Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews.destroy');
    });

});

// Route Job Seeker Skills
Route::middleware(['auth', 'role:user'])->name('job-seeker-')->group(function () {
    Route::get('/my-skills', [JobSeekerSkillController::class, 'index'])->name('skills.index');
    Route::post('/my-skills', [JobSeekerSkillController::class, 'store'])->name('skills.store');
    Route::delete('/my-skills/{skill}', [JobSeekerSkillController::class, 'destroy'])->name('skills.destroy');
});

// Route for Payment Transactions
Route::middleware(['auth', 'role:company'])->group(function () {
    Route::post('/payment/process/{subscription}', [PaymentTransactionController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/waiting/{paymentTransaction}', function ($paymentTransaction) {
        return "Waiting for payment " . $paymentTransaction;
    })->name('payment.waiting');
    Route::get('/payment/success', function () {
        return "Payment Successful!";
    })->name('payment.success');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
});


// Route Subscription Plans (Admin Only)
Route::resource('subscription-plans', SubscriptionPlanController::class);

    // Rute untuk proses berlangganan
Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');


// Webhook Route for Payment Gateway , No need authentication
Route::post('/webhook/payment', [WebhookController::class, 'handlePayment'])->name('webhook.payment');

require __DIR__.'/auth.php';
