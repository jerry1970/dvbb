<?php
/**
 * modController
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

class modController extends controller {

    public function topicClose() {
        $topic = (new post())->getById(store::getViewValue('id'));
        
        if (store::getPostValues()) {
            $topic->closed = 1;
            $topic->save();
            tool::redirectToRoute('forum', array('id' => $topic->forum_id));
        }
        
        if ($topic->closed) {
            $topic->closed = '';
            $topic->save();
            tool::redirectToRoute('forum', array('id' => $topic->forum_id));
        }
        
        store::addViewValue('topic', $topic);
    }

    public function topicDelete() {
        $topic = (new post())->getById(store::getViewValue('id'));
        $forum_id = $topic->forum_id;
        
        if (store::getPostValues()) {
            // delete all replies
            $replies = (new post())->getByCondition('parent_id = ?', $topic->id);
            if (count($replies) > 0) {
                foreach ($replies as $reply) {
                    $reply->delete();
                }
            }
            // delete all unread statuses
            $unreads = (new unread())->getByCondition('post_id = ?', $topic->id);
            if (count($unreads) > 0) {
                foreach ($unreads as $unread) {
                    $unread->delete();
                }
            }
            $topic->delete();
            tool::redirectToRoute('forum', array('id' => $forum_id));
        }
        
        store::addViewValue('topic', $topic);
    }
    
    public function replyDelete() {
        $reply = (new post())->getById(store::getViewValue('id'));
        $topic_id = $reply->parent_id;
        
        if (store::getPostValues()) {
            // delete reply
            $reply->delete();
            tool::redirectToRoute('topic', array('id' => $topic_id));
        }
        
        store::addViewValue('reply', $reply);
    }
    
}