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
    
    /**
     * Returns a row of current type based on the given id
     * 
     * @param int $id
     * @return object
     */
    public function getById($id) {
        $query = (new query($this))->where($this->tableKey . ' = ?', $id);
        $dbResult = store::getDb()->query($query);
        return $this->generateFromRow($dbResult->fetchArray(SQLITE3_ASSOC));
    }
    
    /**
     * Returns all rows of current type
     * 
     * @return array of objects
     */
    public function getAll() {
        $query = new query($this);
        $dbResult = store::getDb()->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    /**
     * Returns all rows of current type where condition matches value
     * 
     * @param string $key
     * @param mixed $value
     * @return array of objects
     */
    public function getByCondition($condition, $value) {
        $query = new query($this);
        $query->where($condition, $value);
        $dbResult = store::getDb()->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    /**
     * Returns all rows of current type where array of key:match:value all match
     * 
     * @param array $fields
     * @return array of objects
     */
    public function getByConditions($fields = array()) {
        $query = new query($this);
        foreach ($fields as $condition => $value) {
            $query->where($condition, $value);
        }

        $dbResult = store::getDb()->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    public function getByQuery($query) {
        // check if it's a query object and if it has a tablename set, if not, default to current model
        if ($query instanceof query && $query->getTableName() === null) {
            $query->setTableName($this->getTableName());
        }
        $dbResult = store::getDb()->query($query);
        $return = array();
        while ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            $return[] = $this->generateFromRow($row);
        }
        return $return;
    }
    
    public function toArray() {
        $array = (array)$this;
        // remove protected values & null values, let the database sort those out
        foreach ($array as $key => $value) {
            if(strpos($key, '*') || $value === null) {
                unset($array[$key]);
            }
        }
        return $array;
    }
    
    public function save() {
        $array = $this->toArray();

        if ($this->id) {
            $query = (new query($this));
            $query->setAction('update');
            $query->addValue($this->tableKey, $this->id);
            
            foreach ($array as $key => $value) {
                $query->addValue($key, $value);
            }
        } else {
            $query = (new query($this));
            $query->setAction('insert');
            
            foreach ($array as $key => $value) {
                $query->addValue($key, $value);
            }
        }
        try {
            store::getDb()->query($query);
            // add the inserted id to the model
            if (!$this->id) {
                $this->id = store::getDb()->lastInsertRowID();
            }
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    
    public function delete() {
        $query = (new query($this));
        $query->setAction('delete');
        $query->where($this->tableKey . ' = ?', $this->id);

        try {
            store::getDb()->query($query);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Returns object of current type populated with values from $row
     *
     * @param array $row
     * @return object
     */
    public function generateFromRow($row) {
        $class = get_called_class();
        $item = new $class();
        if ($row) {
            foreach ($row as $key => $value) {
                $item->$key = $value;
            }
        }
        return $item;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function getTableKey() {
        return $this->tableKey;
    }
    
}