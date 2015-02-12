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
    
    protected $tableKey = 'id';
    
    const QUERY_SIMPLE = 'SELECT * FROM :tableName WHERE :key = :value';
    const QUERY_ALL_ASC = 'SELECT * FROM :tableName ORDER BY :tableKey ASC';
    const QUERY_ALL_DESC = 'SELECT * FROM :tableName ORDER BY :tableKey DESC';
    const QUERY_SIMPLE_KEY_ASC = 'SELECT * FROM :tableName WHERE :key = :value ORDER BY :tableKey ASC';
    const QUERY_SIMPLE_KEY_DESC = 'SELECT * FROM :tableName WHERE :key = :value ORDER BY :tableKey DESC';
    const QUERY_WHERE_KEY_ASC = 'SELECT * FROM :tableName WHERE :where ORDER BY :tableKey DESC';
    const QUERY_WHERE_KEY_DESC = 'SELECT * FROM :tableName WHERE :where ORDER BY :tableKey DESC';
    
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
        $dbResult = app::getDb()->query($query);
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
        $dbResult = app::getDb()->query($query);
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
        $dbResult = app::getDb()->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    /**
     * Returns all rows of current type where array of key:value all match
     * 
     * @param array $fields
     * @return array of objects
     */
    public function getByFields($fields = array()) {
        $where = array();
        foreach ($fields as $field) {
            $where[] = $field['key'] . ' ' . $field['match'] . ' ' . $field['value'];
        }
        $where = implode(' AND ', $where);
        
        $query = $this->assemble(self::QUERY_WHERE_KEY_ASC, array(
            ':tableName' => $this->tableName,
            ':tableKey' => $this->tableKey,
            ':where' => $where,
        ));
        $dbResult = app::getDb()->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    public function getByQuery($query) {
        $dbResult = app::getDb()->query($query);
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