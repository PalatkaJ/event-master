<?php

namespace src;

require_once  SRC_DIR . '/Templator.php';

class View {
    private Templator $templator;

    public function __construct() {
        $this->templator = new Templator();
    }

    private function renderWithDataExtraction(string $filename, array $data): void {
        $path = TEMPLATES_DIR . '/' . $filename;
        $compiled = TEMP_DIR . '/' . $filename;

        try {
            $this->templator->loadTemplate($path);
            $this->templator->compileAndSave($compiled);
        } catch (\Exception $e) {
            echo "Template Error: " . $e->getMessage();
            exit(1);
        }

        extract($data);
        require $compiled;
    }

    public function render(string $filename, array $data): void {
        $this->renderWithDataExtraction('_header.php', $data);
        $this->renderWithDataExtraction($filename, $data);
        $this->renderWithDataExtraction('_footer.php', $data);
    }
}