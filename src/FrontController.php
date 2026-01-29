<?php

namespace src;

use presenters as p;

require_once __DIR__.'/Exceptions.php';

require_once PRESENTERS_DIR . '/PresenterInterface.php';
require_once PRESENTERS_DIR . '/BasePresenter.php';
require_once PRESENTERS_DIR.'/EventsPresenter.php';
require_once PRESENTERS_DIR.'/UsersPresenter.php';
require_once PRESENTERS_DIR . '/ExceptionPresenter.php';

class FrontController {
    private Container $container;

    public function injectContainer(Container $container): void {
        $this->container = $container;
    }

    private function route($urlChunk): p\PresenterInterface {
        switch ($urlChunk) {
            case 'events':
            case '':
                $presenter = new p\EventsPresenter();
                break;
            case 'login':
            case 'register':
            case 'logout':
            case 'settings':
            case 'delete':
                $presenter = new p\UsersPresenter();
                break;
            default:
                $presenter = new p\ExceptionPresenter(new NotFoundException());
                break;
        }

        $presenter->injectContainer($this->container);
        return $presenter;
    }

    private function dispatch($presenter, array $chunks, string $method, mixed $data, mixed $files): void
    {
        try {
            $presenter->process($chunks, $method, $data, $files);
        } catch (\Exception $e) {
            $presenter = new p\ExceptionPresenter($e);
            $presenter->injectContainer($this->container);
            $presenter->process([], 'GET', null, null);
        }
    }

    private function getRelativePath(string $url): string {
        $path = explode('?', $url)[0];

        if (BASE_URL !== '' && str_starts_with($path, BASE_URL)) {
            $path = substr($path, strlen(BASE_URL));
        }

        return trim($path, '/');
    }

    public function routeAndDispatch($serverData): void {
        $url = $serverData['REQUEST_URI'];

        $path = $this->getRelativePath($url);

        $chunks = $path === '' ? [] : explode('/', $path);

        $presenter = $this->route($chunks[0] ?? null);

        $this->dispatch($presenter, $chunks, $serverData['REQUEST_METHOD'], $_POST, $_FILES);
    }
}