<?php

/**
 * Valida um email qualquer.
 * 
 * @param string $email Email a validar
 * @param string|null $extension Extensão permitida do email, ex.: "@escola.pr.gov.br"
 * @return void
 */

function validate_email(string $email, ?string $extension = null): void {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error(
            'validation_error',
            'Erro de validação',
            422,
            'O endereço de email informado é inválido.',
            [ 'email' => 'O endereço de email informado é inválido.' ]
        );
    }

    if ($extension !== null) {
        $normalized_extension = strtolower(ltrim($extension, '@'));
        $email_domain = strtolower(substr($email, strrpos($email, '@') + 1));

        if ($email_domain !== $normalized_extension) {
            $message = 'O email informado deve terminar com @' . $normalized_extension . '.';

            error(
                'validation_error',
                'Erro de validação',
                422,
                $message,
                [ 'email' => $message ]
            );
        }
    }
}

/**
 * Valida uma senha qualquer.
 * 
 * @param string $password Senha a validar
 * @param array $length [min, max]
 * @return void
 */

function validate_password(string $password, ?array $length = null): void {
    if ($password === '') {
        error(
            'validation_error',
            'Erro de validação',
            422,
            'A senha é obrigatória.',
            [ 'password' => 'A senha é obrigatória.' ]
        );
    }

    if ($length === null) {
        return;
    }

    $password_length = strlen($password);

    if (isset($length[0], $length[1]) && ($password_length < $length[0] || $password_length > $length[1])) {
        $message = 'A senha deve ter entre ' . $length[0] . ' e ' . $length[1] . ' caracteres.';

        error(
            'validation_error',
            'Erro de validação',
            422,
            $message,
            [ 'password' => $message ]
        );
    }
}