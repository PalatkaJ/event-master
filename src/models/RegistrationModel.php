<?php

namespace models;

class RegistrationModel
{
    private \mysqli $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    private function deleteWorkshops(string $email, int $eventId) {
        $deleteSql = "DELETE wr FROM workshop_registration wr 
                      JOIN workshop w ON wr.workshop_id = w.id 
                      WHERE wr.user_email = ? AND w.event_id = ?";
        $delStmt = $this->mysqli->prepare($deleteSql);
        $delStmt->bind_param('si', $email, $eventId);
        $delStmt->execute();
    }

    private function registerUserToEvent(string $email, int $eventId) {
        // IGNORE prevents error if the user is already registered
        $stmt = $this->mysqli->prepare("INSERT IGNORE INTO event_registration (user_email, event_id) VALUES (?, ?)");
        $stmt->bind_param('si', $email, $eventId);
        $stmt->execute();
    }

    private function registerUserForWorkshops(string $email, array $workshopIds) {
        if (!empty($workshopIds)) {
            $stmtW = $this->mysqli->prepare("INSERT INTO workshop_registration (user_email, workshop_id) VALUES (?, ?)");
            foreach ($workshopIds as $wId) {
                $wIdInt = (int)$wId;
                $stmtW->bind_param('si', $email, $wIdInt);
                $stmtW->execute();
            }
        }
    }

    public function registerUserForEvent(string $email, int $eventId, array $workshopIds): void {
        $this->mysqli->begin_transaction();
        try {
            $this->registerUserToEvent($email, $eventId);
            $this->deleteWorkshops($email, $eventId);
            $this->registerUserForWorkshops($email, $workshopIds);

            $this->mysqli->commit();
        } catch (\Exception $e) {
            $this->mysqli->rollback();
            throw $e;
        }
    }

    public function getUserRegisteredWorkshops(string $email, int $eventId): array {
        $sql = "SELECT w.id, w.name 
            FROM workshop w
            JOIN workshop_registration wr ON w.id = wr.workshop_id
            WHERE wr.user_email = ? AND w.event_id = ?";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('si', $email, $eventId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}