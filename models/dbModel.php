<?php
class dbModel {
    
    protected $db;
    
    public function __construct() {
        $this->db = new SQLite3('./storage/dvbb.db');
    }
    
    public function getById($id) {
        $dbResult = $this->db->query('SELECT * FROM ' .  $this->tableName . ' WHERE ' . $this->tableKey . ' = ' . (int)$id);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function getAll() {
        $dbResult = $this->db->query('SELECT * FROM ' .  $this->tableName . ' ORDER BY ' . $this->tableKey . ' ASC');
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function getByField($key, $value) {
        $dbResult = $this->db->query('SELECT * FROM ' .  $this->tableName . ' WHERE ' . $key . ' = ' . $value . ' ORDER BY ' . $this->tableKey . ' ASC');
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $row;
        }
        return $return;
    }
    
}