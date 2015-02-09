<?php
$routes[] = array(
    'name' => 'home',
    'method' => 'GET',
    'path' => '/',
    'controller' => 'home',
    'action' => 'index',
);

$routes[] = array(
    'name' => 'test',
    'method' => 'GET',
    'path' => '/test/[i:id]',
    'controller' => 'home',
    'action' => 'test',
);