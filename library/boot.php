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
        require('./library/database.php');
        
        // start execution timer
        store::startExecutionTimer();
        
        // force UTC as default timezone
        date_default_timezone_set("UTC");
        
        // set current working directory as path
        store::setPath(getcwd());
        
        // get the basepath if there is any
        store::setBasePath(str_replace($_SERVER['DOCUMENT_ROOT'], '', store::getPath()));
        
        // now get the complete public url & store it
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        store::setUrl(str_replace('/public', '', $url));

        // initialize config from config/config.ini
        if (file_exists(store::getPath() . '/application/config/config.ini')) {
            store::addConfigValues(parse_ini_file(store::getPath() . '/application/config/config.ini'));
        } else {
            // without config.ini, refuse to run
            die('NO CONFIG.INI FOUND');
        }
        // and if it exists, overwrite values with values from config/custom.ini
        if (file_exists(store::getPath() . '/application/config/custom.ini')) {
            store::addConfigValues(parse_ini_file(store::getPath() . '/application/config/custom.ini'));
        }
        
        // open the database
//         store::setDb(new SQLite3(store::getPath() . '/application/storage/' . store::getConfigValue('sqliteDb')));
        
        // start db (by simply generating a new instance) and store
        store::setDb(new database());
        
        // add the values from the $_GET to the view values
        store::addViewValues($_GET);
        
        // add the values from $_POST to the post values
        store::addPostValues($_POST);
    }
    
}