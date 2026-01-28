<?php

namespace presenters;

use models as m;
use src;

require_once MODELS_DIR.'/EventModel.php';
require_once MODELS_DIR.'/RegistrationModel.php';
require_once MODELS_DIR.'/UserModel.php';

class EventsPresenter extends BasePresenter
{
    private m\EventModel $eventModel;
    private m\UserModel $userModel;

    private m\RegistrationModel $registrationModel;

    private function getSafeEvent(int $id): array {
        $event = $this->eventModel->getEventById($id);

        if (!isset($event)) {
            throw new src\NotFoundException();
        }

        return $event;
    }

    private function isCurrentUserOrganizer(int $eventId): bool {
        $event = $this->getSafeEvent($eventId);
        $currentUser = $this->container->getLoggedUser();

        if (!isset($currentUser)) {
            return false;
        }

        return $event['organizer'] === $currentUser['email'];
    }

    private function addOrganizerToEvents(array &$events): void {
        foreach ($events as &$event) {
            $event['organizer'] = $this->userModel->getUserByEmail($event['organizer']);
        }
    }

    private function addUserWorkshopsToEvents(string $email, array &$events): void {
        $events = array_filter($events, function($event) use ($email) {
            $registeredWorkshops = $this->registrationModel->getUserRegisteredWorkshops($email, $event['id']);

            if (empty($registeredWorkshops)) {
                $this->eventModel->removeUserFromEvent($email, $event['id']);
                return false;
            }

            return true;
        });

        foreach ($events as &$event) {
            $event['registeredWorkshops'] = $this->registrationModel->getUserRegisteredWorkshops($email, $event['id']);
        }
    }

    private function processLandingPage(): void {
        $events = $this->eventModel->getNewestEvents();
        $this->addOrganizerToEvents($events);

        $this->templateFilename = 'landing_page.php';
        $this->templateData['events'] = $events;
    }

    private function saveImage(array $files): ?string {
        if (!isset($files['hero_image']) || $files['hero_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extension = pathinfo($files['hero_image']['name'], PATHINFO_EXTENSION);

        $randomName = uniqid('event_', true) . '.' . $extension;
        $destinationPath = DATA_DIR . '/' . $randomName;

        if (move_uploaded_file($files['hero_image']['tmp_name'], $destinationPath)) {
            return $randomName;
        } else {
            throw new src\ServerException("unable to save image");
        }
    }

    private function isEventFormValid(array $event, array $data, mixed $files): bool {
        $this->formValidator->validateEvent($event, $data, $files);
        return $this->formValidator->isValid();
    }

    private function createEvent(array $currentUser, mixed $data, mixed $files): void {
        $data['hero_image'] = $this->saveImage($files);
        $data['organizer'] = $currentUser;;
        $this->eventModel->createEvent($data);
    }

    private function processEventCreation(string $reqMethod, mixed $data, mixed $files): void {
        $currentUser = $this->requireLogin();
        $this->templateFilename = 'event_create.php';

        switch ($reqMethod) {
            case 'GET':
                break;
            case 'POST':
                if (!$this->isEventFormValid($data, $data, $files)) {
                    $this->templateData['errors'] = $this->formValidator->getErrors();
                    return;
                }
                $this->createEvent($currentUser, $data, $files);
                header("Location: " . BASE_URL . "/");
                exit;
            default:
                throw new src\NotFoundException();
        }
    }

    private function parseDataToEvent(array $data, array $files, array &$event): void {
        $randomName = $this->saveImage($files);

        $event['name'] = $data['name'] ?? $event['name'];
        $event['description'] = $data['description'] ?? $event['description'];
        $event['start_date'] = $data['start_date'] ?? $event['start_date'];
        $event['end_date'] = $data['end_date'] ?? $event['end_date'];
        $event['hero_image'] = $randomName ?? $event['hero_image'];
    }

    private function updateEvent(array $event, mixed $data): void {
        $newWorkshops = $data['workshops'] ?? [];
        $currentWorkshops = array_column($event['workshops'], 'name');
        $workshopsToAdd = array_diff($newWorkshops, $currentWorkshops);
        $workshopsToRemove = array_diff($currentWorkshops, $newWorkshops);

        $this->eventModel->updateEvent($event, $workshopsToAdd, $workshopsToRemove);
    }

    private function processEventEdit(int $id, string $reqMethod, array $data, array $files): void {
        $this->requireLogin();
        $event = $this->getSafeEvent($id);

        if (!$this->isCurrentUserOrganizer($id)) {
            throw new src\UnathorizedAccessException();
        }

        $event['workshops'] = $this->eventModel->getWorkshopsForEvent($event['id']);
        $this->templateData['event'] = $event;
        $this->templateFilename = 'event_update.php';

        switch ($reqMethod) {
            case 'GET':
                break;
            case 'POST':
                $this->parseDataToEvent($data, $files, $event);
                if (!$this->isEventFormValid($event, $data, $files)) {
                    $this->templateData['errors'] = $this->formValidator->getErrors();
                    return;
                }

                $this->updateEvent($event, $data);
                header("Location: " . BASE_URL . "/events/" . $event['id']);
                exit;
        }
    }

    private function areWorkshopsValid(array $data): bool {
        $this->formValidator->validateWorkshops($data);
        return $this->formValidator->isValid();
    }

    private function processEventRegistration(string $id, string $reqMethod, mixed $data): void {
        $currentUser = $this->requireLogin();
        $event = $this->getSafeEvent($id);

        $event['workshops'] = $this->eventModel->getWorkshopsForEvent($id);
        $registeredWorkshops = $this->registrationModel->getUserRegisteredWorkshops($currentUser['email'], $id);

        $this->templateData['event'] = $event;
        $this->templateData['registeredIds'] = array_column($registeredWorkshops, 'id');
        $this->templateFilename = 'event_registration.php';

        switch ($reqMethod) {
            case 'GET':
                break;
            case 'POST':
                if (!$this->areWorkshopsValid($data)) {
                    $this->templateData['errors'] = $this->formValidator->getErrors();
                    return;
                }

                $this->registrationModel->registerUserForEvent($currentUser['email'], $id, $data['workshops']);
                header("Location: " . BASE_URL . "/events/mine");
                exit;
            default:
                throw new src\NotFoundException();
        }
    }

    private function isCurrentUserRegistered(int $eventId): bool {
        $currentUser = $this->container->getLoggedUser();

        if (!isset($currentUser)) {
            return false;
        }

        $currentUsersEvents = $this->eventModel->getAllEventsUsers($currentUser['email']);
        foreach ($currentUsersEvents as $e) {
            if ($e['id'] == $eventId) {
                return true;
            }
        }

        return false;
    }

    private function processEventDetail(int $id): void {
        $event = $this->getSafeEvent($id);

        $event['workshops'] = $this->eventModel->getWorkshopsForEvent($id);

        $this->templateData['isOwner'] = $this->isCurrentUserOrganizer($id);
        $this->templateData['isRegistered'] = $this->isCurrentUserRegistered($id);
        $this->templateData['event'] = $event;
        $this->templateFilename = 'event_detail.php';
    }

    private function processEventsAll() {
        $events = $this->eventModel->getAllEvents();
        $this->addOrganizerToEvents($events);

        $this->templateData['events_json'] = json_encode($events);
        $this->templateFilename = 'all_events.php';
    }

    private function processEventsAllUsers(): void {
        $currentUser = $this->requireLogin();
        $events = $this->eventModel->getAllEventsUsers($currentUser['email']);

        $this->addUserWorkshopsToEvents($currentUser['email'], $events);

        $this->templateData['events'] = $events;
        $this->templateFilename = 'users_events.php';
    }

    private function processEventDeletion(int $id, string $reqMethod): void {
        if ($reqMethod !== 'POST') {
            throw new src\NotFoundException();
        }

        $this->requireLogin();
        if (!$this->isCurrentUserOrganizer($id)) {
            throw new src\UnathorizedAccessException();
        }

        $this->eventModel->deleteEvent($id);
        header("Location: " . BASE_URL . "/");
        exit;
    }

    private function processRegistrationCancel(int $eventId): void {
        $currentUser = $this->requireLogin();
        $event = $this->getSafeEvent($eventId);

        $this->registrationModel->cancelUsersRegistration($currentUser['email'], $eventId);

        header("Location: " . BASE_URL . "/events/" . $event['id']);
        exit;
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
                $this->processEventEdit($id, $reqMethod, $data, $files);
                break;
            case 'register':
                $this->processEventRegistration($id, $reqMethod, $data);
                break;
            case 'delete':
                $this->processEventDeletion($id, $reqMethod);
                break;
            case 'cancel':
                $this->processRegistrationCancel($id);
                break;
            default:
                throw new src\NotFoundException();
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