<?php

class Database {

    /**
     * Estabelece a conexão com o banco de dados e a retorna.
     * 
     * @return mysqli
     */

    public static function Connect() {
        try {
            $mysqli = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME);
            Logger::log("Connection to database established successfully.", "DEBUG");
            return $mysqli;
        } 
        
        catch (mysqli_sql_exception $e) {
            Logger::log("Failed to connect to database: " . $e->getMessage(), "ERROR");
            die("Failed to connect to database.");
        }
    }

    /**
     * Executa uma query SQL qualquer.
     * 
     * @param string $sql Instrução SQL a ser executada.
     * @param array $params Variáveis a serem preenchidas no bind param. Exemplo ... VALUES (?); $param = [$email]
     * @param mysqli $mysqli
     * @return mixed
     */

    public static function query(string $sql, array $params, mysqli $mysqli) {
        $stmt = $mysqli->prepare($sql);

        if (!$stmt) {
            Logger::log("Failed to prepare SQL statement.", "ERROR");
            return false;
        }

        if ($params !== null && ($params === 0 || $params === false || !empty($params))) {
            $params = is_array($params) ? $params : [$params];
            $types = '';
            $values = [];

            foreach ($params as $param) {
                if (is_bool($param)) {
                    $types .= 'i';
                    $values[] = $param ? 1 : 0;
                } 
                elseif (is_int($param)) {
                    $types .= 'i';
                    $values[] = $param;
                } 
                elseif (is_float($param)) {
                    $types .= 'd';
                    $values[] = $param;
                } 
                else {
                    $types .= 's';
                    $values[] = $param;
                }
            }

            $stmt->bind_param($types, ...$values);
        }

        if (!$stmt->execute()) {
            Logger::sql_log("Failed to execute SQL statement.", "ERROR", $sql);
            $stmt->close();
            return false;
        }

        $result = $stmt->get_result();

        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);

            Logger::sql_log("Query executed successfully. Rows returned: " . count($data), "DEBUG", $sql);
            Logger::sql_log("SQL statement: $sql", "DEBUG", $sql);

            $stmt->close();
            return $data;
        }

        $affected = $stmt->affected_rows;
        $insertId = $mysqli->insert_id;

        $stmt->close();

        if (stripos(ltrim($sql), "INSERT") === 0) {
            Logger::sql_log("INSERT query successful. Last insert ID: " . $insertId, "DEBUG", $sql);
            Logger::sql_log("SQL statement: $sql", "DEBUG", $sql);
            return $insertId;
        }

        Logger::sql_log("Query executed successfully. Rows affected: " . $affected, "DEBUG", $sql);
        Logger::sql_log("SQL statement: $sql", "DEBUG", $sql);

        return $affected;
    }

}