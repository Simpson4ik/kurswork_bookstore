<?php

namespace App\Controllers\Admin;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Genre;
use App\Core\Response;
use App\Core\Database;

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

        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock_quantity'];
        $year = (int)$_POST['publication_year'];

        if ($price < 0 || $stock < 0 || $year < 0) {
            $response->json(['success' => false, 'message' => 'Ціна, кількість та рік видання не можуть бути від\'ємними'], 400);
        }

        $coverName = null;
        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $coverName = $this->uploadAndConvertToWebp($_FILES['cover_image']);
            if (!$coverName) {
                $response->json(['success' => false, 'message' => 'Некоректний формат або пошкоджене зображення обкладинки'], 400);
            }
        }

        $bookModel = new Book();
        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            $bookData = $_POST;
            $bookData['cover_image'] = $coverName;
            $bookData['price'] = $price;
            $bookData['stock_quantity'] = $stock;
            $bookData['publication_year'] = $year;

            $bookId = $bookModel->create($bookData);

            if (!empty($_POST['authors'])) {
                $bookModel->attachAuthors($bookId, $_POST['authors']);
            }
            if (!empty($_POST['genres'])) {
                $bookModel->attachGenres($bookId, $_POST['genres']);
            }

            $db->commit();
            $response->json(['success' => true, 'message' => 'Книгу та її медіа-файли успішно додано до каталогу']);
        } catch (\Exception $e) {
            $db->rollBack();
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
            http_response_code(404);
            $this->layout = '';
            $this->view('errors/404');
            exit;
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

        if (empty($_POST['title']) || empty($_POST['publisher_id']) || empty($_POST['price'])) {
            $response->json(['success' => false, 'message' => 'Будь ласка, заповніть всі обов\'язкові поля'], 400);
        }

        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock_quantity'];
        $year = (int)$_POST['publication_year'];

        if ($price < 0 || $stock < 0 || $year < 0) {
            $response->json(['success' => false, 'message' => 'Числові значення не можуть бути від\'ємними'], 400);
        }

        $oldCover = $currentBook['cover_image'];
        $newCover = null;

        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $newCover = $this->uploadAndConvertToWebp($_FILES['cover_image']);
            if (!$newCover) {
                $response->json(['success' => false, 'message' => 'Некоректний формат нового зображення'], 400);
            }
        }

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            $bookData = $_POST;
            $bookData['cover_image'] = $newCover ?? $oldCover;
            $bookData['price'] = $price;
            $bookData['stock_quantity'] = $stock;
            $bookData['publication_year'] = $year;

            $bookModel->update($bookId, $bookData);

            $bookModel->detachAuthors($bookId);
            if (!empty($_POST['authors'])) {
                $bookModel->attachAuthors($bookId, $_POST['authors']);
            }

            $bookModel->detachGenres($bookId);
            if (!empty($_POST['genres'])) {
                $bookModel->attachGenres($bookId, $_POST['genres']);
            }

            $db->commit();

            if ($newCover && $oldCover && file_exists($this->uploadDir . $oldCover)) {
                unlink($this->uploadDir . $oldCover);
            }

            $response->json(['success' => true, 'message' => 'Дані книги оновлено']);
        } catch (\Exception $e) {
            $db->rollBack();
            if ($newCover && file_exists($this->uploadDir . $newCover)) {
                unlink($this->uploadDir . $newCover);
            }
            $response->json(['success' => false, 'message' => 'Помилка збереження: ' . $e->getMessage()], 500);
        }
    }

    public function delete(string $id): void
    {
        $response = new Response();
        $bookId = (int)$id;
        $bookModel = new Book();
        $book = $bookModel->getById($bookId);

        if (!$book) {
            $response->json(['success' => false, 'message' => 'Книгу для видалення не знайдено'], 404);
        }

        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();

            $bookModel->detachAuthors($bookId);
            $bookModel->detachGenres($bookId);
            $bookModel->delete($bookId);

            $db->commit();

            if ($book['cover_image'] && file_exists($this->uploadDir . $book['cover_image'])) {
                unlink($this->uploadDir . $book['cover_image']);
            }

            $response->json(['success' => true, 'message' => 'Книгу успішно видалено з каталогу']);
        } catch (\Exception $e) {
            $db->rollBack();
            $response->json(['success' => false, 'message' => 'Помилка бази даних при видаленні книги: ' . $e->getMessage()], 500);
        }
    }

    private function uploadAndConvertToWebp(array $file): ?string
    {
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