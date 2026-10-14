<?php

class Logger {
    private static string $general_log_path;

    public static function init() {
        self::$general_log_path = LOG_PATH . '/app.log';
        self::create_log_file();
        self::apply_shutdown_log();
    }

    public static function log(string $message, string $level = 'INFO') {
        // Pegando a hora e data
        $timestamp = date('Y-m-d H:i:s');

        // Montando a mensagem de log e registrando no arquivo 
        $log_entry = "├── [$timestamp] [$level] $message" . PHP_EOL;

        file_put_contents(self::$general_log_path, $log_entry, FILE_APPEND);
    }

    public static function sql_log(string $message, string $level = 'INFO', string $sql) {
        // Pegando a hora e data
        $timestamp = date('Y-m-d H:i:s');

        // Montando a mensagem de log e registrando no arquivo
        $log_entry = "├── [$timestamp] [$level] $message" . PHP_EOL;
        $log_entry .= "│   └── SQL Statement: $sql" . PHP_EOL;

        file_put_contents(self::$general_log_path, $log_entry, FILE_APPEND);
    }

    public static function http_log(?string $method, ?string $level, ?string $route,  ?int $status_code, ?float $time = 0) {
        // Pegando a hora e data
        $timestamp = date('Y-m-d H:i:s');

        // Pegando o IP, montando a mensagem de log e registrando no arquivo 
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $log_entry = "├── [$timestamp] [$level] Request $method $route ➔ $status_code ({$time}ms) [IP: $ip]" . PHP_EOL;

        file_put_contents(self::$general_log_path, $log_entry, FILE_APPEND);
    }

    public static function apply_shutdown_log() {
        // Registrando uma função de shutdown para capturar os erros de execução 
        set_exception_handler(function(Throwable $e) {
            http_response_code(500);

            $timestamp = date('Y-m-d H:i:s');

            $log_entry = "├── [$timestamp] [FATAL] {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}" . PHP_EOL . "│   Stack trace:" . PHP_EOL;
            
            $trace_lines = explode("\n", $e->getTraceAsString());
            foreach ($trace_lines as $line) {
                if (trim($line) !== '') {
                    $log_entry .= "│   " . trim($line) . PHP_EOL;
                }
            }

            file_put_contents(self::$general_log_path, $log_entry, FILE_APPEND);

            self::http_log(
                $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN', 
                'FATAL', 
                $_SERVER['REQUEST_URI'] ?? '/',
                500,
                0
            );
        });
    }

    private static function create_log_file() {
        // Pegando o caminho do arquivo de log
        $log_path = LOG_PATH . '/app.log';

        // Caso o arquivo não exista, cria o arquivo com o cabeçalho do log
        if (!file_exists($log_path)) {
            $header = <<<'LOG'

            ███████╗███████╗██████╗ ██╗   ██╗███████╗██████╗     
            ██╔════╝██╔════╝██╔══██╗██║   ██║██╔════╝██╔══██╗    
            ███████╗█████╗  ██████╔╝██║   ██║█████╗  ██████╔╝    
            ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██╔══╝  ██╔══██╗    
            ███████║███████╗██║  ██║ ╚████╔╝ ███████╗██║  ██║    
            ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚══════╝╚═╝  ╚═╝    
                                                                    
            ██╗      ██████╗  ██████╗                    
            ██║     ██╔═══██╗██╔════╝                   
            ██║     ██║   ██║██║  ███╗                  
            ██║     ██║   ██║██║   ██║                  
            ███████╗╚██████╔╝╚██████╔╝                   
            ╚══════╝ ╚═════╝  ╚═════╝                                                                                
            │
            LOG;

            file_put_contents(self::$general_log_path, $header . PHP_EOL);

            self::log(
                'Logger initialized. Log file created.',
                'INFO'
            );
        }
    }

}