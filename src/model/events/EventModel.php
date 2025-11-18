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

        // Bind variables to '?' with hinting the type (string, double).
        // Function takes references, thus you must use variables.
        echo "1";
        $stmt->bind_param('i', $id);
        echo "2";
        $stmt->execute();
        echo "3";
        $query_result = $stmt->get_result();
        echo "4";
        if ($query_result) {
            // fetch associative array
            if ($row = $query_result->fetch_assoc()) {
                var_dump($row);
                return new Event($row["id"], $row["name"]);
            }
        }
        /*
        $content = json_decode(file_get_contents($this->jsonDb));

        foreach ($content as $key => $event) {
            if ($event->id === $id) {
                return new Event($event->id, $event->name);
            }
        }
        */

        return null;
    }

    public function createEvent(string $eventName): void {


        /*
        $content = json_decode(file_get_contents($this->jsonDb));

        $event = new Event($this->freeId, $eventName);
        $content[] = $event;

        file_put_contents($this->jsonDb, json_encode($content));
        $this->freeId++;
        */
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
