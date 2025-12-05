<?php

namespace JamesGifford\LaravelArchitecture\Scaffolds\CreateControllerUnit;

use JamesGifford\LaravelArchitecture\Support\Transfers\AbstractRequestTransfer;
use JamesGifford\LaravelArchitecture\Support\Transfers\Attributes\BuildsFrom;
use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferInterface;

final class CreateControllerUnitRequest extends AbstractRequestTransfer implements RequestTransferInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
    ) {}

    #[BuildsFrom(['string', 'string'])]
    #[BuildsFrom(['string', 'null'])]
    public static function buildFromCommandArguments(string $name, ?string $type): static
    {
        if (empty($type)) {
            $type = 'Pages';
        }

        return new static(
            name: $name,
            type: $type,
        );
    }
}
