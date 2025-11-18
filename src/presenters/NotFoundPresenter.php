<?php

namespace presenters;

use src\Container;

class NotFoundPresenter extends BasePresenter
{
    public function process(array $url, string $requestMethod, mixed $data): void {
        http_response_code(404);
    }

    public function renderBody(): void {
        require_once TEMPLATES_DIR.'/not_found.php';
    }
}