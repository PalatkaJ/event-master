<?php

namespace presenters;

use events as e;
use events\EventModel;
use Exception;

define('EVENTS_DIR', MODELS_DIR.'/events');

require_once EVENTS_DIR.'/Event.php';
require_once EVENTS_DIR.'/IEventModel.php';
require_once EVENTS_DIR.'/EventModel.php';

class EventsPresenter extends BasePresenter
{
    private e\EventModel $eventModel;

    private function processLandingPage(): void {
        $events = $this->eventModel->getNewestEvents();
        if (!isset($events)) {
            throw new NotFoundException();
        }

        $this->templateFilename = 'landing_page.php';
        $this->templateData['events'] = $events;
    }

    private function createEvent(mixed $data, mixed $files): void {
        $extension = pathinfo($files['hero_image']['name'], PATHINFO_EXTENSION);

        $randomName = uniqid('event_', true) . '.' . $extension;
        $destinationPath = DATA_DIR . '/' . $randomName;

        if (move_uploaded_file($files['hero_image']['tmp_name'], $destinationPath)) {
            $data['hero_image'] = $randomName;
            $this->eventModel->createEvent($data);
        } else {
            throw new Exception("Could not save the uploaded image.");
        }
    }

    private function processEventCreation(string $reqMethod, mixed $data, mixed $files): void {
        switch ($reqMethod) {
            case 'GET':
                $this->templateFilename = 'create_event.php';
                break;
            case 'POST':
                $this->createEvent($data, $files);
                $this->templateFilename = 'event_creation_confirmation.php';
                break;
            default:
                throw new NotFoundException();
        }
    }

    private function processEventEdit(string $id): void {

    }

    private function processEventRegistration(string $id): void {

    }

    private function processEventDetail(string $id): void {
        $event = $this->eventModel->getEventById($id);
        if (!isset($event)) {
            throw new NotFoundException();
        }

        $this->templateFilename = 'event.php';
        $this->templateData['eventName'] = $event['name'];
    }

    private function processEventSub(array $url): void {
        $id = $url[0];

        if (sizeof($url) == 1) {
            $this->processEventDetail($id);
            return;
        }

        switch ($url[1]) {
            case 'edit':
                $this->processEventEdit($id);
                break;
            case 'register':
                $this->processEventRegistration($id);
                break;
            default:
                throw new NotFoundException();
        }
    }

    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void {
        $this->templateData = [];

        if (!isset($this->eventModel)) {
            $this->eventModel = new EventModel($this->mysqli);
        }

        if (empty($url)) {
            $this->processLandingPage();
            return;
        }

        if ($url[0] == 'new') {
            $this->processEventCreation($requestMethod, $data, $files);
            return;
        }

        $this->processEventSub($url);
    }
}