<?php


namespace App\Form;


class FormValidator
{
    private array $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function validate(array $rules, array $payload)
    {
        foreach ($rules as $rule) {
            if ($rule['type'] === "file") {
                $this->validateFile($rule, $payload);
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

    public function validateFile(array $rule, array $payload)
    {
        if ($rule['required'] === false && empty($payload[$rule['fieldName']]['name'])) {
            return;
        }
        if (!$this->validateRequiredFile($rule, $payload)) {
            return;
        }
        $this->validateFileName($rule, $payload);
        $this->validateFileError($rule, $payload);
        $this->validateFileSize($rule, $payload);
        $this->validateFileExtension($rule, $payload);
    }

    public function validateRequired(array $rule, array $payload)
    {
        if ($rule['required'] === true && empty($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'Ce champ est requis. Veillez à le remplir à nouveau.';

            return false;
        }

        return true;
    }

    public function validateRequiredFile(array $rule, array $payload)
    {
        if ($rule['required'] === true && empty($payload[$rule['fieldName']]['name'])) {
            $this->errors[$rule['fieldName']][] = 'Ce fichier est requis.';

            return false;
        }

        return true;
    }

    public function validateString(array $rule, array $payload)
    {
        if ($rule['type'] === "string" && !is_string($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'Ceci n\'est pas un texte valide.';
        }
    }

    public function validateEmail(array $rule, array $payload)
    {
        if ($rule['type'] === "email" && !filter_var($payload[$rule['fieldName']], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$rule['fieldName']][] = 'Ceci n\'est pas une adresse email valide. Ex: paulrene@gmail.com';
        }
    }

    public function validatePhone(array $rule, array $payload)
    {
        if ($rule['type'] === "phone" && !preg_match('^((\+)33|0)[1-9](\d{2}){4}$^', $payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'Ceci n\'est pas un numéro de téléphone valide. Ex: 0756722728';
        }
    }

    public function validateMinLength(array $rule, array $payload)
    {
        if ($rule['minLength'] > strlen($payload[$rule['fieldName']])) {
            $this->errors[$rule['fieldName']][] = 'Il n\'y a pas assez de contenu. Il faut au moins ' .$rule['minLength'] .' caractères.';
        }
    }

    public function validateMaxLength(array $rule, array $payload)
    {
        if (strlen($payload[$rule['fieldName']]) > $rule['maxLength']) {
            $this->errors[$rule['fieldName']][] = 'Il y a trop de contenu. Il peut il y avoir au maximum '  .$rule['maxLength'] .' caractères.';
        }
    }

    public function validateFileName(array $rule, array $payload)
    {
        if (!preg_match('^[\w,\s-]+\.[A-Za-z]{3,4}$^', $payload[$rule['fieldName']]['name'])) {
            $this->errors[$rule['fieldName']][] = 'Le nom du fichier est incorrect.';
        }
    }

    public function validateFileError(array $rule, array $payload)
    {
        if ($payload[$rule['fieldName']]['error'] !== 0) {
            $this->errors[$rule['fieldName']][] = 'Ce fichier produit une erreur.';
        }
    }

    public function validateFileSize(array $rule, array $payload)
    {
        if ($rule['size'] < $payload[$rule['fieldName']]['size']) {
            $this->errors[$rule['fieldName']][] = 'Ce fichier est trop lourd. Essayer de le compresser avant de l\'uploader à nouveau.';
        }
    }

    public function validateFileExtension(array $rule, array $payload)
    {
        if (!in_array($payload[$rule['fieldName']]['type'], $rule['extension'])) {
            $this->errors[$rule['fieldName']][] = 'L\'extension du fichier est incorrect.';
        }
    }
}