<?php
class post extends dbModel {

    public $tableName = 'post';
    public $tableKey = 'id';
    
    public $id;
    public $forum_id;
    public $parent_id;
    public $user_id;
    public $title;
    public $body;
    public $created_at;
    public $updated_at;

}