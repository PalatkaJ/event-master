<?php

namespace presenters;

use Cassandra\Exception\UnauthorizedException;
use NotFoundException;
use src\Container;

require_once __DIR__.'/Templator.php';

abstract class BasePresenter implements IPresenter
{
    private Templator $templator;

    protected Container $container;
    protected \mysqli $mysqli;

    protected string $templateFilename;

    protected array $templateData = [];

    public function __construct() {
        $this->templator = new Templator();
    }

    protected function requireLogin(): array {
        $currentUser = $this->container->getLoggedUser();

        if (!isset($currentUser)) {
            throw new \UnathorizedAccessException();
        }

        return $currentUser;
    }

    public abstract function process(array $url, string $requestMethod, mixed $data, mixed $files): void;

    private function getPathTemplateFilename(string $filename): string {
        return TEMPLATES_DIR . '/' . $filename;
    }

    private function getCompiledTemplateFilename(string $filename): string {
        return TEMP_DIR . '/' . $filename;
    }

    private function tryCompileTemplate(string $filename):void {
        try {
            $this->templator->loadTemplate($this->getPathTemplateFilename($filename));
            $this->templator->compileAndSave($this->getCompiledTemplateFilename($filename));
        } catch (\Exception $e) {
            echo "Template Error: " . $e->getMessage();
            exit(1);
        }
    }

    private function renderWithDataExtraction(string $filename): void {
        $this->templateData['user'] = $this->container->getLoggedUser();
        $this->tryCompileTemplate($filename);

        extract($this->templateData);
        require_once $this->getCompiledTemplateFilename($filename);
    }

    public function render(): void {
        $this->renderWithDataExtraction('_header.php');
        $this->renderWithDataExtraction($this->templateFilename);
        $this->renderWithDataExtraction('_footer.php');
    }

    public function injectContainer(Container $container): void {
        $this->container = $container;
        $this->mysqli = $container->getDatabase();
    }
}