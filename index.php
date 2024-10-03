<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Connection</title>
    <style>
        .container { display: flex; }
        .list { margin: 10px; }
    </style>
</head>
<body>
    <h1>Database Connection</h1>
    <h2>Useful for Databases: MySQL, MariaDB, SqLite, PostgreSQL, MS SQL Server</h2>
    <form method="post" action="controllers/connect.php">
        <label for="host">Host:</label>
        <input type="text" id="host" name="host"><br>
        <label for="port">Port:</label>
        <input type="text" id="port" name="port"><br>
        <label for="dbname">Database Name:</label>
        <input type="text" id="dbname" name="dbname" required><br>        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username"><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password"><br>
        <select id="databasetype" name="databasetype" required>
          <option value="mysql">MySQL</option> <!-- $dsn = "mysql:host=<insert_host_address_here>;dbname=rolodex;charset=utf8mb4"; PDO("mysql:host=localhost;dbname=world", 'my_user', 'my_password'); $dbh = new PDO('mysql:host=hotsname;port=3309;dbname=dbname', 'root', 'root'); -->
          <option value="mysql">MariaDB</option>  <!-- $dsn = "mysql:host=<insert_host_address_here>;dbname=rolodex;charset=utf8mb4"; PDO($dsn, 'my_user', 'my_password'); https://mariadb.com/resources/blog/developer-quickstart-php-data-objects-and-mariadb/ -->          
          <option value="pgsql">PostgreSQL</option> <!-- $dsn = "pgsql:host=$host;port=5432;dbname=$db;"; $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); https://www.phptutorial.net/php-pdo/pdo-connecting-to-postgresql/  -->
          <option value="sqlite">SqLite</option>   <!-- $dbname= the.db $db = new PDO("sqlite:" .$dbname);  $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); https://renenyffenegger.ch/notes/development/web/php/snippets/sqlite/index -->
          <option value="sqlsrv">MS SQL Server</option>  
          <!-- connect to a MS SQL Server database on a specified port:
$c = new PDO("sqlsrv:Server=localhost,1521;Database=testdb", "UserName", "Password");
connecto to a SQL Azure database with server ID 12345abcde. Note that when you connect to SQL Azure with PDO, your username will be UserName@12345abcde (UserName@ServerId).

$c = new PDO("sqlsrv:Server=12345abcde.database.windows.net;Database=testdb", "UserName@12345abcde", "Password");
https://www.php.net/manual/en/ref.pdo-sqlsrv.connection.php -->
        </select>        
        <button type="submit">Connect</button>
    </form>

    <div class="container">
        <div class="list">
            <h2>Tables</h2>
            <ul id="tableList"></ul>
        </div>
        <div class="list">
            <h2>Columns</h2>
            <ul id="columnList"></ul>
        </div>
    </div>
</body>
</html>
