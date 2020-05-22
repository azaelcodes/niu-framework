<?php

declare(strict_types=1);

namespace Niu\Http\Factories;

use Niu\Http\Response;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseFactory
 * @package Niu\Psr\Http\Factories
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code);
    }
}