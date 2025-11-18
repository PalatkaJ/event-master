<?php

namespace events;

interface IEventModel {
    public function getEventById(int $id): ?Event;
    public function createEvent(string $eventName, string $eventStart): void;
}