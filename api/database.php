<?php
declare(strict_types=1);

class database{
  private $user,$password,$databaseName,$port, $url;

  private $connection;

  public $success;


  private function make_connection(){

    $this->connection = new mysqli($this->url, $this->user, $this->password, $this->databaseName, $this->port);

    if($this->connection->connect_error){
      $this->success = false;
    }
    $this->success = true;

  }

  public function close_connection(){
    if($this->connection != null){
            $this->connection->close();
            $this->connection = null;
        }
  }

  function __construct(string $url, string $user,string $password,string $databaseName,int $port){
    $this->url = $url;
    $this->user = $user;
    $this->password = $password;
    $this->databaseName = $databaseName;
    $this->port = $port;
    self::make_connection();
  }

  public function __destruct(){
    if($this->connection != null  && $this->success){
        $this->close_connection();
    }
  }

  public function params_get_types(array $params){
    $types = "";
    foreach ($params as $param) {
      switch (gettype($param)) {
        case 'string':
          $types.="s";
          break;
        case 'integer':
          $types.="i";
          break;
        case 'double':
          $types.="d";
        break;
        default:
          $types .= "b";
        break;
      }
    }
    return $types;

  }

  public function execute_query(string $query, array $params = null,string $mode = "i"){
    $completed = false;
    if($params){

      // evaluate SQL injection somehow

      $exec = $this->connection->prepare($query);
      $exec->bind_param(self::params_get_types($params),...$params);


      if($exec->execute()){

        switch ($mode) {
          case 'i':
            $completed = true;
          break;
          case 'ro':
            if($exec->get_result()->num_rows > 0){
              $completed = true;
            }
          break;
        }

      }
    }
    return $completed;
  }

  public function obtain_query(string $query, string $mode = "rone", string $Rdata = ""){
    $ans = "0";
      // evaluate SQL injection somehow

      if($res = $this->connection->query($query)){
        switch ($mode) {
          case 'rone':
            if($row = $res->fetch_assoc()){
              $ans = $row[$Rdata];
            }
          break;
          case 'rmany':
            $ans = [];
            while($row = $res->fetch_assoc()){
              array_push($ans,[$row['nombre'],(int)$row['precio'],$row['URLimagen'],$row["id"]]);
            }
          break;
        }

      }
    
    return $ans;
  }


}

 ?>