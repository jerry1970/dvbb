<?php
// enable or disable error reporting
error_reporting(-1);
ini_set('display_errors', 'On');

/*********************************************
 * require tools and tell it to initialize
 * this will allow the use of getPath(), getBasePath() and getUrl()
 *********************************************/
require('./application/controller/tools.php');
tools::initialize();

// set up autoloader
spl_autoload_register(function ($class) {
    $locations = array('controller', 'model');
    
    if ($class === 'AltoRouter') {
        require(tools::getPath() . '/library/AltoRouter/AltoRouter.php');
    } else {
        foreach ($locations as $location) {
            if (file_exists(tools::getPath() . '/application/' . $location . '/' . $class . '.php')) {
                require(tools::getPath() . '/application/' . $location . '/' . $class . '.php');
            }
        }
    }
});

// instantiate router
tools::setRouter(new AltoRouter());
// if we have a basePath, set it
if (strlen(tools::getBasePath()) > 0) {
    tools::getRouter()->setBasePath(tools::getBasePath());
}

// load routes, this will give us a $routes array to loop through and map the routes
require('./application/routes/routes.php');
foreach($routes as $route) {
    tools::getRouter()->map(
        $route['method'], 
        $route['path'], 
        $route['controller'].'#'.$route['action'], 
        $route['name']
    );
}

// match the current request
$match = tools::getRouter()->match();

if ($match) {
    // split the target into controller & action
    $target = explode('#', $match['target']);
    $controllerName = $target[0];
    $action = $target[1];
    // instantiate controller & call action with params
    $controller = new $controllerName();
    $controller->$action($match['params']);
    // load the view associated with this controller & action
    $view = tools::getViewParams();
    
    // require header, view, then footer
    require(tools::getPath() . '/application/view/layout/header.phtml');
    require(tools::getPath() . '/application/view/' . $controllerName . '/' . $action . '.phtml');
    require(tools::getPath() . '/application/view/layout/footer.phtml');
}
