<?php

namespace App\Controllers\Admin;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Genre;
use App\Core\Response;

class BookController extends AdminController
{
    private string $uploadDir = __DIR__ . '/../../../public/uploads/';

    public function create(): void
    {
        $publisherModel = new Publisher();
        $authorModel = new Author();
        $genreModel = new Genre();

        $this->view('admin/add_book', [
            'title' => 'Додати нову книгу',
            'publishers' => $publisherModel->getAll(),
            'authors' => $authorModel->getAll(),
            'genres' => $genreModel->getAll()
        ]);
    }

    public function store(): void
    {
        $response = new Response();

        if (empty($_POST['title']) || empty($_POST['publisher_id']) || empty($_POST['price'])) {
            $response->json(['success' => false, 'message' => 'Будь ласка, заповніть всі обов\'язкові поля, включаючи видавництво'], 400);
        }

        $coverName = null;
        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $coverName = $this->uploadAndConvertToWebp($_FILES['cover_image']);
            if (!$coverName) {
                $response->json(['success' => false, 'message' => 'Некоректний формат або пошкоджене зображення обкладинки'], 400);
            }
        }

        $bookModel = new Book();

        try {
            $bookData = $_POST;
            $bookData['cover_image'] = $coverName;

            $bookId = $bookModel->create($bookData);

            if (!empty($_POST['authors'])) {
                $bookModel->attachAuthors($bookId, $_POST['authors']);
            }
            if (!empty($_POST['genres'])) {
                $bookModel->attachGenres($bookId, $_POST['genres']);
            }

            $response->json(['success' => true, 'message' => 'Книгу та її медіа-файли успішно додано до каталогу']);
        } catch (\Exception $e) {
            if ($coverName && file_exists($this->uploadDir . $coverName)) {
                unlink($this->uploadDir . $coverName);
            }
            $response->json(['success' => false, 'message' => 'Помилка виконання транзакції: ' . $e->getMessage()], 500);
        }
    }

    public function edit(string $id): void
    {
        $bookModel = new Book();
        $book = $bookModel->getById((int)$id);

        if (!$book) {
            $response = new Response();
            $response->setStatus(404)->send("<h2>Помилка 404</h2><p>Книгу для редагування не знайдено.</p>");
        }

        $publisherModel = new Publisher();
        $authorModel = new Author();
        $genreModel = new Genre();

        $this->view('admin/edit_book', [
            'title' => 'Редагувати книгу',
            'book' => $book,
            'publishers' => $publisherModel->getAll(),
            'authors' => $authorModel->getAll(),
            'genres' => $genreModel->getAll(),
            'currentAuthors' => $bookModel->getAuthorIds((int)$id),
            'currentGenres' => $bookModel->getGenreIds((int)$id)
        ]);
    }

    public function update(string $id): void
    {
        $response = new Response();
        $bookId = (int)$id;

        $bookModel = new Book();
        $currentBook = $bookModel->getById($bookId);

        if (!$currentBook) {
            $response->json(['success' => false, 'message' => 'Об\'єкт редагування відсутній'], 404);
        }

        $coverName = $currentBook['cover_image'];
        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $newCover = $this->uploadAndConvertToWebp($_FILES['cover_image']);
            if ($newCover) {
                if ($coverName && file_exists($this->uploadDir . $coverName)) {
                    unlink($this->uploadDir . $coverName);
                }
                $coverName = $newCover;
            } else {
                $response->json(['success' => false, 'message' => 'Некоректний формат нового зображення'], 400);
            }
        }

        try {
            $bookData = $_POST;
            $bookData['cover_image'] = $coverName;

            $bookModel->update($bookId, $bookData);

            $bookModel->detachAuthors($bookId);
            if (!empty($_POST['authors'])) {
                $bookModel->attachAuthors($bookId, $_POST['authors']);
            }

            $bookModel->detachGenres($bookId);
            if (!empty($_POST['genres'])) {
                $bookModel->attachGenres($bookId, $_POST['genres']);
            }

            $response->json(['success' => true, 'message' => 'Дані книги оновлено']);
        } catch (\Exception $e) {
            $response->json(['success' => false, 'message' => 'Помилка збереження: ' . $e->getMessage()], 500);
        }
    }

    public function delete(string $id): void
    {
        $bookId = (int)$id;
        $bookModel = new Book();
        $book = $bookModel->getById($bookId);

        if ($book) {
            if ($book['cover_image'] && file_exists($this->uploadDir . $book['cover_image'])) {
                unlink($this->uploadDir . $book['cover_image']);
            }
            $bookModel->detachAuthors($bookId);
            $bookModel->detachGenres($bookId);
            $bookModel->delete($bookId);
        }

        header('Location: /coursework/admin/dashboard');
        exit;
    }

    private function uploadAndConvertToWebp(array $file): ?string
    {
        if (!imagesx(imagecreatefromstring(file_get_contents($file['tmp_name'])))) {
            return null;
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            return null;
        }

        $mime = $imageInfo['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($file['tmp_name']);
                break;
            default:
                return null;
        }

        if (!$image) {
            return null;
        }

        $newFileName = bin2hex(random_bytes(16)) . '.webp';

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        imagepalettetotruecolor($image);
        imagewebp($image, $this->uploadDir . $newFileName, 80);
        imagedestroy($image);

        return $newFileName;
    }
}