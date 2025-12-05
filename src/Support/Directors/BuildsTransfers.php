<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors;

use InvalidArgumentException;
use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferInterface;
use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;
use RuntimeException;

/**
 * Imparts the logic to build Request and Response Transfers.
 */
trait BuildsTransfers
{
    /**
     * Build the appropriate Request Transfer object.
     */
    protected function buildRequest(mixed ...$input): RequestTransferInterface
    {
        // If the only input provided is a RequestTransferInterface, just return it.
        if (count($input) === 1 && $input[0] instanceof RequestTransferInterface) {
            return $input[0];
        }

        $requestClass = static::requestTransferClass();

        if (!method_exists($requestClass, 'build')) {
            throw new InvalidArgumentException("$requestClass::build(...) not found.");
        }

        return $requestClass::build(...$input);
    }

    /**
     * Build the appropriate Response Transfer object.
     */
    protected function buildResponse(mixed ...$input): ResponseTransferInterface
    {
        // If the only input provided is a ResponseTransferInterface, just return it.
        if (count($input) === 1 && $input[0] instanceof ResponseTransferInterface) {
            return $input[0];
        }

        $responseClass = static::responseTransferClass();

        if (!method_exists($responseClass, 'build')) {
            throw new RuntimeException("$responseClass::build(...) not found.");
        }

        return $responseClass::build(...$input);
    }

    /**
     * Get the suffix to use with Request Transfer FQCN.
     */
    protected static function requestTransferSuffix(): string
    {
        return 'Request';
    }

    /**
     * Get the suffix to use with Response Transfer FQCN.
     */
    protected static function responseTransferSuffix(): string
    {
        return 'Response';
    }

    /**
     * Get the Request Transfer FQCN.
     */
    protected static function requestTransferClass(): string
    {
        return static::inferTransferClass(static::requestTransferSuffix());
    }

    /**
     * Get the Response Transfer FQCN.
     */
    protected static function responseTransferClass(): string
    {
        return static::inferTransferClass(static::responseTransferSuffix());
    }

    /**
     * Infer Transfer FQCN from the processor FQCN + suffix.
     * Defaults to the SAME namespace as the processor:
     *   App\Foo\Bar\ProcessorClass -> App\Foo\Bar\ProcessorClassRequest.
     * If not available will also look at:
     *   App\Foo\Bar\Transfers\ProcessorClassRequest.
     */
    protected static function inferTransferClass(string $suffix): string
    {
        $fqcn = static::class;
        $lastSeparatorPosition = strrpos($fqcn, '\\');
        $namespace = $lastSeparatorPosition === false
            ? ''
            : substr($fqcn, 0, $lastSeparatorPosition);
        $shortName = $lastSeparatorPosition === false
            ? $fqcn
            : substr($fqcn, $lastSeparatorPosition + 1);

        // Remove the last StudlyCase "word" (e.g. "Director" from "MakeControllerUnitDirector")
        $baseName = preg_replace('/[A-Z][^A-Z]*$/', '', $shortName);

        // If, for some reason, nothing matched, fall back to the original short name
        if ($baseName === $shortName || $baseName === '') {
            $baseName = $shortName;
        }

        $transferClassName = sprintf(
            '%s%s%s',
            $namespace ? $namespace . '\\' : '',
            $baseName,
            $suffix,
        );

        if (class_exists($transferClassName)) {
            return $transferClassName;
        }

        // As a backup, try the Transfers namespace
        $transferClassName = sprintf('%s%s%s',
            ($namespace ? $namespace . '\\Transfers\\' : 'Transfers\\'),
            $shortName,
            $suffix,
        );

        if (class_exists($transferClassName)) {
            return $transferClassName;
        }

        throw new RuntimeException("Transfer class not found: {$transferClassName}");
    }
}
