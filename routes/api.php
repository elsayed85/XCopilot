<?php

use App\Http\Controllers\Api\AskController;
use App\Http\Controllers\Api\GithubController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('accounts', [GithubController::class, 'accounts']);
    Route::get('shared-accounts', [GithubController::class, 'sharedAccounts']);

    Route::post('ask', [AskController::class, 'ask']);
});
