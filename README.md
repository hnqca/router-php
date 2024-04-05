# Router PHP

Suporte a padrões MVC, middlewares, verbos RESTful mais comuns (GET, POST, PUT, PATCH e DELETE), permitindo lidar facilmente com parâmetros de URL e consultas. Facilitando na criação de URLs amigáveis e APIs RESTful.

***

## Instalação:

via [composer](https://getcomposer.org/):

```bash
composer require hnqca/router-php
```

***

## Exemplos:

No diretório "**[example](https://github.com/hnqca/router-php/tree/main/example)**", você encontrará alguns exemplos que demonstram como usar esta biblioteca.

***

## Configuração:

Para garantir que as rotas funcionem corretamente, é necessário redirecionar todo o tráfego para o arquivo principal de rotas (**index.php**).

Abaixo estão as configurações necessárias para realizar esse redirecionamento, tanto para Apache quanto para Nginx.

### Apache (.htaccess):

```bash
RewriteEngine On
Options All -Indexes

## WWW Redirect.
# RewriteCond %{HTTP_HOST} !^www\. [NC]
# RewriteRule ^ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

## HTTPS Redirect
# RewriteCond %{HTTP:X-Forwarded-Proto} !https
# RewriteCond %{HTTPS} off
# RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# URL Rewrite
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php?uri=/$1 [L,QSA]
```

### Nginx:

```bash
location / {
  if ($script_filename !~ "-f"){
    rewrite ^(.*)$ /index.php?uri=/$1 break;
  }
}
```

## Exemplo de Rotas (index.php):

```php
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
```

***

## Path Params (Parâmetros de Caminho):

**index.php**:
```php
$route->get("/users/{id}", [UserController::class, 'show']);
```

**UserController.php**:
```php
class UserController
{
    public function show(Request $req, Response $res)
    {
        // var_dump($req->getParams());

        $userId = $req->getParams()->id;

        if (!filter_var($userId, FILTER_VALIDATE_INT)) {
            return $res->send(400, "It is expected to receive a value of integer type.");
        }
        
        return $res->send(200, "Viewing user data with ID: {$userId}");
    }
}
```

### Exemplo:

<img src="https://i.ibb.co/0nkzgQJ/example1.jpg" />

***

## Query Params (Parâmetros de Consulta):

**index.php**:
```php
$route->get("/products", [ProductControllers::class, 'index']);
```

**ProductController.php**:
```php
class ProductController
{
    public function index(Request $req, Response $res)
    {
        $filter = $req->getQuery()->filter ?? null;

        if ($filter) {
            return $res->send(200, "Filtering products by {$filter}"); 
        }

        return $res->send(200, "Listing all products..."); 
    }
}
```

### Exemplo:

<img src="https://i.ibb.co/JqCbG7V/example2.jpg" />


***