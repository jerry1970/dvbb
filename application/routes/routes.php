<?php
$routes[] = array(
    'name' => 'home',
    'method' => 'GET',
    'path' => '/',
    'controller' => 'home',
    'action' => 'index',
);

$routes[] = array(
    'name' => 'forum',
    'method' => 'GET',
    'path' => '/forum/[i:id]',
    'controller' => 'home',
    'action' => 'forum',
);