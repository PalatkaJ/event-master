<?php

namespace src;

class Container
{
    public function getDatabase() {
        return APP_ROOT.'/src/db.json';
    }
}