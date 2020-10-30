<?php


namespace App\Form;


class FormValidator {

    private bool $isValid = true;

    private array $errors = [];

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getError(string $fieldName): ?string
    {
        return $this->errors[$fieldName] ?? null;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function validate(array $rules, array $payload)
    {
        foreach ($rules as $rule) {
            $this->validateRequired($rule, $payload);
            switch ($rule['type']) {
                case 'string':
                    $this->validateString($rule, $payload);
                    break;
                case 'email':
                    $this->validateEmail($rule, $payload);
                    break;
            }
            $this->validateMinLength($rule, $payload);
            $this->validateMaxLength($rule, $payload);
        }

        return $this->errors;
    }

    public function validateRequired(array $rule, array $payload)
    {
        if ($rule['required'] === true && !isset($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']] = 'This field is required';

            return;
        }

        return;
    }

    public function validateString($rule, $payload)
    {
        if ($rule['type'] === "string" && !is_string($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']] = 'This field is required';

            return;
        }

        return;
    }

    public function validateEmail($rule, $payload)
    {
        if ($rule['type'] === "email" && !filter_var($payload[$rule['fieldName']], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$rule['fieldName']] = 'This is not a valid email';

            return;
        }

        return;
    }

    public function validateMinLength($rule, $payload)
    {
        if ($rule['minLength'] > strlen($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']] = 'This field lenght is too short';

            return;
        }

        return;
    }

    public function validateMaxLength($rule, $payload) {
        if ($rule['maxLength'] < strlen($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']] = 'This field lenght is too long';

            return;
        }
    }
}