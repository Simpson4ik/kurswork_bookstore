<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/app.php';

        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $config['db']['host'],
                $config['db']['dbname'],
                $config['db']['charset']
            );

            $this->connection = new PDO($dsn, $config['db']['username'], $config['db']['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            if (isset($config['env']) && $config['env'] === 'dev') {
                die("Database connection failed: " . $e->getMessage());
            } else {
                if (ob_get_length()) {
                    ob_clean();
                }
                $response = new Response();

                ob_start();
                require __DIR__ . '/../../views/errors/500.php';
                $html = ob_get_clean();

                $response->setStatus(500)
                    ->addHeader('Content-Type: text/html; charset=utf-8')
                    ->send($html);
                exit;
            }
        }
    }

    private function __clone() {}

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}