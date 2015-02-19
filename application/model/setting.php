<?php
/**
 * setting model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class setting extends model {

    protected $tableName = 'setting';
    
    public $id;
    public $user_id;
    public $key;
    public $value;

}