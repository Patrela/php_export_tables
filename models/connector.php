<?php
abstract class Connector
{
  protected string $host;
  protected string $port;
  protected string $database;
  protected string $username;
  protected string $userPassword;

  public function __construct($host, $port, $database, $username, $userPassword)
  {
    $this->host = $host;
    $this->port = $port;
    $this->database = $database;
    $this->username = $username;
    $this->userPassword = $userPassword;
  }

  // Private method to return DSN string for each type of connection
  abstract protected function dsn(): string;

  // Public method to return the PDO active connection
  public function connector(): PDO
  {
    try {
      $dsn = $this->dsn();
      $pdo = new PDO($dsn, $this->username, $this->userPassword);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    } catch (PDOException $e) {
      die('Connection failed: ' . $e->getMessage());
    }
  }

  public function getTableColumnsName(string $tablename): array
  {
    $pdo = $this->connector();
    //$tablename= str_replace("\"","", $tablename);

    $query = $pdo->query("SELECT * FROM $tablename LIMIT 1");
    $result = !$query? false: $query->fetch(PDO::FETCH_ASSOC);
    return !$result? [] : array_keys($result);
  }

  public function getTableColumnsType(string $tablename): array
  {
    $tablename= str_replace("\"","", $tablename);
    $pdo = $this->connector();
    $query = $pdo->query("DESCRIBE $tablename");
    $types = [];
    foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $column) {
      $types[] = $column['Type'];
    }
    return $types;
  }

  // Abstract method to return all non-system tables
  abstract public function getTablesName(): array;
}

// MySQL Connection class
class MysqlConnection extends Connector
{
  protected function dsn(): string
  {
    return "mysql:host={$this->host};port={$this->port};dbname={$this->database}";
  }

  public function getTablesName(): array {
    $pdo = $this->connector();
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
}  
}

// SQLite Connection class
class SqLiteConnection extends Connector
{
  public function __construct($database)
  {
    $this->database = $database;
  }

  protected function dsn(): string
  {
    return "sqlite:{$this->database}";
  }

  public function getTablesName(): array {
    $pdo = $this->connector();
    $query = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
  }  
}

// SQL Server Connection class
class SqlServerConnection extends Connector
{
  protected function dsn(): string
  {
    return "sqlsrv:Server={$this->host},{$this->port};Database={$this->database}";
  }

  public function getTablesName(): array {
    $pdo = $this->connector();
    $query = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema != 'sys'");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
  }  
}

// PostgreSQL Connection class
class PostgresConnection extends Connector
{
  protected function dsn(): string
  {
    return "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";
  }

  public function getTablesName(): array {
    $pdo = $this->connector();
    $query = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = 'public'");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
  }
}

// Example usage:
// $pdo = new MysqlConnection('localhost', '3306', 'test_db', 'user', 'password');
// $conn = pdo->connector();
// $columns = $pdo->getTableColumnsName('table_name');
// print_r($columns);