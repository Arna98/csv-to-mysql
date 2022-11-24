<?php

include_once 'config.php';
include_once 'dbTable.php';

// If this were true, $_POST['submit'] is set
if (isset($_POST['submit']))
{
    // Create instance of Config class
    $configuration = new Config($_POST['dbName']);
    // Create and Connect to Database
    $db = $configuration->dbConnection();

    $numOfTbl = $_POST['numberOfTbl'];

    for ($count=1; $count <= $numOfTbl; $count++) 
    { 
        $sourceFile = $_FILES["csvFile$count"]["name"];

        // Specify the name of the table
        $tblName = $_POST["tblName$count"];
        $tblName = $tblName . "_" . str_replace(".csv","", $sourceFile);
        
        // get Header Row
        $headerRow = getHeaderRow($sourceFile);

        // get 10 Rows of csv file that is not empty
        $get10Rows = getCustomCSV($sourceFile);

        // analisis data for detect it's type
        $dataTypes = analysisDataTypes($get10Rows);

        // Merg header name & data type & size column
        $csvColumns = createCsvColumns($headerRow, $dataTypes);

        // Detection of columns index of DataTime type
        $columnsDataTime = setColumnsDataTime($dataTypes);

        // Create instance of DBTable class
        $createTbl = new dbTable($db, $tblName, $csvColumns, $headerRow, $sourceFile, $columnsDataTime);
        // Create Table
        $createTbl->createTable();
        // insert Data in database
       // $createTbl->loadDataToTable();
    }
}

// get Header Row
function getHeaderRow($srcFile)
{
    if ($file = fopen($srcFile, "r")) {
        // It only takes the first line
        $data = fgetcsv($file, 10000, ",");
        // Cleanse header row
        $data = cleanseHeaderRow($data);
    }
    // Display the result
    // print_r($data);
    return $data;
    
}

// replace underline to space character
function cleanseHeaderRow($headerRow)
{
    $newHeaderRow = array();
    foreach ($headerRow as $key => $firstRow) {
        $newHeaderRow[$key] = strtolower(str_replace(" ", "_", preg_replace("/[^\w]+/", "_", trim($firstRow))));
    }
    return $newHeaderRow;
}

// get 10 Rows of csv file that is not empty for analisis data
function getCustomCSV($srcFile, $lenght = 10)
{
    $numberRow = 0;
    $output = array();

    if ($file = fopen($srcFile, "r")) {
        while (($data = fgetcsv($file, 10000, ",")) == true) {
            if ($numberRow != 0) {
                if ($lenght) {
                    $notEmptyLine = true;
                    foreach ($data as $row) {
                        if (empty($row)) {
                            $notEmptyLine = false;
                        }
                    }
                    if ($notEmptyLine) {
                        $output[] = $data;
                        $lenght--;
                    }
                } else {
                    break;
                }
            }
            $numberRow++;
        }
        fclose($file);
    }
    return $output;
}

// analisis data for detect integer, varchar , datetime type
function analysisDataTypes($get10Rows)
{
    $dataTypes = array();
    // for each rows
    foreach ($get10Rows as $key => $value) {
        // $numberOfCol = sizeof($value);
        
        // for each cells of row
        foreach ($value as $cell) {
            if (is_numeric($cell)) {
                if (detectTinyIntType((int)$cell)) {
                    $dataTypes[$key][] = "TINYINT";
                } else {
                    $dataTypes[$key][] = "INT";
                }
            } elseif (detectDateTimeType($cell)) {
                $dataTypes[$key][] = "DATETIME";
            } else {
                $dataTypes[$key][] = "VARCHAR";
            }
        }
    }


    $compare = true;
    $DT = array();
    // size of columns csv file
    $numberOfCol = sizeof($get10Rows[0]);
    // Compare Data Types
    for ($col = 0; $col < $numberOfCol; $col++) {
        for ($row = 1; $row <= 9; $row++) {
            if ($dataTypes[0][$col] == $dataTypes[$row][$col]) {
                $DT[$col] = $dataTypes[0][$col];
            } else {
                $compare = false;
            }
        }
    }
    
    if ($compare) {
        return $DT;
    } else {
        echo "<pre"."Error : The data type of your CSV file column is not the same!"."</pre>";
        return $dataTypes;
    }
}

// detect date time type
function detectDateTimeType($val)
{
    if (preg_match('/(.*)([0-9]{2}\/[0-9]{2}\/[0-9]{2,4})(.*)/', $val)) {
        return 1;
    } elseif (preg_match('/(.*)([0-9]{2}\-[0-9]{2}\-[0-9]{2,4})(.*)/', $val)) {
        return 1;
    } else {
        return 0;
    }
}

// detect boolean (TINYINT) type
function detectTinyIntType($val)
{
    return (strlen($val) == 1 && ($val == 0 || $val == 1)) ? 1 : 0;
}

// Merg header name & data type & size column to one string
function createCsvColumns($headerRow, $dataTypes)
{
    $csvColumns = array();
    $sizeColumn = 0;

    // We determine the data type and size for each column
    for ($count = 0 ; $count < sizeof($headerRow) ; $count++) {
        if ($dataTypes[$count] == 'INT') {
            $sizeColumn = 20;
        } elseif ($dataTypes[$count] == 'TINYINT') {
            $sizeColumn = 1;
        } elseif ($dataTypes[$count] == 'VARCHAR') {
            $sizeColumn = 255;
        } elseif ($dataTypes[$count] == 'DATETIME') {
            $sizeColumn = 6;
        }
        $csvColumns[] = "$headerRow[$count] " . strtoupper($dataTypes[$count]) . "({$sizeColumn})";
    }
    $csvColumns = join(', ', $csvColumns);

    return $csvColumns;
}

// Detection of columns of DataTime type and storing columns index
function setColumnsDataTime($dataTypes)
{
    $columnsDataTime = array();
    foreach ($dataTypes as $key => $value) {
        if( $value == "DATETIME"){
            $columnsDataTime[] = $key;
        }
    }

    return $columnsDataTime;
}


?>