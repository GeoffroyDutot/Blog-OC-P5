<?php


namespace App\DTO;


abstract class DTO
{
    public function toCamelCase($str)
    {
        $str[0] = strtoupper($str[0]);
        $str = preg_replace_callback('/_([a-z])/', function ($match) {
            return strtoupper($match[1]);
        }, $str);

        return $str;
    }

    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut.
            $method = 'set' . $this->toCamelCase($key);

            // Si le setter correspondant existe.
            if (method_exists($this, $method)) {
                // On appelle le setter.
                $this->$method($value);
            }
        }
    }
}