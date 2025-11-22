<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors\Contracts;

use JamesGifford\LaravelArchitecture\Support\Transfers\Contracts\RequestTransferInterface;

interface DirectorInterface
{
    /**
     * Build the Request Transfer then execute the Director's logic.
     */
    public function __invoke(mixed ...$args);

    /**
     * Execute the Director's logic.
     */
    public function call(RequestTransferInterface $request);
}
