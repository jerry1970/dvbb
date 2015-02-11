<?php
/**
 * forum model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class forum extends model {

    public $tableName = 'forum';
    
    public $id;
    public $parent_id;
    public $category_id;
    public $title;
    public $description;

}