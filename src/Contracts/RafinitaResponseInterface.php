<?php

namespace ApiSaleLibrary\Contracts;

interface RafinitaResponseInterface
{
    public function getStatusCode(): int;
    public function getBody(): array;
}
