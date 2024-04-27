<?php
include_once 'preload.php';
include_once 'autoloader.php';
include_once 'utilities.php';

try {
    // Initialize the application.
    $initialize = new \App\Initialize();

    // Run the action and show the output.
    $initialize->action()->show();
} catch (Exception $e) {
    ob_start();
    require(ROOT . DS . 'templates' . DS . 'layouts' . DS . 'error.phtml');
    $errorPage = ob_get_contents();
    ob_end_clean();

    // Slack message about error

    echo $errorPage;
}