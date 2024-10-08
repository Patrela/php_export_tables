<?php
include '../models/connector.php';
session_destroy();
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $host = $_POST['host'];
  $port = $_POST['port'];
  $dbname = $_POST['dbname'];
  $dbtype = $_POST['databasetype'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $connectdata = $_POST; 
  $connector= new StandardConnection($dbtype, $host, $port, $dbname, $username, $password);
  $pdo= $connector->getConnector();

  try {

    $tables = $pdo->getTablesName();
    $columns = []; 
    foreach ($tables as $key => $table) {
      $columns[$table]= $pdo->getTableColumnsName($table);      
    }
    
    $_SESSION["fieldslist"] = json_encode($columns, JSON_HEX_TAG);
    $_SESSION["tables"] = json_encode($tables); 
    $_SESSION["connectdata"] = $connectdata;

    // echo '<pre>';
    // print_r($_SESSION);
    // echo '</pre>';    
    header("Location: ../views/display.php?dbname=$dbname");
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}
