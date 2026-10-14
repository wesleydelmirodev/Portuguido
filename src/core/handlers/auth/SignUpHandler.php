<?php

class SignUpHandler {

    public function handle(array $data, mysqli $mysqli) {
        // Verificando os campos dos dados enviados pelo usuário
        $data_status = verify_fields(['username', 'email', 'password'], $data);

        if (!$data_status["valid"]) {
            error(
                'validation_error', 
                'Erro de validação', 
                422, 
                'Faltam campos obrigatórios.', 
                $data_status["missing"]
            );
        }

        // Resgatando cada informação necessária
        $username = $data['username'];
        $email = trim($data['email']);
        $password = $data['password'];

        // Validando as informações
        validate_email($email, '@escola.pr.gov.br');
        validate_password($password, [8, 100]);
        validate_string($username, [ 'length' => [3, 80] ]);

        // Verificando se o email já está cadastrado
        $row = Database::query("SELECT email FROM users WHERE email = ?", [$email], $mysqli);

        if (!empty($row)) {
            error(
                'email_already_exists',
                'Email já cadastrado',
                422,
                'Este endereço de email já está cadastrado.',
                [$email]
            );
        }

        // Gerando o hash da senha
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Registrando o usuário no banco de dados
        $user_id = Database::query(
            "INSERT INTO users (username, email, password) VALUES (?, ?, ?) ",
            [$username, $email, $password_hash], $mysqli
        );

        if(cookie_exists(SESSION_COOKIE)) {
            Session::remove_session($mysqli, $_COOKIE[SESSION_COOKIE]);
        }

        $token = bin2hex(random_bytes(32));
        Session::add_session($mysqli, $user_id, $token);

        add_cookie(SESSION_COOKIE, $token, time() + 3600);
        session_start();

        $_SESSION['user_id'] = $user_id;

        return success(
            'signup_success',
            'Cadastro realizado',
            201,
            'Usuário cadastrado com sucesso.',
            []
        );
    }
    
} 