<?php

declare(strict_types=1);

namespace Niu\Core;

use Psr\Http\Message\ResponseInterface;
use Niu\Http\Factories\ResponseFactory;

/**
 * A router to decompose a URI endpoint into a Controller and Action,
 * & serve the result to the browser.
 * Class Router
 */
class Router
{
    /**
     * @var array
     */
    public $routes = [
        'GET' => [],
        'POST' => []
    ];

    /**
     * @param $file
     * @return static
     */
    public static function load($file)
    {
        $router = new static;
        // Require routes.php file
        require __DIR__ . '/niu-framework/' . $file;
        return $router;
    }

    /**
     * @param $pattern
     * @param $controller
     */
    public function get($pattern, $controller)
    {
        $this->routes['GET'][$pattern] = $controller;
    }

    /**
     * @param $uri
     * @param $controller
     */
    public function post($uri, $controller)
    {
        $this->routes['POST'][$uri] = $controller;
    }

    /**
     * @param $uri
     * @param $requestType
     * @return mixed
     * @throws \Exception
     */
    public function direct($uri, $requestType)
    {
        if (! array_key_exists($uri, $this->routes[$requestType])) {
            throw new \Exception('RouteNotDefinedException');
        }

        $this->callAction(
            ...explode('@', $this->routes[$requestType][$uri])
        );
    }

    /**
     * @param $controller
     * @param $action
     * @return mixed
     * @throws \Exception
     */
    protected function callAction($controller, $action)
    {
        $controller = new $controller;

        if (! method_exists($controller, $action) ) {
            throw new \Exception('ActionNotFoundException');
        }

        return $controller->$action(
            $this->getServerRequest(),
            $this->getResponse()
        );
    }

    /**
     * @return ServerRequestInterface
     */
    protected function getServerRequest(): ServerRequestInterface
    {
        $serverRequestParams = $_SERVER;
        $serverRequestFactory = new ServerRequestFactory();
        return $serverRequestFactory->createServerRequest(
            $serverRequestParams['REQUEST_METHOD'],
            $serverRequestParams['REQUEST_URI'],
            $serverRequestParams
        );
    }

    /**
     * @return ResponseInterface
     */
    protected function getResponse(): ResponseInterface
    {
        $responseFactory = new ResponseFactory();
        return $responseFactory->createResponse();
    }
}