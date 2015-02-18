<?php
/**
 * generalController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class generalController extends controller {
    
    public function index() {
        $forums = (new forum())->getAll();
        $lastPostPerForum = array();
        foreach ($forums as $forum) {
            $lastPostPerForum[$forum->id] = null;
            $topic = (new post())->getByQuery('SELECT * FROM post WHERE forum_id = ' . $forum->id . ' ORDER BY last_post_at DESC LIMIT 1');
            if (count($topic) > 0) {
                $lastPostPerForum[$forum->id] = $topic[0];
            }
        }
        
        // now add the most recent data to the view
        store::addParam('lastPostPerForum', $lastPostPerForum);
    }
    
    public function forum() {
        // get forum based on id
        $forum = (new forum())->getById(store::getParam('id'));
        
        // get limit based on posts_per_page
        $originalLimit = store::getConfigParam('posts_per_page');
        // see if there's a page available so we have an offset for limit
        if (store::getParam('page')) {
            $limit = ($originalLimit * ((int)store::getParam('page') - 1)) . ', ' . $originalLimit;
        } else {
            $limit = $originalLimit;
        }
        
        // get topics based on parameters
        $topics = (new post())->getByQuery('SELECT * FROM post WHERE forum_id = ' . $forum->id . ' ORDER BY last_post_at DESC LIMIT ' . $limit);
        $postTotal = count((new post())->getByQuery('SELECT * FROM post WHERE forum_id = ' . $forum->id));
        
        // get some more info
        $pageTotal = ceil($postTotal / store::getConfigParam('posts_per_page'));
        
        store::addParams(array(
            'forum' => $forum,
            'topics' => $topics,
            'postTotal' => $postTotal,
            'pageTotal' => $pageTotal,
        ));
    }
    
    public function topic() {
        // get topic based on id
        $topic = (new post())->getById(store::getParam('id'));

        // store that the user has opened this topic now if we're logged in
        if (auth::getUser()) {
            // create new item for the topic
            $unread = (new unread())->generateFromRow(array(
                'user_id' => auth::getUser()->id,
                'forum_id' => $topic->forum_id,
                'post_id' => $topic->id,
                'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ));
            // now call updateOrInsert
            $unread->updateOrInsert();
        }
        
        // get limit based on posts_per_page
        $originalLimit = store::getConfigParam('posts_per_page');
        
        // see if there's a page available so we have an offset for limit
        if (store::getParam('page')) {
            $limit = (($originalLimit - 1) * ((int)store::getParam('page') - 1)) . ', ' . $originalLimit;
        } else {
            $limit = $originalLimit - 1;
        }
        
        // get posts based on parameters
        $posts = (new post())->getByQuery('SELECT * FROM post WHERE parent_id = ' . $topic->id . ' ORDER BY id ASC LIMIT ' . $limit);
        if (!store::getParam('page')) {
            // we're on page 1 so add the topic post to the front of the replies
            $posts = array_merge(array($topic), $posts);
        }
        $postTotal = count((new post())->getByQuery('SELECT * FROM post WHERE parent_id = ' . $topic->id)) + 1;
        
        // get some more info
        $pageTotal = ceil($postTotal / store::getConfigParam('posts_per_page'));

        store::addParams(array(
            'topic' => $topic,
            'posts' => $posts,
            'postTotal' => $postTotal,
            'pageTotal' => $pageTotal,
        ));
    }
    
}