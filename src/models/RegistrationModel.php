<?php

namespace models;

class RegistrationModel
{
    private \mysqli $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function registerUserForEvent(string $email, int $eventId, array $workshopIds): void {
        $this->mysqli->begin_transaction();
        try {
            $stmt = $this->mysqli->prepare("INSERT INTO event_registration (user_email, event_id) VALUES (?, ?)");
            $stmt->bind_param('si', $email, $eventId);
            $stmt->execute();

            if (!empty($workshopIds)) {
                $stmtW = $this->mysqli->prepare("INSERT INTO workshop_registration (user_email, workshop_id) VALUES (?, ?)");
                foreach ($workshopIds as $wId) {
                    $stmtW->bind_param('si', $email, $wId);
                    $stmtW->execute();
                }
            }

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