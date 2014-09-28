<?php

error_reporting(-1);
ini_set("display_errors", true);

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add('Errosha\\', __DIR__ . '/tests');
