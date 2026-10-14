<?php

/**
 * Injeta as variáveis ed $client_config no front-end. Possibilitando sua 
 * utilização.
 * 
 * @return string
 */

function buildClientConfigScript(): string {
    global $client_config;

    if (!is_array($client_config) || empty($client_config)) {
        return '';
    }

    $config = [];

    foreach ($client_config as $key => $value) {
        $config[$key] = $value;
    }

    $json = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return "<script>window.__APP__ = " . $json . ";</script>";
}

function resolveViewPath(mixed $view): ?string {
    if (!is_string($view) || trim($view) === '') {
        throw new InvalidArgumentException('A view precisa ter um nome válido.');
    }

    $publicPath = ROOT_PATH . '/public';
    $cleanView = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, ltrim($view, '/'));

    $candidates = [];

    if (str_ends_with($cleanView, '.php') === false) {
        $candidates[] = $publicPath . DIRECTORY_SEPARATOR . $cleanView . '.php';
    } else {
        $candidates[] = $publicPath . DIRECTORY_SEPARATOR . $cleanView;
    }

    $filename = basename($cleanView, '.php') . '.php';
    $candidates[] = $publicPath . DIRECTORY_SEPARATOR . $filename;

    $directory = new RecursiveDirectoryIterator($publicPath, FilesystemIterator::SKIP_DOTS);
    $iterator = new RecursiveIteratorIterator($directory);

    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $relativePath = str_replace($publicPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
        $relativePath = str_replace('\\', '/', $relativePath);

        $viewPath = str_replace('\\', '/', $cleanView);

        if (
            $relativePath === $viewPath ||
            $relativePath === $viewPath . '.php' ||
            basename($file->getPathname()) === $filename ||
            strtolower($relativePath) === strtolower($viewPath)
        ) {
            $candidates[] = $file->getPathname();
        }
    }

    foreach ($candidates as $candidate) {
        if (file_exists($candidate)) {
            return $candidate;
        }
    }

    return null;
}

/**
 * Renderiza uma view e envia seu conteúdo ao cliente.
 *
 *
 * @param string $view Nome da View a ser renderizada
 * @param mixed $data Dados que serão disponibilizados para
 * a view
 * @return never 
 */

function view(mixed $view, array $data = []) {
    header("Content-Type: text/html; charset=UTF-8");

    $viewPath = resolveViewPath($view);

    if ($viewPath === null) {
        throw new Error("Rota de view não encontrada.");
    }

    extract($data);
    ob_start();
    require $viewPath;
    $content = ob_get_clean();

    $script = buildClientConfigScript();

    echo $script . $content;
}