<?php

namespace JamesGifford\LaravelArchitecture\Support\Transfers;

interface TransferInterface
{
    /**
     * Build a new instance of the class using the provided input.
     *
     * @param mixed ...$input Input parameters for constructing the instance.
     * @return static A new instance of the class.
     */
    public static function build(mixed ...$input): static;

    /**
     * Validate the class properties against any provided rules.
     *
     * @return void
     */
    public function validate(): void;

    /**
     * Define the validation rules for the class properties.
     *
     * @return array
     */
    public function rules(): array;

    /**
     * Define the validation error messages for the class properties.
     *
     * @return array The array of validation messages.
     */
    public function messages(): array;

    /**
     * Define any attributes for the class properties.
     *
     * @return array The array of validation attributes.
     */
    public function attributes(): array;
}
