<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobSeekerSkillController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
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
    Route::prefix('role')->name('role.')->group(function () {
        
        Route::get('/', function () {
            return view('role.index');
        })->name('index');
        
        Route::get('/{role}', function ($role) {
            return view('role.show', ['role' => $role]);
        })->name('show');
    
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


// Route Subscription Plans (Admin Only)
Route::resource('subscription-plans', SubscriptionPlanController::class);

Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    // Rute untuk proses berlangganan
Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');


require __DIR__.'/auth.php';
