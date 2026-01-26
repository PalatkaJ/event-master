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

    private function insertEvent(array $eventData) {
        $sql = "INSERT INTO event (name, description, start_date, end_date, hero_img, organizer) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('ssssss',
            $eventData['name'],
            $eventData['description'],
            $eventData['start_date'],
            $eventData['end_date'],
            $eventData['hero_image'],
            $eventData['organizer']['email']
        );
        $stmt->execute();
    }

    private function insertWorkshops(int $eventId, array $workshopNames) {
        $workshopSql = "INSERT INTO workshop (event_id, name) VALUES (?, ?)";
        $workshopStmt = $this->mysqli->prepare($workshopSql);

        foreach ($workshopNames as $name) {
            $name = trim($name);
            if (empty($name)) continue;

            $workshopStmt->bind_param('is', $eventId, $name);
            $workshopStmt->execute();
        }
    }

    public function createEvent(array $eventData, array $workshopNames): void {
        $this->mysqli->begin_transaction();

        try {
            $this->insertEvent($eventData);

            $eventId = $this->mysqli->insert_id;

            $this->insertWorkshops($eventId, $workshopNames);

            $this->mysqli->commit();

        } catch (\mysqli_sql_exception $e) {
            $this->mysqli->rollback();
            throw new \ServerException("Failed to create event and workshops: " . $e->getMessage());
        }
    }

    public function getNewestEvents(int $limit = 3): array {
        $sql = "SELECT * FROM event ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllEvents(): array {
        $sql = "SELECT * FROM event ORDER BY created_at DESC";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}