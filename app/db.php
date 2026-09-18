<?php
/**
 * db.php
 * Fournit une connexion PDO unique (singleton) à la base de données.
 */

require_once __DIR__ . '/config.php';

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // En production, on ne doit jamais afficher les détails de connexion.
            http_response_code(500);
            die('Erreur de connexion à la base de données. Vérifiez app/config.php.');
        }
    }

    return $pdo;
}
