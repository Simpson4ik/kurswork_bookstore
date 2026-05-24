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
            $response = new Response();

            $base = defined('BASE_PATH') ? BASE_PATH : '';

            $html = "
        <body style='background:#0b0f19; color:#f8fafc; font-family:sans-serif; text-align:center; padding-top:100px;'>
            <h1 style='color:#ef4444; font-size:48px;'>403</h1>
            <h2>Доступ заборонено</h2>
            <p style='color:#94a3b8;'>У вас немає адміністративних прав для перегляду цієї сторінки.</p>
            <a href='{$base}/' style='color:#2563eb; text-decoration:none; font-weight:bold;'>&larr; Повернутися на сайт</a>
        </body>
    ";

            $response->setStatus(403)
                ->addHeader('Content-Type: text/html; charset=utf-8')
                ->send($html);
            exit;
        }
    }
}