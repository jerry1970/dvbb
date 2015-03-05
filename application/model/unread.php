<?php
/**
 * unread model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class unread extends model {

    protected $tableName = 'unread';
    
    public $id;
    public $user_id;
    public $forum_id;
    public $post_id;
    public $created_at;

    public function updateOrInsert() {
        
        $fields = array(
            'user_id = ?' => $this->user_id,
            'forum_id = ?' => $this->forum_id,
        );
        if ($this->post_id) {
            $fields['post_id = ?'] = $this->post_id;
        } else {
            $fields['post_id IS NULL'] = null;
        }
        
        $existing = $this->getByConditions($fields);

        if (count($existing) > 0) {
            $existing = $existing[0];
            $this->id = $existing->id;
        }
        $this->save();
        
    }
        
}