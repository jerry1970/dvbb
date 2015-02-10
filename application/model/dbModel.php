<?php
class dbModel {
    
    protected $db;
    
    const QUERY_SIMPLE = 'SELECT * FROM :tableName WHERE :key = :value';
    const QUERY_ALL_ASC = 'SELECT * FROM :tableName ORDER BY :tableKey ASC';
    const QUERY_ALL_DESC = 'SELECT * FROM :tableName ORDER BY :tableKey DESC';
    const QUERY_SIMPLE_KEY_ASC = 'SELECT * FROM :tableName WHERE :key = :value ORDER BY :tableKey ASC';
    const QUERY_SIMPLE_KEY_DESC = 'SELECT * FROM :tableName WHERE :key = :value ORDER BY :tableKey DESC';
    
    public function __construct() {
        $this->db = new SQLite3(tools::getPath() . '/application/storage/dvbb.db');
    }
    
    protected function assemble($query, $params = array()) {
        foreach ($params as $target => $value) {
            $query = str_replace($target, SQLite3::escapeString($value), $query);
        } 
        return $query;
    }
    
    public function getById($id) {
        $query = $this->assemble(self::QUERY_SIMPLE, array(
            ':tableName' => $this->tableName,
            ':key' => $this->tableKey,
            ':value' => $id,
        ));
        $dbResult = $this->db->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function getAll() {
        $query = $this->assemble(self::QUERY_ALL_ASC, array(
            ':tableName' => $this->tableName,
            ':tableKey' => $this->tableKey,
        ));
        $dbResult = $this->db->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $row;
        }
        return $return;
    }
    
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
            $return[] = $row;
        }
        return $return;
    }
    
}