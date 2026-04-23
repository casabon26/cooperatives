<?php
$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];
if ($argc < 3) {
    echo json_encode(['error' => 'usage: php dump_row_mysql.php table id']);
    exit(1);
}
$table = $argv[1];
$id = intval($argv[2]);
// MySQL connection (from .env)
$host = '127.0.0.1';
$db   = 'ccldo_dbs';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->prepare('SELECT * FROM ' . preg_replace('/[^a-z_]/', '', $table) . ' WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
