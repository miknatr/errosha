<?php

require __DIR__ . '/../bootstrap.php';

$badLogger = function () {
    $c = $d;
};

$errosha = new \Errosha\SimpleErrorHandler(__DIR__ . '/../test.log');

$a = null;
$a->something();
