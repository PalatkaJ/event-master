<?php

namespace src;

use presenters as p;

require_once PRESENTERS_DIR.'/IPresenter.php';
require_once PRESENTERS_DIR.'/BasePresenter.php';
require_once PRESENTERS_DIR.'/EventsPresenter.php';
require_once PRESENTERS_DIR.'/NotFoundPresenter.php';

class FrontController {
    private Container $container;

    public function injectContainer(Container $container): void {
        $this->container = $container;
    }

    private function route($urlChunk): p\IPresenter {
        switch ($urlChunk) {
            case 'events':
                $presenter = new p\EventsPresenter();
                break;
            default:
                $presenter = new p\NotFoundPresenter();
                break;
        }

        $presenter->injectContainer($this->container);
        return $presenter;
    }

    private function dispatch($presenter, array $chunks, string $method, mixed $data)
    {
        try {
            $presenter->process($chunks, $method, $data);
        } catch (p\NotFoundException $e) {
            $presenter = new p\NotFoundPresenter();
            $presenter->injectContainer($this->container);
            $presenter->process([], 'GET', null);
        } catch (\Exception $e) {
            http_response_code(500);
            die("Internal server error.");
        }

        $presenter->render();
    }

    public function routeAndDispatch($serverData): void {
        $url = $serverData['REQUEST_URI'];
        $chunks = explode("/", $url);

        $presenter = $this->route($chunks[1] ?? null);

        $this->dispatch($presenter, array_slice($chunks, 2), $serverData['REQUEST_METHOD'], $_POST);
    }
}