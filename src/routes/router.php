<?php

return function($router) {
    require_once __DIR__ . '/endpoints/_autoloader.php';    

    $router->view('/', function() {
        return view('index');
    });

    $router->view('/auth', function() {
        return view('auth');
    });
};
