<?php

namespace presenters;

use models\EventModel as EventModel;
use Exception;
use NotFoundException;

require_once MODELS_DIR.'/EventModel.php';

class EventsPresenter extends BasePresenter
{
    private EventModel $eventModel;

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
            throw new \ServerException("Could not save the uploaded image.");
        }
    }

    private function processEventCreation(string $reqMethod, mixed $data, mixed $files): void {
        switch ($reqMethod) {
            case 'GET':
                $this->templateFilename = 'event_create.php';
                break;
            case 'POST':
                $this->createEvent($data, $files);
                header("Location: " . BASE_URL . "/events/list");
                exit;
            default:
                throw new NotFoundException("invalid method");
        }
    }

    private function processEventEdit(string $id): void {

        $this->templateFilename = 'event_update.php';
    }

    private function processEventRegistration(string $id): void {

        $this->templateFilename = 'event_registration.php';
    }

    private function processEventDetail(string $id): void {
        $event = $this->eventModel->getEventById($id);
        if (!isset($event)) {
            throw new NotFoundException("event not found");
        }

        $this->templateFilename = 'event_detail.php';
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
                throw new NotFoundException("invalid url");
        }
    }

    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void {
        $this->templateData = [];
        $url = array_slice($url, 1);

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