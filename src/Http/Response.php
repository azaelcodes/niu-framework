<?php

declare(strict_types=1);

namespace Niu\Http;

use Niu\Http\Factories\StreamFactory;
use Niu\Psr\Http\Headers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    /**
     * @var
     */
    private $body;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * @var HeadersInterface
     */
    protected $headers;

    /**
     * Response constructor.
     * @param int $code
     * @param StreamInterface|null $body
     * @param HeadersInterface|null $headers
     */
    public function __construct(
        int $code,
        ?StreamInterface $body = null,
        ?HeadersInterface $headers = null
    ) {
        $this->status = $this->filterStatus($code);
        $this->body = !$body ? (new StreamFactory())->create() : $body;
        $this->headers = !$headers ? new Headers() : $headers;
    }

    /**
     * @inheritDoc
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version)
    {
        // TODO: Implement withProtocolVersion() method.
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return $this->headers->getHeaders(true);
    }

    /**
     * @inheritDoc
     */
    public function hasHeader($name)
    {
        return $this->headers->hasHeader($name);
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        return $this->headers->getHeader($name);
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name)
    {
        $values = $this->headers->getHeader($name);
        return implode(',', $values);
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value)
    {
        $clone = clone $this;
        $clone->headers->setHeader($name, $value);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value)
    {

    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name)
    {

    }

    /**
     * @inheritDoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $code = $this->filterStatus($code);

        $clone = clone $this;
        $clone->status = $code;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase()
    {
        // TODO: Implement getReasonPhrase() method.
    }

    /**
     * @param $status
     * @return int
     */
    protected function filterStatus($status): int
    {
        if (
            !is_integer($status) ||
            $status < 100 ||
            $status > 599
        ) {
            throw new \InvalidArgumentException('Invalid HTTP status code.');
        }

        return $status;
    }
}