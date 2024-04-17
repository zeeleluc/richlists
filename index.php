<?php
include_once 'preload.php';
include_once 'autoloader.php';
include_once 'utilities.php';

if (is_cli()) {
    try {
        if (!isset($argv[0]) && !isset($argv[1])) {
            throw new Exception('Missing required parameters');
        }

        // validate action
        $action = camelize($argv[1]);

        // implement cli actions

    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
} else {
    // todo
}
