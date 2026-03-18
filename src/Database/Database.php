<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

/**
 * Singleton PDO — única instancia de conexión a la base de datos.
 *
 * Uso:
 *   $db = Database::getInstance()->getConnection();
 *   $db = Database::getInstance()->query($sql, $params);
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_NAME']
        );

        try {
            $this->pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    /**
     * Ejecutar consulta con parámetros y retornar todos los resultados.
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecutar consulta y retornar solo un registro.
     */
    public function queryOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Ejecutar INSERT, UPDATE o DELETE. Retorna filas afectadas.
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Retornar el ID del último registro insertado.
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    // Prevenir clonación e instanciación desde afuera
    private function __clone() {}
    public function __wakeup(): void
    {
        throw new \RuntimeException('No se puede deserializar un Singleton.');
    }
}
