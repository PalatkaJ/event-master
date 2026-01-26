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
}