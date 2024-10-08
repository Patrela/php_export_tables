<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php
    session_start();
    $dbname = $_GET['dbname'];
    $tables = $_SESSION["tables"];
    $columns = $_SESSION["fieldslist"];
    $pdo = $_SESSION['connectdata'];
    ?>

    <title><?php echo htmlspecialchars($dbname); ?></title>
    <link rel="stylesheet" href="../styles/app.css">
</head>

<body>
    <script type="text/javascript">
        let tables = <?php echo $tables; ?>; // Global variable to store all table names
        console.log(tables);
        let columns = <?php echo $columns; ?>;
        console.log(columns);
        //<//?php echo $js_code; ?>
    </script>
    <h1>Database <?php echo htmlspecialchars($dbname); ?></h1>
    <form method="post" action="../controllers/exportdata.php">
        <div class="container">
            <div class="list">
                <h2>Tables</h2>
                <div id="tableList"></div>
                <input type="hidden" id="tablename" name="tablename">
            </div>
            <div class="list">
                <h2>Columns</h2>
                <div id="columnList">
                <select class="select-list"></select>
                </div>
                <input type="hidden" id="tablefields" name="tablefields">

            </div>
            <div class="list">
                <h2>Fields List</h2>
                <select id="columnListSelected" class="select-list">
                    <option value="*">Select All</option>
                </select>
            </div>
        </div>
        <div class="centered">
            <!-- <a class="btn btn-info" download="export_database_query" href="export_database_query.php">Export Selected Table Fields</a> -->
            <button type="submit" class="btn btn-info">Export to Excel</button>
        </div>
    </form>
    <script>
        /*
        function fetchTables() {

        const tableList = document.getElementById('tableList');
        tables.forEach(table => {
                    const li = document.createElement('li');
                    li.textContent = table;
                    li.onclick = () => fetchColumns(table);
                    tableList.appendChild(li);
                });
        }
        */
        function fetchTables() {

            const tableList = document.getElementById('tableList');
            const columnList = document.getElementById('columnListSelected');
            columnList.innerHTML = '';
            const selectList = document.createElement('select');
            selectList.size = "15";
            selectList.classList.add("select-list");
            tables.forEach(table => {
                const option = document.createElement('option');
                option.textContent = table;
                option.value = table;
                option.onclick = () => fetchColumns(table);
                selectList.appendChild(option);
            });
            tableList.appendChild(selectList);
        }

        function fetchColumns(table) {
            tableColumns = columns[table];
            const tablename = document.getElementById('tablename');
            tablename.value = table;
            const columnList = document.getElementById('columnList');
            columnList.innerHTML = '';
            const columnListSelected = document.getElementById('columnListSelected');
            columnListSelected.innerHTML = '';
            const selectList = document.createElement('select');
            selectList.size = "15";
            selectList.classList.add("select-list");

            const option = document.createElement('option');
            option.textContent = '*';
            option.value = '*';
            option.onclick = () => selectColumns(option);
            selectList.appendChild(option);

            tableColumns.forEach(column => {
                const option = document.createElement('option');
                option.textContent = column;
                option.value = column;
                option.onclick = () => selectColumns(option);
                selectList.appendChild(option);
            });
            columnList.appendChild(selectList);
        }

        function selectColumns(element) {
            const selectList = document.getElementById('columnListSelected');
            selectList.size = "15";
            //selectList.classList.add("select-list");
            if (!haschildTag(selectList, element.value)) {
                const option = document.createElement('option');
                option.textContent = element.value;
                option.value = element.value;

                option.onclick = () => removechildTag(selectList, element.value);
                selectList.appendChild(option);
            }
            const tablefields = document.getElementById('tablefields');
            tablefields.value = childsValueList(selectList);
        }

        function childsValueList(element) {
            childValues = "";
            for (const child of element.children) {
                childValues = childValues + child.value + ",";
            }
            childValues = childValues.substring(0, childValues.length - 1);
            return childValues;
        }

        function haschildTag(element, tagvalue) {
            hasChild = false;
            for (const child of element.children) {
                if (child.value == tagvalue) {
                    hasChild = true;
                    break;
                }
            }
            return hasChild;
        }

        function removechildTag(parent, tagvalue) {
            for (const child of parent.children) {
                if (child.value == tagvalue) {
                    parent.removeChild(child);
                    break;
                }
            }
        }

        document.addEventListener("DOMContentLoaded", fetchTables);
    </script>

</body>

</html>