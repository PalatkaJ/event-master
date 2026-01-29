<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

define('BASE_URL', rtrim(str_replace('/public', '', dirname($_SERVER['SCRIPT_NAME'])), '/'));
define('APP_ROOT', dirname(__DIR__));

const SRC_DIR = APP_ROOT.'/src';
const PRESENTERS_DIR = SRC_DIR . '/presenters';
const MODELS_DIR = SRC_DIR . '/models';
const TEMPLATES_DIR = SRC_DIR . '/templates';
const TEMP_DIR = SRC_DIR . '/temp';
const DATA_DIR = APP_ROOT .'/public/data';

use src\FrontController;
use src\Container;

require_once APP_ROOT . '/src/FrontController.php';
require_once APP_ROOT . '/src/Container.php';

function main(): void {
    $db_config = json_decode(file_get_contents(APP_ROOT.'/config.json'), true);

    $fc = new FrontController();

    $container = new Container();
    $container->createDatabase($db_config['host'], $db_config['user'], $db_config['password'], $db_config['database']);

    $fc->injectContainer($container);
    $fc->routeAndDispatch($_SERVER);
}


main();