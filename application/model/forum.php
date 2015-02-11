<?php
class forum extends dbModel {

    public $tableName = 'forum';
    public $tableKey = 'id';
    
    public $id;
    public $parent_id;
    public $category_id;
    public $title;
    public $description;

}