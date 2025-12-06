<?php

namespace JamesGifford\LaravelArchitecture\Support\Queries\Transfers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use JamesGifford\LaravelArchitecture\Support\Transfers\TransferAbstract;

class Pagination extends TransferAbstract
{
    /** @param array<int, object> $items */
    public function __construct(
        public array $items,
        public LengthAwarePaginator $paginator,
        public array $meta = [],
    ) {}
}
