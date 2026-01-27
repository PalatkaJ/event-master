<?php

class NotFoundException extends \Exception {
    public function __construct() {
        $this->message = "Not Found";
        $this->code = 404;
    }
}
class ServerException extends \Exception {
    public function __construct() {
        $this->message = "Internal Server Error";
        $this->code = 500;
    }
}

class UnathorizedAccessException extends \Exception {
    public function __construct() {
        $this->message = "Unauthorized Access";
        $this->code = 403;
    }
}

class UserAlreadyExistsException extends \Exception {}