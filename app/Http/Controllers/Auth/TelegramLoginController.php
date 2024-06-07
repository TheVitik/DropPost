<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\NotTelegramException;
use App\Exceptions\TelegramDataOutdatedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TelegramLoginRequest;
use App\Models\User;
use App\Services\Telegram\TelegramAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramLoginController extends Controller
{
    /**
     * @throws NotTelegramException
     * @throws TelegramDataOutdatedException
     */
    public function login(TelegramLoginRequest $request, TelegramAuth $auth): JsonResponse
    {
        $auth->checkAuthorization($request->validated());

        $user = User::where('telegram_user_id', $request->id)->first();

        if (!$user) {
            $user = User::create([
                'telegram_user_id' => $request->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name ?? null,
                'photo_url' => $request->photo_url ?? null,
                'username' => $request->username ?? null,
            ]);
        }

        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Ви успішно вийшли з системи.']);
    }
}
