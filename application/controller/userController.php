<?php
/**
 * userController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class userController extends controller {

    public function userlist() {
    }

    public function profile() {
    }
    
    public function create() {
        // check to see if we've got posted values
        if (store::getPostValues()) {
            // deal with post here
            $values = store::getPostValues();
            
            // set validation values
            $error = array();

            // check username values
            if (!empty($values['new_username'])) {
                // check validity first
                if (!preg_match('/^\w+$/', $values['new_username'])) {
                    $error[] = 'Username can only contain alphanumeric characters and underscores.';
                } else {
                    // Only check the database if the username is valid
                    $user = (new user())->getByField('username', $values['new_username']);
                    if (count($user) > 0) {
                        $error[] = 'This username already exists in the database.';
                    }
                }
            } else {
                $error[] = 'Username is required.';
            }
            
            // check password values
            if (!empty($values['new_password']) && !empty($values['new_password_confirm'])) {
                // check if they're the same & valid
                if ($values['new_password'] !== $values['new_password_confirm']) {
                    $error[] = 'Password & confirm password need to be exactly the same.';
                } else {
                    // now check if the password is correct. This check isn't that strict, because 
                    // - minimum of 8 characters
                    // - minimum of 1 digit (to prevent 'password')
                    // - minimum of 1 letter (to prevent '123456')
                    if (!preg_match('/^(?=^.{8,}$)(?=.*\d)(?![.\n])(?=.*[a-zA-Z]).*$/', $values['new_password'])) {
                        $error[] = 'Password must contain at least one numeric character and be a minimum of 8 characters long.';
                    }
                }
            } else {
                $error[] = 'Password & confirm password are required.';
            }
            
            // check email values
            if (!empty($values['new_email']) && !empty($values['new_email_confirm'])) {
                // We're not going to strictly check the e-mail address, since e-mail addresses can come in too many
                // different forms and they're getting more elaborate every day
                // see: http://davidcel.is/blog/2012/09/06/stop-validating-email-addresses-with-regex/\
                
                // We're going to be sending a confirmation mail instead of strict checking here
                if (!strpos($values['new_email'], '@')) {
                    $error[] = 'E-mail doesn\'t seem to be valid.';
                } else {
                    // Only check the database if the e-mail is valid
                    $user = (new user())->getByField('email', $values['new_email']);
                    if (count($user) > 0) {
                        $error[] = 'This e-mail address already exists in the database.';
                    }
                }
            } else {
                $error[] = 'E-mail & confirm e-mail are required.';
            }
            
            if (count($error) == 0) {
                $createdAt = (new DateTime)->format('Y-m-d H:i:s');
                // generate new user item model
                $user = (new user())->generateFromRowSafe(array(
                    'username' => $values['new_username'],
                    'email' => $values['new_email'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ));
                if ($user->save()) {
                    // generate new password item model
                    $password = (new password())->generateFromRowSafe(array(
                        'user_id' => $user->id,
                        'password' => md5($values['new_password']),
                    ));
                    if ($password->save()) {
                        // create token and save it
                        $token = (new token())->createToken();
                        $token->user_id = $user->id;
                        $token->context = 'register_validate';
                        $token->save();
                        
                        // send validation e-mail here
                        $mail = new PHPMailer;
                        $mail->isHTML(true);
                        $mail->From = 'nobody@dvbb';
                        $mail->FromName = store::getConfigParam('name');
                        $mail->addAddress($user->email, $user->username);
                        $mail->Subject = 'Your account is almost ready - validate now';
                        
                        // set body now
                        $tokenUrl = store::getUrlWithoutBasePath() . store::getRouter()->generate('token-redeem', array('token' => $token->token));
                        
                        $mail->Body = 'Click or copy/paste the following link to validate your account:<br /><br />';
                        $mail->Body .= '<a href="' . $tokenUrl . '">' . $tokenUrl . '</a>';
                        
                        if(!$mail->send()) {
                            $error[] = 'Message could not be sent. Your registration is incomplete.';
                        } else {
                            $mail->ClearAddresses();
                            tool::redirectToRoute('register-done');
                        }
                    }
                }
            }

            store::addParam('error', $error);
        }
        
    }
    
    public function createDone() {
    }
}