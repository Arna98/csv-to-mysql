<?php

include_once 'config.php';

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
?>