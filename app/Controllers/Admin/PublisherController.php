<?php

namespace App\Controllers\Admin;

use App\Models\Publisher;

class PublisherController extends AdminController
{
    public function index(): void
    {
        $publisherModel = new Publisher();
        $publishers = $publisherModel->getAll();

        $this->view('admin/publishers', [
            'title' => 'Керування видавництвами',
            'publishers' => $publishers
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['publisher_name']))) {
            $publisherModel = new Publisher();
            $publisherModel->create(trim($_POST['publisher_name']));
        }

        header('Location: /coursework/admin/publishers');
        exit;
    }
}