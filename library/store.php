<?php
/**
 * store class
 * 
 * This class stores and returns values that are application-wide
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class store {

    static $path;
    static $basePath;
    static $url;
    static $router;
    static $viewValues = array();
    static $postValues = array();
    static $configValues = array();
    static $db;
    static $executionTime = array();
    
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
     * Returns the public url used for links and front-end logic with the basepath removed
     * 
     * @param string
     */
    public static function getUrlWithoutBasePath() {
        $url = str_replace(self::getBasePath(), '', self::getUrl());
        return $url;
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
     * Adds an array of key/value pairs to the view values
     * 
     * @param array $array
     * @return array
     */
    public static function addViewValues($array = array()) {
        foreach($array as $key => $value) {
            self::$viewValues[$key] = $value;
        }
        return self::$viewValues;
    }
    
    /**
     * Adds a single key/value pair to the view values
     * 
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public static function addViewValue($key, $value) {
        self::$viewValues[$key] = $value;
        return self::$viewValues;
    }
    
    /**
     * Removes a key/value pair from the view values
     * 
     * @param string $key
     * @return array
     */
    public static function removeViewValue($key) {
        unset(self::$viewValues[$key]);
        return self::$viewValues;
    }
    
    /**
     * Returns all view values
     * 
     * @return multitype:array
     */
    public static function getViewValues() {
        return self::$viewValues;
    }
    
    /**
     * Returns a view value by key or false if the key doesn't exist
     * 
     * @param string $key
     * @return string|null
     */
    public static function getViewValue($key) {
        if (isset(self::$viewValues[$key])) {
            return self::$viewValues[$key];
        }
        return null;
    }

    /**
     * Returns the current url by appending the currently available GET path variable to the main url from getUrl()
     * 
     * @return string
     */
    public static function getCurrentUrl() {
        if (isset($_GET['path'])) {
            return self::$url . '/' . $_GET['path'];
        }
        return self::$url;
    }
    
    /**
     * Merge $config with existing config values, overwriting pre-existing values
     * 
     * @param array $array
     * @return array
     */
    public static function addConfigValues($array = array()) {
        foreach($array as $key => $value) {
            self::$configValues[$key] = $value;
        }
        return self::$configValues;
    }
    
    /**
     * Returns entire config array
     * 
     * @return array
     */
    public static function getConfigValues() {
        return self::$configValues;
    }
    
    /**
     * Returns specific config value by key
     * 
     * @param string $key
     * @return mixed
     */
    public static function getConfigValue($key) {
        if (isset(self::$configValues[$key])) {
            return self::$configValues[$key];
        }
        return null;
    }
    
    /**
     * Merge $post with existing post values, overwriting pre-existing values
     * 
     * @param array $array
     * @return array
     */
    public static function addPostValues($array = array()) {
        foreach($array as $key => $value) {
            self::$postValues[$key] = $value;
        }
        return self::$postValues;
    }
    
    /**
     * Returns entire post array
     * 
     * @return array
     */
    public static function getPostValues() {
        return self::$postValues;
    }
    
    /**
     * Returns specific post value by key
     * 
     * @param string $key
     * @return mixed
     */
    public static function getPostValue($key) {
        if (isset(self::$postValues[$key])) {
            return self::$postValues[$key];
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
    
    public static function startExecutionTimer() {
        self::$executionTime['start'] = microtime(true); 
    }
    
    public static function endExecutionTimer() {
        self::$executionTime['end'] = microtime(true);
        return number_format(self::$executionTime['end'] - self::$executionTime['start'], 4);
    }
    
}