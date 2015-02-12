<?php
/**
 * dvbb index.php
 * 
 * This will load the app class and run initialize, then set up the autoloader, router, load the routes, try to match 
 * the route and 'dispatch' (in quotation marks due to its ridiculous simplicity) the controller, action & view if 
 * found.
 * 
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * Enable or disable debug mode
 */
$debug = true;

/**
 * If debug, show all errors
 */
if ($debug) {
    error_reporting(-1);
    ini_set('display_errors', 'On');
}

/**
 * Require app class and tell it to initialize, this will allow the use of getPath(), getBasePath() ,getUrl(), etc.
 */
require('./library/app.php');
app::initialize();

/**
 * We're going to need an autoloader to look in the directories where our classes live.
 * We will look from most common (models) to least common (library)
 */
spl_autoload_register(function ($class) {
    $locations = array('application/model', 'application/controller', 'library');
    foreach ($locations as $location) {
        if (file_exists(app::getPath() . '/' . $location . '/' . $class . '.php')) {
            require(app::getPath() . '/' . $location . '/' . $class . '.php');
        }
    }
});

/**
 * We instantiate a new AltoRouter instance and store it in app
 */
app::setRouter(new AltoRouter());
/**
 * If we have a basePath, set it on AltoRouter so it knows how to deal with requests
 */
if (app::getBasePath()) {
    app::getRouter()->setBasePath(app::getBasePath());
}

/**
 * Load the routes file, which will define & loop through an array of routes and map the routes in AltoRouter
 */
require(app::getPath() . '/application/routes/routes.php');

/**
 * Now that we have a router instance with routes defined, we can match the current request
 */
$match = app::getRouter()->match();

/**
 * $match is false if there's no match
 */
if ($match) {
    // split the target into controller & action (targets are set as controller#action)
    $target = explode('#', $match['target']);
    $controllerName = $target[0];
    $action = $target[1];
    // instantiate controller with params & call action
    $controller = new $controllerName($match['params']);
    $controller->$action();
    // get the Controller-less name
    $controllerShortName = str_replace('Controller', '', $controllerName);
    
    // require header, view, then footer
    require(app::getPath() . '/application/view/layout/header.phtml');
    require(app::getPath() . '/application/view/' . $controllerShortName . '/' . $action . '.phtml');
    require(app::getPath() . '/application/view/layout/footer.phtml');
} else {
    echo '404 - requested path "' . app::getUrl() . '/' .  $_GET['path'] . '" not found.';
}
