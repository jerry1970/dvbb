<?php
class tools {
    
    static $path;
    static $viewArray = array();
    
    public static function setPath($path) {
        self::$path = $path;
    }
    
    public static function getPath() {
        return self::$path;
    }
    
    public static function addToView($array) {
        foreach($array as $key => $value) {
            self::$viewArray[$key] = $value;
        }
        return self::$viewArray;
    }
    
    public static function removeFromView($key) {
        unset(self::$viewArray[$key]);
        return self::$viewArray;
    }
    
    public static function resetViewArray() {
        self::$viewArray = array();
        return self::$viewArray;
    }
    
    public static function getViewArray() {
        return self::$viewArray;
    }
    
    public static function dp($string) {
        echo '<pre>';
        print_r($string);
        echo '</pre>';
        die();
    }
}