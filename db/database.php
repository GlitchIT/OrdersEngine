<?php

include_once('dbConfig.php');

class DB{
  
  protected $config;
  
  public $dbcon;
  
  public function __construct()
    {
        $this->config = new dbConfig();
        try {
          $this->dbcon = $this->connect();
        }catch(PDOException $e){
            die("Database Query Error: ".$e->getMessage());
        }
    }

    private function getCredentials($name = 'mysql')
    {
        foreach($this->config->config as $key => $conn)
        {
            if(isset($conn[$name])) return $conn[$name];
        }
    }

    public function connect()
    {
        $data = $this->getCredentials();

        try {
            $con_opts = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            );
            $this->conn =  new PDO("mysql:host=" . $data['host'] . ";dbname=" . $data['database'], $data['username'], $data['password'], $con_opts);

            return $this->conn;

        }catch (PDOException $e){

            die( "Unable to connect to mysql server. " . $e->getMessage());
        }
    }

    public function execQuery($qry,$params=null){
        try{
            if(is_null($params)){
                return $this->dbcon->exec($qry);
            }else{
                $ps = $this->dbcon->prepare($qry);
                $this->bindParamArray($ps,$params);
                $ps->execute();
                return $ps->rowCount();
            }
        }catch(PDOException $e){
            die("Database Query Error: ".$e->getMessage());
        }
    }


    public function singleColQuery($qry,$params=null){
        try{
            if(is_null($params)){
                return $this->dbcon->query($qry)->fetchColumn();
            }else{
                $ps = $this->dbcon->prepare($qry);
                $this->bindParamArray($ps,$params);
                $ps->execute();
                return $ps->fetchColumn();
            }
        }catch(PDOException $e){
            die("Database Query Error: ".$e->getMessage());
        }
    }


    public function singleRowQuery($qry,$params=null){
        try{
            if(is_null($params)){
                return $this->dbcon->query($qry)->fetch();
            }else{
                $ps = $this->dbcon->prepare($qry);
                $this->bindParamArray($ps,$params);
                $ps->execute();
                return $ps->fetch();
            }
        }catch(PDOException $e){
            die("Database Query Error: ".$e->getMessage());
        }
    }


    public function multiRowQuery($qry,$params=null,$processor=null){
        try{
            if(is_null($params)){
                $stmt = $this->dbcon->query($qry);
                $stmt->execute();
            }else{
                $stmt = $this->dbcon->prepare($qry);
                $this->bindParamArray($stmt,$params);
                $stmt->execute();
            }
            if(is_callable($processor)){
                while (($row = $stmt->fetch()) !== false) {
                    $processor($row);
                }
            }else{
                return $stmt->fetchAll();
            }
        }catch(PDOException $e){
            die("Database Query Error: ".$e->getMessage());
        }
    }

    public function quoteString($str){
        return $this->dbcon->quote($str);
    }


    public function getLastInsertedId(){
        return $this->dbcon->lastInsertId();
    }

    public function __toString(){
        return "";
    }

    private function bindParamArray($ps,$params){
        foreach($params as $placeholder=>$valandtype){
            $ps->bindValue($placeholder,$valandtype[0],$valandtype[1]);
        }
    }
}
