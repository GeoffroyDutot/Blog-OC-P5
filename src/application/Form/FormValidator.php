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
            if ($rule['type'] === "file") {
                if ($rule['required'] === false && empty($payload[$rule['fieldName']]['name'])) {
                    continue;
                }
                if (!$this->validateRequiredFile($rule, $payload)) {
                    continue;
                }
                $this->validateFileName($rule, $payload);
                $this->validateFileError($rule, $payload);
                $this->validateFileSize($rule, $payload);
                $this->validateFileExtension($rule, $payload);
                continue;
            }
            if ($rule['required'] === false && empty($payload[$rule['fieldName']])) {
                continue;
            }
            if (!$this->validateRequired($rule, $payload)) {
                continue;
            }
            switch ($rule['type']) {
                case 'string':
                    $this->validateString($rule, $payload);
                    break;
                case 'email':
                    $this->validateEmail($rule, $payload);
                    break;
                case 'phone':
                    $this->validatePhone($rule, $payload);
                    break;
            }
            $this->validateMinLength($rule, $payload);
            $this->validateMaxLength($rule, $payload);
        }

        return $this->errors;
    }

    public function validateRequired(array $rule, array $payload)
    {
        if ($rule['required'] === true && empty($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'This field is required';

            return false;
        }
        return true;
    }

    public function validateRequiredFile(array $rule, array $payload)
    {
        if ($rule['required'] === true && empty($payload[$rule['fieldName']]['name'])) {
            $this->errors[$rule['fieldName']][] = 'This field is required';

            return false;
        }
        return true;
    }

    public function validateString(array $rule, array $payload)
    {
        if ($rule['type'] === "string" && !is_string($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'This field is not a valid string';
        }
    }

    public function validateEmail(array $rule, array $payload)
    {
        if ($rule['type'] === "email" && !filter_var($payload[$rule['fieldName']], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$rule['fieldName']][] = 'This is not a valid email';
        }
    }

    public function validatePhone(array $rule, array $payload)
    {
        if ($rule['type'] === "phone" && !preg_match('^((\+)33|0)[1-9](\d{2}){4}$^', $payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'This is not a valid phone number';
        }
    }

    public function validateMinLength(array $rule, array $payload)
    {
        if ($rule['minLength'] > strlen($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'This field lenght is too short';
        }
    }

    public function validateMaxLength(array $rule, array $payload)
    {
        if ($rule['maxLength'] < strlen($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'This field lenght is too long';
        }
    }

    public function validateFileName(array $rule, array $payload)
    {
        if (!preg_match('^[\w,\s-]+\.[A-Za-z]{3,4}$^', $payload[$rule['fieldName']]['name'])) {
            $this->errors[$rule['fieldName']][] = 'This filename is incorrect';
        }
    }

    public function validateFileError(array $rule, array $payload)
    {
        if ($payload[$rule['fieldName']]['error'] !== 0) {
            $this->errors[$rule['fieldName']][] = 'This file return an error';
        }
    }

    public function validateFileSize(array $rule, array $payload)
    {
        if ($payload[$rule['fieldName']]['size'] > 1000000) {
            $this->errors[$rule['fieldName']][] = 'This file is too big';
        }
    }

    public function validateFileExtension(array $rule, array $payload)
    {
        if (!in_array($payload[$rule['fieldName']]['type'], $rule['extension'])) {
            $this->errors[$rule['fieldName']][] = 'This file extension is not accepted';
        }
    }
}