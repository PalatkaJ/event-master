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

    public function __destruct() {
        $this->mysqli->close();
    }
}