<?php
/**
 * boot class
 * 
 * This class initializes the store with values relevant to base operation
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class boot {
    
    /**
     * Initializes some values necessary for the application to run
     */
    public static function initialize() {
        
        // require store class
        require('./library/store.php');
        
        // set current working directory as path
        store::setPath(getcwd());
        
        // get the basepath if there is any
        store::setBasePath(str_replace($_SERVER['DOCUMENT_ROOT'], '', store::getPath()));
        
        // now get the complete public url & store it
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        store::setUrl(str_replace('/public', '', $url));
        
        // initialize config from config/app.ini
        store::setConfigParams(parse_ini_file(store::getPath() . '/application/config/app.ini'));
        
        // open the database
        store::setDb(new SQLite3(store::getPath() . '/application/storage/' . store::getConfigParam('sqliteDb')));
        
        // loop through get and add the values to the view params
        $params = array();
        foreach ($_GET as $key => $value) {
            if ($key !== 'path') {
                $params[$key] = $value;
            }
        }
        store::addParams($params);
        // loop through the post and add the values to the post params
        $params = array();
        foreach ($_POST as $key => $value) {
            if ($key !== 'path') {
                $params[$key] = $value;
            }
        }
        store::addPostValues($params);
    }
    
}