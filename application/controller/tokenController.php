<?php
/**
 * tokenController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class tokenController extends controller {

    public function redeem() {
        // get the token
        $token = (new token())->getByCondition('token = ?', store::getViewValue('token'));
        if (count($token) > 0) {
            $token = $token[0];
            
            if ($token->context == 'register_validate') {
                // call password reset function
                $this->registerValidate($token);
            } elseif ($token->context == 'password_reset') {
                // call password reset function
                $this->passwordReset($token);
            }

        }
    }
    
    public function registerValidate($token) {
        // validate a user account
        $user = (new user())->getById($token->user_id);
        $user->validated_at = (new DateTime())->format('Y-m-d H:i:s');
        $user->save();

        $token->delete();
        
        store::addViewValues(array(
            'user' => $user,
            'token' => $token,
        ));
    }
    
    public function passwordReset($token) {
        // show passwordReset page
        $user = (new user())->getById($token->user_id);
        
        if (store::getPostValues()) {
            // check password values
            $errors = (new password())->getNewPasswordErrors(
                store::getPostValue('new_password'), 
                store::getPostValue('new_password_confirm')
            );
            
            if (!$errors) {
                $password = new password();
                
                // no errors, so first remove existing password
                $query = new query($password);
                $query->setAction('delete');
                $query->where('user_id = ?', $user->id);
                store::getDb()->query($query);
                
                // now store the new password
                $password->user_id = $user->id;
                $password->password = password_hash(store::getPostValue('new_password'), PASSWORD_BCRYPT);
                $password->save();
                
                // remove the token
                $token->delete();
                
                // and log out the user
                session_destroy();
                
            }

            store::addViewValues(array(
                'errors' => $errors,
                'success' => true,
            ));
            
        }
        
        store::addViewValues(array(
            'user' => $user,
            'token' => $token,
        ));
    }

}