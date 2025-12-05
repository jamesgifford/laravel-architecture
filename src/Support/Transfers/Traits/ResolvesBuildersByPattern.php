<?php

namespace JamesGifford\LaravelArchitecture\Support\Transfers\Traits;

use JamesGifford\LaravelArchitecture\Support\Utilities\NormalizeUtility;

trait ResolvesBuildersByPattern
{
    /**
     * Determine the builder method to use based on the input types pattern.
     */
    protected static function resolveBuilderFromPattern(array $input): ?callable
    {
        $builderName = static::determineBuilderNameFromPattern($input);

        if (method_exists(static::class, $builderName)) {
            return fn (...$input) => static::{$builderName}(...$input);
        }

        return null;
    }

    /**
     * Infer the name of the builder method based on the input types.
     */
    protected static function determineBuilderNameFromPattern(array $input): string
    {
        $builderMethodPrefix = 'buildFrom';

        // If no input was provided, use the null builder
        if (empty($input)) {
            return $builderMethodPrefix . 'Null';
        }

        $inputTypes = NormalizeUtility::normalizeTypes($input);

        // When providing an array as the sole input, assume it contains all properties in the Transfer
        if (count($inputTypes) === 1 && reset($inputTypes) === 'Array') {
            return $builderMethodPrefix . 'PropertyArray';
        }

        return $builderMethodPrefix . implode('And', array_map('ucfirst', $inputTypes));  // eg: buildFromIntAndString
    }
}
