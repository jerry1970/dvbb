<?php
/**
 * category model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class category extends model {

    protected $tableName = 'category';
    
    public $id;
    public $title;
    public $sort;

    public function getAllSorted() {
        $query = (new query($this))->orderBy('sort', 'ASC');
        return $this->getByQuery($query);
    }
    
}