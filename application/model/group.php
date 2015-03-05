<?php
/**
 * group model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class group extends model {

    protected $tableName = 'user_group';
    
    public $id;
    public $title;
    
    public function getDeleted() {
        $this->title = 'deleted';
        return $this;
    }
    
    public function getRights() {
        $query = (new query())
            ->setTableName('rights')
            ->where('child_type = ?', 'group')
            ->where('child_id = ?', $this->id);
        return (new rights())->getByQuery($query);
    }
    
}