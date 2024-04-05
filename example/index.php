<?php

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

use Hnqca\Router\Router;
use Hnqca\Router\Request;
use Hnqca\Router\Response;

/**
 * Examples of Middleware classes
 */
require_once __DIR__ . '/Middlewares/AuthMiddleware.php';

use Middlewares\AuthMiddleware;

/**
 * Examples of Controller classes
 */
require_once __DIR__ . '/Controllers/HomeController.php';
require_once __DIR__ . '/Controllers/ProductController.php';
require_once __DIR__ . '/Controllers/UserController.php';
require_once __DIR__ . '/Controllers/AdminController.php';
require_once __DIR__ . '/Controllers/ErrorController.php';

use Controllers\HomeController;
use Controllers\ProductController;
use Controllers\UserController;
use Controllers\AdminController;
use Controllers\ErrorController;

/**
 * Routes
 */
$route = new Router();

$route->get('/',         [HomeController::class,     'index']);
$route->get("/products", [ProductController::class,  'index']);


/**
 * Route without a controller class
 */
$route->get('/hello/{name}', [], function(Request $req, Response $res){

    $name = $req->getParams()->name;
    
    return $res->send(200, "Hello, {$name}!");
});

/**
 * Route with middleware
 */
$route->get('/admin', [AdminController::class, 'index'], function () {
    return (new AuthMiddleware)->onlyAdmin();
});


/**
 * Example of routes for CRUD operations
 */
$route->post("/users",        [UserController::class,  'create']);
$route->get("/users",         [UserController::class,  'index']);
$route->get("/users/{id}",    [UserController::class,  'show']);
$route->put("/users/{id}",    [UserController::class,  'update']);
$route->patch("/users/{id}",  [UserController::class,  'update']);
$route->delete("/users/{id}", [UserController::class,  'delete']);


/**
 * Execute
 */
$route->dispatch();


/**
 * Example for error handling in the route
 * 404 Not Found, 405 Method Not Allowed and 501 Not Implemented
 */
if ($route->error()) {
    return (new ErrorController)->show($route->error());
}