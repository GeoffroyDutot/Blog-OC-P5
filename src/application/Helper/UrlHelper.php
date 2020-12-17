<?php


namespace App\Helper;


trait UrlHelper
{
    // redirect function by a string
    public function redirect(string $url, $permanent = false)
    {
        if ($permanent) {
            header('HTTP/1.1 301 Moved Permanently');
        }
        header('Location: '.$url);
        exit();
    }
}