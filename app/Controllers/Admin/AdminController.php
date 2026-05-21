<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

abstract class AdminController extends Controller
{
    protected string $layout = 'layouts/admin';

    public function __construct()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            echo "403 - Доступ заборонено";
            exit;
        }
    }
}