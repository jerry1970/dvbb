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
        $forum = (new forum())->getById(app::getViewByKey('id'));
        app::addToView(array(
            'forum' => $forum,
        ));
        
        if (app::getPost()) {
            // deal with post here
            $values = app::getPost();
            if (!empty($values['title']) && !empty($values['body'])) {
                $createdAt = (new DateTime)->format('Y-m-d H:i:s');
                $post = (new post())->generateFromRowSafe(array(
                    'forum_id' => $forum->id,
                    'user_id' => auth::getUser()->id,
                    'title' => $values['title'],
                    'body' => trim($values['body']),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    'last_post_at' => $createdAt,
                ));
                if ($post->save()) {
                    app::redirectToRoute('topic', array('id' => $post->id));
                }
            } else {
                app::addToView(array('error' => 'All fields are required.'));
            }
        }
    }

    public function update() {
    }
    
}