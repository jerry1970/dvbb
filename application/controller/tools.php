<?php
class tools {

    static $path;
    static $basePath;
    static $url;
    static $router;
    static $viewParams = array();
    
    public static function initialize() {
        // set current working directory as path
        tools::setPath(getcwd());
        // get the basepath if there is any
        tools::setBasePath(str_replace($_SERVER['DOCUMENT_ROOT'], '', tools::getPath()));
        // now get the complete public url & store it
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        tools::setUrl($url);
    }
    
    public static function setPath($path) {
        self::$path = $path;
    }
    
    public static function getPath() {
        return self::$path;
    }
    
    public static function setBasePath($basePath) {
        self::$basePath = $basePath;
    }
    
    public static function getBasePath() {
        return self::$basePath;
    }
    
    public static function setUrl($url) {
        self::$url = $url;
    }
    
    public static function getUrl() {
        return self::$url;
    }
    
    public static function setRouter($router) {
        self::$router = $router;
    }
    
    public static function getRouter() {
        return self::$router;
    }
    
    public static function addToViewParams($array) {
        foreach($array as $key => $value) {
            self::$viewParams[$key] = $value;
        }
        return self::$viewParams;
    }
    
    public static function removeFromViewParams($key) {
        unset(self::$viewParams[$key]);
        return self::$viewParams;
    }
    
    public static function resetViewParams() {
        self::$viewParams = array();
        return self::$viewParams;
    }
    
    public static function getViewParams() {
        return self::$viewParams;
    }
    
    public static function dp($string) {
        echo '<pre>';
        print_r($string);
        echo '</pre>';
        die();
    }
}