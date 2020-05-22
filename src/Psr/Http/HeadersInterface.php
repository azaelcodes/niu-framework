<?php

declare(strict_types=1);

namespace Niu\Psr\Http;


interface HeadersInterface
{
    /**
     * @param string $name
     * @return HeadersInterface
     */
    public function removeHeader(string $name): HeadersInterface;

    /**
     * @param string $name
     * @param array $default
     * @return array
     */
    public function getHeader(string $name, $default = []): array;

    /**
     * @param $name
     * @param $value
     * @return HeadersInterface
     */
    public function setHeader($name, $value): HeadersInterface;

    /**
     * @param array $headers
     * @return HeadersInterface
     */
    public function setHeaders(array $headers): HeadersInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool;

    /**
     * @param bool $originalCase
     * @return array
     */
    public function getHeaders(bool $originalCase = false): array;
}