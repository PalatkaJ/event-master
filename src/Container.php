<?php

namespace src;

class Container
{
    public function getDatabase() {
        $host = 'localhost';
        $user = '81112441';
        $password = '1vSMAzyH';
        $database = 'stud_81112441';
        return new \mysqli($host, $user, $password, $database);
        //return APP_ROOT.'/src/db.json';
    }
}