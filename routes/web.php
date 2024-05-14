<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\JobController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobsController::class, 'index'])->name('jobs');
Route::get('/jobs/job-detail/{id}', [JobsController::class, 'jobDetail'])->name('jobDetail');

Route::post('/jobs/job-apply', [JobsController::class, 'applyJob'])->name('applyJob');
Route::post('/jobs/save-job', [JobsController::class, 'saveJob'])->name('saveJob');

Route::group(['prefix'=> 'account'], function (){
    Route::group(['middleware' => 'guest'], function (){
        Route::get('/register', [AccountController::class, 'registration'])->name('account.registration');
        Route::post('/process-register', [AccountController::class, 'register'])->name('account.register');
        Route::get('/login', [AccountController::class, 'login'])->name('account.login');
        Route::post('/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');
    });

    Route::group(['middleware' => 'auth'], function (){
        Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');
        Route::put('/update-profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');
        Route::post('/update-avatar', [AccountController::class, 'updateAvatar'])->name('account.updateAvatar');
        Route::get('/create-job', [AccountController::class, 'createJob'])->name('account.createJob');
        Route::post('/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');
        Route::get('/my-jobs', [AccountController::class, 'listJobs'])->name('account.myJob');
        Route::get('/my-jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');
        Route::post('/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');
        Route::post('/delete-job', [AccountController::class, 'deleteJob'])->name('account.deleteJob');
        Route::get('/my-jobs-applied', [AccountController::class, 'jobsApplied'])->name('account.jobsApplied');
        Route::post('/remove-job-application', [AccountController::class, 'removeJob'])->name('account.removeJob');
        Route::get('/list-saved-job', [AccountController::class, 'savedJobs'])->name('account.savedJobs');
        Route::post('/remove-saved-job', [AccountController::class, 'removeSavedJob'])->name('account.removeSavedJob');
        Route::post('/change-password', [AccountController::class, 'changePassword'])->name('account.changePassword');

    });
});

Route::group(['prefix'=> 'admin','middleware' =>'checkRole'], function (){
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/list-user', [UserController::class, 'list'])->name('admin.list');
    Route::get('/edit-user/{id}', [UserController::class, 'editUser'])->name('admin.editUser');
    Route::put('/update-user', [UserController::class, 'updateUser'])->name('admin.updateUser');
    Route::post('/remove-user', [UserController::class, 'removeUser'])->name('admin.removeUser');

    Route::get('/list-job', [JobController::class, 'listJob'])->name('admin.listJob');
    Route::get('/edit-job/{id}', [JobController::class, 'editJob'])->name('admin.editJob');
});
