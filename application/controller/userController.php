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
                    $user = (new user())->getByCondition('username = ?', $values['new_username']);
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
                    $user = (new user())->getByCondition('email = ?', $values['new_email']);
                    if (count($user) > 0) {
                        $error[] = 'This e-mail address already exists in the database.';
                    }
                }
            } else {
                $error[] = 'E-mail & confirm e-mail are required.';
            }
            
            if (count($error) == 0) {
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
                        $mail->FromName = store::getConfigValue('name');
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

            store::addViewValue('error', $error);
        }
        
    }
    
    public function createDone() {
    }
    
    public function settings() {
        if (auth::getUser()) {
            $user = auth::getUser();
            // set default return values
            $success = false;
            $error = array();
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
                    $error[] = 'E-mail address can\'t be empty.';
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
                            $error[] = 'Files of type ' . $realType . ' are not allowed.';
                        }
                    } else {
                        $error[] = 'Avatars can\'t be bigger than ' . store::getConfigValue('avatar_width') . 'x' . store::getConfigValue('avatar_height');
                    }
                }
    
                // since we're done checking email, remove it from the values
                unset($values['email']);
                
                // everything else goes into settings, but first we delete existing values
                $settings = $user->getSettings();
                foreach ($settings as $setting) {
                    $setting->delete();
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
                
                if (count($error) == 0) {
                    // no errors
                    $success = true;
                }
            }
            store::addViewValues(array(
                'success' => $success,
                'error' => $error,
            ));
        } else {
            // not logged in so redirect to home
            tool::redirectToRoute('home');
        }
        
    }
}