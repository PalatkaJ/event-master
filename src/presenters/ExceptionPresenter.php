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
        if ($this->exception instanceof \NotFoundException) {
            http_response_code(404);
        }
        elseif ($this->exception instanceof \ServerException) {
            http_response_code(500);
        }
        else {
            http_response_code(500);
        }

        $this->templateData['error_msg'] = $this->exception->getMessage();
        $this->templateFilename = 'error.php';
    }
}