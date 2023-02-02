<?php

declare(strict_types=1);

namespace Chestnut\Controller;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function header;
use function http_response_code;
use function json_encode;
use function method_exists;

abstract class AbstractController
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ServerRequestInterface $request,
        private readonly ResponseInterface $response,
        private readonly RouterInterface $router,
        private readonly ViewInterface $view,
    )
    {

    }

    public function hasAction(string $name): bool
    {
        return method_exists($this, $name);
    }

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    protected function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    protected function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    protected function getView(): ViewInterface
    {
        return $this->view;
    }

    protected function render(string $view, array $context = []): ResponseInterface
    {
        // todo: maybe $this->getView()->bulkAssign($context);
        foreach ($context as $key => $value) {
            $this->getView()->assign($key, $value);
        }

        // todo: get view path from config
        // todo: get file extension from config
        $this->getView()->render($view, 'php', __DIR__ . '/../../src/Views/');

        return new Response;
    }

    protected function response(array $data = [], int $statusCode = 200): void
    {
        $this->getResponse()->withBody()->withStatus($statusCode);
    }

    protected function jsonResponse(array $data = [], int $statusCode = 200): void
    {
        $this->getResponse()->withBody(json_encode($data))->withHeader('Content-Type', 'ResponseInterface');
    }

    protected function redirect(string $url): void
    {
        $this->response([], 302);
    }

    protected function redirectToRoute(string $route): void
    {
        // todo: route to url magic
        $url = '';

        $this->redirect($url);
    }
}
