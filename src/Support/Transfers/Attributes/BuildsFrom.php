<?php

namespace JamesGifford\LaravelArchitecture\Support\Transfers\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class BuildsFrom
{
    public array $types;
    public bool $ordered;

    /**
     * @param string|array<int,string> $types   Class-string or list of class-strings / scalar tokens
     */
    public function __construct(string|array $types, bool $ordered = true)
    {
        $this->types = is_array($types) ? array_values($types) : [$types];
        $this->ordered = $ordered;
    }
}
