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
    
    /**
     * Returns a DateTime object with timezone adjustment
     * 
     * @param string|DateTime $date
     * @return DateTime
     */
    public static function getDateTime($date) {
        
        $timezone = store::getConfigParam('timezone');
        if (auth::getUser()) {
            $setting = (new setting())->getByQuery('SELECT * FROM setting WHERE user_id = ' . auth::getUser()->id . ' AND key = \'timezone\'');
            if (count($setting) > 0) {
                $timezone = $setting[0]->value;
            }
        }
        $timezone = new DateTimeZone($timezone);
        
        if (!$date instanceof DateTime) {
            $date = new DateTime($date);
        }

        $date->setTimezone($timezone);
        
        return $date;
    }
    
    public static function getDateFormatted($date) {
        return self::getDateTime($date)->format('d-m-Y H:i:s');
    }
    
    public static function getTimeZones() {
        $zones = DateTimeZone::listIdentifiers();
        $timezones = array();
        foreach ($zones as $zone) {
            $timezone = new DateTimeZone($zone);
            $gmt = ($timezone->getOffset(new DateTime()) / 3600);
        
            $hours = floor($gmt);
            $minutes = 60 * (abs($gmt) - abs($hours));
        
            $offset = $hours . ':' . ($minutes > 0 ? $minutes : '00');
        
            if ($offset >= 0) {
                $offset = '+' . $offset;
            }
        
            $timezones[$zone] = str_replace('/', ': ', $zone) . ' (UTC/GMT ' . $offset .')';
        }
        
        return $timezones;
    }
    
    public static function fromBB($text) {
        // based on code from http://digitcodes.com/create-simple-php-bbcode-parser-function/
        
        // BBcode array
        $find = array(
            '~\[b\](.*?)\[/b\]~s',
            '~\[i\](.*?)\[/i\]~s',
            '~\[u\](.*?)\[/u\]~s',
            '~\[s\](.*?)\[/s\]~s',
            
            '~\[left\](.*?)\[/left\]~s',
            '~\[right\](.*?)\[/right\]~s',
            '~\[center\](.*?)\[/center\]~s',
            '~\[justify\](.*?)\[/justify\]~s',
            
            '~\[quote\](.*?)\[/quote\]~s',
            '~\[code\](.*?)\[/code\]~s',
            '~\[color=(.*?)\](.*?)\[/color\]~s',
            
            '~\[url\](.*?)\[/url\]~s',
            '~\[url=(.*?)\](.*?)\[/url\]~s',
            '~\[img\](.*?)\[/img\]~s',
            '~\[video=youtube\](.*?)\[/video\]~s',
        );
        
        // HTML tags to replace BBcode
        $replace = array(
            '<b>$1</b>',
            '<i>$1</i>',
            '<span style="text-decoration:underline;">$1</span>',
            '<span style="text-decoration:line-through;">$1</span>',

            '<div style="text-align:left;">$1</div>',
            '<div style="text-align:right;">$1</div>',
            '<div style="text-align:center;">$1</div>',
            '<div style="text-align:justify;">$1</div>',
            
            '<div class="dvbb-quote">$1</div>',
            '<pre class="dvbb-code">$1</pre>',
            '<span style="color:$1;">$2</span>',
            
            '<a href="$1">$1</a>',
            '<a href="$1">$2</a>',
            '<img src="$1" alt="" />',
           '<iframe width="420" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>'
        );
        
        // Replacing the BBcodes with corresponding HTML tags
        return preg_replace($find, $replace, $text);
        
    }
    
    public static function getAllowedImageTypes() {
        return array(
            'image/gif',
            'image/jpeg',
            'image/png',
        );
    }
    
    public static function getBase64ImageSrc($image) {
        if ($image) {
            return 'data:' . $image->type . ';base64,' . $image->data;
        }
        return null;
    }
    
}