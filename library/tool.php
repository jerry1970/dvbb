<?php
/**
 * tool class
 * 
 * This class provides tools such as redirect
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class tool {
    
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
     * Returns a string with every paragraph wrapped with a <p /> tag
     * 
     * @param string $string
     * @return string
     */
    public static function nl2p($string) {
        $return = '';
        foreach (explode("\n", trim($string)) as $part) {
            $return .= '<p>' . $part . '</p>';
        }
        return $return;
    }
    
    public static function redirect($url) {
        header('Location: ' . $url);
        die();
    }
    
    public static function redirectToRoute($name, $params = array()) {
        self::redirect(store::getRouter()->generate($name, $params));
    }
    
}