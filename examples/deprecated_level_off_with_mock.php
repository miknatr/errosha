<?php

require __DIR__ . '/../bootstrap.php';

$errosha = new \Errosha\SimpleErrorHandler(__DIR__ . '/../test.log', false);
$errosha->setIgnoreLevels(array(E_DEPRECATED));

ereg('test', '');
echo 'All right';
