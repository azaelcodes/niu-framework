<?php

declare(strict_types=1);

namespace Niu\Http\Factories;

use Niu\Http\Request;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new Request($method, $uri, $serverParams);
    }
}