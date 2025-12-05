<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors;

/**
 * Base class for Director class within a Unit.
 */
abstract class Director implements DirectorInterface
{
    use DirectsUnit;
}
