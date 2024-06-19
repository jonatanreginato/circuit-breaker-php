<?php

declare(strict_types=1);

date_default_timezone_set('Etc/GMT+3');
chdir(dirname(realpath(__DIR__)));

// Setup auto-loading
require 'vendor/autoload.php';
require __DIR__ . $_SERVER['REQUEST_URI'] . '.php';
