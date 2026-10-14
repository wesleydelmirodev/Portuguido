<?php

$router->post('/sign-up', function() {
    header('Content-Type: application/json; charset=UTF-8');
    echo (new AuthService())->signUp(get_data() ?? []);
});

$router->post('/login', function() {
    header('Content-Type: application/json; charset=UTF-8');
    echo (new AuthService())->login(get_data() ?? []);
});

$router->get('/logout', function() {
    header('Content-Type: application/json; charset=UTF-8');
    echo (new AuthService())->logout([]);
});

