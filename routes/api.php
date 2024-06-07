<?php

use App\Http\Controllers\AIBotController;
use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\Auth\TelegramLoginController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostTemplateController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUserController;
use App\Http\Controllers\TimezoneController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'guest'], function () {
    Route::post('/auth/login', [TelegramLoginController::class, 'login']);
    /*Route::post('/auth/login', [ClientLoginController::class, 'login']);
    Route::post('/auth/confirm', [ClientLoginController::class, 'confirm']);
    Route::post('/auth/2fa', [ClientLoginController::class, 'password']);*/
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/logout', [ClientLoginController::class, 'logout']);

    Route::apiResource('projects', ProjectController::class);
    Route::group(['prefix' => 'projects/{project}'], function () {
        Route::get('/users', [ProjectUserController::class, 'index']);
        Route::post('/users', [ProjectUserController::class, 'invite']);
        Route::delete('/users', [ProjectUserController::class, 'remove']);

        // Channels
        Route::apiResource('channels', ChannelController::class);
        Route::get('/channels-list', [ChannelController::class, 'listForFilter']);
        // set bot to channels
        Route::post(
            '/channels/{channel}/set-bot',
            [\App\Http\Controllers\ChannelSettings\AIBotController::class, 'set']
        );

        // activate bot for channel
        Route::post(
            '/channels/{channel}/activate-bot',
            [\App\Http\Controllers\ChannelSettings\AIBotController::class, 'activate']
        );

        // deactivate bot for channel
        Route::post(
            '/channels/{channel}/deactivate-bot',
            [\App\Http\Controllers\ChannelSettings\AIBotController::class, 'deactivate']
        );

        // AIBots
        Route::apiResource('ai-bots', AIBotController::class);

        // Post Templates
        Route::apiResource('post-templates', PostTemplateController::class);
    });

    // Posts
    Route::apiResource('posts', PostController::class)->except('index');
    Route::get('/channels/{channel}/posts', [PostController::class, 'index']);

    Route::post('/invite/{invitation}/accept', [InvitationController::class, 'acceptLink']);
    Route::post('/invite/{invitation}/decline', [InvitationController::class, 'declineLink']);
});

Route::get('/invite/{invitation}', [InvitationController::class, 'showLink'])->name('invitation.show');
Route::get('timezones', [TimezoneController::class, 'index'])->name('timezones');