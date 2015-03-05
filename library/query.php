<?php
/**
 * query class
 * 
 * This class provides query building functionality
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class query {

    protected $tableName;
    protected $tableKey;
    protected $select = '*';
    protected $action = 'select';
    protected $where = array();
    protected $values = array();
    protected $orderBy = array();
    protected $groupBy = array();
    protected $limit;

    /**
     * On construct, if we're given an object, use this object to set the tableName & key
     * 
     * @param stdClass $object
     * @return query
     */
    public function __construct($object = null) {
        if ($object !== null && is_object($object)) {
            $this->setTableName($object->getTableName());
            $this->setTableKey($object->getTableKey());
        }
        return $this;
    }

    /**
     * Set the tableName to work on
     * 
     * @param string $tableName
     * @return query
     */
    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Get the currently set tableName
     * 
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    /**
     * Set the tableKey to work with (for delete & update
     * )
     * @param string $key
     * @return query
     */
    public function setTableKey($key) {
        $this->tableKey = $key;
        return $this;
    }
    
    /**
     * Set the type of query we're going to do
     * 
     * @param string $action
     */
    public function setAction($action) {
        if (in_array($action, array('select', 'insert', 'delete', 'update'))) {
            $this->action = $action;
        }
        return $this;
    }
    
    /**
     * In case of a select, what we're going to select (default *)
     * 
     * @param string $select
     * @return query
     */
    public function select($select) {
        $this->select = $select;
        return $this;
    }

    /**
     * Adds a where condition for relevant queries
     * 
     * @param string $condition
     * @param mixed $value
     * @return query
     */
    public function where($condition, $value = null) {
        $this->where[] = array('condition' => $condition, 'value' => $value);
        return $this;
    }
    
    /**
     * Adds a value to update/insert queries
     * 
     * @param string $key
     * @param mixed $value
     * @return query
     */
    public function addValue($key, $value) {
        $this->values[] = array('key' => $key, 'value' => $value);
        return $this;
    }

    /**
     * Sets the order for select queries
     * 
     * @param string $key
     * @param string $direction (default DESC)
     * @return query
     */
    public function orderBy($key, $direction = 'DESC') {
        $this->orderBy[] = array('key' => $key, 'direction' => $direction);
        return $this;
    }

    /**
     * Sets the group by for select queries
     * 
     * @param string $key
     * @return query
     */
    public function groupBy($key) {
        $this->groupBy[] = $key;
        return $this;
    }

    /**
     * Sets the limit
     * 
     * @param int $limit
     * @param int $offset
     * @return query
     */
    public function limit($limit, $offset = null) {
        $this->limit = array('limit' => $limit, 'offset' => $offset);
        return $this;
    }

    /**
     * Outputs the actual query for use, empty string if invalid/incomplete values given
     * 
     * @return string
     */
    public function __toString() {
        $query = array();

        if ($this->action === 'select') {
            
            // set select & what needs to be selected
            $query[] = "SELECT " . $this->select;
            // table
            $query[] = "FROM " . $this->tableName;
             
            // now get the where clauses
            if (count($this->where) > 0) {
                $wheres = array();
                foreach ($this->where as $where) {
                    if ($where['value'] !== null) {
                        $wheres[] = str_replace('?', "'" . SQLite3::escapeString($where['value']) . "'", $where['condition']);
                    } else {
                        $wheres[] = $where['condition'];
                    }
                }
                $query[] = "WHERE " . implode(' AND ', $wheres);
            }
             
            // now get the order(s)
            if (count($this->orderBy) > 0) {
                $orders = array();
                foreach ($this->orderBy as $orderBy) {
                    $orders[] = $orderBy['key'] . ' ' . $orderBy['direction'];
                }
                $query[] = "ORDER BY " . implode(', ', $orders);
            }
             
            // now get the group(s)
            if (count($this->groupBy) > 0) {
                $groups = array();
                foreach ($this->groupBy as $groupBy) {
                    $groups[] = $groupBy;
                }
                $query[] = "GROUP BY " . implode(', ', $groups);
            }
                
            // and the limit
            if (is_array($this->limit)) {
                if ($this->limit['offset'] !== null && $this->limit['limit'] !== null) {
                    $query[] = "LIMIT " . $this->limit['offset'] . ", " . $this->limit['limit'];
                } elseif ($this->limit['limit'] !== null) {
                    $query[] = "LIMIT " . $this->limit['limit'];
                }
            }
            
        } elseif ($this->action === 'delete') {
            
            // set delete to the proper table
            $query[] = "DELETE FROM " . $this->tableName;

            // now get the where clauses
            if (count($this->where) > 0) {
                $wheres = array();
                foreach ($this->where as $where) {
                    if ($where['value'] !== null) {
                        $wheres[] = str_replace('?', "'" . SQLite3::escapeString($where['value']) . "'", $where['condition']);
                    } else {
                        $wheres[] = $where['condition'];
                    }
                }
                $query[] = "WHERE " . implode(' AND ', $wheres);
            } else {
                $query = [];
            }
            
        } elseif ($this->action === 'update') {
            
            // set update to the proper table
            $query[] = "UPDATE " . $this->tableName;

            // now get the values
            if (count($this->values) > 0) {
                $values = array();
                foreach ($this->values as $value) {
                    // skip id, since we'll use that as a where condition
                    if ($value['key'] !== $this->tableKey) {
                        $values[] = "'" . $value['key'] . "'='" . SQLite3::escapeString($value['value']) . "'";
                    } else {
                        $tableKey = $value['key'];
                        $tableKeyValue = $value['value'];
                    }
                }
                $query[] = "SET " . implode(',', $values);
                $query[] = "WHERE " . $tableKey . " = '" . SQLite3::escapeString($tableKeyValue) . "'";
            } else {
                $query = [];
            }
            
        } elseif ($this->action === 'insert') {

            // set insert to the proper table
            $query[] = "INSERT INTO " . $this->tableName;
            
            // now get the values
            if (count($this->values) > 0) {
                foreach ($this->values as $value) {
                    $keys[] = "'" . $value['key'] . "'";
                    $values[] = "'" . SQLite3::escapeString($value['value']) . "'";
                }
                
                $query[] = "(" . implode(',', $keys) . ")";
                $query[] = "VALUES";
                $query[] = "(" . implode(',', $values) . ")";
            } else {
                $query = [];
            }
            
        }
             
        // and now implode it into a nice string, if possible
        if (count($query) > 0) {
            return implode(' ', $query);
        }
        return '';

    }

}
