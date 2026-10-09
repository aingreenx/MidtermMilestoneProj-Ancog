<?php
// The ONE place the database connection is defined. Edit credentials here.
class Database {
    private static ?PDO $pdo = null;
    public static function get(): PDO {
        if (!self::$pdo) {
            self::$pdo = new PDO('mysql:host=localhost;dbname=greek_recipe_hub;charset=utf8mb4', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}
