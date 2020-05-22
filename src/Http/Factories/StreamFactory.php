<?php

declare(strict_types=1);

namespace Niu\Http\Factories;

use Niu\Http\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * Class StreamFactory
 * @package Niu\Http\Factories
 */
class StreamFactory implements StreamInterface
{

    /**
     * @param string $content
     * @return StreamInterface
     */
    public function create(string $content = ''): StreamInterface
    {
        $resource = fopen('php://temp', 'rw+');

        if (!is_resource($resource)) {
            throw new \RuntimeException('Could not open file temp stream');
        }

        fwrite($resource, $content);
        rewind($resource);

        return $this->createStreamFromResource($resource);
    }

    /**
     * @param $resource
     * @return StreamInterface
     */
    protected function createStreamFromResource($resource): StreamInterface
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(
                'Parameter 1 of StreamFactory::createStreamFromResource() must be a resource.'
            );
        }
        return new Stream($resource);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        // TODO: Implement close() method.
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        // TODO: Implement detach() method.
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        // TODO: Implement getSize() method.
    }

    /**
     * @inheritDoc
     */
    public function tell()
    {
        // TODO: Implement tell() method.
    }

    /**
     * @inheritDoc
     */
    public function eof()
    {
        // TODO: Implement eof() method.
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        // TODO: Implement isSeekable() method.
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        // TODO: Implement seek() method.
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        // TODO: Implement isWritable() method.
    }

    /**
     * @inheritDoc
     */
    public function write($string)
    {
        // TODO: Implement write() method.
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        // TODO: Implement isReadable() method.
    }

    /**
     * @inheritDoc
     */
    public function read($length)
    {
        // TODO: Implement read() method.
    }

    /**
     * @inheritDoc
     */
    public function getContents()
    {
        // TODO: Implement getContents() method.
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        // TODO: Implement getMetadata() method.
    }
}