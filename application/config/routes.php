<?php

use CoffeeCode\Router\Router;

$router = new Router("http://localhost");
$router->namespace('RestAPI');

// CRAFTING ROUTES -----------------------------------------
$router->get('/', 'Controllers\Controller:index');

// User
$router->get('/users', 'Controllers\User:index');
$router->get('/users/{id}', 'Controllers\User:show');
$router->get('/users/{id}/records', 'Controllers\User:records');
$router->post('/users', 'Controllers\User:store');
$router->put('/users/{id}', 'Controllers\User:update');
$router->delete('/users/{id}', 'Controllers\User:destroy');
$router->post('/login', 'Controllers\User:login');
$router->post('/users/{id}/drink', 'Controllers\User:drink');

// ---------------------------------------------------------

$router->dispatch();

if ($router->error()) {
    dump($router->error());
}
