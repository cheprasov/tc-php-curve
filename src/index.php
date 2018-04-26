<?php

include(__DIR__ . '/autoloader.php');
\TC\Curve\Config\Config::init();

$App = new \TC\Curve\App\App();
$App->run();
