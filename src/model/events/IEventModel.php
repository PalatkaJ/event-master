<?php

namespace events;

interface IEventModel {
    public function getEventById(int $id): ?array;
    public function createEvent(array $eventData): void;
}