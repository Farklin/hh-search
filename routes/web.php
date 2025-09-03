<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeadHunterController;
use App\Http\Controllers\CoverLetterController;

Route::get('/', function () {
    return view('pages.home');
})->name('home');


Route::get('/get-code', [HeadHunterController::class, 'getCode'])->name('get-code');
Route::get('/hh-code', [HeadHunterController::class, 'hhCode']);
Route::get('/hh-out', [HeadHunterController::class, 'hhOut'])->name('hh-out');

Route::get('/token', [HeadHunterController::class, 'token'])->name('token');
Route::get('/vacancies', [HeadHunterController::class, 'vacancies']);



// Управление сопроводительными письмами (требует авторизации через HH)
Route::middleware(['hh.auth'])->group(function () {
    Route::get('/me', [HeadHunterController::class, 'me'])->name('me');
    Route::get('/resumes', [HeadHunterController::class, 'resumes'])->name('resumes');
    Route::get('/negotiation', [HeadHunterController::class, 'negotiation'])->name('negotiations');

    // Автоматический отклик
    Route::get('/auto-apply', [HeadHunterController::class, 'autoApply'])->name('auto-apply');
    Route::post('/auto-apply/preview', [HeadHunterController::class, 'previewVacancies'])->name('auto-apply.preview');
    Route::post('/auto-apply/execute', [HeadHunterController::class, 'executeAutoApply'])->name('auto-apply.execute');
    Route::get('/auto-apply/task/{taskId}', [HeadHunterController::class, 'getTaskStatus'])->name('auto-apply.task-status');


    Route::get('/cover-letters', [CoverLetterController::class, 'index'])->name('cover-letters');
    Route::post('/cover-letters/save', [CoverLetterController::class, 'save'])->name('cover-letters.save');
    Route::post('/cover-letters/delete', [CoverLetterController::class, 'delete'])->name('cover-letters.delete');
});
