<?php
/**
 * password model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class password extends model {

    protected $tableName = 'password';
    
    public $id;
    public $user_id;
    public $password;

    /**
     * Checks if $password & $passwordConfirm are the same and adhere to the rules for passwords
     * 
     * Returns error array or null
     * 
     * @param string $password
     * @param string $passwordConfirm
     * @return null|array
     */
    public function getNewPasswordErrors($password, $passwordConfirm) {
        $errors = array();
        
        if (!empty($password) && !empty($passwordConfirm)) {
            // check if they're the same & valid
            if ($password !== $passwordConfirm) {
                $errors[] = 'Password & confirm password need to be exactly the same.';
            } else {
                // now check if the password is correct. This check isn't that strict, because
                // - minimum of 8 characters
                // - minimum of 1 digit (to prevent 'password')
                // - minimum of 1 letter (to prevent '123456')
                if (!preg_match('/^(?=^.{8,}$)(?=.*\d)(?![.\n])(?=.*[a-zA-Z]).*$/', $password)) {
                    $errors[] = 'Password must contain at least one numeric character and be a minimum of 8 characters long.';
                }
            }
        } else {
            $errors[] = 'Password & confirm password are required.';
        }
        
        if (count($errors) === 0) {
            return null;
        }
        return $errors;
        
    }
    
}