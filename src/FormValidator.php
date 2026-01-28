<?php

namespace src;

use DateTime;

const NAME_LEN = 64;
const DESC_LEN = 1024;

class FormValidator {
    private array $errors = [];

    public function getErrors(): array {
        return $this->errors;
    }

    public function isValid(): bool {
        return empty($this->errors);
    }

    public function validateEventName(string $name) {
        if (empty(trim($name ?? '')) || strlen($name) > NAME_LEN) {
            $this->errors['name'] = "Name must be between 1 and ".NAME_LEN." characters.";
        }
    }

    public function validateEventDescription(string $description) {
        if (empty(trim($description ?? '')) || strlen($description) > DESC_LEN) {
            $this->errors['description'] = "Name must be between 1 and ".DESC_LEN." characters.";
        }
    }

    public function validateDates(array $data) {
        $today = new DateTime('today');
        $start = !empty($data['start_date']) ? new DateTime($data['start_date']) : null;
        $end = !empty($data['end_date']) ? new DateTime($data['end_date']) : null;

        if (!$start || $start <= $today) {
            $this->errors['start_date'] = "Start date must be in the future.";
        }

        if (!$end || ($start && $end < $start)) {
            $this->errors['end_date'] = "End date must be equal to or later than start date.";
        }
    }

    public function validateImage(array $data) {
        if ($data['hero_image'] === null) {
            $this->errors['hero_image'] = "Hero image is required.";
        }
    }

    private function validateWorkshopName(string $wName) {
        if (empty(trim($wName)) || strlen($wName) > NAME_LEN) {
            $this->errors['workshops'] = "Workshop names must be 1-".NAME_LEN." characters.";
        }
    }

    public function validateWorkshops(array $workshops) {
        if (empty($workshops) || !is_array($workshops)) {
            $this->errors['workshops'] = "At least one workshop is required.";
        } else {
            foreach ($workshops as $wName) {
                $this->validateWorkshopName($wName);
            }
        }
    }

    public function validateEvent(array $data, array $workshops): void {
        $this->validateEventName($data['name']);
        $this->validateEventDescription($data['description']);
        $this->validateDates($data);

        $this->validateImage($data);

        $this->validateWorkshops($workshops);
    }


    private function validateFullName(array $data) {
        if (empty(trim($data['full_name'] ?? ''))) {
            $this->errors['full_name'] = "Full name is required.";
        }
    }

    public function validateEmail(array $data) {
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "A valid e-mail address is required.";
        }
    }

    public function validateUser(array $data): void {
        $this->validateFullName($data);
        $this->validateEmail($data);
    }
}