<?php

class Env {

    public static function get() {

        $env_require = [
            "API_KEY",
            "API_SECRET",
            "DBNAME",
            "DBPORT",
            "DBUSER",
            "DBPASSWORD",
            "DBHOST"
        ];

        $fields = [];
        $envPath = __DIR__ . '/../../.env';

        $lines = file_exists($envPath) ? file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

        foreach ($env_require as $i => $field) {
            $valorEncontrado = null;

            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                
                if (strpos($line, '=') !== false) {

                    list($key, $value) = explode('=', $line, 2);

                    if (trim($key) === $field) {
                        $valorEncontrado = trim($value, " \t\n\r\0\x0B\"'");
                        break;
                    }

                }
                
            }

            $fields[$field] = $valorEncontrado;
        }

        return $fields;
    }
}
