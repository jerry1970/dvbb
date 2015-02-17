<?php
/**
 * controller class
 * 
 * This class provides some shared behaviour for controllers
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class controller {
    
    protected $params;
    
    /**
     * Makes the given $params available to the view by storing them using the app class
     * and to controllers by storing it locally as $this->params
     * 
     * Also stores $_GET values in the view
     * 
     * @param array $params
     */
    public function __construct($params = array()) {
        $this->params = $params;
        app::addToView($params);
    }
    
}