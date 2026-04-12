<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Arr
{
    public static function wrap(mixed $value): array
    {
        if (is_null($value)) return [];

        return is_array($value) ? $value : [$value];
    }

    public static function uniqueBy(
        array $array,
        \Closure $callback,
        int $mode = 0,
        bool $strict = true,
    ): array
    {
        $exists = [];

        return array_filter(
            $array,
            static function (...$args) use($callback, $strict, &$exists) {
                $search = call_user_func_array($callback, func_get_args());
                if (in_array($search, $exists, $strict)) return false;
                $exists[] = $search;
                return true;
            },
            $mode,
        );
    }

    public static function mapWithKeys(array $array, callable $callback): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

    public static function flatten(array $array, int|float $depth = INF): array
    {
        $depth = (int) $depth;
        $result = [];

        foreach ($array as $item) {
            if (! is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : self::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    public static function whereFilled(array $array): array
    {
        return array_filter($array, static fn ($item) => namespace\Data::filled($item));
    }
}
