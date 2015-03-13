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
    public $updated_user_id;
    public $title;
    public $body;
    public $closed;
    public $sticky;
    public $created_at;
    public $updated_at;
    public $last_post_at;

    public function getUnreadStatus() {
        if (auth::getUser()) {
            $unreadStatus = (new unread())->getByConditions(array(
                'user_id = ?' => auth::getUser()->id,
                'post_id = ?' => $this->id,
            ));

            if (count($unreadStatus) == 0 || $this->last_post_at > $unreadStatus[0]->created_at) {
                return true;
            }
        }
        return null;
    }
    
    public function getReplyLast() {
        $query = (new query($this))
            ->where('parent_id = ?', $this->id)
            ->orderBy('created_at')
            ->limit(1);
        
        $lastReply = (new post())->getByQuery($query);
        
        if (count($lastReply)) {
            return $lastReply[0];
        }
        return null;
    }
    
    public function getLastPage() {
        $replies = (new post())->getByCondition('parent_id = ?', $this->id);
        $postCount = count($replies) + 1;
        return ceil($postCount / tool::getPostsPerPage()); 
    }
    
    /** newer, better functions **/
    public function getReplyFirstUnseen() {
        $unread = (new unread())->getByConditions(array(
            'user_id = ?' => auth::getUser()->id,
            'post_id = ?' => $this->id,
        ));
        
        if (count($unread) > 0) {
            $replies = (new post())->getByCondition('parent_id = ?', $this->id);
        
            $unreadDate = new DateTime($unread[0]->created_at);
        
            $replyFirstUnseen = null;
            foreach ($replies as $reply) {
                $replyDate = new DateTime($reply->created_at);
        
                if ($replyDate > $unreadDate) {
                    $replyFirstUnseen = $reply;
                    break;
                }
            }
        } else {
            $replyFirstUnseen = $this;
        }
        return $replyFirstUnseen;
    }

    public function getReplyLastSeen() {
        $lastReplySeen = null;
            
        $replyFirstUnseen = $this->getReplyFirstUnseen();
        if ($replyFirstUnseen) {
            $replies = (new post())->getByCondition('parent_id = ?', $this->id);
            
            foreach ($replies as $reply) {
                if ($reply->id == $replyFirstUnseen->id) {
                    break;
                }
                $lastReplySeen = $reply;
            }
        }
        return $lastReplySeen;
    }
    
    public function getReplyPage() {
        // get all replies in the same parent, but BEFORE this reply
        $replies = (new post())->getByConditions(array(
            'parent_id = ?' => $this->parent_id,
            'id < ?' => $this->id,
        ));
        $replyCount = count($replies);
        // now add 2 (+1 for topic post, +1 for this post itself)
        $replyCount = $replyCount + 2;
        
        return ceil($replyCount / tool::getPostsPerPage());
    }
    
}