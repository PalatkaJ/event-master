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

    private function getSafeEvent(int $id): array {
        $event = $this->eventModel->getEventById($id);

        if (!isset($event)) {
            throw new NotFoundException();
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
        foreach ($events as &$event) {
            $event['registeredWorkshops'] =
                $this->registrationModel->getUserRegisteredWorkshops($email, $event['id']);
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
            throw new \ServerException();
        }
    }

    private function createEvent(array $currentUser, mixed $data, mixed $files): void {
        $data['hero_image'] = $this->saveImage($files);
        $data['organizer'] = $currentUser;;
        $this->eventModel->createEvent($data);
    }

    private function processEventCreation(string $reqMethod, mixed $data, mixed $files): void {
        $currentUser = $this->requireLogin();

        switch ($reqMethod) {
            case 'GET':
                $this->templateFilename = 'event_create.php';
                break;
            case 'POST':
                $this->createEvent($currentUser, $data, $files);
                header("Location: " . BASE_URL . "/");
                exit;
            default:
                throw new NotFoundException();
        }
    }

    private function updateEvent(array $event, mixed $data, mixed $files): void {
        $randomName = $this->saveImage($files);

        // TODO validation
        $event['name'] = $data['name'] ?? $event['name'];
        $event['description'] = $data['description'] ?? $event['description'];
        $event['start_date'] = $data['start_date'] ?? $event['start_date'];
        $event['end_date'] = $data['end_date'] ?? $event['end_date'];
        $event['hero_image'] = $randomName ?? $event['hero_image'];

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
            throw new \UnathorizedAccessException();
        }

        $event['workshops'] = $this->eventModel->getWorkshopsForEvent($event['id']);
        switch ($reqMethod) {
            case 'GET':
                $this->templateData['event'] = $event;
                $this->templateFilename = 'event_update.php';
                break;
            case 'POST':
                $this->updateEvent($event, $data, $files);
                header("Location: " . BASE_URL . "/events/" . $event['id']);
                exit;
        }
    }

    private function processEventRegistrationGet(int $id, array $currentUser): void {
        $event = $this->getSafeEvent($id);
        $event['workshops'] = $this->eventModel->getWorkshopsForEvent($id);
        $registeredWorkshops = $this->registrationModel->getUserRegisteredWorkshops($currentUser['email'], $id);

        $this->templateData['event'] = $event;
        $this->templateData['registeredIds'] = array_column($registeredWorkshops, 'id');
        $this->templateFilename = 'event_registration.php';
    }

    private function processEventRegistration(string $id, string $reqMethod, mixed $data): void {
        $currentUser = $this->requireLogin();

        switch ($reqMethod) {
            case 'GET':
                $this->processEventRegistrationGet($id, $currentUser);
                break;
            case 'POST':
                $this->registrationModel->registerUserForEvent($currentUser['email'], $id, $data['workshops']);
                header("Location: " . BASE_URL . "/events/mine");
                exit;
            default:
                throw new NotFoundException();
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

        //$this->templateData['events'] = $events;
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
            throw new NotFoundException();
        }

        $this->requireLogin();
        if (!$this->isCurrentUserOrganizer($id)) {
            throw new \UnathorizedAccessException();
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
                throw new NotFoundException();
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