<?php
/**
 * token model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class token extends model {

    protected $tableName = 'token';
    
    public $id;
    public $user_id;
    public $token;
    public $context;

    public function createToken() {
        $token = new $this;
        $token->token = uniqid();
        return $token;
    }
    
}