<?php
abstract class Connector
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
    $this->connector = $this->setConnection();
  }

  // Private method to return DSN string for each type of connection
  abstract public function dsn(): string;

  // Public method to return the PDO active connection
  public function setConnection(): PDO
  {
    //$this->closeConnection();
    try {
      $dsn = $this->dsn();
      $pdo = new PDO($dsn, $this->username, $this->userPassword);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->connector = $pdo;
      return $pdo;
    } catch (PDOException $e) {
      die('Connection failed: ' . $e->getMessage());
    }
  }
  public function closeConnection(): void
  {
      $this->connector = null; // Closing the connection by setting it to null
  }

  public function query(string $query, bool $isOne = false): array
  {
      $pdo = $this->setConnection(); // Ensure connection is active
      $stmt = $pdo->query($query);
      if($isOne){
        $result = !$stmt? false: $stmt->fetch(PDO::FETCH_ASSOC);
      }
      else{
        $result = !$stmt? false: $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      return $result ?: []; // Return empty array if no records
  }

  public function getTableColumnsName(string $tablename): array
  {
    //$tablename= str_replace("\"","", $tablename);
    $query = $this->query("SELECT * FROM $tablename LIMIT 1", true);
    return array_keys($query);

    // $pdo = $this->setConnection();    
    // $query = $pdo->query("SELECT * FROM $tablename LIMIT 1");
    // $result = !$query? false: $query->fetch(PDO::FETCH_ASSOC);
    // return !$result? [] : array_keys($result);
  }

  public function getTableColumnsType(string $tablename): array
  {
    $tablename= str_replace("\"","", $tablename);
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
class MysqlConnection extends Connector
{
  public function dsn(): string
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
class SqLiteConnection extends Connector
{
  public function __construct($database)
  {
    $this->database = $database;
  }

  public function dsn(): string
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
class SqlServerConnection extends Connector
{
  public function dsn(): string
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
class PostgresConnection extends Connector
{
  public function dsn(): string
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

class StandardConnection
{
  protected string $databasetype;
  protected string $host;
  protected string $port;
  protected string $database;
  protected string $username;
  protected string $userpassword;
  protected ?Connector $connector = null;

  public function __construct($databasetype, $host, $port, $database, $username, $userpassword)
  {
    $this->host = $host;
    $this->port = $port;
    $this->database = $database;
    $this->username = $username;
    $this->userpassword = $userpassword;
    $this->databasetype = $databasetype;
  }  

  public function getConnector(): Connector
  {
    switch ($this->databasetype) {
      case 'mysql':
        $connector = new MysqlConnection($this->host, $this->port, $this->database, $this->username, $this->userpassword);
        break;
      case 'pgsql':
        $connector = new PostgresConnection($this->host, $this->port, $this->database, $this->username, $this->userpassword);
        break;
      case 'sqlite':
        $connector = new SqLiteConnection($this->database);
        break;
      case 'sqlsrv':
        $connector = new SqlServerConnection($this->host, $this->port, $this->database, $this->username, $this->userpassword);
        break;
    }
    $this->connector = $connector;
    return  $this->connector;  
  }
}
// Example usage:
// $pdo = new MysqlConnection('localhost', '3306', 'test_db', 'user', 'password');
// $conn = pdo->setConnection();
// $columns = $pdo->getTableColumnsName('table_name');
// print_r($columns);