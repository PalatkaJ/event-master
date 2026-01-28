<?php

namespace presenters;

class ExceptionPresenter extends BasePresenter
{
    private \Exception $exception;

    public function __construct(\Exception $exception) {
        parent::__construct();
        $this->exception = $exception;
    }

    public function process(array $url, string $requestMethod, mixed $data, mixed $files): void {
        http_response_code($this->exception->getCode());
        $this->templateData['error_code'] = $this->exception->getCode();
        $this->templateData['error_msg'] = $this->exception->getMessage();

        $this->templateFilename = 'error.php';
    }
}