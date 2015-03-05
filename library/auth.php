<?php
/**
 * auth class
 * 
 * This class provides authentication functionality and rights management
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
        // set the user if it's valid
        if ($user instanceof user) {
            self::$user = $user;
            
            // load the rights, we only want to do this once for performance reasons
            self::$user->loadRights();
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
            self::setUser((new user())->getById($row['id']));
        }
        return self::getUser();
    }

    public static function can($right = null, $type = null, $id = null) {
        // check if a user is set, and if so, 
        if (!self::$user) {
            // create fake guest user to check on
            $guest = new user();
            $guest->group_id = 2;
            $return = $guest->can($right, $type, $id);
        } else {
            $return = self::$user->can($right, $type, $id);
        }
        
        return $return;
    }
    
}