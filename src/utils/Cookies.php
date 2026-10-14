<?php

/**
 * Adiciona um novo cookie ao navegador.
 * 
 * @param string $name Nome do cookie.
 * @param mixed $value Valor armazenado no cookie.
 * @param int $expires Tempo de expiração do cookie.
 * @param string $path
 * @return void
 */

function add_cookie(string $name, mixed $value, int $expires = 0, string $path = '/') : void {
    setcookie($name, $value, [
        'expires' => $expires,
        'path' => $path,
        'secure' => HTTPS,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

/**
 * Remove um cookie do navegador.
 * 
 * @param string $name Nome do cookie.
 * @return void
 */

function remove_cookie(string $name): void {
    setcookie($name, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => HTTPS,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

/**
 * Verifica se um cookie existe.
 * 
 * @param string $name Nome do cookie.
 * @return bool
 */

function cookie_exists(string $name): bool {
    return isset($_COOKIE[$name]);
}