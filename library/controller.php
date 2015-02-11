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
    
    /**
     * Makes the given $params available to the view by storing them using the app class
     * 
     * @param array $params
     */
    public function __construct($params = array()) {
        app::addToViewParams($params);
    }
    
}