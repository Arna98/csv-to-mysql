<?php

include_once 'config.php';

// If this were true, $_POST['submit'] is set
if (isset($_POST['submit']))
{
    $configuration = new Config($_POST['dbname']);
    $db = $configuration->dbConnection();

}

?>