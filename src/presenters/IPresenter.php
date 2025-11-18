<?php

namespace presenters;

use src\Container;


class NotFoundException extends \Exception {}

interface IPresenter
{
    public function process(array $url, string $requestMethod, mixed $data): void;

    public function render(): void;

    public function injectContainer(Container $container): void;
}