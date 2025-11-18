<?php

namespace events;

use mysqli;

class EventModel implements IEventModel {
    private int $freeId;
    //private string $jsonDb;

    private \mysqli $mysqli;

    public function __construct($mysqli) {
        //$this->freeId = $freeId;
        //$this->jsonDb = $jsonDb;

        if ($mysqli->connect_errno) {
            // handle error
            echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        }

        $this->mysqli = $mysqli;
    }

    public function getEventById(int $id): ?Event {

        $stmt = $this->mysqli->prepare("SELECT * FROM event WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $query_result = $stmt->get_result();

        if ($query_result) {
            if ($row = $query_result->fetch_assoc()) {
                return new Event($row["id"], $row["name"]);
            }
        }

        return null;
    }

    public function createEvent(string $eventName, string $eventStart): void {
        $stmt = $this->mysqli->prepare("INSERT INTO event (name, start) VALUES (?, ?)");
        $stmt->bind_param('ss', $eventName, $eventStart);
        $stmt->execute();
        //$query_result = $stmt->get_result();
    }

    public function __destruct() {
        $this->mysqli->close();
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
