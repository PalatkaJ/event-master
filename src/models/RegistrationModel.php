<?php

namespace models;

class RegistrationModel
{
    private \mysqli $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    private function deleteWorkshops(string $email, int $eventId) {
        $deleteSql = "DELETE FROM workshop_registration WHERE user_email = ? AND event_id = ?";
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

    private function registerUserForWorkshops(string $email, array $workshopIds, int $eventId) {
        if (!empty($workshopIds)) {
            $stmtW = $this->mysqli->prepare("INSERT INTO workshop_registration (user_email, workshop_id, event_id) VALUES (?, ?, ?)");
            foreach ($workshopIds as $wId) {
                $wIdInt = (int)$wId;
                $stmtW->bind_param('sii', $email, $wIdInt, $eventId);
                $stmtW->execute();
            }
        }
    }

    public function registerUserForEvent(string $email, int $eventId, array $workshopIds): void {
        $this->mysqli->begin_transaction();
        try {
            $this->registerUserToEvent($email, $eventId);
            $this->deleteWorkshops($email, $eventId);
            $this->registerUserForWorkshops($email, $workshopIds, $eventId);

            $this->mysqli->commit();
        } catch (\Exception $e) {
            $this->mysqli->rollback();
            throw new \ServerException();
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

    public function cancelUsersRegistration(string $email, int $eventId): void {
        $sql = "DELETE er FROM event_registration er where er.user_email = ? and er.event_id = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('si', $email, $eventId);
        $stmt->execute();
    }
}