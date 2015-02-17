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
            array(
                'key' => 'user_id',
                'match' => '=',
                'value' => $this->user_id,
            ),
            array(
                'key' => 'forum_id',
                'match' => '=',
                'value' => $this->forum_id,
            ),
        );
        if ($this->post_id) {
            $fields[] = array(
                'key' => 'post_id',
                'match' => '=',
                'value' => $this->post_id,
            );
        } else {
            $fields[] = array(
                'key' => 'post_id',
                'match' => 'IS',
                'value' => 'NULL',
            );
        }
        
        $existing = $this->getByFields($fields);

        if (count($existing) > 0) {
            $existing = $existing[0];
            $this->id = $existing->id;
        }
        $this->save();
        
    }
        
}