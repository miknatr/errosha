<?php

require __DIR__ . '/../bootstrap.php';

$errosha = new \Errosha\ErrorHandler(new \Errosha\Display\TextDisplay());
$errosha->addLogger(new \Errosha\Logger\FileLogger(__DIR__ . '/../test.log'));

ereg('test', '');
