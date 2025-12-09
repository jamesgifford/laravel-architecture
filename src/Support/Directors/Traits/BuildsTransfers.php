<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors\Traits;

use InvalidArgumentException;
use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferInterface;
use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;
use ReflectionClass;
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
     * Infer Transfer fully qualified class name from the namespace of the Director class.
     * Transfer classes should have the same name as the Unit with the provided suffix.
     * (eg: CreatePostDataHandler (director) -> CreatePostRequest (transfer)
     */
    protected static function inferTransferClass(string $suffix): string
    {
        $directorFullyQualifiedClassName = get_called_class();
        $lastSeparatorPosition = strrpos($directorFullyQualifiedClassName, '\\');
        $directorNamespace = $lastSeparatorPosition === false
            ? ''
            : substr($directorFullyQualifiedClassName, 0, $lastSeparatorPosition);

        if (empty($directorNamespace)) {
            throw new RuntimeException("Unable to infer namespace for [$directorFullyQualifiedClassName].");
        }

        // The last segment of the namespace should be the unit name
        $unitName = substr($directorNamespace, strrpos($directorNamespace, '\\') + 1);

        if (empty($unitName)) {
            throw new RuntimeException("Unable to infer unit name for [$directorFullyQualifiedClassName].");
        }

        $transferFullyQualifiedClassName = sprintf(
            '%s\%s%s',
            $directorNamespace,
            $unitName,
            $suffix,
        );

        if (class_exists($transferFullyQualifiedClassName)) {
            return $transferFullyQualifiedClassName;
        }

        throw new RuntimeException(
            sprintf(
                'Transfer class [%s] not found for [%s].',
                $transferFullyQualifiedClassName,
                $directorFullyQualifiedClassName,
            )
        );
    }
}
