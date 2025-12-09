<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors\Traits;

use BadMethodCallException;
use InvalidArgumentException;
use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferInterface;
use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;
use RuntimeException;

/**
 * Defines the functionality and responsibilities of a Unit Director class.
 *
 * @template TransferRequest of RequestTransferInterface
 * @template TransferResponse of ResponseTransferInterface
 */
trait DirectsUnit
{
    use BuildsTransfers;

    /**
     * Build the Request Transfer then carry out the Unit's purpose.
     *
     * @return ResponseTransferInterface
     */
    final public function __invoke(mixed ...$input): ResponseTransferInterface
    {
        /** @var RequestTransferInterface $requestClass */
        $requestClass = static::requestTransferClass();

        /** @var ResponseTransferInterface $responseClass */
        $responseClass = static::responseTransferClass();

        // Build the appropriate Request Transfer.
        $request = $this->buildRequest(...$input);

        // The Request Transfer must be of the correct type.
        if (! $request instanceof $requestClass) {
            throw new InvalidArgumentException(
                sprintf('Invalid request type: %s, expected %s.', $request::class, $requestClass)
            );
        }

        if (! method_exists($this, 'handle')) {
            throw new BadMethodCallException('The handle method must be defined in the Unit Director.');
        }

        // Execute the logic and get the Response Transfer.
        $response = $this->handle($request);

        // The Response Transfer must be of the correct type.
        if (! $response instanceof $responseClass) {
            throw new RuntimeException(
                sprintf('Invalid response type: %s, expected %s.', $response::class, $responseClass)
            );
        }

        return $response;
    }
}
