<?php

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

require_once __DIR__. '/../bootstrap.php';

$router = new Router();

$routes = require __DIR__ . '/routes/router.php';
$routes($router);

$route = $_GET['route'] ?? $_SERVER['REQUEST_URI'] ?? '/';

if (!empty($_GET['route'])) {
    $route = '/' . ltrim((string) $_GET['route'], '/');
} 

else {
    $parsedRoute = parse_url((string) $route, PHP_URL_PATH);
    $route = $parsedRoute !== false && $parsedRoute !== '' ? $parsedRoute : '/';
}

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $route
);