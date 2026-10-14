<?php 

class AuthService {

    public function signUp(array $data) {
        $mysqli = Database::Connect();
        $status = (new SignUpHandler())->handle($data, $mysqli);
        
        $mysqli->close();
        return json_encode($status);
    }

    public function login(array $data) {
        $mysqli = Database::Connect();
        $status = (new LogInHandler())->handle($data, $mysqli);

        $mysqli->close();
        return json_encode($status);
    }

    public function logout(array $data = []) {
        $mysqli = Database::Connect();

        if (cookie_exists(SESSION_COOKIE)) {
            Session::remove_session($mysqli, $_COOKIE[SESSION_COOKIE]);
        }

        remove_cookie(SESSION_COOKIE);
        $mysqli->close();

        return json_encode(success(
            'logout_success',
            'Log Out Successful',
            200,
            'User logged out successfully.',
            []
        ));
    }

}