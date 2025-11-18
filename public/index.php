<?php

define('APP_ROOT', dirname(__DIR__));
const PRESENTERS_DIR = APP_ROOT . '/src/presenters';
const MODELS_DIR = APP_ROOT . '/src/model';
const TEMPLATES_DIR = APP_ROOT . '/src/templates';

use src\FrontController;
use src\Container;

require_once APP_ROOT . '/src/FrontController.php';
require_once APP_ROOT . '/src/Container.php';

function main(): void {
    $fc = new FrontController();

    $container = new Container();
    $fc->injectContainer($container);

    $fc->routeAndDispatch($_SERVER);
}


main();