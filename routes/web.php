<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'main'], function () {
    Route::any('conversation', [ChatController::class, 'conversation'])->name('chat.conversation');
});

Route::get('/{chat?}/{conversation?}', [ChatController::class, 'index'])->name('chat.index');
