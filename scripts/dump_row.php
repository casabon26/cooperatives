<?php
$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];
if ($argc < 3) {
    echo json_encode(['error' => 'usage: php dump_row.php table id']);
    exit(1);
}
$table = $argv[1];
$id = intval($argv[2]);
$db = __DIR__ . '/../database/database.sqlite';
if (!file_exists($db)) {
    echo json_encode(['error' => 'no sqlite']);
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $db);
    $stmt = $pdo->prepare('SELECT * FROM ' . preg_replace('/[^a-z_]/', '', $table) . ' WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
