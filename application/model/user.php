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
    public $group_id;
    public $sub_group_id;
    public $username;
    public $email;
    public $validated_at;
    public $created_at;
    public $updated_at;
    
    protected $rights = null;
    
    public function getSettings() {
        $return = array();
        
        // get settings, loop through and build one array
        $settings = (new setting())->getByCondition('user_id = ?', $this->id);
        foreach ($settings as $setting) {
            $return[$setting->key] = $setting;
        }
        
        return $return;
    }
    
    public function getDefinitiveSetting($key) {
        // by default we're going to return the app config value
        $return = store::getConfigValue($key);
        
        // get settings and see if we have a user setting
        $settings = $this->getSettings();
        if (isset($settings[$key])) {
            $return = $settings[$key]->value;
        }
        return $return;
    }
    
    public function getAvatar() {
        $avatar = null;
        if ($this->id) {
            $avatar = (new avatar())->getByCondition('user_id = ?', $this->id);
            
            if (count($avatar) > 0) {
                $avatar = $avatar[0];
            }
        }
        return $avatar;
    }
    
    public function getTopicCount() {
        $topics = (new post())->getByConditions(array(
                'user_id = ?' => $this->id,
                'forum_id IS NULL' => null,
        ));
        return count($topics);
    }
    
    public function getReplyCount() {
        $replies = (new post())->getByConditions(array(
                'user_id = ?' => $this->id,
                'parent_id IS NOT NULL' => null,
        ));
        return count($replies);
    }
    
    public function getLastPost() {
        $query = (new query())
            ->select('created_at')
            ->orderBy('id', 'DESC')
            ->limit(1);
        return $this->getByQuery($query);
    }
    
    public function getDeleted() {
        $this->username = 'nobody';
        return $this;
    }
    
    public function getGroup() {
        return (new group())->getById($this->group_id);
    }
    
    public function getSubGroup() {
        if ($this->sub_group_id) {
            return (new group())->getById($this->sub_group_id);
        }
        return null;
    }

    /**
     * Loads the rights associated with the current user
     */
    public function loadRights() {
        $allRights = array();

        $group = $this->getGroup();
        $subgroup = $this->getSubGroup();
        
        $groupRights = $group->getRights();
        $subGroupRights = null;
        if ($subgroup) {
            $subGroupRights = $subgroup->getRights();
        }

        foreach ($groupRights as $right) {
            $rightsArray = $right->getRightsArray();
            if ($right->parent_type === 'default') {
                $allRights[$right->parent_type] = $rightsArray;
            } else {
                $allRights[$right->parent_type][$right->parent_id] = $rightsArray;
            }
        }
        if ($subGroupRights) {
            foreach ($subGroupRights as $right) {
                $rightsArray = $right->getRightsArray();
                if ($right->parent_type === 'default') {
                    if (!isset($allRights[$right->parent_type])) {
                        // not yet set, so write away!
                        $allRights[$right->parent_type] = $rightsArray;
                    } else {
                        // already set so COMPARE 
                        $setRightsArray = $allRights[$right->parent_type];
                        foreach ($setRightsArray as $mainRight => $mainValue) {
                            // check if main has force right, if so, nothing else matters
                            // if sub has no force right, main wins
                            if (
                                $mainValue === 2 
                                || $mainValue === 3
                                || !isset($rightsArray[$mainRight])
                                || $rightsArray[$mainRight] === 0 
                                || $rightsArray[$mainRight] === 1
                            ) {
                                // force rights in main or no force rights in sub, so WRITE
                                $allRights[$right->parent_type][$mainRight] = $mainValue;
                            } else {
                                // no force right in main and force right in sub, so sub wins here
                                $allRights[$right->parent_type][$mainRight] = $rightsArray[$mainRight];
                            }
                        }
                    }
                } else {
                    if (!isset($allRights[$right->parent_type][$right->parent_id])) {
                        // not yet set, so write away!
                        $allRights[$right->parent_type][$right->parent_id] = $rightsArray;
                    } else {
                        // already set so COMPARE 
                        $setRightsArray = $allRights[$right->parent_type][$right->parent_id];
                        foreach ($setRightsArray as $mainRight => $mainValue) {
                            // check if main has force right, if so, nothing else matters
                            // if sub is not set or has no force right, main wins
                            if ($mainValue === 2 || $mainValue === 3 || !isset($rightsArray[$mainRight])) {
                                // force rights in main or no force rights in sub, so WRITE
                                $allRights[$right->parent_type][$right->parent_id][$mainRight] = $mainValue;
                            } else {
                                // no force right in main and force right in sub, so sub wins here
                                $allRights[$right->parent_type][$right->parent_id][$mainRight] = $rightsArray[$mainRight];
                            }
                        }
                    }
                }
            }
        }

        $this->rights = $allRights;
    }
    
    /**
     * can returns a boolean based on whether or not user has rights to do a thing. If called on auth, it uses the user
     * set on auth to check, otherwise it's directly on the user object's current values
     *
     * possible rights: create/read/update/delete/mod/admin
     * possible types: category, forum, page
     * possible ids: int|string (page uses strings)
     *
     * use: auth::can('read', 'forum', 1);
     *      $user->can('read', 'page', 'user-list');
     *
     * @param string $right
     * @param string $type
     * @param mixed $id
     * @return boolean
     */
    public function can($right = null, $type = null, $id = null) {
        $return = false;
        
        /**
         * Check if rights have already been loaded
         */
        if (!$this->rights) {
            $this->loadRights();
        }
        
        /**
         * By this point it's a guarantee that rights have been loaded and we can start checking against the defaults
         * at the very least.
         *
         * However, if we're not given a valid right, always return false
         */
        if ($right !== null && in_array($right, array('create','read','update','delete','mod','admin'))) {
            // return value is set to the group default so we can at least return those
            $return = $this->rights['default'][$right];
            // specific rights (different from default either on group or user basis) might overrule the default
            if ($type !== null && $id !== null) {
                // we have a specific parent/id to look for
                if (isset($this->rights[$type]) && isset($this->rights[$type][$id])) {
                    $return = $this->rights[$type][$id][$right];
                }
            }
        }
        
        if (!$return || $return == 0 || $return == 2) {
            return false;
        }
        
        return true;
    }

}