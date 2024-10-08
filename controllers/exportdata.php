<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> <!--Importante--->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Export Data</title>
</head>

<body>

  <?php
    include '../models/connector.php';
    session_start();
    $connectdata = $_SESSION["connectdata"];
    // echo '<pre>';
    // print_r($connectdata);
    // echo '</pre>';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $tablename = $_POST['tablename'];
      $tablefields = $_POST['tablefields'];

      $connector = new StandardConnection(
        $connectdata['databasetype'],
        $connectdata['host'],
        $connectdata['port'],
        $connectdata['dbname'],
        $connectdata['username'],
        $connectdata['password']
      );
      $pdo = $connector->getConnector();
      $query = "SELECT $tablefields FROM $tablename";

      try {

        $records = $pdo->query($query);
        $fields = count($records) > 0 ? array_keys($records[0]) : [];
        // $_SESSION["records"] = $records;
        // $_SESSION["fields"] = $fields;
        // $columns = [];
        // foreach ($tables as $key => $table) {
        //   $columns[$table] = $pdo->getTableColumnsName($table);
        // }

        // echo $query .'<br />';
        // echo '<pre>';
        // print_r($records);
        // echo '</pre>';  
        // echo '<pre> Keys: ';
        // print_r(array_keys($records[0]));
        // echo '</pre>';      
        // header("Location: ../views/display.php?dbname=$tablename");
      } catch (PDOException $e) {
        echo "Export data failed: " . $e->getMessage();
      }
    }

    date_default_timezone_set("America/Bogota");

    $filename = "export_" . $tablename . "-" . date("d/m/Y") . ".xls";

    header("Content-Type: text/html;charset=utf-8");
    header("Content-Type: application/vnd.ms-excel charset=iso-8859-1");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Disposition: attachment; filename=" . $filename . "");

  ?>

  <table style="text-align: center;" border='1' cellpadding= 1  cellspacing=1>
    <thead>
      <tr style="background: #D0CDCD;">
        <th> # </th>
        <?php foreach ($fields  as $key => $value) { ?>
          <th> <?php echo $value ?></th>
        <?php } ?>
      </tr>

    </thead>
    <tbody>
      <?php foreach ($records  as $keyX => $valueX) { ?>
        <tr>
          <td> <?php echo intval($keyX)+1 ?></td>
          <?php foreach ($fields  as $key => $value) { ?>
            <td> <?php echo $valueX["$value"] ?></td>
          <?php } ?>
        </tr>
      <?php } ?>
    </tbody>
  </table>

</body>

</html>