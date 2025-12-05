<?php

namespace JamesGifford\LaravelArchitecture\Scaffolds\ControllerUnit;

use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferAbstract;
use JamesGifford\LaravelArchitecture\Support\Transfers\Attributes\BuildsFrom;
use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferInterface;

final class ControllerUnitRequest extends RequestTransferAbstract implements RequestTransferInterface
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
