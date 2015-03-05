<?php
/**
 * replyController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class replyController extends controller {

    public function create() {
        $topic = (new post())->getById(store::getViewValue('id'));

        if (!auth::can('create', 'forum', $topic->forum_id)) {
            // not allowed
            tool::redirectToRoute('home');
        }

        // get limit based on posts_per_page
        $posts_per_page = tool::getPostsPerPage();
        
        // a reply given to quote is optional
        $replyQuote = null;
        $replyQuoteId = store::getViewValue('quoteId');
        if ($replyQuoteId) {
            $replyQuote = (new post())->getById($replyQuoteId);
            if ($replyQuote) {
                store::addViewValue('replyQuote', $replyQuote);
            }
        }
        
        store::addViewValue('topic', $topic);
        
        if (store::getPostValues()) {
            // deal with post here
            $values = store::getPostValues();
            if (!empty($values['body'])) {
                $createdAt = (new DateTime)->format('Y-m-d H:i:s');
                $reply = (new post())->generateFromRow(array(
                    'parent_id' => $topic->id,
                    'user_id' => auth::getUser()->id,
                    'title' => $topic->title,
                    'body' => strip_tags(trim($values['body'])),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ));
                if ($reply->save()) {
                    // Since we've just saved a reply, we need to update the topic's last_post_at
                    $topic->last_post_at = $createdAt;
                    $topic->save();
                    
                    // need to now calculate the number of pages in this topic and add ?page=x to the url
                    $postTotal = count((new post())->getByCondition('parent_id = ?', $topic->id)) + 1;
                    // get some more info
                    $pageTotal = ceil($postTotal / $posts_per_page);
                    $url = store::getRouter()->generate('topic', array('id' => $topic->id));
                    if ($pageTotal > 1) {
                        $url .= '?page=' . $pageTotal;
                    }
                    $url .= '#post-' . $reply->id; 
                    tool::redirect($url);
                }
            } else {
                store::addViewValue('error', 'All fields are required.');
            }
        }
    }
    
    public function update() {
        $reply = (new post())->getById(store::getViewValue('id'));
        if ($reply->parent_id) {
            $topic = (new post())->getById($reply->parent_id);
        } else {
            $topic = $reply;
        }
        
        if (!auth::can('update', 'forum', $topic->forum_id)) {
            // not allowed
            tool::redirectToRoute('home');
        }

        // get limit based on posts_per_page
        $posts_per_page = tool::getPostsPerPage();

        if (store::getPostValues()) {
            // deal with post here
            $values = store::getPostValues();
            if (!empty($values['body'])) {
                $updatedAt = (new DateTime)->format('Y-m-d H:i:s');
                $reply->body = $values['body'];
                $reply->updated_at = $updatedAt;
                $reply->updated_user_id = auth::getUser()->id;
                
                if ($reply->save()) {
                    // need to now calculate the number of pages in this topic and add ?page=x to the url
                    $postTotal = count((new post())->getByCondition('parent_id = ?', $topic->id)) + 1;
                    // get some more info
                    $pageTotal = ceil($postTotal / $posts_per_page);
                    $url = store::getRouter()->generate('topic', array('id' => $topic->id));
                    if ($pageTotal > 1) {
                        $url .= '?page=' . $pageTotal;
                    }
                    $url .= '#post-' . $reply->id;
                    tool::redirect($url);
                }
            } else {
                store::addViewValue('error', 'All fields are required.');
            }
        }
        
        store::addViewValues(array(
            'topic' => $topic,
            'reply' => $reply,
        ));
    }
    
}