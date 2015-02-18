<?php
/**
 * auth class
 * 
 * This class provides authentication functionality
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class auth {
   
    static $user = null;
    
    /**
     * Sets a user object as the authenticated user
     * 
     * @param user $user
     * @return user|null
     */
    public static function setUser($user) {
        if ($user instanceof user) {
            self::$user = $user;
        }
        return self::$user;
    }
    
    /**
     * Returns the user if set, otherwise null
     * 
     * @return user|null
     */
    public static function getUser() {
        return self::$user;
    }
    
    /**
     * Authenticate a username/password combination and set the user if it's valid.
     * 
     * @param string $username
     * @param string $password
     * @return user|null
     */
    public static function authenticate($username, $password) {
        $password = md5($password);
        $query = '  SELECT 
                        user.id 
                    FROM user 
                        INNER JOIN password 
                            ON user.id = password.user_id 
                    WHERE 
                        user.username = \'' . SQLite3::escapeString($username) . '\'
                        AND
                        password.password = \'' . $password . '\'';
        $dbResult = store::getDb()->query($query);
        if ($row = $dbResult->fetchArray(SQLITE3_ASSOC)) {
            self::$user = (new user())->getById($row['id']);
        }
        return self::$user;
    }
    
}