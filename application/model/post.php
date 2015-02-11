<?php
/**
 * post model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class post extends model {

    public $tableName = 'post';
    
    public $id;
    public $forum_id;
    public $parent_id;
    public $user_id;
    public $title;
    public $body;
    public $created_at;
    public $updated_at;

}