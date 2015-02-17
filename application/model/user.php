<?php
/**
 * user model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class user extends model {

    protected $tableName = 'user';
    
    public $id;
    public $username;
    public $email;
    public $validated_at;
    public $created_at;
    public $updated_at;

}