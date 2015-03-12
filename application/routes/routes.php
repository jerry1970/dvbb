<?php
/**
 * routes
 * 
 * This file contains all routes for the application and then maps them to AltoRouter
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

// General routes
$routes['home'] = array(
    'method' => 'GET',
    'path' => '/',
    'controller' => 'generalController',
    'action' => 'index',
);
$routes['forum'] = array(
    'method' => 'GET',
    'path' => '/forum/[i:id]',
    'controller' => 'generalController',
    'action' => 'forum',
);
$routes['topic'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]',
    'controller' => 'generalController',
    'action' => 'topic',
);
$routes['password-reset'] = array(
    'method' => 'GET',
    'path' => '/password-reset',
    'controller' => 'generalController',
    'action' => 'passwordReset',
);

// User routes
$routes['user-list'] = array(
    'method' => 'GET',
    'path' => '/user/list',
    'controller' => 'userController',
    'action' => 'userlist',
);
$routes['user-profile'] = array(
    'method' => 'GET',
    'path' => '/user/[i:id]',
    'controller' => 'userController',
    'action' => 'profile',
);
$routes['user-settings'] = array(
    'method' => 'GET|POST',
    'path' => '/user/settings',
    'controller' => 'userController',
    'action' => 'settings',
);
$routes['user-password-reset'] = array(
    'method' => 'GET',
    'path' => '/user/settings/password-reset',
    'controller' => 'userController',
    'action' => 'passwordReset',
);

// Create routes
$routes['topic-create'] = array(
    'method' => 'GET|POST',
    'path' => '/forum/[i:id]/add-topic',
    'controller' => 'topicController',
    'action' => 'create',
);
$routes['reply-create'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]/add-reply',
    'controller' => 'replyController',
    'action' => 'create',
);
$routes['reply-create-quote'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]/add-reply-quote/[i:quoteId]',
    'controller' => 'replyController',
    'action' => 'create',
);
$routes['register'] = array(
    'method' => 'GET|POST',
    'path' => '/register',
    'controller' => 'userController',
    'action' => 'create',
);
$routes['register-done'] = array(
    'method' => 'GET|POST',
    'path' => '/register/done',
    'controller' => 'userController',
    'action' => 'createDone',
);

// Reply routes
$routes['reply-update'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/reply/[i:id]/update',
    'controller' => 'replyController',
    'action' => 'update',
);

// AJAX routes
$routes['ajax-login'] = array(
    'method' => 'POST',
    'path' => '/login',
    'controller' => 'ajaxController',
    'action' => 'login',
    'output' => 'json',
);
$routes['ajax-logout'] = array(
    'method' => 'GET',
    'path' => '/logout',
    'controller' => 'ajaxController',
    'action' => 'logout',
    'output' => 'json',
);
$routes['ajax-topic-sticky'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]/sticky',
    'controller' => 'ajaxController',
    'action' => 'topicSticky',
    'output' => 'json',
);

// Token routes
$routes['token-redeem'] = array(
    'method' => 'GET|POST',
    'path' => '/token-redeem/[a:token]',
    'controller' => 'tokenController',
    'action' => 'redeem',
);

// Admin routes
$routes['admin'] = array(
    'method' => 'GET',
    'path' => '/admin',
    'controller' => 'adminController',
    'action' => 'index',
);

$routes['admin-forums'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/forums',
    'controller' => 'adminController',
    'action' => 'forums',
);
$routes['admin-forum-update'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/forum/[i:id]/update',
    'controller' => 'adminController',
    'action' => 'forumUpdate',
);
$routes['admin-forum-delete'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/forum/[i:id]/delete',
    'controller' => 'adminController',
    'action' => 'forumDelete',
);
$routes['admin-forum-create'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/category/[i:id]/forum/create',
    'controller' => 'adminController',
    'action' => 'forumCreate',
);
$routes['admin-category-update'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/category/[i:id]/update',
    'controller' => 'adminController',
    'action' => 'categoryUpdate',
);
$routes['admin-category-delete'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/category/[i:id]/delete',
    'controller' => 'adminController',
    'action' => 'categoryDelete',
);
$routes['admin-category-create'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/category/create',
    'controller' => 'adminController',
    'action' => 'categoryCreate',
);

$routes['admin-users'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/users',
    'controller' => 'adminController',
    'action' => 'users',
);
$routes['admin-user-delete'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/user/[i:id]/delete',
    'controller' => 'adminController',
    'action' => 'userDelete',
);

$routes['admin-usergroups'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/usergroups',
    'controller' => 'adminController',
    'action' => 'usergroups',
);
$routes['admin-usergroup-update'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/usergroup/[i:id]/update',
    'controller' => 'adminController',
    'action' => 'usergroupUpdate',
);
$routes['admin-usergroup-delete'] = array(
    'method' => 'GET|POST',
    'path' => '/admin/usergroup/[i:id]/delete',
    'controller' => 'adminController',
    'action' => 'usergroupDelete',
);

$routes['admin-maintenance'] = array(
    'method' => 'GET',
    'path' => '/admin/maintenance',
    'controller' => 'adminController',
    'action' => 'maintenance',
);

// moderation routes
$routes['mod-topic-close'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]/close',
    'controller' => 'modController',
    'action' => 'topicClose',
);
$routes['mod-topic-delete'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]/delete',
    'controller' => 'modController',
    'action' => 'topicDelete',
);
$routes['mod-reply-delete'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/reply/[i:id]/delete',
    'controller' => 'modController',
    'action' => 'replyDelete',
);


/**
 * Now map all the routes to AltoRouter if it exists
 */
if (store::getRouter() instanceof AltoRouter) {
    foreach($routes as $name => $parameters) {
        $output = null;
        if (isset($parameters['output'])) {
            $output = '#'.$parameters['output'];
        }
        store::getRouter()->map(
            $parameters['method'],
            $parameters['path'],
            $parameters['controller'].'#'.$parameters['action'].$output,
            $name
        );
    }
}