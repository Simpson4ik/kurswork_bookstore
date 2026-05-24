<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;

abstract class AdminController extends Controller
{
    protected string $layout = 'layouts/admin';

    public function __construct()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            if (ob_get_length()) {
                ob_clean();
            }

            $response = new Response();

            ob_start();
            require __DIR__ . '/../../../views/errors/403.php';
            $html = ob_get_clean();

            $response->setStatus(403)
                ->addHeader('Content-Type: text/html; charset=utf-8')
                ->send($html);
            exit;
        }
    }
}