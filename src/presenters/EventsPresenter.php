<?php

namespace presenters;

use models as m;
use NotFoundException;

require_once MODELS_DIR.'/EventModel.php';
require_once MODELS_DIR.'/RegistrationModel.php';
require_once MODELS_DIR.'/UserModel.php';

class EventsPresenter extends BasePresenter
{
    private m\EventModel $eventModel;
    private m\UserModel $userModel;

    private m\RegistrationModel $registrationModel;

    private function addOrganizerToEvents(array &$events): void {
        foreach ($events as &$event) {
            $event['organizer'] = $this->userModel->getUserByEmail($event['organizer']);
        }
    }

    private function processLandingPage(): void {
        $events = $this->eventModel->getNewestEvents();
        if (!isset($events)) {
            throw new NotFoundException();
        }

        $this->addOrganizerToEvents($events);

        $this->templateFilename = 'landing_page.php';
        $this->templateData['events'] = $events;
    }

    private function createEvent(mixed $data, mixed $files): void {
        $extension = pathinfo($files['hero_image']['name'], PATHINFO_EXTENSION);

        $randomName = uniqid('event_', true) . '.' . $extension;
        $destinationPath = DATA_DIR . '/' . $randomName;

        if (move_uploaded_file($files['hero_image']['tmp_name'], $destinationPath)) {
            $data['hero_image'] = $randomName;
            $data['organizer'] = $this->container->getLoggedUser();
            $this->eventModel->createEvent($data, $data['workshops']);
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
                header("Location: " . BASE_URL . "/events");
                exit;
            default:
                throw new NotFoundException("invalid method");
        }
    }

    private function processEventEdit(string $id): void {
        // TODO
        $this->templateFilename = 'event_update.php';
    }

    private function processEventRegistration(string $id, string $reqMethod, mixed $data): void {
        switch ($reqMethod) {
            case 'GET':
                $this->templateFilename = 'event_registration.php';
                break;
            case 'POST':
                $currentUser = $this->container->getLoggedUser();
                $this->registrationModel->registerUserForEvent($currentUser['email'], $id, $data['workshops']);
                header("Location: " . BASE_URL . "/events/list");
                exit;
            default:
                throw new NotFoundException("invalid method");
        }
    }

    private function processEventDetail(string $id): void {
        $event = $this->eventModel->getEventById($id);
        if (!isset($event)) {
            throw new NotFoundException("event not found");
        }

        $event['workshops'] = $this->eventModel->getWorkshopsForEvent($event['id']);
        $isOwner = false;
        $isRegistered = false;

        $currentUser = $this->container->getLoggedUser();

        if (isset($currentUser)) {
            if ($event['organizer'] == $currentUser['email']) {
                $isOwner = true;
            }

            $currentUsersEvents = $this->eventModel->getAllEventsUsers($currentUser['email']);
            foreach ($currentUsersEvents as $e) {
                if ($e['id'] == $event['id']) {
                    $isRegistered = true;
                }
            }
        }

        $this->templateData['isOwner'] = $isOwner;
        $this->templateData['isRegistered'] = $isRegistered;
        $this->templateData['event'] = $event;
        $this->templateFilename = 'event_detail.php';
    }

    private function processEventsAll() {
        $events = $this->eventModel->getAllEvents();

        $this->addOrganizerToEvents($events);

        $this->templateData['events'] = $events;
        $this->templateFilename = 'all_events.php';
    }

    private function processEventsAllUsers(): void {
        $email = $this->container->getLoggedUser()['email'];
        $events = $this->eventModel->getAllEventsUsers($email);

        $this->templateData['events'] = $events;
        $this->templateFilename = 'users_events.php';
    }

    private function processEventSub(array $url, string $reqMethod, mixed $data, mixed $files): void {
        // base/events/new or mine or ...
        if (!is_numeric($url[0])) {
            switch ($url[0]) {
                case 'new':
                    $this->processEventCreation($reqMethod, $data, $files);
                    break;
                case 'mine':
                    $this->processEventsAllUsers();
                    break;
            }
            return;
        }

        // base/events/id/...
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
                $this->processEventRegistration($id, $reqMethod, $data);
                break;
            default:
                throw new NotFoundException("invalid url");
        }
    }

    private function ensureModelsCreated(): void {
        if (!isset($this->eventModel)) {
            $this->eventModel = new m\EventModel($this->mysqli);
        }
        if (!isset($this->userModel)) {
            $this->userModel = new m\UserModel($this->mysqli);
        }
        if (!isset($this->registrationModel)) {
            $this->registrationModel = new m\RegistrationModel($this->mysqli);
        }
    }

    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void {
        $this->templateData = [];

        $this->ensureModelsCreated();

        // base/
        if (empty($url)) {
            $this->processLandingPage();
            return;
        }

        // base/events
        if (sizeof($url) == 1) {
            $this->processEventsAll();
            return;
        }

        // base/events/...
        $this->processEventSub(array_slice($url, 1), $requestMethod, $data, $files);
    }
}