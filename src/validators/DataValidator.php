<?php

/**
 * Verifica se os campos de um array existem
 * 
 * @param array $fields Campos que devem constar no array
 * @param array $iterable Array para verificar
 * @return mixed 
 */

function verify_fields(array $fields, array $iterable) {
    $non_existent = [];

    if (empty($iterable)) {
        error('invalid_data', 'Dados inválidos', 422, 'Os dados enviados estão vazios.', []);
    }

    for ($i = 0; $i < count($fields); $i++) {
        if(!array_key_exists($fields[$i], $iterable)) {
            $non_existent[] = $fields[$i];
        }
    }

    return [
        'valid' => count($non_existent) <= 0,
        'missing' => $non_existent
    ];
}

/**
 * Valida uma string qualquer.
 * 
 * @param mixed $value Valor a ser verificado
 * @param array $options [
 *   'required' => boolean,
 *   'length' => [min, max],
 *   'regex' => boolean,
 *   'in' => [values]
 * ]
 * @return mixed 
 */

function validate_string(mixed $value, array $options = []) {

    $required = $options['required'] ?? true;
    $length = $options['length'] ?? null;
    $regex = $options['regex'] ?? null;
    $in = $options['in'] ?? null;

    // Caso não for uma string
    if (!is_string($value)) {
        error(
            'invalid_data',
            'Dados inválidos',
            422,
            'O valor informado deve ser uma string.',
            []
        );
    }

    // Caso o valor precise estar presente
    if ($required && trim($value) === '') {
        error(
            'validation_error',
            'Erro de validação',
            422,
            'O valor informado é obrigatório.',
            []
        );
    }

    // Caso necessite setar um tamanho de caracteres
    if ($length !== null) {
        $min = $length[0] ?? 0;
        $max = $length[1] ?? PHP_INT_MAX;

        if (strlen($value) < $min || strlen($value) > $max) {
            error(
                'validation_error',
                'Erro de validação',
                422,
                'O valor informado tem um tamanho inválido.',
                []
            );
        }
    }

    // Caso necessite de regex
    if ($regex !== null && !preg_match($regex, $value)) {
        error(
            'validation_error',
            'Erro de validação',
            422,
            'O valor informado tem um formato inválido.',
            []
        );
    }

    // Caso necessite ser algum valor específico
    if ($in !== null && !in_array($value, $in, true)) {
        error(
            'validation_error',
            'Erro de validação',
            422,
            'O valor informado não é permitido.',
            []
        );
    }
}
