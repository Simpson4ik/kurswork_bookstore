<?php

namespace App\Models;

use App\Core\Model;

class Book extends Model
{
    public function getAll(): array
    {
        $statement = $this->db->query("
            SELECT books.*, publishers.publisher_name,
                   GROUP_CONCAT(DISTINCT CONCAT(authors.last_name, ' ', authors.first_name) SEPARATOR ', ') AS authors_list,
                   GROUP_CONCAT(DISTINCT genres.genre_name SEPARATOR ', ') AS genres_list
            FROM books 
            JOIN publishers ON books.publisher_id = publishers.publisher_id 
            LEFT JOIN book_authors ON books.book_id = book_authors.book_id 
            LEFT JOIN authors ON book_authors.author_id = authors.author_id 
            LEFT JOIN book_genres ON books.book_id = book_genres.book_id 
            LEFT JOIN genres ON book_genres.genre_id = genres.genre_id 
            GROUP BY books.book_id 
            ORDER BY books.book_id DESC
        ");
        return $statement->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $statement = $this->db->prepare("
            SELECT books.*, publishers.publisher_name,
                   GROUP_CONCAT(DISTINCT CONCAT(authors.last_name, ' ', authors.first_name) SEPARATOR ', ') AS authors_list,
                   GROUP_CONCAT(DISTINCT genres.genre_name SEPARATOR ', ') AS genres_list
            FROM books 
            JOIN publishers ON books.publisher_id = publishers.publisher_id 
            LEFT JOIN book_authors ON books.book_id = book_authors.book_id 
            LEFT JOIN authors ON book_authors.author_id = authors.author_id 
            LEFT JOIN book_genres ON books.book_id = book_genres.book_id 
            LEFT JOIN genres ON book_genres.genre_id = genres.genre_id 
            WHERE books.book_id = ?
            GROUP BY books.book_id
        ");
        $statement->execute([$id]);
        $result = $statement->fetch();
        return $result ?: null;
    }

    public function create(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare("
                INSERT INTO books (title, isbn, publication_year, price, stock_quantity, publisher_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $data['title'],
                $data['isbn'],
                (int)$data['publication_year'],
                (float)$data['price'],
                (int)$data['stock_quantity'],
                (int)$data['publisher_id']
            ]);

            $bookId = $this->db->lastInsertId();

            if (!empty($data['authors'])) {
                $authorStatement = $this->db->prepare("INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)");
                foreach ($data['authors'] as $authorId) {
                    $authorStatement->execute([$bookId, (int)$authorId]);
                }
            }

            if (!empty($data['genres'])) {
                $genreStatement = $this->db->prepare("INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)");
                foreach ($data['genres'] as $genreId) {
                    $genreStatement->execute([$bookId, (int)$genreId]);
                }
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            die("Критична помилка при збереженні книги та її зв'язків: " . $e->getMessage());
        }
    }

    public function getAuthorIds(int $bookId): array
    {
        $statement = $this->db->prepare("SELECT author_id FROM book_authors WHERE book_id = ?");
        $statement->execute([$bookId]);
        return $statement->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }

    public function getGenreIds(int $bookId): array
    {
        $statement = $this->db->prepare("SELECT genre_id FROM book_genres WHERE book_id = ?");
        $statement->execute([$bookId]);
        return $statement->fetchAll(\PDO::FETCH_COLUMN) ?: [];
    }

    public function update(int $id, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $statement = $this->db->prepare("
                UPDATE books 
                SET title = ?, isbn = ?, publication_year = ?, price = ?, stock_quantity = ?, publisher_id = ? 
                WHERE book_id = ?
            ");
            $statement->execute([
                $data['title'],
                $data['isbn'],
                (int)$data['publication_year'],
                (float)$data['price'],
                (int)$data['stock_quantity'],
                (int)$data['publisher_id'],
                $id
            ]);

            $deleteAuthors = $this->db->prepare("DELETE FROM book_authors WHERE book_id = ?");
            $deleteAuthors->execute([$id]);

            if (!empty($data['authors'])) {
                $authorStatement = $this->db->prepare("INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)");
                foreach ($data['authors'] as $authorId) {
                    $authorStatement->execute([$id, (int)$authorId]);
                }
            }

            $deleteGenres = $this->db->prepare("DELETE FROM book_genres WHERE book_id = ?");
            $deleteGenres->execute([$id]);

            if (!empty($data['genres'])) {
                $genreStatement = $this->db->prepare("INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)");
                foreach ($data['genres'] as $genreId) {
                    $genreStatement->execute([$id, (int)$genreId]);
                }
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            die("Критична помилка при оновленні книги та її зв'язків: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM books WHERE book_id = ?");
        return $statement->execute([$id]);
    }
}