<?php
/**
 * ajaxController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class ajaxController extends controller {

    public function login() {
        $values = store::getPostValues();
        if (!empty($values['username']) && !empty($values['password'])) {
            if (auth::authenticate($values['username'], $values['password'])) {
                // check if user is validated yet
                if (auth::getUser()->validated_at === null) {
                    session_destroy();
                } else {
                    $_SESSION['id'] = auth::getUser()->id;
                }
            }
        }
        echo json_encode(auth::getUser());
    }
    
    public function logout() {
        session_destroy();
        echo json_encode(array('status' => 'ok'));
    }
    
}