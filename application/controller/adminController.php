<?php
/**
 * adminController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class adminController extends controller
{

    public function __construct($params)
    {
        if (! auth::can('admin')) {
            // not allowed
            tool::redirectToRoute('home');
        }
        parent::__construct($params);
    }

    public function index()
    {
        store::addViewValue('active', 'admin');
    }

    /**
     * CATEGORY / FORUM FUNCTIONS
     */
    public function forums()
    {
        store::addViewValue('active', 'admin-forums');
        
        if (store::getPostValues()) {
            // loop through categories & update
            $categoryOrders = store::getPostValue('category');
            if ($categoryOrders) {
                foreach ($categoryOrders as $category_id => $order) {
                    $category = (new category())->getById($category_id);
                    $category->sort = (int) $order;
                    $category->save();
                }
            }
            // loop through forums & update
            $forumOrders = store::getPostValue('forum');
            if ($forumOrders) {
                foreach ($forumOrders as $forum_id => $order) {
                    $forum = (new forum())->getById($forum_id);
                    $forum->sort = (int) $order;
                    $forum->save();
                }
            }
            tool::redirectToRoute('admin-forums');
        }
    }

    public function forumUpdate()
    {
        store::addViewValue('active', 'admin-forums');
        
        $error = array();
        if (store::getViewValue('create')) {
            // create a new forum object
            $forum = new forum();
            $forum->category_id = store::getViewValue('id');
            $forum->sort = 0;
        } else {
            // get the forum by id
            $forum = (new forum())->getById(store::getViewValue('id'));
            // set view values to get specific rights (if any)
            store::addViewValues(array(
                'parent_type' => 'forum',
                'parent_id' => $forum->id
            ));
        }
        
        if (store::getPostValues()) {
            $title = store::getPostValue('title');
            $description = store::getPostValue('description');
            
            if (empty($title)) {
                $error[] = 'Title can\'t be empty.';
            }
            if (empty($description)) {
                $error[] = 'Description can\'t be empty.';
            }
            if (count($error) === 0) {
                $forum->title = $title;
                $forum->description = $description;
                $forum->save();
                store::addViewValue('success', true);
                
                // now save the rights, if any
                if (store::getPostValue('rights')) {
                    $postRights = store::getPostValue('rights');

                    foreach ($postRights as $group_id => $rights) {
                        // get default values for the usergroup
                        $defaultRights = (new rights())->getByConditions(array(
                            'child_type = ?' => 'group',
                            'child_id = ?' => $group_id,
                            'parent_type = ?' => 'default'
                        ));
                        $currentRights = $defaultRights[0];
                        
                        $specificRights = (new rights())->getByConditions(array(
                            'child_type = ?' => 'group',
                            'child_id = ?' => $group_id,
                            'parent_type = ?' => 'forum',
                            'parent_id = ?' => $forum->id
                        ));
                        if (count($specificRights) > 0) {
                            $currentRights = $specificRights[0];
                        }
                        
                        // clone the default rights from above & apply post
                        $newRights = clone $currentRights;
                        $newRights->applyPost($rights);
                        
                        // if they're not equal, we need to save them, otherwise do nothing and let default rights rule
                        if ($currentRights != $newRights) {
                            // only reset id & set relevant parent data if not already set on specific rights
                            if (count($specificRights) === 0) {
                                $newRights->id = null;
                                $newRights->parent_type = 'forum';
                                $newRights->parent_id = $forum->id;
                            }
                            
                            $newRights->save();
                        }
                    }
                }
            }
        }
        
        store::addViewValues(array(
            'error' => $error,
            'forum' => $forum
        ));
    }

    public function categoryUpdate()
    {
        store::addViewValue('active', 'admin-forums');
        
        $error = array();
        if (store::getViewValue('create')) {
            // create new category object
            $category = new category();
            $category->sort = 0;
        } else {
            // get category by id
            $category = (new category())->getById(store::getViewValue('id'));
            // set view values to get specific rights (if any)
            store::addViewValues(array(
                'parent_type' => 'category',
                'parent_id' => $category->id
            ));
        }
        
        if (store::getPostValues()) {
            $title = store::getPostValue('title');
            
            if (empty($title)) {
                $error[] = 'Title can\'t be empty.';
            } else {
                $category->title = $title;
                $category->save();
                store::addViewValue('success', true);
                
                // now save the rights, if any
                if (store::getPostValue('rights')) {
                    $postRights = store::getPostValue('rights');
                    
                    foreach ($postRights as $group_id => $rights) {
                        // get default values for the usergroup
                        $defaultRights = (new rights())->getByConditions(array(
                            'child_type = ?' => 'group',
                            'child_id = ?' => $group_id,
                            'parent_type = ?' => 'default'
                        ));
                        $currentRights = $defaultRights[0];
                        
                        $specificRights = (new rights())->getByConditions(array(
                            'child_type = ?' => 'group',
                            'child_id = ?' => $group_id,
                            'parent_type = ?' => 'category',
                            'parent_id = ?' => $category->id
                        ));
                        if (count($specificRights) > 0) {
                            $currentRights = $specificRights[0];
                        }
                        
                        // clone the default rights from above & apply post
                        $newRights = clone $currentRights;
                        $newRights->applyPost($rights);
                        
                        // if they're not equal, we need to save them, otherwise do nothing and let default rights rule
                        if ($currentRights != $newRights) {
                            // only reset id & set relevant parent data if not already set on specific rights
                            if (count($specificRights) === 0) {
                                $newRights->id = null;
                                $newRights->parent_type = 'category';
                                $newRights->parent_id = $category->id;
                            }
                            
                            $newRights->save();
                        }
                    }
                }
            }
        }
        
        store::addViewValues(array(
            'error' => $error,
            'category' => $category
        ));
    }

    public function categoryDelete()
    {
        store::addViewValue('active', 'admin-forums');
        
        $category = (new category())->getById(store::getViewValue('id'));
        
        if (store::getPostValues()) {
            $id = store::getViewValue('id');
            if (! empty($id)) {
                $category = (new category())->getById(store::getViewValue('id'));
                if ($category->id) {
                    // get all unread statuses and delete
                    $rights = (new rights())->getByConditions(array(
                        'parent_type' => 'category',
                        'parent_id' => $category->id,
                    ));
                    foreach ($rights as $right) {
                        $right->delete();
                    }
                    $forums = (new forum())->getByCondition('category_id = ?', $category->id);
                    foreach ($forums as $forum) {
                        // get all unread statuses and delete
                        $rights = (new rights())->getByConditions(array(
                            'parent_type' => 'forum',
                            'parent_id' => $forum->id,
                        ));
                        foreach ($rights as $right) {
                            $right->delete();
                        }
                        // get all topics in forum
                        $topics = (new post())->getByCondition('forum_id = ?', $forum->id);
                        foreach ($topics as $topic) {
                            // get all replies belonging to this topic
                            $replies = (new post())->getByCondition('parent_id = ?', $topic->id);
                            foreach ($replies as $reply) {
                                // delete reply
                                $reply->delete();
                            }
                            // get all unread statuses and delete
                            $unreads = (new unread())->getByCondition('post_id', $topic->id);
                            foreach ($unreads as $unread) {
                                $unread->delete();
                            }
                            // delete topic
                            $topic->delete();
                        }
                        // delete forum
                        $forum->delete();
                    }
                    // delete category
                    $category->delete();
                    
                    tool::redirectToRoute('admin-forums');
                }
            }
        }
        
        store::addViewValue('category', $category);
    }

    public function forumDelete()
    {
        store::addViewValue('active', 'admin-forums');
        
        $forum = (new forum())->getById(store::getViewValue('id'));
        
        if (store::getPostValues()) {
            $id = store::getViewValue('id');
            if (! empty($id)) {
                $forum = (new forum())->getById(store::getViewValue('id'));
                if ($forum->id) {
                    // get all unread statuses and delete
                    $rights = (new rights())->getByConditions(array(
                        'parent_type' => 'forum',
                        'parent_id' => $forum->id,
                    ));
                    foreach ($rights as $right) {
                        $right->delete();
                    }
                    // get all topics in forum
                    $topics = (new post())->getByCondition('forum_id = ?', $forum->id);
                    foreach ($topics as $topic) {
                        // get all replies belonging to this topic
                        $replies = (new post())->getByCondition('parent_id = ?', $topic->id);
                        foreach ($replies as $reply) {
                            $reply->delete();
                        }
                        foreach ($unreads as $unread) {
                            $unread->delete();
                        }
                        // get all unread statuses and delete
                        $unreads = (new unread())->getByCondition('post_id', $topic->id);
                        foreach ($unreads as $unread) {
                            $unread->delete();
                        }
                        // delete topic
                        $topic->delete();
                    }
                    // delete forum
                    $forum->delete();
                    
                    tool::redirectToRoute('admin-forums');
                }
            }
        }
        
        store::addViewValue('forum', $forum);
    }

    public function categoryCreate()
    {
        store::addViewValue('active', 'admin-forums');
        store::addViewValue('create', true);
        
        // re-use update without id, causing insert instead of update
        $this->categoryUpdate();
        
        if (store::getViewValue('success')) {
            tool::redirectToRoute('admin-forums');
        }
    }

    public function forumCreate()
    {
        store::addViewValue('active', 'admin-forums');
        store::addViewValue('create', true);
        
        // re-use update without id, causing insert instead of update
        $this->forumUpdate();
        
        if (store::getViewValue('success')) {
            tool::redirectToRoute('admin-forums');
        }
    }

    /**
     * USER FUNCTIONS
     */
    public function users()
    {
        store::addViewValue('active', 'admin-users');
        
        if (store::getPostValues()) {
            foreach (store::getPostValue('usergroup') as $user_id => $groupInfo) {
                $user = (new user())->getById($user_id);
                
                // get current group and make sure it isn't 1/admin
                $currentGroup = (new group())->getById($user->group_id);
                if ($currentGroup->id !== 1) {
                    $user->group_id = (int)$groupInfo['main'];
                }
                // only set subgroup if it's set
                if ((int)$groupInfo['sub'] !== 0) {
                    $user->sub_group_id = (int)$groupInfo['sub'];
                } else {
                    $user->sub_group_id = '';
                }
                $user->save();
            }
            store::addViewValue('success', true);
        }
        
        $users = (new user())->getAll();
        store::addViewValue('users', $users);
    }

    public function userDelete()
    {
        store::addViewValue('active', 'admin-users');
        
        $user = (new user())->getById(store::getViewValue('id'));
        
        if (store::getPostValues()) {
            // delete password
            $password = (new password())->getByCondition('user_id = ?', $user->id);
            $password[0]->delete();
            // delete avatars
            $avatars = (new avatar())->getByCondition('user_id = ?', $user->id);
            if (count($avatars) > 0) {
                foreach ($avatars as $avatar) {
                    $avatar->delete();
                }
            }
            // delete settings
            $settings = (new setting())->getByCondition('user_id = ?', $user->id);
            if (count($settings) > 0) {
                foreach ($settings as $setting) {
                    $setting->delete();
                }
            }
            // delete unread statuses
            $unreads = (new unread())->getByCondition('user_id = ?', $user->id);
            if (count($unreads) > 0) {
                foreach ($unreads as $unread) {
                    $unread->delete();
                }
            }
            // and delete the user
            $user->delete();
            tool::redirectToRoute('admin-users');
        }
        
        store::addViewValue('user', $user);
    }

    /**
     * USER GROUPS FUNCTIONS
     */
    public function usergroups() {
        store::addViewValue('active', 'admin-usergroups');
        
        if (store::getPostValues()) {
            $title = store::getPostValue('title');
            if (!empty($title)) {
                $group = new group();
                $group->title = store::getPostValue('title');
                $group->save();
                
                // now create & save default user rights based on guest
                $rights = (new rights())->getByConditions(array(
                    'child_type = ?' => 'group',
                    'child_id = ?' => 2,
                    'parent_type = ?' => 'default'
                ));
                if (count($rights) > 0) {
                    // reset id & set the new user group's id as child_id
                    $rights[0]->id = null;
                    $rights[0]->child_id = $group->id;
                    $rights[0]->save();
                }
                
                store::addViewValue('success', true);
            }
        }
    }

    public function usergroupDelete()
    {
        store::addViewValue('active', 'admin-usergroups');
        
        $userGroup = (new group())->getById(store::getViewValue('id'));
        
        if (store::getPostValues()) {
            // get all users belonging to this group
            $users = (new user())->getByCondition('group_id = ?', $userGroup->id);
            foreach ($users as $user) {
                $user->group_id = store::getPostValue('target');
                $user->save();
            }
            // remove the rights associated with this group
            $rights = (new rights())->getByConditions(array(
                'child_type = ?' => 'group',
                'child_id = ?' => $userGroup->id,
            ));
            if (count($rights) > 0) {
                foreach ($rights as $right) {
                    $right->delete();
                }
            }
            // and delete the usergroup
            $userGroup->delete();
            tool::redirectToRoute('admin-usergroups');
        }
        
        store::addViewValue('userGroup', $userGroup);
    }

    public function usergroupUpdate()
    {
        store::addViewValue('active', 'admin-usergroups');
        
        $error = array();
        if (store::getViewValue('create')) {
            // create new category object
            $usergroup = new category();
        } else {
            // get category by id
            $usergroup = (new group())->getById(store::getViewValue('id'));
        }
        
        if (store::getPostValues()) {
            $title = store::getPostValue('title');
            
            if (empty($title)) {
                $error[] = 'Title can\'t be empty.';
            } else {
                $usergroup->title = $title;
                $usergroup->save();
                store::addViewValue('success', true);
                
                // now save the rights, if any
                if (store::getPostValue('rights')) {
                    $postRights = store::getPostValue('rights');
                    
                    foreach ($postRights as $group_id => $rights) {
                        // get default values for the usergroup
                        $defaultRights = (new rights())->getByConditions(array(
                            'child_type = ?' => 'group',
                            'child_id = ?' => $group_id,
                            'parent_type = ?' => 'default'
                        ));
                        $defaultRights = $defaultRights[0];
                        $defaultRights->applyPost($rights);
                        $defaultRights->save();
                    }
                }
            }
        }
        
        store::addViewValues(array(
            'error' => $error,
            'usergroup' => $usergroup
        ));
        
    }

    /**
     * MAINTENANCE FUNCTIONS
     */
    public function maintenance()
    {
        store::addViewValue('active', 'admin-maintenance');
    }
}