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

    for ($count=1; $i <= $numOfTbl; $count++) 
    { 
        $sourceFile = $_FILES["csvFile$count"]["name"];

        // Specify the name of the table
        $tblName = $_POST["tblName$count"];
        $tblName = $tblName . "_" . str_replace(".csv","", $sourcFile);
        
    }
}


?>