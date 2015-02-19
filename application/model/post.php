<?php
/**
 * post model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class post extends model {

    protected $tableName = 'post';
    
    public $id;
    public $forum_id;
    public $parent_id;
    public $user_id;
    public $title;
    public $body;
    public $created_at;
    public $updated_at;
    public $last_post_at;

    public function getUnreadStatus() {
        if (auth::getUser()) {
            $query = 'SELECT * FROM unread WHERE user_id = \'' . auth::getUser()->id . '\' AND post_id = \'' . $this->id . '\'';
            $unreadStatus = (new unread())->getByQuery($query);

            if (count($unreadStatus) == 0 || $this->last_post_at > $unreadStatus[0]->created_at) {
                return true;
            }
        }
        return null;
    }
    
    public function getLastReply() {
        $query = 'SELECT * FROM post WHERE parent_id = \'' . $this->id . '\' ORDER BY created_at DESC LIMIT 1';
        $lastReply = (new post())->getByQuery($query);
        
        if (count($lastReply)) {
            return $lastReply[0];
        }
        return null;
    }
    
}