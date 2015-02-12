<?php
/**
 * password model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class password extends model {

    public $tableName = 'password';
    
    public $id;
    public $user_id;
    public $password;

}