<?php

class Event {
    public int $id;
    public string $name;

    public function __construct(int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }
}

interface IEventModel {
    public function getEventById(int $id): ?string;
    public function createEvent(string $eventName): void;
}

class EventModel implements IEventModel {
    private int $freeId;
    private string $jsonDb;

    public function __construct(string $jsonDb, int $freeId = 0) {
        $this->freeId = $freeId;
        $this->jsonDb = $jsonDb;
    }

    public function getEventById(int $id): ?string {
        $content = json_decode(file_get_contents($this->jsonDb));

        foreach ($content as $key => $event) {
            // var_dump($event);
            if ($event->id === $id) return $event->name;
        }

        return null;
    }

    public function createEvent(string $eventName): void {
        $content = json_decode(file_get_contents($this->jsonDb));

        $event = new Event($this->freeId, $eventName);
        $content[] = $event;

        file_put_contents($this->jsonDb, json_encode($content));
        $this->freeId++;
    }

}

function test() {
    $eventModel = new EventModel("db.json");
    /*
    $eventModel->createEvent("e1");
    $eventModel->createEvent("e2");
    $eventModel->createEvent("e3");
    $eventModel->createEvent("e4");
    $eventModel->createEvent("e5");
    */

    $e = $eventModel->getEventById(3);

    echo $e->name;
}
