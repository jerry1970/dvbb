<?php
/**
 * avatar model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class avatar extends model {

    protected $tableName = 'avatar';
    
    public $id;
    public $user_id;
    public $data;
    public $type;
    
}