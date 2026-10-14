<?php

class LogInHandler {

    public function handle(array $data, mysqli $mysqli) {
        $data_status = verify_fields(['email', 'password'], $data);

        if (!$data_status["valid"]) {
            error(
                'validation_error',
                'Erro de validação',
                422,
                'Faltam campos obrigatórios.',
                $data_status["missing"]
            );
        }

        $email = trim($data['email']);
        $password = $data['password'];

        validate_email($email, '@escola.pr.gov.br');
        validate_password($password);

        $result = Database::query(
            "SELECT id, password FROM users WHERE email = ?",
            [$email],
            $mysqli
        );

        if (empty($result)) {
            error(
                'invalid_credentials',
                'Credenciais inválidas',
                401,
                'O email ou a senha estão incorretos.',
                []
            );
        }

        if (!password_verify($password, $result[0]['password'])) {
            error(
                'invalid_credentials',
                'Credenciais inválidas',
                401,
                'O email ou a senha estão incorretos.',
                []
            );
        }

        $token = bin2hex(random_bytes(32));

        if (cookie_exists(SESSION_COOKIE)) {
            Session::remove_session($mysqli, $_COOKIE[SESSION_COOKIE]);
        }

        Session::add_session($mysqli, $result[0]['id'], $token);

        add_cookie(SESSION_COOKIE, $token, time() + 3600);

        return success(
            'login_success',
            'Login realizado',
            200,
            'Login realizado com sucesso.',
            []
        );
    }
}