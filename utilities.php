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

/**
 * return null|string
 */
if (!function_exists('env')) {
    function env (string $key)
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        if (!array_key_exists($key, $_ENV)) {
            return null;
        }

        return $_ENV[$key];
    }
}

if (!function_exists('abort')) {
    function abort(string $message = null, string $type = 'success') {

        if ($message) {
            $session = new \App\Session();
            $session->setSession('alert', [
                'message' => $message,
                'type' => $type,
            ]);
        }

        header('Location: /');
        exit;
    }
}

if (!function_exists('generate_token')) {
    function generate_token() {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $token = '';
        for ($i = 0; $i < 24; $i++) {
            $randomIndex = rand(0, $charactersLength - 1);
            $token .= $characters[$randomIndex];
        }

        return $token;
    }
}
