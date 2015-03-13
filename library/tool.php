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
     * Outputs print_r wrapped in pre tags with 'debug' calss, then dies.
     * Takes any number of arguments, all wrapped in one large pre tag.
     * Shows NULL and empty string and boolean values as such (instead of empty values like print_r does).
     */
    public static function dp() {
        print '<pre class="debug">';
        if (func_num_args() > 0) {
            foreach (func_get_args() as $arg) {
                print '<pre class="debug">';
                if (is_object($arg) || is_array($arg)) {
                    print_r($arg);
                } elseif ($arg === null) {
                    print 'NULL';
                } elseif ($arg === '') {
                    print '(empty string)';
                } elseif (is_bool ($arg)) {
                    print ($arg ? 'TRUE' : 'FALSE' ) . ' (boolean)';
                } else {
                    print $arg . ' (' . gettype($arg) . ')';
                }
                print '</pre>';
            }
        }
        print '</pre>';
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

        $timezone = store::getConfigValue('timezone');
        if (auth::getUser()) {
            $setting = (new setting())->getByConditions(array(
                'user_id = ?' => auth::getUser()->id,
                'key = ?' => 'timezone',
            ));
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
            '~\[quote=(.*?)\](((?R)|.*?)+)\[/quote\]~s',
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

            '<div class="dvbb-quote"><span class="dvbb-quote-username">quote</span>$1</div>',
            '<div class="dvbb-quote"><span class="dvbb-quote-username">quoting $1</span>$2</div>',
            '<pre class="dvbb-code">$1</pre>',
            '<span style="color:$1;">$2</span>',

            '<a href="$1">$1</a>',
            '<a href="$1">$2</a>',
            '<img src="$1" alt="" />',
           '<iframe width="420" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>'
        );

        // Replacing the BBcodes with corresponding HTML tags
        do {
            $text = preg_replace($find, $replace, $text , -1 , $c);
        } while($c > 0);
        return $text;

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

    /**
     * Loads and returns partial phtml if it exists. Looks only in /application/view/ and path given should be from
     * there. So admin/partial/forums.phtml, for example.
     *
     * @param unknown $path
     * @return Ambigous <NULL, string>
     */
    public static function partial($path) {
        $return = null;

        $path = store::getPath() . '/application/view/' . $path;
        if (file_exists($path)) {
            ob_start();
            require($path);
            $return = ob_get_clean();
        }

        return $return;
    }

    public static function getPostsPerPage() {
        $posts_per_page = store::getConfigValue('posts_per_page');
        if (auth::getUser()) {
            $posts_per_page = auth::getUser()->getDefinitiveSetting('posts_per_page');
        }
        return $posts_per_page;
    }

}