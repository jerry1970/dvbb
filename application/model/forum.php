<?php
/**
 * forum model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class forum extends model {

    protected $tableName = 'forum';
    
    public $id;
    public $parent_id;
    public $category_id;
    public $title;
    public $description;

    public function getUnreadStatus() {
        
        /*
         * logic: get last post date of all underlying topics
         *        compare with unread entries and it is unread if they are null or of a later date
         *        
         * loop through topics in this forum
         *     unreadstatus = getunreadstatus
         *     if unreadstatus->date < topic->last_post_at or unreadstatus = null
         *         return unread
         *     else
         *         return read
         * end loop
         */
        
        if (auth::getUser()) {
            $topics = (new post())->getByField('forum_id', $this->id);
            foreach ($topics as $topic) {
                if ($topic->getUnreadStatus()) {
                    return true;
                }
            }
        }
        return false;
    }
    
}