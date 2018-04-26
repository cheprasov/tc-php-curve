<?php

namespace TC\Curve\Response;

class Response implements ResponseInterface
{
    /**
     * @var int
     */
    protected $code = ResponseInterface::CODE_OK;

    /**
     * @var array
     */
    protected $headers = [
        'Content-Type' => ResponseInterface::CONTENT_TYPE_HTML,
    ];

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @inheritdoc
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @inheritdoc
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritdoc
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @inheritdoc
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @inheritdoc
     */
    public function setBody(string $body) {
        $this->body = $body;
    }
}
