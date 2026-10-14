<?php

require_once __DIR__ . '../../handlers/_autoloader.php';

spl_autoload_register(function ($class) {
    $baseDir = __DIR__;
    $class = str_replace('\\', '/', $class);
    $file = $baseDir . '/' . ltrim($class, '/') . '.php';

    if (file_exists($file)) {
        require_once $file;
        return;
    }

    $directory = new RecursiveDirectoryIterator($baseDir);
    $iterator = new RecursiveIteratorIterator($directory);

    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getExtension() === 'php') {
            if (pathinfo($item->getFilename(), PATHINFO_FILENAME) === basename($class)) {
                require_once $item->getPathname();
                return;
            }
        }
    }
    
});