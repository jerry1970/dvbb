<?php
// enable or disable error reporting
error_reporting(-1);
ini_set('display_errors', 'On');

/*********************************************
 * require dvbb class and tell it to initialize
 * this will allow the use of getPath(), getBasePath() and getUrl()
 *********************************************/
require('./library/dvbb.php');
dvbb::initialize();

// set up autoloader
spl_autoload_register(function ($class) {
    $locations = array('controller', 'model');
    
    if ($class === 'AltoRouter') {
        require(dvbb::getPath() . '/library/AltoRouter/AltoRouter.php');
    } else {
        foreach ($locations as $location) {
            if (file_exists(dvbb::getPath() . '/application/' . $location . '/' . $class . '.php')) {
                require(dvbb::getPath() . '/application/' . $location . '/' . $class . '.php');
            }
        }
    }
});

// instantiate router
dvbb::setRouter(new AltoRouter());
// if we have a basePath, set it
if (strlen(dvbb::getBasePath()) > 0) {
    dvbb::getRouter()->setBasePath(dvbb::getBasePath());
}

// load routes, this will give us a $routes array to loop through and map the routes
require('./application/routes/routes.php');
foreach($routes as $route) {
    dvbb::getRouter()->map(
        $route['method'], 
        $route['path'], 
        $route['controller'].'#'.$route['action'], 
        $route['name']
    );
}

// match the current request
$match = dvbb::getRouter()->match();

if ($match) {
    // split the target into controller & action
    $target = explode('#', $match['target']);
    $controllerName = $target[0];
    $action = $target[1];
    // instantiate controller & call action with params
    $controller = new $controllerName();
    $controller->$action($match['params']);
    // load the view associated with this controller & action
    $view = dvbb::getViewParams();
    
    // require header, view, then footer
    require(dvbb::getPath() . '/application/view/layout/header.phtml');
    require(dvbb::getPath() . '/application/view/' . $controllerName . '/' . $action . '.phtml');
    require(dvbb::getPath() . '/application/view/layout/footer.phtml');
}
