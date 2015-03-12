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
            
            $query = (new query(new post()))
                ->where('forum_id = ?', $forum->id)
                ->orderBy('last_post_at')
                ->limit(1);
            
            $topic = (new post())->getByQuery($query);
            if (count($topic) > 0) {
                $lastPostPerForum[$forum->id] = $topic[0];
            }
        }
        
        // now add the most recent data to the view
        store::addViewValue('lastPostPerForum', $lastPostPerForum);
    }
    
    public function forum() {
        // get forum based on id
        $forum = (new forum())->getById(store::getViewValue('id'));
        
        if (!auth::can('read', 'forum', $forum->id)) {
            // not allowed
            tool::redirectToRoute('home');
        }
        
        // get limit based on posts_per_page
        $posts_per_page = tool::getPostsPerPage();

        $limit = $posts_per_page;
        $offset = null;
        if (store::getViewValue('page')) {
            $offset = ($posts_per_page * store::getViewValue('page'));
            $offset = ($offset - $posts_per_page);
        }
        
        $query = (new query())
            ->where('forum_id = ?', $forum->id)
            ->orderBy('last_post_at')
            ->limit($limit, $offset);
        
        $topics = (new post())->getByQuery($query);
        $postTotal = count((new post())->getByCondition('forum_id = ?', $forum->id));
        
        // get some more info
        $pageTotal = ceil($postTotal / $posts_per_page);
        
        store::addViewValues(array(
            'forum' => $forum,
            'topics' => $topics,
            'postTotal' => $postTotal,
            'pageTotal' => $pageTotal,
        ));
    }
    
    public function topic() {
        // get topic based on id
        $topic = (new post())->getById(store::getViewValue('id'));

        if (!auth::can('read', 'forum', $topic->forum_id)) {
            // not allowed
            tool::redirectToRoute('home');
        }
        
        // handle post (if allowed) before we do any of the remaining logic so new replies are taken into account
        if (auth::can('create', 'forum', $topic->forum_id)) {
            if (store::getPostValues()) {
                $body = store::getPostValue('body');
                if (!empty($body)) {
                    $now = (new DateTime())->format('Y-m-d H:i:s');
                    
                    $reply = new post();
                    $reply->created_at = $now;
                    $reply->updated_at = $now;
                    
                    $reply->user_id = auth::getUser()->id;
                    
                    $reply->title = $topic->title;
                    $reply->parent_id = $topic->id;
                    
                    $reply->body = store::getPostValue('body');
                    $reply->save();
                    
                    $topic->last_post_at = $now;
                    $topic->save();
                    
                    // now redirect to ourselves
                    $currentUrl = store::getCurrentUrl() . '?page=' . $topic->getLastPage() . '#post-' . $reply->id;
                    tool::redirect($currentUrl);
                }
            }
        }
        
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
        $posts_per_page = tool::getPostsPerPage();
        
        $limit = $posts_per_page;
        $offset = null;
        if (store::getViewValue('page') && store::getViewValue('page') !== '1') {
            $offset = ($posts_per_page * store::getViewValue('page')) - $posts_per_page;
            $offset = $offset - 1;
        } else {
            $limit = $posts_per_page - 1;
        }
        
        $query = (new query())
            ->where('parent_id = ?', $topic->id)
            ->orderBy('id', 'ASC')
            ->limit($limit, $offset);
        
        // get posts based on parameters
        $posts = (new post())->getByQuery($query);
        if (!store::getViewValue('page') || store::getViewValue('page') == 1) {
            // we're on page 1 so add the topic post to the front of the replies
            $posts = array_merge(array($topic), $posts);
        }
        $postTotal = count((new post())->getByCondition('parent_id = ?', $topic->id)) + 1;
        
        // get some more info
        $pageTotal = ceil($postTotal / $posts_per_page);

        store::addViewValues(array(
            'topic' => $topic,
            'posts' => $posts,
            'postTotal' => $postTotal,
            'pageTotal' => $pageTotal,
        ));
    }
    
    public function passwordReset() {
        
    }
    
}