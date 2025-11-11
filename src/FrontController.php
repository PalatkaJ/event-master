<?php

namespace src;

use IEventModel;

class FrontController {
    private IEventModel $eventModel;

    public function __construct(IEventModel $eventModel) {
        $this->eventModel = $eventModel;
    }

    private function generateSite(?string $eventName): void {
        require_once __DIR__ . '/../src/templates/_header.php';

        require_once __DIR__.'/../src/templates/event.php';

        require_once __DIR__ . '/../src/templates/_footer.php';
    }

    private function route($urlChunk) {
        return match ($urlChunk) {
            'events' => new EventsPresenter(),
            default => new NotFoundPresenter(),
        };
    }

    public function routeAndDispatch($serverData): void {
        $url = $serverData['REQUEST_URI'];
        $chunks = explode("/", $url);

        $presenter = $this->route($chunks[1] ?? null);

        $presenter->process(array_slice($chunks, 2), $serverData['REQUEST_METHOD'], $_POST);

        /*
        $eventId = $urlInArr[2];

        $eventName = $this->eventModel->getEventById($eventId);

        $this->generateSite($eventName);
        */
    }
}