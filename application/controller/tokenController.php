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
        $token = (new token())->getByField('token', app::getViewByKey('token'));
        if (count($token) > 0) {
            $token = $token[0];
            if ($token->context == 'register_validate') {
                // validate a user account
                $user = (new user())->getById($token->user_id);
                $user->validated_at = (new DateTime())->format('Y-m-d H:i:s');
                $user->save();
    
                $token->delete();
                
                app::addToView(array('user' => $user));
            }
        }
    }

}