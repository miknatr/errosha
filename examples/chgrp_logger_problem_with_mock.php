<?php

require __DIR__ . '/../bootstrap.php';

$errosha = new \Errosha\SimpleErrorHandler(__DIR__ . '/../test.log', false);
//$errosha->setLogChmod(0100);
$errosha->setLogChgrp('root');

$a = $b;
