<?php

class DBTable{

    private $connection;
    private $columns;
    private $tableName;

    public function __construct($conn, $tblNmame, $csvColumns){
        $this->connection = $conn;
        $this->tableName = $tblNmame;
        $this->columns = $csvColumns;
    }

    function createTable(){
        $query = "CREATE TABLE IF NOT EXISTS $this->tableName (
            ID int(11) AUTO_INCREMENT PRIMARY KEY,
            $this->columns
            )";
        $this->connection->exec($query);
        echo "<br>Table $this->tableName Created Successfully \n";
    }
}

?>