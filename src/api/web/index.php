<?php

use core\api\Application;
use core\api\UrlManagerConfigurator;
use core\AppLoader;

$rootPath = dirname(__DIR__, 2);

$loader = require($rootPath . '/vendor/autoload.php');

require($rootPath . '/core/AppLoader.php');

$application = AppLoader::getInstance()->loadApplicationForSection('api', Application::class);

(new UrlManagerConfigurator($loader, $application))->configure();

$application->run();
