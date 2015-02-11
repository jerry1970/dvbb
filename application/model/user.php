<?php
/**
 * user model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class user extends model {

    public $tableName = 'user';
    
    public $id;
    public $username;
    public $password;
    public $email;
    public $created_at;
    public $updated_at;

}