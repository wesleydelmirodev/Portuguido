<?php

// Definindo diretórios do projeto
define('ROOT_PATH', __DIR__);
define('SRC_PATH', ROOT_PATH . '/src');
define('CORE_PATH', SRC_PATH . '/core');
define('HTTP_PATH', CORE_PATH . '/http/Http.php');
define('ROUTES_PATH', SRC_PATH . '/routes');
define('UTILS_PATH', SRC_PATH . '/utils');
define('MODULES_PATH', ROOT_PATH . '/modules');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STATIC_PATH', PUBLIC_PATH . '/static');
define('CSS_PATH', STATIC_PATH . '/css');
define('JS_PATH', STATIC_PATH . '/js');
define('IMAGES_PATH', STATIC_PATH . '/images');
define('HANDLERS_PATH', CORE_PATH . '/handlers');
define('SERVICES_PATH', CORE_PATH . '/services');
define('VALIDATORS_PATH', SRC_PATH . '/validators');
define('LOG_PATH', ROOT_PATH . '/storage/logs');

// Dependências
require_once MODULES_PATH . '/_autoloader.php';
require_once UTILS_PATH . '/_autoloader.php';
require_once SERVICES_PATH . '/_autoloader.php';
require_once HANDLERS_PATH . '/_autoloader.php';
require_once VALIDATORS_PATH . '/_autoloader.php';
require_once HTTP_PATH;
    
// Resgatando os campos do .env
$fields = Env::get();

// Definindo as constantes do .env
define('API_KEY', $fields['API_KEY']);
define('API_SECRET', $fields['API_SECRET']);
define('DBNAME', $fields['DBNAME']);
define('DBPORT', $fields['DBPORT']);
define('DBPASSWORD', $fields['DBPASSWORD']);
define('DBUSER', $fields['DBUSER']);
define('DBHOST', $fields['DBHOST']);

// Configurações da aplicação
define('BASE_URL', getBaseUrl());
define('ASSET_PATH', getAssetPath());
define('HTTPS', isHttps());
define('SITE_NAME', 'Framework');
define('SESSION_COOKIE', 'session_token');

// Log
define('DEBUG', true);
define('INFO', true);

date_default_timezone_set('America/Sao_Paulo');

// Iniciando o Logger  
Logger::init();

// Compartilhamento de variáveis com o cliente (JS)
$client_config = [
    'BASE_URL' => BASE_URL,
    'ASSET_PATH' => ASSET_PATH,
    'SITE_NAME' => SITE_NAME,
    'DEBUG' => DEBUG,
];
