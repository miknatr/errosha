<?php

require __DIR__ . '/../bootstrap.php';

$badLogger = function () {
    $c = $d;
};

$errosha = new \Errosha\SimpleErrorHandler($badLogger, false);

$a = $b;
