<?php
/**
 * user model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class user extends model {

    protected $tableName = 'user';
    
    public $id;
    public $username;
    public $email;
    public $validated_at;
    public $created_at;
    public $updated_at;
    
    public function getSettings() {
        $return = array();
        
        // get settings, loop through and build one array
        $settings = (new setting())->getByField('user_id', $this->id);
        foreach ($settings as $setting) {
            $return[$setting->key] = $setting;
        }
        
        return $return;
    }
    
    public function getDefinitiveSetting($key) {
        // by default we're going to return the app config value
        $return = store::getConfigParam($key);
        
        // get settings and see if we have a user setting
        $settings = $this->getSettings();
        if (isset($settings[$key])) {
            $return = $settings[$key]->value;
        }
        return $return;
    }
    
    public function getAvatar() {
        $avatar = (new avatar())->getByField('user_id', $this->id);
        if (count($avatar) > 0) {
            $avatar = $avatar[0];
        }
        return $avatar;
    }

}