<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
define('APP_ROOT', dirname(__DIR__));
const PRESENTERS_DIR = APP_ROOT . '/src/presenters';
const MODELS_DIR = APP_ROOT . '/src/models';
const TEMPLATES_DIR = APP_ROOT . '/src/templates';
const TEMP_DIR = APP_ROOT . '/src/temp';
const DATA_DIR = APP_ROOT .'/public/data';

use src\FrontController;
use src\Container;

require_once APP_ROOT . '/src/FrontController.php';
require_once APP_ROOT . '/src/Container.php';
require_once APP_ROOT . '/.config.php';

function main(): void {
    global $DB_CONFIG;

    $fc = new FrontController();

    $container = new Container();
    $container->createDatabase($DB_CONFIG['host'], $DB_CONFIG['user'], $DB_CONFIG['password'], $DB_CONFIG['database']);

    $fc->injectContainer($container);
    $fc->routeAndDispatch($_SERVER);
}


main();