<?php

require __DIR__ . '/../bootstrap.php';

$badLogger = function () {
    $c = $d;
};

$errosha = new \Errosha\SimpleErrorHandler(__DIR__ . '/../test.log', false);

$a = null;
$a->something();
