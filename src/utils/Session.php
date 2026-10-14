<?php

class Session {

    /**
     * Remove a sessão do usuário.
     * 
     * @param mysqli $mysqli 
     * @param string $old_token Token do cookie a ser excluído
     * @return void
     */

    public static function remove_session(mysqli $mysqli, string $old_token) {
        remove_cookie(SESSION_COOKIE);

        Database::query(
            'DELETE FROM session_tokens WHERE token = ?',
            [$old_token],
            $mysqli
        );

    }

    /**
     * Adiciona a sessão do usuário.
     * 
     * @param mysqli $mysqli 
     * @param int $user_id Identificador único do usuário
     * @param string $token Token de sessão
     * @return void
     */

    public static function add_session(mysqli $mysqli, int $user_id, string $token) {
        Database::query(
            "INSERT INTO session_tokens (user_id, token, exp) VALUES (?, ?, ?)",
            [$user_id, $token, time()],
            $mysqli
        );
    }
 
}