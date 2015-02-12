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
    }
    
    public function forum() {
        // get forum based on id
        $forum = (new forum())->getById(app::getViewParam('id'));
        // get limit based on posts_per_page
        $originalLimit = app::getConfigKey('posts_per_page');
        // see if there's a $_GET['page'] available so we have an offset for limit
        if (app::getViewParam('page')) {
            $limit = ($originalLimit * ((int)app::getViewParam('page') - 1)) . ', ' . $originalLimit;
        } else {
            $limit = $originalLimit;
        }
        
        // get posts based on parameters
        $posts = (new post())->getByQuery('SELECT * FROM post WHERE forum_id = ' . $forum->id . ' ORDER BY id LIMIT ' . $limit);
        $postTotal = count((new post())->getByQuery('SELECT * FROM post WHERE forum_id = ' . $forum->id));
        // get some more info
        $pageTotal = ceil($postTotal / app::getConfigKey('posts_per_page'));
        
        app::addToViewParams(array(
            'forum' => $forum,
            'posts' => $posts,
            'postTotal' => $postTotal,
            'pageTotal' => $pageTotal,
        ));
    }
    
    public function topic() {
        // get topic based on id
        $topic = (new post())->getById(app::getViewParam('id'));
        // get limit based on posts_per_page
        $originalLimit = app::getConfigKey('posts_per_page');
        // see if there's a $_GET['page'] available so we have an offset for limit
        if (app::getViewParam('page')) {
            $limit = (($originalLimit - 1) * ((int)app::getViewParam('page') - 1)) . ', ' . $originalLimit;
        } else {
            $limit = $originalLimit - 1;
        }
        
        // get posts based on parameters
        $posts = (new post())->getByQuery('SELECT * FROM post WHERE parent_id = ' . $topic->id . ' ORDER BY id LIMIT ' . $limit);
        if (!app::getViewParam('page')) {
            // we're on page 1 so add the topic post to the front of the replies
            $posts = array_merge(array($topic), $posts);
        }
        $postTotal = count((new post())->getByQuery('SELECT * FROM post WHERE parent_id = ' . $topic->id)) + 1;
        // get some more info
        $pageTotal = ceil($postTotal / app::getConfigKey('posts_per_page'));

        app::addToViewParams(array(
            'topic' => $topic,
            'posts' => $posts,
            'postTotal' => $postTotal,
            'pageTotal' => $pageTotal,
        ));
    }
    
}