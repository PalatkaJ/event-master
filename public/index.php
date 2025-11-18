<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('APP_ROOT', dirname(__DIR__));
const PRESENTERS_DIR = APP_ROOT . '/src/presenters';
const MODELS_DIR = APP_ROOT . '/src/model';
const TEMPLATES_DIR = APP_ROOT . '/src/templates';

use src\FrontController;
use src\Container;

require_once APP_ROOT . '/src/FrontController.php';
require_once APP_ROOT . '/src/Container.php';
require_once APP_ROOT . '/.config.php';

function main(): void {
    $fc = new FrontController();

    $container = new Container();
    $container->createDatabase($DB_CONFIG['host'], $DB_CONFIG['user'], $DB_CONFIG['password'], $DB_CONFIG['database']);

    $fc->injectContainer($container);
    $fc->routeAndDispatch($_SERVER);
}


main();