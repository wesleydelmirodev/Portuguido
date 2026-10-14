<?php

foreach (scandir(__DIR__) as $file) {
    if ($file === '.' || $file === '..' || $file === '_autoloader.php') {
        continue;
    }

    $path = __DIR__ . '/' . $file;
    if (is_file($path)) {
        require_once $path;
    }
}