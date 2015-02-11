<?php
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