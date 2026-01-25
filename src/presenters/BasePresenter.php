<?php

namespace presenters;

use src\Container;

require_once __DIR__.'/Templator.php';

abstract class BasePresenter implements IPresenter
{
    //protected string $jsonDb;

    private Templator $templator;
    protected \mysqli $mysqli;
    protected string $templateFilename;
    protected string $compiledTemplateLocation;

    protected array $templateData = [];

    public function __construct() {
        $this->templator = new Templator();
    }

    public abstract function process(array $url, string $requestMethod, mixed $data, mixed $files): void;

    private function renderHeader(): void {
        require_once TEMPLATES_DIR.'/_header.php';
    }

    private function renderFooter(): void {
        require_once TEMPLATES_DIR.'/_footer.php';
    }

    private function getPathTemplateFilename(): string {
        return TEMPLATES_DIR . '/' . $this->templateFilename;
    }

    private function setCompiledTemplateLocation(): void {
        $this->compiledTemplateLocation = TEMP_DIR . '/' . $this->templateFilename;
    }

    private function tryCompileTemplate():void {
        try {
            $this->templator->loadTemplate($this->getPathTemplateFilename());
            $this->templator->compileAndSave($this->compiledTemplateLocation);
        } catch (\Exception $e) {
            // Output the specific error message
            echo "Template Error: " . $e->getMessage();
            // Stop further execution
            exit(1);
        }
    }

    private function renderBody(): void {
        $this->setCompiledTemplateLocation();

        $this->tryCompileTemplate();

        extract($this->templateData);
        require_once $this->compiledTemplateLocation;
    }

    public function render(): void {

        $this->renderHeader();
        $this->renderBody();
        $this->renderFooter();
    }

    public function injectContainer(Container $container): void {
        $this->mysqli = $container->getDatabase();
    }
}