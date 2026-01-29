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
require_once APP_ROOT . '/.config.php';

function main($config): void {


    $fc = new FrontController();

    $container = new Container();
    $container->createDatabase($config['host'], $config['user'], $config['password'], $config['database']);

    $fc->injectContainer($container);
    $fc->routeAndDispatch($_SERVER);
}


main($DB_CONFIG);