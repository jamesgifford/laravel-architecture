<?php

namespace JamesGifford\LaravelArchitecture\Support\Transfers\Common;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use JamesGifford\LaravelArchitecture\Support\Transfers\TransferAbstract;

final class Pagination extends TransferAbstract
{
    /** @param array<int, object> $items */
    public function __construct(
        public array $items,
        public LengthAwarePaginator $paginator,
        public array $meta = [],
    ) {}
}
