<?php

class AdminController {

    public function dashboard() {

        $nome = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário');
        $papel = htmlspecialchars($_SESSION['usuario_papel'] ?? 'adm');

        require __DIR__ . '/../View/admin_dashboard.php';
    }

}