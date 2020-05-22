<?php

declare(strict_types=1);

namespace Niu\Core;

use Psr\Http\Message\ResponseInterface;

/**
 * Based on SlimPHP ResponseEmitter Class.
 * Emit a response to an HTTP Request
 *
 * The whole /User code base is inspired by SlimPHP so it was only
 * natural to use the same techniques used within that amazing framework.
 *
 * Class ResponseEmitter
 * @package Niu\Core
 */
class ResponseEmitter
{

    const RESPONSE_CHUNK_SIZE = 4096;

    /**
     * @param ResponseInterface $response
     */
    public function emit(ResponseInterface $response): void
    {
        $isEmpty = $this->isResponseEmpty($response);
        if (headers_sent() === false) {
            if ($isEmpty) {
                $response = $response
                    ->withoutHeader('Content-Type')
                    ->withoutHeader('Content-Length');
            }
            $this->emitStatusLine($response);
            $this->emitHeaders($response);
        }

        if (!$isEmpty) {
            $this->emitBody($response);
        }
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public function isResponseEmpty(ResponseInterface $response): bool
    {
        if (in_array($response->getStatusCode(), [204, 205, 304], true)) {
            return true;
        }
        $stream = $response->getBody();
        $seekable = $stream->isSeekable();
        if ($seekable) {
            $stream->rewind();
        }
        return $seekable ? $stream->read(1) === '' : $stream->eof();
    }

    /**
     * @param ResponseInterface $response
     */
    protected function emitStatusLine(ResponseInterface $response): void
    {
        $statusLine = sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
        header($statusLine, true, $response->getStatusCode());
    }

    /**
     * @param ResponseInterface $response
     */
    protected function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            $first = strtolower($name) !== 'set-cookie';
            foreach ($values as $value) {
                $header = sprintf('%s: %s', $name, $value);
                header($header, $first);
                $first = false;
            }
        }
    }

    /**
     * @param ResponseInterface $response
     */
    protected function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();
        if ($body->isSeekable()) {
            $body->rewind();
        }

        $amountToRead = (int) $response->getHeaderLine('Content-Length');
        if (!$amountToRead) {
            $amountToRead = $body->getSize();
        }

        if ($amountToRead) {
            while ($amountToRead > 0 && !$body->eof()) {
                $length = min(static::RESPONSE_CHUNK_SIZE, $amountToRead);
                $data = $body->read($length);
                echo $data;

                $amountToRead -= strlen($data);

                if (connection_status() !== CONNECTION_NORMAL) {
                    break;
                }
            }
        } else {
            while (!$body->eof()) {
                echo $body->read(static::RESPONSE_CHUNK_SIZE);
                if (connection_status() !== CONNECTION_NORMAL) {
                    break;
                }
            }
        }
    }
}