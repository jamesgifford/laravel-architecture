<?php

namespace JamesGifford\LaravelArchitecture\Support\Utilities;

class NormalizeUtility
{
    /**
     * Normalize an input list to canonical type tokens.
     */
    public static function normalizeTypes(array $input): array
    {
        return array_map(function ($argument): string {
            if (is_object($argument)) {
                return static::normalizeTypeString($argument::class);   // eg: MyClassName
            }

            $argumentType = get_debug_type($argument);

            // Normalize the rare resource variant
            if ($argumentType === 'resource (closed)') {
                $argumentType = 'resource';
            }

            return $argumentType;   // eg: int, string, bool, array, object, resource, etc.
        }, $input);
    }

    /**
     * Normalize an attribute-declared type (class-string or scalar token).
     */
    public static function normalizeTypeString(?string $type): string
    {
        return ltrim($type, '\\');
    }

    /**
     * Normalize an attribute-declared list of types (class-strings or scalar tokens).
     */
    public static function normalizeTypeStrings(array $types): array
    {
        return array_map(static fn($type) => static::normalizeTypeString($type), $types);
    }
}
