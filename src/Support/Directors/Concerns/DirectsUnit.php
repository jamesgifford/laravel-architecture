<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors\Concerns;

use InvalidArgumentException;
use JamesGifford\LaravelArchitecture\Support\Transfers\Concerns\BuildsTransfers;
use JamesGifford\LaravelArchitecture\Support\Transfers\Contracts\RequestTransferInterface;
use JamesGifford\LaravelArchitecture\Support\Transfers\Contracts\ResponseTransferInterface;
use RuntimeException;

/**
 * Trait for framework-bound classes (e.g. Controllers, Commands, Jobs) that should behave like a Unit Director.
 *
 * @template TransferRequest of RequestTransferInterface
 * @template TransferResponse of ResponseTransferInterface
 */
trait DirectsUnit
{
    use BuildsTransfers;

    /**
     * @return TransferResponse
     */
    final public function __invoke(mixed ...$input): ResponseTransferInterface
    {
        /** @var TransferRequest $request */
        $request = $this->buildRequest(...$input);

        /** @var TransferResponse $response */
        $response = $this->call($request);

        return $response;
    }

    /**
     * Host class MUST implement this.
     *
     * @param  TransferRequest  $request
     * @return TransferResponse
     */
    abstract protected function handle(RequestTransferInterface $request): ResponseTransferInterface;

    /**
     * @param  TransferRequest  $request
     * @return TransferResponse
     */
    public function call(RequestTransferInterface $request): ResponseTransferInterface
    {
        /** @var TransferRequest $requestClass */
        $requestClass = static::requestTransferClass();

        /** @var TransferResponse $responseClass */
        $responseClass = static::responseTransferClass();

        if (! $request instanceof $requestClass) {
            throw new InvalidArgumentException(
                "Invalid request type: {$request::class}, expected {$requestClass}."
            );
        }

        $response = $this->handle($request);

        if (! $response instanceof $responseClass) {
            throw new RuntimeException(
                "Invalid response type: {$response::class}, expected {$responseClass}."
            );
        }

        return $response;
    }
}
