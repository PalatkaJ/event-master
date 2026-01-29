<?php

namespace presenters;

use src;

require_once SRC_DIR.'/FormValidator.php';
require_once SRC_DIR.'/View.php';

abstract class BasePresenter implements PresenterInterface
{
    protected src\FormValidator $formValidator;

    private src\View $view;

    protected src\Container $container;
    protected \mysqli $mysqli;

    protected string $templateFilename;

    protected array $templateData = [];

    public function __construct() {
        $this->formValidator = new src\FormValidator();
        $this->view = new src\View();
    }

    protected function requireLogin(): array {
        $currentUser = $this->container->getLoggedUser();

        if (!isset($currentUser)) {
            throw new src\UnathorizedAccessException();
        }

        return $currentUser;
    }

    protected function setDataForRedirect(string $location): void {
        $this->templateData['location'] = $location;
        $this->templateFilename = 'redirect.php';
    }

    public abstract function process(array $url, string $requestMethod, mixed $data, mixed $files): void;

    public function render(): void {
        $this->templateData['user'] = $this->container->getLoggedUser();
        $this->view->render($this->templateFilename, $this->templateData);
    }

    public function injectContainer(src\Container $container): void {
        $this->container = $container;
        $this->mysqli = $container->getDatabase();
    }
}