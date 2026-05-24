<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Book extends Model
{
    public function getAll(): array
    {
        return $this->filter([]);
    }

    public function getPaginated(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
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
            GROUP BY books.book_id 
            ORDER BY books.book_id DESC
            LIMIT :limit OFFSET :offset
        ");
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function getTotalCount(): int
    {
        $statement = $this->db->query("SELECT COUNT(*) FROM books");
        return (int)$statement->fetchColumn();
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

    public function create(array $data): int
    {
        $statement = $this->db->prepare("
            INSERT INTO books (title, publisher_id, publication_year, isbn, price, stock_quantity, cover_image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $statement->execute([
            $data['title'],
            $data['publisher_id'],
            $data['publication_year'],
            $data['isbn'],
            $data['price'],
            $data['stock_quantity'],
            $data['cover_image'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->db->prepare("
            UPDATE books 
            SET title = ?, publisher_id = ?, publication_year = ?, isbn = ?, price = ?, stock_quantity = ?, cover_image = ? 
            WHERE book_id = ?
        ");
        return $statement->execute([
            $data['title'],
            $data['publisher_id'],
            $data['publication_year'],
            $data['isbn'],
            $data['price'],
            $data['stock_quantity'],
            $data['cover_image'],
            $id
        ]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->db->prepare("DELETE FROM books WHERE book_id = ?");
        return $statement->execute([$id]);
    }

    public function attachAuthors(int $bookId, array $authorIds): void
    {
        $this->db->beginTransaction();
        try {
            $statement = $this->db->prepare("INSERT INTO book_authors (book_id, author_id) VALUES (?, ?)");
            foreach ($authorIds as $authorId) {
                $statement->execute([$bookId, (int)$authorId]);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function detachAuthors(int $bookId): void
    {
        $statement = $this->db->prepare("DELETE FROM book_authors WHERE book_id = ?");
        $statement->execute([$bookId]);
    }

    public function attachGenres(int $bookId, array $genreIds): void
    {
        $this->db->beginTransaction();
        try {
            $statement = $this->db->prepare("INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)");
            foreach ($genreIds as $genreId) {
                $statement->execute([$bookId, (int)$genreId]);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function detachGenres(int $bookId): void
    {
        $statement = $this->db->prepare("DELETE FROM book_genres WHERE book_id = ?");
        $statement->execute([$bookId]);
    }

    public function getAuthorIds(int $bookId): array
    {
        $statement = $this->db->prepare("SELECT author_id FROM book_authors WHERE book_id = ?");
        $statement->execute([$bookId]);
        return $statement->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function getGenreIds(int $bookId): array
    {
        $statement = $this->db->prepare("SELECT genre_id FROM book_genres WHERE book_id = ?");
        $statement->execute([$bookId]);
        return $statement->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public function search(string $query): array
    {
        return $this->filter(['q' => $query]);
    }

    public function filter(array $filters): array
    {
        $sql = "
            SELECT books.*, publishers.publisher_name,
                   GROUP_CONCAT(DISTINCT CONCAT(authors.last_name, ' ', authors.first_name) SEPARATOR ', ') AS authors_list,
                   GROUP_CONCAT(DISTINCT genres.genre_name SEPARATOR ', ') AS genres_list
            FROM books 
            JOIN publishers ON books.publisher_id = publishers.publisher_id 
            LEFT JOIN book_authors ON books.book_id = book_authors.book_id 
            LEFT JOIN authors ON book_authors.author_id = authors.author_id 
            LEFT JOIN book_genres ON books.book_id = book_genres.book_id 
            LEFT JOIN genres ON book_genres.genre_id = genres.genre_id
        ";

        $conditions = [];
        $params = [];

        if (!empty($filters['q'])) {
            $search = '%' . $filters['q'] . '%';
            $conditions[] = "(books.title LIKE ? OR publishers.publisher_name LIKE ? OR authors.last_name LIKE ? OR authors.first_name LIKE ? OR genres.genre_name LIKE ?)";
            array_push($params, $search, $search, $search, $search, $search);
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $conditions[] = "books.price >= ?";
            $params[] = (float)$filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $conditions[] = "books.price <= ?";
            $params[] = (float)$filters['max_price'];
        }

        if (!empty($filters['in_stock']) && $filters['in_stock'] === 'true') {
            $conditions[] = "books.stock_quantity > 0";
        }

        if (!empty($filters['genres']) && is_array($filters['genres'])) {
            $genreIds = array_map('intval', $filters['genres']);
            $placeholders = implode(',', array_fill(0, count($genreIds), '?'));
            $conditions[] = "books.book_id IN (SELECT book_id FROM book_genres WHERE genre_id IN ($placeholders))";
            foreach ($genreIds as $id) {
                $params[] = $id;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY books.book_id ORDER BY books.book_id DESC";

        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll() ?: [];
    }
}