<?php
$host = 'your_host';
$username = 'your_username';
$password = 'your_password';
$port = 'your_port';
$dbname = $_GET['dbname'];

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['tables' => $tables]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
