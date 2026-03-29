<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Support\Auth;

class ApiAuthController extends Controller
{
    public function check(): void
    {
        Auth::start();

        $autenticado = Auth::check();
        $papel = $autenticado ? Auth::role() : null;

        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'autenticado' => $autenticado,
            'papel' => $papel,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
