<?php
include '../models/connector.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $host = $_POST['host'];
  $port = $_POST['port'];
  $dbname = $_POST['dbname'];
  $dbtype = $_POST['databasetype'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  switch ($dbtype) {
    case 'mysql':
      $pdo = new MysqlConnection($host, $port, $dbname, $username, $password);
      break;
    case 'pgsql':
      $pdo = new PostgresConnection($host, $port, $dbname, $username, $password);
      break;
    case 'sqlite':
      $pdo = new SqLiteConnection($dbname);
      break;
    case 'sqlsrv':
      $pdo = new SqlServerConnection($host, $port, $dbname, $username, $password);
      break;
  }

  try {
    // $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    // $pdo = new PDO($dsn, $username, $password);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $tables = $pdo->getTablesName();
    session_start();
    $_SESSION["pdo"] = $pdo;
    $_SESSION["tables"] = json_encode($tables); 
    header("Location: ../views/display.php?dbname=$dbname");
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}
