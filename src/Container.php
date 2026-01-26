<?php

namespace src;

class Container
{
    private \mysqli $mysqli;

    public function createDatabase($host, $user, $password, $database) {
        $this->mysqli = new \mysqli($host, $user, $password, $database);

        if ($this->mysqli->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->mysqli->connect_error;
        }
    }

    public function getDatabase() {
        return $this->mysqli;
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_email']);
    }

    public function getLoggedUser(): ?array {
        if (isset($_SESSION['user_email'])) {
            return [
                'email' => $_SESSION['user_email'],
                'full_name' => $_SESSION['user_full_name']
            ];
        }

        return null;
    }

    public function loginUser(string $email, string $fullName): void {
        $_SESSION['user_email'] = $email;
        $_SESSION['user_full_name'] = $fullName;
    }

    public function logoutUser(): void {
        unset($_SESSION['user_email']);
        unset($_SESSION['user_full_name']);
    }

    public function __destruct() {
        $this->mysqli->close();
    }
}