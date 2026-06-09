<?php

declare(strict_types=1);

/**
 * Load key=value pairs from a .env file into the environment.
 */
function loadEnv(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if ($key === '' || array_key_exists($key, $_ENV)) {
            continue;
        }

        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv(sprintf('%s=%s', $key, $value));
    }
}

loadEnv(__DIR__ . '/.env');

/**
 * Start a PHP session when needed.
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Read an environment variable with a fallback value.
 */
function envValue(string $name, string $default = ''): string
{
    $value = getenv($name);

    return $value !== false ? $value : $default;
}

/**
 * Create or return a shared PDO connection for MariaDB.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = envValue('DB_HOST', '127.0.0.1');
    $port = envValue('DB_PORT', '3306');
    $name = envValue('DB_NAME');
    $user = envValue('DB_USER');
    $pass = envValue('DB_PASSWORD');
    $charset = envValue('DB_CHARSET', 'utf8mb4');

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        throw new RuntimeException('Failed to connect to the database.', 0, $exception);
    }

    return $pdo;
}

/**
 * Run a SELECT query and return all rows.
 */
function dbQuery(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/**
 * Run an INSERT/UPDATE/DELETE query and return affected row count.
 */
function dbExecute(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount();
}
