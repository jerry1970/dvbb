<?php
/**
 * model class
 * 
 * This class provides some shared behaviour for models
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class model {
    
    protected $db;
    protected $tableKey = 'id';
    
    const QUERY_SIMPLE = 'SELECT * FROM :tableName WHERE :key = :value';
    const QUERY_ALL_ASC = 'SELECT * FROM :tableName ORDER BY :tableKey ASC';
    const QUERY_ALL_DESC = 'SELECT * FROM :tableName ORDER BY :tableKey DESC';
    const QUERY_SIMPLE_KEY_ASC = 'SELECT * FROM :tableName WHERE :key = :value ORDER BY :tableKey ASC';
    const QUERY_SIMPLE_KEY_DESC = 'SELECT * FROM :tableName WHERE :key = :value ORDER BY :tableKey DESC';
    
    /**
     * Open & store the database connection
     */
    public function __construct() {
        $this->db = new SQLite3(app::getPath() . '/application/storage/dvbb.db');
    }
    
    /**
     * Assemble and return an escaped query string
     * 
     * @param string $query
     * @param array $params
     * @return string
     */
    protected function assemble($query, $params = array()) {
        foreach ($params as $target => $value) {
            $query = str_replace($target, SQLite3::escapeString($value), $query);
        } 
        return $query;
    }
    
    /**
     * Returns a row of current type based on the given id
     * 
     * @param int $id
     * @return object
     */
    public function getById($id) {
        $query = $this->assemble(self::QUERY_SIMPLE, array(
            ':tableName' => $this->tableName,
            ':key' => $this->tableKey,
            ':value' => $id,
        ));
        $dbResult = $this->db->query($query);
        return $this->generateFromRow($dbResult->fetchArray(SQLITE3_ASSOC));
    }
    
    /**
     * Returns all rows of current type
     * 
     * @return array of objects
     */
    public function getAll() {
        $query = $this->assemble(self::QUERY_ALL_ASC, array(
            ':tableName' => $this->tableName,
            ':tableKey' => $this->tableKey,
        ));
        $dbResult = $this->db->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    /**
     * Returns all rows of current type where key matches value
     * 
     * @param string $key
     * @param mixed $value
     * @return array of objects
     */
    public function getByField($key, $value) {
        $query = $this->assemble(self::QUERY_SIMPLE_KEY_ASC, array(
            ':tableName' => $this->tableName,
            ':key' => $key,
            ':value' => $value,
            ':tableKey' => $this->tableKey,
        ));
        $dbResult = $this->db->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    /**
     * Returns object of current type populated with values from $row
     * 
     * @param array $row
     * @return object
     */
    protected function generateFromRow($row) {
        $class = get_called_class();
        $item = new $class();
        foreach ($row as $key => $value) {
            $item->$key = $value;
        }
        return $item;
    }
    
}