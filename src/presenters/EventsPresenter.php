<?php

namespace presenters;

use events as e;

define('EVENTS_DIR', MODELS_DIR.'/events');

require_once EVENTS_DIR.'/Event.php';
require_once EVENTS_DIR.'/IEventModel.php';
require_once EVENTS_DIR.'/EventModel.php';

class EventsPresenter extends BasePresenter
{
    private e\Event $event;

    public function process(array $url, string $requestMethod, mixed $data): void {
        $eventModel = new e\EventModel($this->mysqli);
        // TODO more logic when creating and event, deciding based on requestMethod, etc.
        $event = $eventModel->getEventById($url[0]);

        if (!isset($event)) {
            throw new NotFoundException();
        }

        $this->event = $event;
    }

    public function renderBody(): void
    {
        $eventName = $this->event->name;
        require_once TEMPLATES_DIR.'/event.php';
    }
}