<?php

namespace JamesGifford\LaravelArchitecture\Scaffolds\ControllerUnit;

use JamesGifford\LaravelArchitecture\Support\Transfers\TransferAbstract;
use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;

final class ControllerUnitResponse extends TransferAbstract implements ResponseTransferInterface
{
    public function __construct(
        public readonly string $unitName,
        public readonly string $unitPath,
        public readonly bool $createdDirector,
        public readonly bool $createdRequest,
        public readonly bool $createdResponse,
    ) {}
}
