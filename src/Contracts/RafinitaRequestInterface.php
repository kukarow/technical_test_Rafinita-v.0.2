<?php

namespace ApiSaleLibrary\Contracts;

interface RafinitaRequestInterface
{
    public function setEndpoint(string $endpoint): void;
    public function setData(array $data): void;
    public function send(): RafinitaResponseInterface;
}
