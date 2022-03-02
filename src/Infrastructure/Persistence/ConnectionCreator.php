<?php


namespace Alura\Pdo\Infrastructure\Persistence;


use PDO;

class ConnectionCreator
{
    public static function createConnection(): PDO
    {

        //$databasePath = __DIR__ . '/../../../banco.sqlite';
        $connection = new PDO(
            "mysql:host=127.0.0.1;dbname=banco",
            "root",
            "");
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $connection;
    }
}