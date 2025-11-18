<?php

namespace events;

use mysqli;

class EventModel implements IEventModel {
    private \mysqli $mysqli;

    public function __construct($mysqli) {
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
}