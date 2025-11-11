<?php

namespace public;

use EventModel;
use src\FrontController;

function main() {
    $modelDir = __DIR__.'/../src/model';

    require_once $modelDir.'/EventModel.php';
    $eventModel = new EventModel($modelDir.'/db.json');


    $appFrontController = new FrontController($eventModel);
    $appFrontController->routeAndDispatch($_SERVER);
}


main();