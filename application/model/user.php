<?php
class user extends dbModel {

    public $tableName = 'user';
    public $tableKey = 'id';
    
    public $id;
    public $username;
    public $password;
    public $email;
    public $created_at;
    public $updated_at;

}