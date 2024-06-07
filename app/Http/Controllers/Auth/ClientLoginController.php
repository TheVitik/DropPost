<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Telegram\PasswordRequiredException;
use App\Exceptions\Telegram\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\LoginConfirmRequest;
use App\Http\Requests\LoginPasswordRequest;
use App\Models\User;
use App\Services\Telegram\Client\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientLoginController extends Controller
{

    public function __construct(/*private AuthService $authService*/)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $authService = new AuthService($request->phone);
        $authService->login($request->phone);
        return response()->json(['message' => 'Код надіслано']);
    }

    /**
     * @throws UnauthorizedException
     * @throws PasswordRequiredException
     */
    public function confirm(LoginConfirmRequest $request): JsonResponse
    {
        $authService = new AuthService($request->phone);
        $tgUser = $authService->confirm($request->code);

        $user = User::where('phone', $tgUser['phone'])->first();

        if (!$user) {
            $user = User::create([
                'telegram_user_id' => $tgUser['id'],
                'first_name' => $tgUser['first_name'],
                'last_name' => $tgUser['last_name'] ?? null,
                'username' => $tgUser['username'] ?? null,
                'phone' => $tgUser['phone'],
            ]);
        }

        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * @throws UnauthorizedException
     */
    public function password(LoginPasswordRequest $request): JsonResponse
    {
        $authService = new AuthService($request->phone);
        $tgUser = $authService->password($request->password);

        $user = User::where('phone', $tgUser['phone'])->first();

        if (!$user) {
            $user = User::create([
                'telegram_user_id' => $tgUser['id'],
                'first_name' => $tgUser['first_name'],
                'last_name' => $tgUser['last_name'] ?? null,
                'username' => $tgUser['username'] ?? null,
                'phone' => $tgUser['phone'],
            ]);
        }

        $token = $user->createToken('token-name')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $authService = new AuthService($request->user()->phone);

        $request->user()->currentAccessToken()->delete();
        $authService->logOut();
        return response()->json(['message' => 'Ви вийшли']);
    }
}
