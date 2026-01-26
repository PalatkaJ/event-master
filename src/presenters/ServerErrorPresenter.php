<?php

namespace presenters;

class ServerErrorPresenter extends BasePresenter
{
    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void {
        http_response_code(500);
        $this->templateFilename = 'server_error.php';
    }
}