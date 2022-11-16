<?php

include_once 'config.php';

// If this were true, $_POST['submit'] is set
if (isset($_POST['submit']))
{
    // Create instance of Config class
    $configuration = new Config($_POST['dbname']);
    // Create and Connect to Database
    $db = $configuration->dbConnection();

}

?>