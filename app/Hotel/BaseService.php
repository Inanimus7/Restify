<?php

namespace Hotel;

use PDO;
use Support\Configuration\Configuration;
use Exception;

class BaseService
{
    private static $pdo;

    public function __construct()
    {
        $this->initializePdo();
    }

    /**
     * Initialize PDO connection if not already set
     */
    protected function initializePdo()
    {
        if (!empty(self::$pdo)) {
            return;
        }
        
        // Load database configuration
        $config = Configuration::getInstance();
        $databaseConfig = $config->getConfig()['database'];

        // Establish database connection
        self::$pdo = new PDO(
            sprintf(
                'mysql:host=%s;dbname=%s;charset=UTF8',
                $databaseConfig['host'],
                $databaseConfig['dbname']
            ),
            $databaseConfig['username'],
            $databaseConfig['password'],
            [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"]
        );
    }

    /**
     * Fetch multiple records from the database
     */
    protected function fetchAll($sql, $parameters = [], $type = PDO::FETCH_ASSOC)
    {  
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll($type);
    }
 
    /**
     * Fetch a single record from the database
     */
    protected function fetch($sql, $parameters = [], $type = PDO::FETCH_ASSOC)
    {  
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetch($type);
    }
    
    /**
     * Execute a SQL query with parameters
     */
    protected function execute($sql, $parameters)
    {
        $statement = $this->getPdo()->prepare($sql);
        $status = $statement->execute($parameters);
        
        if (!$status) {
            throw new Exception($statement->errorInfo()[2]);
        }
        
        return $statement; 
    }

    /**
     * Get the PDO instance
     */
    protected function getPdo()
    {
        return self::$pdo;
    }
}
