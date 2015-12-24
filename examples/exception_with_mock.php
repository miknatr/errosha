<?php

require __DIR__ . '/../bootstrap.php';

$errosha = new \Errosha\SimpleErrorHandler(__DIR__ . '/../test.log', false);

throw new \Exception('Bad thing happened');
