<?php

namespace TC\Curve\Response;

interface ResponseInterface
{
    const CODE_OK = 200;
    const CODE_NOT_FOUND = 404;

    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @param int $code
     */
    public function setCode(int $code);

    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers);

    /**
     * @param string $key
     * @param string $value
     */
    public function setHeader(string $key, string $value);

    /**
     * @return string
     */
    public function getBody(): string;

    /**
     * @param string $body
     */
    public function setBody(string $body);
}
