<?php

namespace JamesGifford\LaravelArchitecture\Scaffolds\CreateControllerUnit;

use JamesGifford\LaravelArchitecture\Support\Transfers\AbstractTransfer;
use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;

final class CreateControllerUnitResponse extends AbstractTransfer implements ResponseTransferInterface
{
    public function __construct(
        public readonly string $unitName,
        public readonly string $unitPath,
        public readonly bool $createdDirector,
        public readonly bool $createdRequest,
        public readonly bool $createdResponse,
    ) {}
}
