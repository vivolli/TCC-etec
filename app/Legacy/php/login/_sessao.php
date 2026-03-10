<?php
// canonical copy of php/login/_sessao.php
// This wrapper delegates to the OOP SessionManager but remains compatible.
@include_once __DIR__ . '/../../../app/Core/Bootstrap.php';

use App\Core\SessionManager;

function getSessaoInfo(): array
{
    $sm = SessionManager::getInstance();
    return $sm->getSessionInfo();
}


