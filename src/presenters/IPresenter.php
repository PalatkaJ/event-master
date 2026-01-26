<?php

namespace presenters;

use src\Container;

interface IPresenter
{
    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void;

    public function render(): void;

    public function injectContainer(Container $container): void;
}