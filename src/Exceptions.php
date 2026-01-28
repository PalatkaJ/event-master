<?php

namespace src;

class NotFoundException extends \Exception {
    public function __construct(string $msg="") {
        parent::__construct();
        $this->message = "Not Found: ".$msg;
        $this->code = 404;
    }
}
class ServerException extends \Exception {
    public function __construct(string $msg="") {
        parent::__construct();
        $this->message = "Internal Server Error: ".$msg;
        $this->code = 500;
    }
}

class UnathorizedAccessException extends \Exception {
    public function __construct(string $msg="") {
        parent::__construct();
        $this->message = "Unauthorized Access: ".$msg;
        $this->code = 403;
    }
}

class UserAlreadyExistsException extends \Exception {}