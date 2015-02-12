<?php
/**
 * app class
 * 
 * This class provides the most central functionality for the application. This includes storing the main path, the 
 * base path if we're running from a subdirectory (required for AltoRouter), the url and the router.
 * 
 * This is also the view parameter 'registry', of sorts, so we can store these centrally from controllers and
 * reference them easily from the views. Debug functions are also found here (see dp).
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class app {

    static $path;
    static $basePath;
    static $url;
    static $router;
    static $viewParams = array();
    static $db;
    static $config = array();
    
    /**
     * Initializes some values necessary for the application to run
     */
    public static function initialize() {
        // set current working directory as path
        self::setPath(str_replace('/public/', '/', getcwd()));
        // get the basepath if there is any
        self::setBasePath(str_replace($_SERVER['DOCUMENT_ROOT'], '', self::getPath()));
        // now get the complete public url & store it
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        self::setUrl(str_replace('/public', '', $url));
        // initialize config from config/app.ini
        self::setConfig(parse_ini_file(self::getPath() . '/application/config/app.ini'));
        // open the database
        self::setDb(new SQLite3(app::getPath() . '/application/storage/' . app::getConfigKey('sqliteDb')));
    }
    
    /**
     * Sets the local path
     * 
     * @param string $path
     * @return string
     */
    public static function setPath($path) {
        self::$path = $path;
        return self::$path;
    }

    /**
     * Returns the local path
     * 
     * @return string
     */
    public static function getPath() {
        return self::$path;
    }
    
    /**
     * Sets the base path the application is in (in addition to the local path, this is usually a directory)
     * 
     * @param string $basePath
     * @return string
     */
    public static function setBasePath($basePath) {
        self::$basePath = $basePath;
        return self::$basePath;
    }

    /**
     * Returns the base path the application is in (in addition to the local path, this is usually a directory)
     * 
     * @return string|null
     */
    public static function getBasePath() {
        if (!empty(self::$basePath)) {
            return self::$basePath;
        }
        return null;
    }

    /**
     * Sets the public url used for links and front-end logic
     * 
     * @param string $url
     * @return string
     */
    public static function setUrl($url) {
        self::$url = $url;
        return self::$url;
    }

    /**
     * Returns the public url used for links and front-end logic
     * 
     * @param string
     */
    public static function getUrl() {
        return self::$url;
    }
    
    /**
     * Sets the AltoRouter router instance
     * 
     * @param AltoRouter $router
     * @return AltoRouter
     */
    public static function setRouter($router) {
        self::$router = $router;
        return self::$router;
    }
    
    /**
     * Returns the AltoRouter router instance
     * 
     * @return AltoRouter
     */
    public static function getRouter() {
        return self::$router;
    }
    
    /**
     * Adds an array of key/value pairs to the view parameters
     * 
     * @param array $array
     * @return array
     */
    public static function addToViewParams($array = array()) {
        foreach($array as $key => $value) {
            self::$viewParams[$key] = $value;
        }
        return self::$viewParams;
    }
    
    /**
     * Removes a key/value pair from the view parameters
     * 
     * @param string $key
     * @return array
     */
    public static function removeFromViewParams($key) {
        unset(self::$viewParams[$key]);
        return self::$viewParams;
    }
    
    /**
     * Resets the view parameters and returns the empty viewParams
     * 
     * @return array
     */
    public static function resetViewParams() {
        self::$viewParams = array();
        return self::$viewParams;
    }
    
    /**
     * Returns all view parameters
     * 
     * @return multitype:array
     */
    public static function getViewParams() {
        return self::$viewParams;
    }
    
    /**
     * Returns a view parameters by key or false if the key doesn't exist
     * 
     * @param string $key
     * @return string|false
     */
    public static function getViewParam($key) {
        if (isset(self::$viewParams[$key])) {
            return self::$viewParams[$key];
        }
        return false;
    }

    /**
     * Returns the current url by appending the currently available GET path variable to the main url from getUrl()
     * 
     * @return string
     */
    public static function getCurrentUrl() {
        return self::$url . '/' . $_GET['path'];
    }
    
    /**
     * Outputs print_r wrapped in pre tags, then dies 
     * 
     * @param string $string
     */
    public static function dp($string) {
        echo '<pre>';
        print_r($string);
        echo '</pre>';
        die();
    }

    /**
     * Store config array and overwrite pre-existing config
     * 
     * @param array $array
     * @return mixed
     */
    public static function setConfig($array = array()) {
        self::$config = $array;
        return self::$config;
    }
    
    /**
     * Merge $config with existing config values, overwriting pre-existing values
     * 
     * @param array $array
     * @return array
     */
    public static function addToConfig($array = array()) {
        foreach($array as $key => $value) {
            self::$config[$key] = $value;
        }
        return self::$config;
    }
    
    /**
     * Returns entire config array
     * 
     * @return array
     */
    public static function getConfig() {
        return self::$config;
    }
    
    /**
     * Returns specific config value by key
     * 
     * @param string $key
     * @return mixed
     */
    public static function getConfigKey($key) {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        return false;
    }
    
    /**
     * Store the database object
     * 
     * @param SQLite3 $db
     */
    public static function setDb($db) {
        self::$db = $db;
    }
    
    /**
     * Returns the database object
     * 
     * @return SQLite3
     */
    public static function getDb() {
        return self::$db;
    }
}