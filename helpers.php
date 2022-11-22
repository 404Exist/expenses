<?php

if (!function_exists("encrypt")) {
    function encrypt(string $password, int $cost = 10): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }
}

if (!function_exists("validate")) {
    function validate(
        array $rules = [],
        array $data = [],
        ?callable $callback = null
    ): \Valitron\Validator|callable|bool {
        return (new \App\Custom\Vaildator($rules, $data))->validate($callback);
    }
}
