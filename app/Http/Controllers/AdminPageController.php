<?php

namespace App\Http\Controllers;

use App\Core\Controller;
use App\Support\Auth;

class AdminPageController extends Controller
{
    public function __invoke(): void
    {
        Auth::start();
        Auth::requireAuth();
        Auth::requireRole('admin');
        
        echo $this->view('react-app');
    }
}
