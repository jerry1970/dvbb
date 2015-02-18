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
    'method' => 'GET',
    'path' => '/topic/[i:id]',
    'controller' => 'generalController',
    'action' => 'topic',
);

// User routes
$routes['user-list'] = array(
    'method' => 'GET',
    'path' => '/user/list',
    'controller' => 'userController',
    'action' => 'userlist',
    'secure' => true,
);
$routes['user-profile'] = array(
    'method' => 'GET',
    'path' => '/user/[i:id]',
    'controller' => 'userController',
    'action' => 'profile',
    'secure' => true,
);

// Create routes
$routes['topic-create'] = array(
    'method' => 'GET|POST',
    'path' => '/forum/[i:id]/add-topic',
    'controller' => 'topicController',
    'action' => 'create',
    'secure' => true,
);
$routes['reply-create'] = array(
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]/add-reply',
    'controller' => 'replyController',
    'action' => 'create',
    'secure' => true,
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

// AJAX routes
$routes['login'] = array(
    'method' => 'POST',
    'path' => '/login',
    'controller' => 'ajaxController',
    'action' => 'login',
    'output' => 'json',
);
$routes['logout'] = array(
    'method' => 'GET',
    'path' => '/logout',
    'controller' => 'ajaxController',
    'action' => 'logout',
    'output' => 'json',
);

// Token routes
$routes['token-redeem'] = array(
    'method' => 'GET',
    'path' => '/token-redeem/[a:token]',
    'controller' => 'tokenController',
    'action' => 'redeem',
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