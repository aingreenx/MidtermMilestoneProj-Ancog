<?php
declare(strict_types=1);

class Database {
    private static ?PDO $pdo = null;

    public static function get(): PDO {
        if (self::$pdo === null) {
            $host = getenv('DB_HOST');
            $name = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $password = getenv('DB_PASS');
            $isDevelopment = getenv('APP_ENV') === 'development';
            if ($host === false || $host === '' || $name === false || $name === '' ||
                $user === false || $user === '' || $password === false || ($password === '' && !$isDevelopment)) {
                throw new RuntimeException('Database environment variables are not configured.');
            }

            self::$pdo = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}
