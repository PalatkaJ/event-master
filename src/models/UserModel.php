<?php

namespace models;

use mysqli_sql_exception;
use src;

const USER_EXISTS_CODE = 1062;

class UserModel {
    private \mysqli $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function getUserByEmail(string $email): ?array {
        $stmt = $this->mysqli->prepare("SELECT * FROM user WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $query_result = $stmt->get_result();

        if ($query_result) {
            if ($row = $query_result->fetch_assoc()) {
                return $row;
            }
        }

        return null;
    }

    public function createUser(array $userData): void {
        try {
            $sql = "INSERT INTO user (email, full_name) VALUES (?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('ss', $userData['email'], $userData['full_name']);
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === USER_EXISTS_CODE) {
                throw new src\UserAlreadyExistsException();
            }
            throw new src\ServerException();
        }
    }

    public function updateUser(string $email, string $newName): void {
        try {
            $sql = "UPDATE user SET full_name = ? WHERE email = ?";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param('ss', $newName, $email);
            $stmt->execute();

        } catch (\mysqli_sql_exception $e) {
            throw new src\ServerException();
        }
    }

    public function deleteUser(string $email): void {
        $sql = "DELETE FROM user WHERE email = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
    }
}