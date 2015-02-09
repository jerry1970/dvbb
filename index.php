<?php
// enable or disable error reporting
error_reporting(-1);
ini_set('display_errors', 'On');

// get and set some paths
require('./controllers/tools.php');
tools::setPath(getcwd());
$basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', tools::getPath());

// set up autoloader
spl_autoload_register(function ($class) {
    $locations = array('controllers', 'models');
    
    if ($class === 'AltoRouter') {
        require(tools::getPath() . '/library/AltoRouter/AltoRouter.php');
    } else {
        foreach ($locations as $location) {
            if (file_exists(tools::getPath() . '/' . $location . '/' . $class . '.php')) {
                require(tools::getPath() . '/' . $location . '/' . $class . '.php');
            }
        }
    }
});

// instantiate router
$router = new AltoRouter();
// if we have a basePath, set it
if (strlen($basePath) > 0) {
    $router->setBasePath($basePath);
}

// load routes, this will give us a $routes array to loop through and map the routes
require('./routes/routes.php');
foreach($routes as $route) {
    $router->map(
        $route['method'], 
        $route['path'], 
        $route['controller'].'#'.$route['action'], 
        $route['name']
    );
}

// match the current request
$match = $router->match();

if ($match) {
    // split the target into controller & action
    $target = explode('#', $match['target']);
    $controllerName = $target[0];
    $action = $target[1];
    // instantiate controller & call action with params
    $controller = new $controllerName();
    $controller->$action($match['params']);
    // load the view associated with this controller & action
    $view = tools::getViewArray();
    require(tools::getPath() . '/views/' . $controllerName . '/' . $action . '.phtml');
}
