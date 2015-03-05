<?php
/**
 * rights model class
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class rights extends model {

    protected $tableName = 'rights';
    
    public $id;
    public $parent_type;
    public $parent_id;
    public $child_type;
    public $child_id;

    public $create;
    public $read;
    public $update;
    public $delete;
    public $mod;
    public $admin;
    
    public function applyPost($post) {
        $this->create = (isset($post['create']) ? $post['create'] : 0);
        $this->read   = (isset($post['read'])   ? $post['read']   : 0);
        $this->update = (isset($post['update']) ? $post['update'] : 0);
        $this->delete = (isset($post['delete']) ? $post['delete'] : 0);
        $this->mod    = (isset($post['mod'])    ? $post['mod']    : 0);
        $this->admin  = (isset($post['admin'])  ? $post['admin']  : 0);
        
        return $this;
    }
    
    public function getRightsArray() {
        return array(
            'create' => $this->create,
            'read'   => $this->read,
            'update' => $this->update,
            'delete' => $this->delete,
            'mod'    => $this->mod,
            'admin'  => $this->admin,
        );
    }
}