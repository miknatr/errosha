<?php

require __DIR__ . '/../bootstrap.php';

$errosha = new \Errosha\ErrorHandler(new \Errosha\Display\ProductionDisplay());
$errosha->addLogger(new \Errosha\Logger\LoggerWIthErrorIn());

$a = $b;
