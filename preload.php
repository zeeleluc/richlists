<?php
session_start();

error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
ini_set('display_errors', 'On');

const DS = DIRECTORY_SEPARATOR;
const ROOT = __DIR__;
