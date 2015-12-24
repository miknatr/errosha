<?php

require __DIR__ . '/../bootstrap.php';

$errosha = new \Errosha\SimpleErrorHandler(__DIR__ . '/../test.log');

$a = null;
$a->something();
