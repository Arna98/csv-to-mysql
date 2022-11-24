<?php

class DBTable
{

    private $connection;
    private $columns;
    private $tableName;
    private $headers;
    private $srcFile;
    private $colDT;

    public function __construct($db, $tblNmame, $csvColumns, $headerRow, $sourceFile, $columnsDataTime)
    {
        $this->connection = $db;
        $this->tableName = $tblNmame;
        $this->columns = $csvColumns;
        $this->headers = $headerRow;
        $this->srcFile = $sourceFile;
        $this->colDT = $columnsDataTime;
    }

    function createTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS $this->tableName (
            ID int(11) AUTO_INCREMENT PRIMARY KEY,
            $this->columns
            )";
        $this->connection->exec($query);
        echo "<br>Table $this->tableName Created Successfully \n";
    }

    // Load Data(csv file) to table by one query
    function loadDataToTable()
    {
        // counts the total number of rows present in the database table.
        $query = "SELECT COUNT(*) count from $this->tableName";
        $stmt = $this->connection->exec($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $firstCount = (int)$row['count'];

        $atHeaderRow = createHeaderString($this->headers, $this->colDT);
        $setString = createSetString($this->headers, $this->colDT);
        print_r($atHeaderRow);
        $atHeaderRow = join(', ', $atHeaderRow);
        print_r($atHeaderRow);

        // Load data (csv file) to table
        $query = ' LOAD DATA LOCAL INFILE "' . $this->srcFile . '"
            INTO TABLE ' . $this->tableName . '
            FIELDS TERMINATED by \',\'
            LINES TERMINATED BY \'\n\'
            IGNORE 1 ROWS
            (' . $atHeaderRow . ') 
            ' . $setString . ';';

        $this->connection->exec($query);

        // counts the total number of rows present in the database table.
        $query = "SELECT COUNT(*) count from $this->tableName";
        $stmt = $this->connection->exec($query);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $secCount = (int)$row['count'];

        // Total records have been added to the table
        $count = $secCount - $firstCount;
        if ($count > 0)
            echo "<b> total $count records have been added to the table $this->tableName </b> ";
    
    }

    // create string for @ header
    function createHeaderString($headerRow, $columnsDataTime)
    {
        foreach ($headerRow as $key => $value) {
            foreach ($columnsDataTime as $index) {
                if ($key == $index) {
                    $headerRow[$key] = '@' . $value;
                }
            }
        }
        return $headerRow;
    }

    // create string for set query | STR_TO_DATE
    function createSetString($headerRow, $columnsDataTime)
    {
        $setString = '';
        $commaState = false;
        $chk = false;
        foreach ($headerRow as $key => $value) {
            foreach ($columnsDataTime as $index) {
                if ($key == $index) {
                    if ($commaState) {
                        $setString .= ', ';
                    }
                    $setString .= $value . ' = STR_TO_DATE(' . $headerRow[$key] . ', "%m/%d/%Y")';
                    $chk = true;
                    $commaState = true;
                }
            }
        }
        if ($chk) {
            $allSetString = 'SET ' . $setString;
        }

        return $allSetString;
    }
}

?>