<?php

namespace App\Controllers\Admin;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $this->view('admin/dashboard', [
            'title' => 'Панель адміністратора'
        ]);
    }
}