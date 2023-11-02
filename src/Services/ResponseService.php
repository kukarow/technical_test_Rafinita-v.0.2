<?php

declare(strict_types=1);

namespace ApiSaleLibrary\Services;

use ApiSaleLibrary\Contracts\RafinitaResponseInterface;

class ResponseService implements RafinitaResponseInterface
{
    protected int $statusCode;
    protected array $body;

    public function __construct(int $statusCode, array $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
    }
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}
