<?php

namespace JamesGifford\LaravelArchitecture\Support\Utilities;

use BackedEnum;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JsonSerializable;
use UnitEnum;

class SerializeUtility
{
    public static function serializeValue(mixed $value): mixed
    {
        if ($value === null || is_scalar($value)) {
            return $value; // int, float, string, bool, null
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof UnitEnum) {
            return $value->name;
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            /** @var mixed $arr */
            $arr = $value->toArray();
            return is_array($arr)
                ? array_map([self::class, __FUNCTION__], $arr)
                : $arr;
        }

        if ($value instanceof Arrayable) {
            return array_map([self::class, __FUNCTION__], $value->toArray());
        }

        if ($value instanceof Collection) {
            return $value->map([self::class, __FUNCTION__])->all();
        }

        if ($value instanceof JsonSerializable) {
            $j = $value->jsonSerialize();
            return is_array($j)
                ? array_map([self::class, __FUNCTION__], $j)
                : $j;
        }

        if (is_array($value)) {
            return array_map([self::class, __FUNCTION__], $value);
        }

        // Last resort: string-cast if available, else a descriptive token
        if (method_exists($value, '__toString')) {
            return (string) $value;
        }

        return sprintf('[object %s]', $value::class);
    }

    public static function objectToArray(object $obj): array
    {
        $out = [];
        foreach (get_object_vars($obj) as $k => $v) {
            $out[$k] = self::serializeValue($v);
        }
        return $out;
    }
}
