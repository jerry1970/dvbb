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
    static $params = array();
    static $post = array();
    static $config = array();
    static $db;
    
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
     * Adds an array of key/value pairs to the parameters
     * 
     * @param array $array
     * @return array
     */
    public static function addParams($array = array()) {
        foreach($array as $key => $value) {
            self::$params[$key] = $value;
        }
        return self::$params;
    }
    
    /**
     * Adds a single key/value pair to the parameters
     * 
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public static function addParam($key, $value) {
        self::$params[$key] = $value;
        return self::$params;
    }
    
    /**
     * Removes a key/value pair from the parameters
     * 
     * @param string $key
     * @return array
     */
    public static function removeParam($key) {
        unset(self::$params[$key]);
        return self::$params;
    }
    
    /**
     * Resets the view parameters and returns the empty view parameterss
     * 
     * @return array
     */
    public static function resetParams() {
        self::$params = array();
        return self::$params;
    }
    
    /**
     * Returns all view parameters
     * 
     * @return multitype:array
     */
    public static function getParams() {
        return self::$params;
    }
    
    /**
     * Returns a view parameters by key or false if the key doesn't exist
     * 
     * @param string $key
     * @return string|null
     */
    public static function getParam($key) {
        if (isset(self::$params[$key])) {
            return self::$params[$key];
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
     * Store config array and overwrite pre-existing config
     * 
     * @param array $array
     * @return mixed
     */
    public static function setConfigParams($array = array()) {
        self::$config = $array;
        return self::$config;
    }
    
    /**
     * Merge $config with existing config values, overwriting pre-existing values
     * 
     * @param array $array
     * @return array
     */
    public static function addConfigParams($array = array()) {
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
    public static function getConfigParams() {
        return self::$config;
    }
    
    /**
     * Returns specific config value by key
     * 
     * @param string $key
     * @return mixed
     */
    public static function getConfigParam($key) {
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        return false;
    }
    
    /**
     * Merge $post with existing post values, overwriting pre-existing values
     * 
     * @param array $array
     * @return array
     */
    public static function addPostValues($array = array()) {
        foreach($array as $key => $value) {
            self::$post[$key] = $value;
        }
        return self::$post;
    }
    
    /**
     * Returns entire post array
     * 
     * @return array
     */
    public static function getPostValues() {
        return self::$post;
    }
    
    /**
     * Returns specific post value by key
     * 
     * @param string $key
     * @return mixed
     */
    public static function getPostValue($key) {
        if (isset(self::$post[$key])) {
            return self::$post[$key];
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