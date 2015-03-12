<?php
/**
 * topicController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class topicController extends controller {

    public function create() {
        $forum = (new forum())->getById(store::getViewValue('id'));

        if (!auth::can('create', 'forum', $forum->id)) {
            // not allowed
            tool::redirectToRoute('home');
        }
        
        store::addViewValue('forum', $forum);
        
        if (store::getPostValues()) {
            // deal with post here
            $values = store::getPostValues();
            if (!empty($values['title']) && !empty($values['body'])) {
                $createdAt = (new DateTime)->format('Y-m-d H:i:s');
                $post = (new post())->generateFromRow(array(
                    'forum_id' => $forum->id,
                    'user_id' => auth::getUser()->id,
                    'title' => $values['title'],
                    'body' => strip_tags(trim($values['body'])),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'last_post_at' => $createdAt,
                ));
                if ($post->save()) {
                    tool::redirectToRoute('topic', array('id' => $post->id));
                }
            } else {
                store::addViewValue(array('errors' => 'All fields are required.'));
            }
        }
    }
    
}