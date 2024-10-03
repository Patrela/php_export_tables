<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">    
    <?php
        // to unserialize the PDO object $pdo
        include '../models/connector.php';
        session_start();
        $dbname = $_GET['dbname'];
        $tables = $_SESSION["tables"];
        $pdo = $_SESSION['pdo'];
        $js_code = '';
        $tableArray = explode(',', substr($tables,1,strlen($tables)-2) ); // remove [] characters

            $columns = []; 
            $i = 0;
            foreach ($tableArray as $table) {
                $tablename= str_replace("\"","", $table);
                $columns[$tablename]= $pdo->getTableColumnsName($tablename);
            }          
            //$js_code = 'console.log(' . json_encode($columns, JSON_HEX_TAG) . ');';
            //if ($with_script_tags) {
            //$js_code = '<script>' . $js_code . '</script>';
            //}
            //echo $js_code;           
    ?>
  
  <title><?php echo htmlspecialchars($dbname); ?></title>
 <link rel="stylesheet" href="../styles/app.css">
</head>
<body>
    <script type="text/javascript">
        let tables = <?php echo $tables; ?>; // Global variable to store all table names
        let columns = <?php echo json_encode($columns, JSON_HEX_TAG) ?>; 
        console.log(columns); 
        //<//?php echo $js_code; ?>
    </script>  
    <h1>Database <?php echo htmlspecialchars($dbname); ?></h1>
    <div class="container">
        <div class="list">
            <h2>Tables</h2>
            <div id="tableList"></div>
        </div>
        <div class="list">
            <h2>Columns</h2>
            <div id="columnList"></div>
        </div>
        <div class="list">
            <h2>Selected</h2>
            <select id="columnListSelected" class="select-list">
                <option value="*">Select All</option>
            </select>
        </div>        
    </div>
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
                    selectList.size="15";
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
                    const columnList = document.getElementById('columnList');
                    columnList.innerHTML = '';
                    const columnListSelected = document.getElementById('columnListSelected');
                    columnListSelected.innerHTML = '';
                    const selectList = document.createElement('select');
                    selectList.size="15";
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
        function selectColumns(element){
            const selectList = document.getElementById('columnListSelected');
            selectList.size="15";
            //selectList.classList.add("select-list");
            if(!haschildTag(selectList,element.value)){
                const option = document.createElement('option');
                option.textContent = element.value;
                option.value = element.value;
                option.onclick = () => removechildTag(selectList, element.value);                
                selectList.appendChild(option);
            }
        }

        function haschildTag(element, tagvalue){
            hasChild = false;
            for (const child of element.children) {
                if (child.value == tagvalue){
                    hasChild = true;
                    break;
                }
            }   
            return hasChild;         
        }

        function removechildTag(parent, tagvalue){
            for (const child of parent.children) {
                if (child.value == tagvalue){
                    parent.removeChild(child);
                    break;
                }
            }   
        }
        
        document.addEventListener("DOMContentLoaded", fetchTables);
       
    </script>    

</body>
</html>
