<?php
/**
 * database parent class
 * 
 * This class sets up and loads the configurated database driver class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class database {

    protected $database;
    
    public function __construct() {

        switch (store::getConfigValue('database_type')) {
            case 'sqlite3':
                require_once('./library/database/sqlite3.php');
                // sqlite only takes location
                $this->database = new db_sqlite3(
                    store::getPath() . store::getConfigValue('database_location')
                );
                break;
        }
        
    }
    
    public function query($query) {
        return $this->database->query($query);
    }
    
    public function escapeString($string) {
        return $this->database->escapeString($string);
    }
    
    public function fetchArray($resultObject) {
        return $this->database->fetchArray($resultObject);
    }
    
    public function lastInsertRowID() {
        return $this->database->lastInsertRowID();
    }

}
