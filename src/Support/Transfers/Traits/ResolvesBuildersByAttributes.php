<?php

namespace JamesGifford\LaravelArchitecture\Support\Transfers\Traits;

use JamesGifford\LaravelArchitecture\Support\Transfers\Attributes\BuildsFrom;
use JamesGifford\LaravelArchitecture\Support\Utilities\NormalizeUtility;
use LogicException;
use ReflectionClass;
use ReflectionMethod;

trait ResolvesBuildersByAttributes
{
    /**
     * @var array<class-string, array{ordered: array<string,string>, unordered: array<string,string>, default: string|null}>
     */
    protected static array $attributeBuilderCache = [];

    /**
     * Determine the builder method to use based on attribute-declared types.
     */
    protected static function resolveBuilderFromAttributes(array $input): ?callable
    {
        $builderName = static::determineBuilderNameFromAttributes($input);

        if (method_exists(static::class, $builderName)) {
            return fn (...$input) => static::{$builderName}(...$input);
        }

        return null;
    }

    /**
     * Infer the name of the builder method based on attribute-declared types.
     */
    protected static function determineBuilderNameFromAttributes(array $input): string
    {
        $class = static::class;
        $cache = &static::$attributeBuilderCache[$class];

        if (!isset($cache)) {
            $cache = static::initializeAttributeBuilderCache($class);
        }

        $inputTypes = NormalizeUtility::normalizeTypes($input);

        // 1) Order-sensitive (ordered key)
        $orderedKey = static::signatureKeyOrdered($inputTypes);
        if (isset($cache['ordered'][$orderedKey])) {
            return $cache['ordered'][$orderedKey];
        }

        // 2) Order-insensitive (unordered key)
        $unorderedKey = static::signatureKeyUnordered($inputTypes);
        if (isset($cache['unordered'][$unorderedKey])) {
            return $cache['unordered'][$unorderedKey];
        }

        // 3) Fallbacks
        if ($cache['default']) {
            return $cache['default'];
        }

        return 'buildFromDefault';
    }

    /**
     * @return array{ordered: array<string,string>, unordered: array<string,string>, default: string|null}
     */
    protected static function initializeAttributeBuilderCache(string $class): array
    {
        $reflection = new ReflectionClass($class);

        $orderedMap = $unorderedMap = [];
        $default = null;

        $methods = $reflection->getMethods(
            ReflectionMethod::IS_STATIC
            | ReflectionMethod::IS_PRIVATE
            | ReflectionMethod::IS_PROTECTED
            | ReflectionMethod::IS_PUBLIC
        );

        foreach ($methods as $method) {
            $attributes = $method->getAttributes(BuildsFrom::class);

            if (!$attributes) {
                continue;
            }

            foreach ($attributes as $attribute) {
                /** @var BuildsFrom $attributeInstance */
                $attributeInstance = $attribute->newInstance();
                $declaredTypes = NormalizeUtility::normalizeTypeStrings($attributeInstance->types);

                if ($attributeInstance->ordered) {
                    $key = static::signatureKeyOrdered($declaredTypes);

                    if (isset($orderedMap[$key])) {
                        throw new LogicException("Duplicate order-sensitive attribute builder for signature {$key} in {$class}.");
                    }

                    $orderedMap[$key] = $method->getName();
                } else {
                    $key = static::signatureKeyUnordered($declaredTypes);

                    if (isset($unorderedMap[$key])) {
                        throw new LogicException("Duplicate order-insensitive attribute builder for signature {$key} in {$class}.");
                    }

                    $unorderedMap[$key] = $method->getName();
                }
            }
        }

        if ($reflection->hasMethod('buildDefault')) {
            $reflectionMethod = $reflection->getMethod('buildDefault');

            if (!$reflectionMethod->isStatic()) {
                throw new LogicException("Method buildDefault() on {$class} must be static.");
            }

            $default = 'buildDefault';
        }

        return ['ordered' => $orderedMap, 'unordered' => $unorderedMap, 'default' => $default];
    }

    /**
     * Build an order-sensitive signature key (no sorting).
     */
    protected static function signatureKeyOrdered(array $types): string
    {
        return implode('|', $types);
    }

    /**
     * Build an order-insensitive signature key (sort before join).
     */
    protected static function signatureKeyUnordered(array $types): string
    {
        $keyTypes = array_values($types);   // Prevent mutating the original array
        sort($keyTypes, SORT_STRING);

        return implode('|', $keyTypes);
    }
}
