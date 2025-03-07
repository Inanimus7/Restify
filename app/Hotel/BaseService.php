<?php

namespace Hotel;


use PDO;
use Support\Configuration\Configuration;

class BaseService
{
    private static $pdo;

    public function __construct()
    {
      $this->initializePdo();
    }

    protected function initializePdo()
    {
      //Chcek if pdo is already initialized
      if(!empty(self::$pdo))
      {
        return;
      }
        
      //Load database configuration
    $config = Configuration::getInstance();
    $databaseConfig = $config->getConfig()['database'];

      //Connect to database
    self::$pdo = new PDO(sprintf('mysql:host=%s;dbname=%s;charset=UTF8', $databaseConfig['host'], $databaseConfig['dbname']), $databaseConfig['username'], $databaseConfig['password'], 
    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]);

    }
    protected function fetchAll($sql, $parameters = [], $type = PDO::FETCH_ASSOC) 
    {  
       //Prepare statement
        $statement = $this->getPdo()->prepare($sql);
 
       //Bind Parameters
       foreach($parameters as $key => $value) {
          $statement->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
       }
        //Execute
        $statement->execute(); 
 
       //Fetch all
       return $statement->fetchAll($type);
    }
 
    protected function fetch($sql, $parameters = [], $type = PDO::FETCH_ASSOC) 
    {  
       //Prepare statement
        $statement = $this->getPdo()->prepare($sql);
 
       //Bind Parameters
       foreach($parameters as $key => $value) {
          $statement->bindParam($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
       }
        //Execute
        $statement->execute();
 
       //Fetch all
       return $statement->fetch($type);
    }

    protected function getPdo()
   {
    return self::$pdo;
   }
}