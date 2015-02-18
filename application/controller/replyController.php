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
        $topic = (new post())->getById(store::getParam('id'));
        store::addParam('topic', $topic);
        
        if (store::getPostValues()) {
            // deal with post here
            $values = store::getPostValues();
            if (!empty($values['body'])) {
                $createdAt = (new DateTime)->format('Y-m-d H:i:s');
                $post = (new post())->generateFromRowSafe(array(
                    'parent_id' => $topic->id,
                    'user_id' => auth::getUser()->id,
                    'title' => $topic->title,
                    'body' => trim($values['body']),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ));
                if ($post->save()) {
                    // Since we've just saved a reply, we need to update the topic's last_post_at
                    $topic->last_post_at = $createdAt;
                    $topic->save();
                    
                    // need to now calculate the number of pages in this topic and add ?page=x to the url
                    $postTotal = count((new post())->getByQuery('SELECT * FROM post WHERE parent_id = ' . $topic->id)) + 1;
                    // get some more info
                    $pageTotal = ceil($postTotal / store::getConfigParam('posts_per_page'));
                    $url = store::getRouter()->generate('topic', array('id' => $topic->id));
                    if ($pageTotal > 1) {
                        $url .= '?page=' . $pageTotal;
                    }
                    tool::redirect($url);
                }
            } else {
                store::addParam('error', 'All fields are required.');
            }
        }
    }

    public function update() {
    }
    
}