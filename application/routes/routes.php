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

$routes[] = array(
    'name' => 'home',
    'method' => 'GET|POST',
    'path' => '/',
    'controller' => 'home',
    'action' => 'index',
);
$routes[] = array(
    'name' => 'forum',
    'method' => 'GET|POST',
    'path' => '/forum/[i:id]',
    'controller' => 'home',
    'action' => 'forum',
);
$routes[] = array(
    'name' => 'post',
    'method' => 'GET|POST',
    'path' => '/post/[i:id]',
    'controller' => 'home',
    'action' => 'post',
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