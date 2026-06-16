<?php
$env = parse_ini_file('.env');

$host = $env['DB_HOST'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$name = $env['DB_NAME'];
$port = $env['DB_PORT'];

try {
    $dsn = "mysql:host=$host;dbname=$name;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

function getPokemonById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM pokemon WHERE pokedex_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getStatsByPokemonId($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM pokemon_stats WHERE pokemon_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getTypesByPokemonId($pdo, $id) {
    $stmt = $pdo->prepare("SELECT t.id, t.name FROM type t JOIN pokemon_type pt ON t.id = pt.type_id WHERE pt.pokemon_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}

function getEvolutionsByPokemonId($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT p.pokedex_id, p.name_fr, pe.evolution_condition
        FROM pokemon_evolution pe
        JOIN pokemon p ON pe.post_evolution_id = p.pokedex_id
        WHERE pe.pre_evolution_id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}

function getReversedEvolutionsByPokemonId($pdo, $id){
    $stmt = $pdo->prepare("
        SELECT p.pokedex_id, p.name_fr
        FROM pokemon_evolution pe
        JOIN pokemon p ON pe.pre_evolution_id = p.pokedex_id
        WHERE pe.post_evolution_id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}