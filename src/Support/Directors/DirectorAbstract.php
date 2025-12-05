<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors;

use JamesGifford\LaravelArchitecture\Support\Directors\Traits\DirectsUnit;

/**
 * Base class for Director class within a Unit.
 */
abstract class DirectorAbstract implements DirectorInterface
{
    use DirectsUnit;
}
