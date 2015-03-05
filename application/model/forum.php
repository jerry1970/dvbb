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
    public $sort;

    public function getUnreadStatus() {
        
        if (auth::getUser()) {
            $topics = (new post())->getByCondition('forum_id = ?', $this->id);
            foreach ($topics as $topic) {
                if ($topic->getUnreadStatus()) {
                    return true;
                }
            }
        }
        return false;
    }
    
}