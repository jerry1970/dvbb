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
 * Start session
 */
session_start('dvbb');

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
 * Because PHPMailer has its own autoloader, it's easiest to directly load it
 */
require(app::getPath() . '/library/PHPMailer/PHPMailerAutoload.php');

/**
 * We're going to need an autoloader to look in the directories where our classes live.
 * We will look from most common (models) to least common (library)
 */
spl_autoload_register(function ($class) {
    $locations = array('application/model', 'application/controller', 'library', 'library/PHPMailer');
    foreach ($locations as $location) {
        if (file_exists(app::getPath() . '/' . $location . '/' . $class . '.php')) {
            require(app::getPath() . '/' . $location . '/' . $class . '.php');
        }
    }
});

/**
 * Check if a user should be loaded from the session or stored in it
 */
if (isset($_SESSION['id'])) {
    $user = (new user())->getById($_SESSION['id']);
    auth::setUser($user);
}

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
 * Check if maintenance mode is on, and if so, show the maintenance page
 */
if (app::getConfigKey('maintenance_mode') == 1) {
    require(app::getPath() . '/application/view/layout/header.phtml');
    require(app::getPath() . '/application/view/layout/maintenance.phtml');
    require(app::getPath() . '/application/view/layout/footer.phtml');
    die();
}

/**
 * Now that we have a router instance with routes defined, we can match the current request
 */
$match = app::getRouter()->match();

/**
 * $match is false if there's no match
 */
if ($match) {
    /**
     * Check if this route is secure
     * 
     * If secure is set on the route AND it's true AND the user isn't authorized, just toss 'm back to the index
     */
    if (isset($routes[$match['name']]['secure']) && $routes[$match['name']]['secure'] && !auth::getUser()) {
        app::redirect(app::getRouter()->generate('home'));
    }
    
    /**
     * target is made up of multiple values divided by #
     * 
     * Example: controller#action(#output)
     * 
     * #output is optional, values currently accepted: raw (no header/footer)
     */
    $target = explode('#', $match['target']);
    $controllerName = $target[0];
    $action = $target[1];
    
    // check for output
    $output = null;
    if (isset($target[2])) {
        $output = $target[2];
    }
    
    // if output is json, we need to output a json header
    if ($output === 'json') {
        header('Content-type: application/json');
    }
    
    // instantiate controller with params & call action
    $controller = new $controllerName($match['params']);
    $controller->$action();
    
    // get the Controller-less name
    $controllerShortName = str_replace('Controller', '', $controllerName);
    
    // require header, view, then footer
    if (!$output) {
        require(app::getPath() . '/application/view/layout/header.phtml');
    }
    require(app::getPath() . '/application/view/' . $controllerShortName . '/' . $action . '.phtml');
    if (!$output) {
        require(app::getPath() . '/application/view/layout/footer.phtml');
    }
} else {
    echo '404 - requested path "' . app::getUrl() . '/' .  $_GET['path'] . '" not found.';
}
