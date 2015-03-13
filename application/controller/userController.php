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
        if (!auth::can('read', 'page', 'user-list')) {
            // not allowed
            tool::redirectToRoute('home');
        }
    }

    public function profile() {
        if (!auth::can('read', 'page', 'user-profile')) {
            // not allowed
            tool::redirectToRoute('home');
        }
    }
    
    public function passwordReset() {
        if (auth::getUser()) {
            // first remove all tokens currently stored with password_reset as context
            $query = new query(new token());
            $query->setAction('delete');
            $query->where('user_id = ?', auth::getUser()->id);
            $query->where('context = ?', 'password_reset');
            
            store::getDb()->query($query);
            
            // create token and save it
            $token = (new token())->createToken();
            $token->user_id = auth::getUser()->id;
            $token->context = 'password_reset';
            
            if ($token->save()) {
                // send validation e-mail here
                $mail = new PHPMailer;
                $mail->isHTML(true);
                $mail->From = 'nobody@dvbb';
                $mail->FromName = store::getConfigValue('name');
                $mail->addAddress(auth::getUser()->email, auth::getUser()->username);
                $mail->Subject = 'Password reset has been requested';
                
                // set body now
                $tokenUrl = store::getUrlWithoutBasePath() . store::getRouter()->generate('token-redeem', array('token' => $token->token));
                
                $mail->Body = 'Click or copy/paste the following link to set a new password:<br /><br />';
                $mail->Body .= '<a href="' . $tokenUrl . '">' . $tokenUrl . '</a><br /><br />';
                $mail->Body .= 'You are the only recipient of this mail, so if you didn\'t request a new password, you can simply ignore it.';
                
                if(!$mail->send()) {
                    // since we couldn't send the message, there's no reason to keep the token
                    $token->delete();
                    $errors[] = 'Message could not be sent. Password reset token has been removed - please try again.';
                } else {
                    $mail->ClearAddresses();
                    tool::redirectToRoute('user-settings');
                }
            }
            
        }
    }
    
    public function create() {
        // check to see if we've got posted values
        if (store::getPostValues()) {
            // deal with post here
            $values = store::getPostValues();
            
            // set validation values
            $errors = array();

            // check username values
            if (!empty($values['new_username'])) {
                // check validity first
                if (!preg_match('/^\w+$/', $values['new_username'])) {
                    $errors[] = 'Username can only contain alphanumeric characters and underscores.';
                } else {
                    // Only check the database if the username is valid
                    $user = (new user())->getByCondition('username = ?', $values['new_username']);
                    if (count($user) > 0) {
                        $errors[] = 'This username already exists in the database.';
                    }
                }
            } else {
                $errors[] = 'Username is required.';
            }

            // check the password values through the standardized password getNewPasswordErrors function
            $passwordErrors = (new password())->getNewPasswordErrors(
                store::getPostValue('new_password'), 
                store::getPostValue('new_password_confirm')
            );
            foreach ($passwordErrors as $passwordError) {
                $errors[] = $passwordError;
            }
            
            // check email values
            if (!empty($values['new_email']) && !empty($values['new_email_confirm'])) {
                // We're not going to strictly check the e-mail address, since e-mail addresses can come in too many
                // different forms and they're getting more elaborate every day
                // see: http://davidcel.is/blog/2012/09/06/stop-validating-email-addresses-with-regex/\
                
                // We're going to be sending a confirmation mail instead of strict checking here
                if (!strpos($values['new_email'], '@')) {
                    $errors[] = 'E-mail doesn\'t seem to be valid.';
                } else {
                    // Only check the database if the e-mail is valid
                    $user = (new user())->getByCondition('email = ?', $values['new_email']);
                    if (count($user) > 0) {
                        $errors[] = 'This e-mail address already exists in the database.';
                    }
                }
            } else {
                $errors[] = 'E-mail & confirm e-mail are required.';
            }
            
            if (count($errors) == 0) {
                $createdAt = (new DateTime)->format('Y-m-d H:i:s');
                
                // see if this user is the first user or not. If so, make them admin, if not, guest
                $users = (new user())->getAll();
                $userGroup = 2; // 2 = guest
                if (count($users) === 0) {
                    $userGroup = 1; // 1 = admin
                }
                
                // generate new user item model
                $user = (new user())->generateFromRow(array(
                    'username' => $values['new_username'],
                    'email' => $values['new_email'],
                    'group_id' => $userGroup,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ));
                if ($user->save()) {
                    // generate new password item model
                    $password = (new password())->generateFromRow(array(
                        'user_id' => $user->id,
                        'password' => password_hash($values['new_password'], PASSWORD_BCRYPT),
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
                        $mail->FromName = store::getConfigValue('name');
                        $mail->addAddress($user->email, $user->username);
                        $mail->Subject = 'Your account is almost ready - validate now';
                        
                        // set body now
                        $tokenUrl = store::getUrlWithoutBasePath() . store::getRouter()->generate('token-redeem', array('token' => $token->token));
                        
                        $mail->Body = 'Click or copy/paste the following link to validate your account:<br /><br />';
                        $mail->Body .= '<a href="' . $tokenUrl . '">' . $tokenUrl . '</a>';
                        
                        if(!$mail->send()) {
                            $errors[] = 'Message could not be sent. Your registration is incomplete.';
                        } else {
                            $mail->ClearAddresses();
                            tool::redirectToRoute('register-done');
                        }
                    }
                }
            }

            store::addViewValue('errors', $errors);
        }
        
    }
    
    public function createDone() {
    }
    
    public function settings() {
        if (auth::getUser()) {
            $user = auth::getUser();
            // set default return values
            $success = false;
            $errors = array();
            if (store::getPostValues()) {
                // get values
                $values = store::getPostValues();
                // remove the submit button
                unset($values['post']);
                
                // if e-mail is set, we need to update the user
                if (!empty($values['email'])) {
                    $user->email = $values['email'];
                    $user->save();
                    auth::setUser($user);
                } else {
                    $errors[] = 'E-mail address can\'t be empty.';
                }
                
                // if there's a file upload for the avatar, deal with it here
                if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['type'])) {
                    $upload = $_FILES['avatar'];
                    unset($_FILES['avatar']);
                    $realType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $upload['tmp_name']);
                    list($width, $height) = getimagesize($upload['tmp_name']);
                    
                    $allowedTypes = tool::getAllowedImageTypes();
                    
                    if ($width <= store::getConfigValue('avatar_width') && $height <= store::getConfigValue('avatar_height')) {
                        if ($upload['type'] === $realType && in_array($realType, $allowedTypes)) {
                            // remove existing avatars
                            $avatars = (new avatar())->getByCondition('user_id = ?', $user->id);
                            foreach ($avatars as $avatar) {
                                $avatar->delete();
                            }
                            
                            $data = base64_encode(file_get_contents($upload['tmp_name']));
                            
                            $avatar = (new avatar())->generateFromRow(array(
                                'user_id' => $user->id,
                                'data' => $data,
                                'type' => $realType,
                            ));
                            $avatar->save();
                        } else {
                            $errors[] = 'Files of type ' . $realType . ' are not allowed.';
                        }
                    } else {
                        $errors[] = 'Avatars can\'t be bigger than ' . store::getConfigValue('avatar_width') . 'x' . store::getConfigValue('avatar_height');
                    }
                }
    
                // since we're done checking email, remove it from the values
                unset($values['email']);
                
                // everything else goes into settings, but first we delete existing values
                $settings = $user->getSettings();
                if (count($settings) > 0) {
                    foreach ($settings as $setting) {
                        $setting->delete();
                    }
                }
                // now loop through the post settings
                foreach ($values as $key => $value) {
                    if ($value === '0' || !empty($value)) {
                        $setting = (new setting())->generateFromRow(array(
                            'user_id' => $user->id,
                            'key' => $key,
                            'value' => $value,
                        ));
                        $setting->save();
                    }
                }
                
                if (count($errors) == 0) {
                    // no errors
                    $success = true;
                }
            }
            store::addViewValues(array(
                'success' => $success,
                'errors' => $errors,
            ));
        } else {
            // not logged in so redirect to home
            tool::redirectToRoute('home');
        }
        
    }
}