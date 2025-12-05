<?php

namespace JamesGifford\LaravelArchitecture\Support\Transfers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use JamesGifford\LaravelArchitecture\Support\Utilities\SerializeUtility;

abstract class AbstractTransfer implements TransferInterface
{
    use ResolvesBuildersByAttributes, ResolvesBuildersByPattern;

    /**
     * @throws InvalidArgumentException
     */
    public static function build(mixed ...$input): static
    {
        // Attempt finding a builder based on attributes
        $builder = static::resolveBuilderFromAttributes($input);

        // As a fallback, attempt finding a builder based on the input pattern
        if (empty($builder)) {
            $builder = static::resolveBuilderFromPattern($input);
        }

        // As a last resort, use a default builder
        if (empty($builder)) {
            $builder = fn (...$input) => static::buildFromDefault(...$input);
        }

        return $builder(...$input);
    }

    /**
     * Build the Transfer from its individual properties.
     */
    public static function buildFromDefault(...$properties): static
    {
        return new static(...$properties);
    }

    /**
     * Build the Transfer from nothing.
     */
    public static function buildFromNull(): static
    {
        return static::buildFromDefault();
    }

    /**
     * Build the Transfer and convert to an array.
     */
    public static function buildToArray(mixed ...$input): array
    {
        return get_object_vars(static::build(...$input));
    }

    /**
     * Convert to an array.
     */
    public function toArray(): array
    {
        return SerializeUtility::objectToArray($this);
    }

    /**
     * Serialize for JSON
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @throws ValidationException
     */
    public function validate(): void
    {
        if (!method_exists(static::class, 'rules')) {
            return;
        }

        $data = get_object_vars($this);
        $rules = $this->rules();

        $messages = [];
        if (method_exists(static::class, 'messages')) {
            $messages = $this->messages();
        }

        $attributes = [];
        if (method_exists(static::class, 'attributes')) {
            $attributes = $this->attributes();
        }

        $validator = Validator::make($data, $rules, $messages, $attributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }
}
