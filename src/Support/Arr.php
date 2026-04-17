<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Arr
{
    public static function start(iterable $source, mixed ...$prefix): array
    {
        $source = array_values(self::wrap($source));
        if (namespace\Data::blank($prefix)) {
            return $source;
        }

        $result = $prefix;
        $prefixLen = count($prefix);
        $maxOverlap = 0;

        for ($len = 1; $len <= min($prefixLen, count($source)); $len++) {
            $prefixSlice = array_slice($prefix, $prefixLen - $len);
            $sourceSlice = array_slice($source, 0, $len);

            if ($prefixSlice === $sourceSlice) {
                $maxOverlap = $len;
            }
        }

        if ($maxOverlap < count($source)) {
            $result = array_merge($result, array_slice($source, $maxOverlap));
        }
        return $result;
    }

    public static function end(iterable $source, mixed ...$suffix): array
    {
        $source = array_values(self::wrap($source));
        if (namespace\Data::blank($suffix)) {
            return $source;
        }

        $result = $source;
        $sourceLen = count($source);
        $suffixLen = count($suffix);

        $maxOverlap = 0;

        for ($len = 1; $len <= min($sourceLen, $suffixLen); $len++) {
            $baseSlice   = array_slice($source, $sourceLen - $len);
            $suffixSlice = array_slice($suffix, 0, $len);

            if ($baseSlice === $suffixSlice) {
                $maxOverlap = $len;
            }
        }

        if ($maxOverlap < $suffixLen) {
            $result = array_merge($result, array_slice($suffix, $maxOverlap));
        }

        return $result;
    }

    public static function wrap(mixed $value): array
    {
        if (is_null($value)) return [];
        if (is_iterable($value)) return iterator_to_array($value);

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

    public static function join(
        array $array,
        string $glue,
        string $finalGlue = '',
        string $prefix = '',
        string $suffix = '',
    ): string
    {
        if (count($array) === 0) return '';

        if ($finalGlue === '') {
            $ret = implode($glue, $array);
        }else {
            if (count($array) === 1) {
                $ret = array_last($array);
            }else {
                $finalItem = array_pop($array);
                $ret = implode($glue, $array).$finalGlue.$finalItem;
            }
        }
        return namespace\Str::wrap($ret, $prefix, $suffix);
    }
}
