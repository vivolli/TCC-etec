<?php
require_once __DIR__ . '/../../app/Core/Bootstrap.php';

use App\Core\SessionManager;

function getSessaoInfo(): array
{
    // We keep this thin wrapper for compatibility but delegate to SessionManager (OOP)
    $sm = SessionManager::getInstance();
    return $sm->getSessionInfo();
}


