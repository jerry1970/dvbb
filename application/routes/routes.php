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
$routes[] = array(
    'name' => 'home',
    'method' => 'GET|POST',
    'path' => '/',
    'controller' => 'generalController',
    'action' => 'index',
);
$routes[] = array(
    'name' => 'forum',
    'method' => 'GET|POST',
    'path' => '/forum/[i:id]',
    'controller' => 'generalController',
    'action' => 'forum',
);
$routes[] = array(
    'name' => 'topic',
    'method' => 'GET|POST',
    'path' => '/topic/[i:id]',
    'controller' => 'generalController',
    'action' => 'topic',
);

// User routes
$routes[] = array(
    'name' => 'user-list',
    'method' => 'GET|POST',
    'path' => '/user/list',
    'controller' => 'userController',
    'action' => 'getList',
);


/**
 * Now map all the routes to AltoRouter if it exists
 */
if (app::getRouter() instanceof AltoRouter) {
    foreach($routes as $route) {
        app::getRouter()->map(
        $route['method'],
        $route['path'],
        $route['controller'].'#'.$route['action'],
        $route['name']
        );
    }
}