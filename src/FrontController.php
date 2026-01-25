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
            case '':
                $presenter = new p\EventsPresenter();
                break;
            default:
                $presenter = new p\NotFoundPresenter();
                break;
        }

        $presenter->injectContainer($this->container);
        return $presenter;
    }

    private function dispatch($presenter, array $chunks, string $method, mixed $data, mixed $files): void
    {
        try {
            $presenter->process($chunks, $method, $data, $files);
        } catch (p\NotFoundException $e) {
            $presenter = new p\NotFoundPresenter();
            $presenter->injectContainer($this->container);
            $presenter->process([], 'GET', null, null);
        } catch (\Exception $e) {
            http_response_code(500);
            die($e->getMessage());
        }

        $presenter->render();
    }

    private function getUrlIndex(mixed $chunks): int {
        return in_array("~81112441", $chunks) ? 3: 1;
    }

    public function routeAndDispatch($serverData): void {
        $url = $serverData['REQUEST_URI'];

        $chunks = explode("/", $url);

        $urlIndex = $this->getUrlIndex($chunks);

        $presenter = $this->route($chunks[$urlIndex] ?? null);

        $this->dispatch($presenter, array_slice($chunks, $urlIndex+1), $serverData['REQUEST_METHOD'], $_POST, $_FILES);
    }
}