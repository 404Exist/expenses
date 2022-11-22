<?php

declare(strict_types=1);

namespace App\Custom;

use App\Exceptions\ValidationException;
use Valitron\Validator;

class Vaildator
{
    protected Validator $validator;

    public function __construct(protected array $rules = [], protected array $data = [])
    {
        $this->data = count($this->data) ? $this->data : $_POST;

        $this->validator = new Validator($this->data);
    }

    public function validate(?callable $callback = null): \Valitron\Validator|callable|bool
    {
        if (! $this->rules) {
            return $this->validator;
        }

        $this->applyAllRules();

        if ($this->validator->validate()) {
            return $callback ? $callback() : true;
        }

        throw new ValidationException($this->validator->errors());
    }

    protected function applyAllRules()
    {
        foreach ($this->rules as $rule) {
            @[$rule, $fields, $methods] = $rule;


            if (! $rule || ! $fields) {
                continue;
            }

            if (! is_array($fields)) {
                $fields = [$fields];
            }

            $rule = $this->validator->rule($rule, ...$fields);

            if (isset($methods)) {
                foreach ($methods as $method => $value) {
                    $rule->$method($value);
                }
            }
        }
    }
}
