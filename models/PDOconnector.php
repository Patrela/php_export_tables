<?php
abstract class PDOConnector
{
  protected string $host;
  protected string $port;
  protected string $database;
  protected string $username;
  protected string $userPassword;
  protected ?PDO $connector = null;


  public function __construct($host, $port, $database, $username, $userPassword)
  {
    $this->host = $host;
    $this->port = $port;
    $this->database = $database;
    $this->username = $username;
    $this->userPassword = $userPassword;
    //$this->connector = null;
    $this->connector = $this->setConnection();
  }

  abstract protected function dsn(): string;

  // public function setConnection(): PDO
  // {
  //   $this->closeConnection();

  //     try {
  //         $dsn = $this->dsn();
  //         $pdo = new PDO($dsn, $this->username, $this->userPassword);
  //         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  //         $this->connector= $pdo;
  //         return $pdo;
  //     } catch (PDOException $e) {
  //         die('Connection failed: ' . $e->getMessage());
  //     }
  // }

  public function closeConnection(): void
  {
      $this->connector = null; // Closing the connection by setting it to null
  }
  public function query(string $query): array
  {
      $pdo = $this->setConnection(); // Ensure connection is active
      $stmt = $pdo->query($query);
      $result = !$stmt? false: $stmt->fetchAll(PDO::FETCH_ASSOC);
      return $result ?: []; // Return empty array if no records
  }

  public function setConnection(): PDO
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
    //$result = $this->query("SELECT * FROM $tablename LIMIT 1");
    $pdo = $this->setConnection();
    $query = $pdo->query("SELECT * FROM $tablename LIMIT 1");
    $result = !$query? false: $query->fetch(PDO::FETCH_ASSOC);
    return !$result? [] : array_keys($result);
    //return array_keys($result);
  }

  public function getTableColumnsType(string $tablename): array
  {
    //$tablename= str_replace("\"","", $tablename);
    // $query = $this->query("DESCRIBE $tablename");
    // $types = [];
    // foreach ($query as $column) {
    //   $types[] = $column['Type'];
    // }
    // return $types;    
    
    $pdo = $this->setConnection();
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
class MysqlConnection extends PDOConnector
{
  protected function dsn(): string
  {
    return "mysql:host={$this->host};port={$this->port};dbname={$this->database}";
  }

  public function getTablesName(): array {
    $pdo = $this->setConnection();
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
}  
}

// SQLite Connection class
class SqLiteConnection extends PDOConnector
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
    $pdo = $this->setConnection();
    $query = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
  }  
}

// SQL Server Connection class
class SqlServerConnection extends PDOConnector
{
  protected function dsn(): string
  {
    return "sqlsrv:Server={$this->host},{$this->port};Database={$this->database}";
  }

  public function getTablesName(): array {
    $pdo = $this->setConnection();
    $query = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema != 'sys'");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
  }  
}

// PostgreSQL Connection class
class PostgresConnection extends PDOConnector
{
  protected function dsn(): string
  {
    return "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";
  }

  public function getTablesName(): array {
    $pdo = $this->setConnection();
    $query = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = 'public'");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);
    return $tables;
  }
}

// Example usage:
// $pdo = new MysqlConnection('localhost', '3306', 'test_db', 'user', 'password');
// $conn = pdo->setConnection();
// $columns = $pdo->getTableColumnsName('table_name');
// print_r($columns);