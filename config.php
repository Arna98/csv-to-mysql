<?php
class Config{
 
  // specify your own database credentials
  private $host = "localhost";
  private $username = "root";
  private $password = "";
  private $dbName;
  private $connection;
  private $state;

  public function __construct($dbName){
    $this->dbName = $dbName;
  }

  // get the database connection
  public function dbConnection(){

    $this->connection = null;
    //echo $this->createDB();
    if($this->createDB()){
      try{
        $this->connection = new PDO("mysql:host=". $this->host .";dbname=" . $this->dbName, $this->username, $this->password);
        // set the PDO error mode to exception
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
      }catch(PDOException $exception){
        echo "Connection failed: " . $exception->getMessage();
      }
    }

    return $this->connection;
  }

  private function createDB(){

    try {
        $conn = new PDO("mysql:host=". $this->host, $this->username, $this->password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "CREATE DATABASE IF NOT EXISTS $this->dbName";
        // use exec() because no results are returned
        $conn->exec($query);
        $this->state = true;
      } catch(PDOException $exception) {
        echo $query ."<br> " . $exception->getMessage();
        $this->state = false;
      }
    
      return $this->state;
  }
}

?>