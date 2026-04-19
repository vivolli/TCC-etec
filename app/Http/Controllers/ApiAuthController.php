<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\AuthService;
use App\Services\JwtService;

class ApiAuthController extends Controller
{
    private AuthService $authService;
    private JwtService $jwtService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->jwtService = new JwtService();
    }

    public function login(Request $request): void
    {
        $email = trim((string)$request->input('email', ''));
        $password = (string)$request->input('password', $request->input('senha', ''));
        $profile = $request->input('profile', $request->input('perfil', null));
        $fingerprint = AuthService::fingerprintFromGlobals();

        $result = $this->authService->attempt($email, $password, $profile, $fingerprint);

        if (!$result['ok']) {
            $this->json(['ok' => false, 'message' => $result['message']], 401);
            return;
        }

        $accessToken = $this->jwtService->generateAccessToken($result['user']);
        $refreshToken = $this->jwtService->generateRefreshToken($result['user']);

        $this->json([
            'ok' => true,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => 900,
            'user' => $result['user'],
        ]);
    }

    public function refresh(Request $request): void
    {
        $refreshToken = trim((string)$request->input('refresh_token', ''));
        if ($refreshToken === '') {
            $this->json(['ok' => false, 'message' => 'Refresh token não informado.'], 400);
            return;
        }

        $user = $this->jwtService->validateRefreshToken($refreshToken);
        if ($user === null) {
            $this->json(['ok' => false, 'message' => 'Refresh token inválido ou expirado.'], 401);
            return;
        }

        $accessToken = $this->jwtService->generateAccessToken($user);

        $this->json([
            'ok' => true,
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => 900,
        ]);
    }

    public function logout(Request $request): void
    {
        $refreshToken = trim((string)$request->input('refresh_token', ''));
        if ($refreshToken !== '') {
            $this->jwtService->invalidateRefreshToken($refreshToken);
        }

        $this->json(['ok' => true, 'message' => 'Logout realizado com sucesso.']);
    }

    public function check(Request $request): void
    {
        $user = $request->user ?? ($_REQUEST['auth_user'] ?? null);
        $this->json([
            'autenticado' => !empty($user),
            'user' => $user,
        ]);
    }
}
