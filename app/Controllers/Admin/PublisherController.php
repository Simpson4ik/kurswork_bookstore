<?php

namespace App\Controllers\Admin;

use App\Models\Publisher;
use App\Core\Response;

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
        $publisherModel = new Publisher();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(trim($_POST['publisher_name']))) {
            $name = trim($_POST['publisher_name']);

            if ($publisherModel->getByName($name)) {
                $this->view('admin/publishers', [
                    'title' => 'Керування видавництвами',
                    'publishers' => $publisherModel->getAll(),
                    'error' => "Видавництво «{$name}» вже існує в базі!"
                ]);
                return;
            }

            $publisherModel->create($name);
        }

        $this->redirect('admin/publishers');
    }

    public function delete(string $id): void
    {
        $response = new Response();
        $publisherId = (int)$id;
        $publisherModel = new Publisher();

        try {
            $publisherModel->delete($publisherId);
            $response->json(['success' => true, 'message' => 'Видавництво успішно ліквідовано з реєстру.']);
        } catch (\Exception $e) {
            $response->json(['success' => false, 'message' => 'Помилка цілісності: існуючі книги посилаються на це видавництво.'], 400);
        }
    }
}