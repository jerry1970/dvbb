<?php
/**
 * db_sqlite3 class
 * 
 * This class provides sqlite3 functionality standardized for dvbb
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class db_sqlite3 {

    protected $connection;
    
    public function __construct($location) {
        $this->connection = new SQLite3($location);
    }
    
    public function query($query) {
        return $this->connection->query($query);
    }
    
    public function escapeString($string) {
        return $this->connection->escapeString($string);
    }
    
    public function fetchArray($resultObject) {
        $return = array();
        while ($row = $resultObject->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function lastInsertRowID() {
        return $this->connection->lastInsertRowID();
    }
    
}
