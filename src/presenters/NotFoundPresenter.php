<?php

namespace presenters;

use src\Container;

class NotFoundPresenter extends BasePresenter
{
    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void {
        http_response_code(404);
        $this->templateFilename = 'not_found.php';
    }
}