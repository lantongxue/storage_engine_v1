<?php

declare(strict_types=1);

namespace V1\StorageEngine\Util;


class PathUtil
{
    public static function RealPath(string $path) : string
    {
        return str_replace('//', '/', $path);
    }

    public static function explodeKey($key)
    {
        // Remove a leading slash if one is found
        $split_key = explode('/', $key && $key[0] == '/' ? substr($key, 1) : $key);
        // Remove empty element
        $split_key = array_filter($split_key, function($var) {
            return !($var == '' || $var == null);
        });
        $final_key = implode("/", $split_key);
        if (substr($key, -1)  == '/') {
            $final_key = $final_key . '/';
        }
        return $final_key;
    }
}