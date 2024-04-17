<?php

if (!function_exists('is_cli')) {
    function is_cli() {
        if ( defined('STDIN') ) {
            return true;
        }
        if ( php_sapi_name() === 'cli' ) {
            return true;
        }
        if ( array_key_exists('SHELL', $_ENV) ) {
            return true;
        }
        if ( empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
            return true;
        }
        if ( !array_key_exists('REQUEST_METHOD', $_SERVER) ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('camelize')) {
    function camelize(string $string): string
    {
        $separator = '-';
        $result = lcfirst(str_replace($separator, '', ucwords($string, $separator)));

        $separator = '_';
        return lcfirst(str_replace($separator, '', ucwords($result, $separator)));
    }
}
