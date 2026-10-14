<?php

/**
 * Resgata os dados enviados por meio de uma requisição HTTP.
 * 
 * @return mixed
 */

function get_data(){
    return json_decode(file_get_contents('php://input'), true);
}

/**
 * Verifica se o site está em HTTPS
 * 
 * @return bool true para HTTPS, false para HTTP
 */

function isHttps() {
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
        return true;
    }

    return false;
}

/**
 * Gera a URL base da aplicação.
 * 
 * @return string URL base da aplicação
 */

function getBaseUrl() {
    $protocol = isHttps() ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    
    $basePath = str_replace('/public', '', $scriptPath);
    if (empty($basePath) || $basePath === '/') {
        $basePath = '';
    }
    
    return $protocol . '://' . $host . $basePath;
}

/**
 * Retorna o caminho para os assets 
 * 
 * @return string caminho para os assets 
 */

function getAssetPath() {
    return getBaseUrl() . '/public/static';
}


/**
 * Retorna um erro padronizado
 * 
 * @return never
 */

function error(string $type, string $title, int $status, string $detail, array $errors = []): never {
    http_response_code($status);
    Logger::log($title, 'DEBUG');

    echo json_encode([
        'success' => false,
        'error' => true,
        'type' => $type,
        'title' => $title,
        'status' => $status,
        'detail' => $detail,
        'message' => $detail,
        'errors' => $errors
    ]);

    exit;
}

/**
 * Retorna uma mensagem de sucesso padronizada
 * 
 * @return mixed 
 */

function success(string $type, string $title, int $status, string $detail, array $data = []) {
    http_response_code($status);
    Logger::log($title, 'DEBUG');

    return [
        'success' => true,
        'error' => false,
        'type' => $type,
        'title' => $title,
        'status' => $status,
        'detail' => $detail,
        'message' => $detail,
        'data' => $data
    ];

}