<?php

namespace models;

use mysqli;
use mysqli_sql_exception;

class EventModel {
    private \mysqli $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getEventById(int $id): ?array {
        $stmt = $this->mysqli->prepare("SELECT * FROM event WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $query_result = $stmt->get_result();

        if ($query_result) {
            if ($row = $query_result->fetch_assoc()) {
                return $row;
            }
        }

        return null;
    }

    public function createEvent(array $eventData): void {
        try {
            $sql = "INSERT INTO event (name, description, start_date, end_date, hero_img) VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('sssss', $eventData['name'], $eventData['description'],
                $eventData['start_date'], $eventData['end_date'], $eventData['hero_image']);
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            throw new \ServerException("Database error: " . $e->getMessage());
        }
    }

    public function getNewestEvents(int $limit = 3): array {
        $sql = "SELECT * FROM event ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}