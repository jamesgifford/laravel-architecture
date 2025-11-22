<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors\Abstracts;

use JamesGifford\LaravelArchitecture\Support\Directors\Concerns\DirectsUnit;
use JamesGifford\LaravelArchitecture\Support\Directors\Contracts\DirectorInterface;

/**
 * Base class for Director class within a Unit.
 */
abstract class AbstractDirector implements DirectorInterface
{
    use DirectsUnit;
}
