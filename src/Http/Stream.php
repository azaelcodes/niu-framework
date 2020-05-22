<?php

declare(strict_types=1);

namespace Niu\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /**
     * Bit mask to determine if the stream is a pipe
     *
     * This is octal as per header stat.h
     */
    const FSTAT_MODE_S_IFIFO = 0010000;

    /**
     * @var resource|null
     */
    private $stream;

    /**
     * @var bool
     */
    private $writable;

    /**
     * @var array
     */
    private $meta;

    /**
     * @var
     */
    private $size;

    /**
     * @var bool
     */
    private $finished;

    /**
     * @var bool
     */
    private $seekable;

    /**
     * @var bool
     */
    private $isPipe;

    /**
     * @var bool
     */
    private $readable;

    /**
     * Stream constructor.
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->attach($resource);
    }

    /**
     * @param $resource
     */
    protected function attach($resource): void
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException(
                __METHOD__ . ' argument must be a valid PHP resource'
            );
        }

        if ($this->stream) {
            $this->detach();
        }

        $this->stream = $resource;
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
        $oldResource = $this->stream;
        $this->stream = null;
        $this->size = null;
        $this->meta = null;
        return $oldResource;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if ($this->stream && !$this->size) {
            $stats = fstat($this->stream);

            if ($stats) {
                $this->size = isset($stats['size']) && !$this->isPipe() ? $stats['size'] : null;
            }
        }
        return $this->size;
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
        return $this->stream ? feof($this->stream) : true;
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        if ($this->seekable === null) {
            $this->seekable = false;

            if ($this->stream) {
                $this->seekable = !$this->isPipe() && $this->getMetadata('seekable');
            }
        }

        return $this->seekable;
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
        if (!$this->isSeekable() || $this->stream && rewind($this->stream) === false) {
            throw new \RuntimeException('Could not rewind stream.');
        }
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        if ($this->writable === null) {
            $this->writable = false;

            if ($this->stream) {
                $mode = $this->getMetadata('mode');
                if (
                    strstr($mode, 'w') !== false ||
                    strstr($mode, '+') !== false
                ) {
                    $this->writable = true;
                }
            }
        }

        return $this->writable;
    }

    /**
     * @inheritDoc
     */
    public function write($string)
    {
        $written = false;

        if ($this->isWritable() && $this->stream) {
            $written = fwrite($this->stream, $string);
        }

        if ($written !== false) {
            $this->size = null;
            return $written;
        }

        throw new \RuntimeException('Could not write to stream.');
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        if ($this->readable === null) {
            if ($this->isPipe()) {
                $this->readable = true;
            } else {
                $this->readable = false;

                if ($this->stream) {
                    $mode = $this->getMetadata('mode');

                    if (strstr($mode, 'r') !== false || strstr($mode, '+') !== false) {
                        $this->readable = true;
                    }
                }
            }
        }

        return $this->readable;
    }

    /**
     * @inheritDoc
     */
    public function read($length)
    {
        $data = false;

        if ($this->isReadable() && $this->stream) {
            $data = fread($this->stream, $length);
        }

        if (is_string($data)) {
            if ($this->eof()) {
                $this->finished = true;
            }
            return $data;
        }

        throw new \RuntimeException('Could not read from stream.');
    }

    /**
     * @inheritDoc
     */
    public function getContents()
    {
        $contents = false;

        if ($this->stream) {
            $contents = stream_get_contents($this->stream);
        }

        if (is_string($contents)) {
            if ($this->eof()) {
                $this->finished = true;
            }
            return $contents;
        }

        throw new \RuntimeException('Could not read from stream.');
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        if (!$this->stream) {
            return null;
        }

        $this->meta = stream_get_meta_data($this->stream);
        if (!$key) {
            return $this->meta;
        }

        return $this->meta[$key] ?? null;
    }

    /**
     * @return bool
     */
    protected function isPipe(): bool
    {
        if ($this->isPipe === null) {
            $this->isPipe = false;

            if ($this->stream) {
                $stats = fstat($this->stream);

                if ($stats) {
                    $this->isPipe = isset($stats['mode']) && ($stats['mode'] & self::FSTAT_MODE_S_IFIFO) !== 0;
                }
            }
        }

        return $this->isPipe;
    }
}