<?php

namespace presenters;

use src\Container;

abstract class BasePresenter implements IPresenter
{
    //protected string $jsonDb;

    protected \mysqli $mysqli;

    public abstract function process(array $url, string $requestMethod, mixed $data): void;

    private function renderHeader(): void {
        require_once TEMPLATES_DIR.'/_header.php';
    }

    protected abstract function renderBody(): void;

    private function renderFooter(): void {
        require_once TEMPLATES_DIR.'/_footer.php';
    }

    public function render(): void {

        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    public function injectContainer(Container $container): void {
        //$this->jsonDb = $container->getDatabase();
        $this->mysqli = $container->getDatabase();
    }
}