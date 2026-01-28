<?php

namespace models;

use src;

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

    public function removeUserFromEvent(string $email, int $id) {
        $sql = "DELETE FROM event_registration WHERE user_email=? AND event_id=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('si', $email, $id);
        $stmt->execute();
    }

    private function insertEvent(array $eventData) {
        $sql = "INSERT INTO event (name, description, start_date, end_date, hero_image, organizer) VALUES (?, ?, ?, ?, ?, ?)";
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

    public function createEvent(array $eventData): void {
        $this->mysqli->begin_transaction();

        try {
            $this->insertEvent($eventData);

            $eventId = $this->mysqli->insert_id;

            $this->insertWorkshops($eventId, $eventData['workshops']);

            $this->mysqli->commit();

        } catch (\mysqli_sql_exception $e) {
            $this->mysqli->rollback();
            throw new src\ServerException("unable to create event: ".$e->getMessage());
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

    public function getAllEventsUsers(mixed $email): array {
        $sql = "SELECT e.* FROM event e JOIN event_registration er ON e.id = er.event_id WHERE er.user_email = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getWorkshopsForEvent(int $eventId): array {
        $sql = "SELECT * FROM workshop WHERE event_id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $eventId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function updateEventMain(array $eventData): void {
        $sql = "UPDATE event SET name = ?, description = ?, start_date = ?, end_date = ?, hero_image = ? WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('sssssi',
            $eventData['name'],
            $eventData['description'],
            $eventData['start_date'],
            $eventData['end_date'],
            $eventData['hero_image'],
            $eventData['id']
        );
        $stmt->execute();
    }

    private function removeWorkshops(int $eventId, array $workshopsToRemove): void {
        $sql = "DELETE FROM workshop WHERE name = ? AND event_id = ?";
        $stmt = $this->mysqli->prepare($sql);

        foreach ($workshopsToRemove as $name) {
            $name = trim($name);
            $stmt->bind_param('si', $name, $eventId);
            $stmt->execute();
        }
    }

    public function updateEvent(array $eventData, array $workshopsToAdd, array $workshopsToRemove): void {
        $this->mysqli->begin_transaction();

        try {
            $this->updateEventMain($eventData);
            $this->insertWorkshops($eventData['id'], $workshopsToAdd);
            $this->removeWorkshops($eventData['id'], $workshopsToRemove);

            $this->mysqli->commit();
        } catch (\mysqli_sql_exception $e) {
            $this->mysqli->rollback();
            throw new src\ServerException();
        }
    }
    public function deleteEvent(int $eventId): void {
        $sql = "DELETE FROM event WHERE id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $eventId);
        $stmt->execute();
    }
}