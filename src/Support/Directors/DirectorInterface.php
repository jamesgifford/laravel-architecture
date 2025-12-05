<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors;

interface DirectorInterface
{
    /**
     * Build the Request Transfer then execute the Director's logic.
     */
    public function __invoke(mixed ...$args);
}
