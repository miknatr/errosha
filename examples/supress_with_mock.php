<?php

require __DIR__ . '/../bootstrap.php';

$errosha = new \Errosha\ErrorHandler(new \Errosha\Display\ProductionDisplay());

@unserialize('blah');
echo 'All right';
