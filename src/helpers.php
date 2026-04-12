<?php

declare(strict_types=1);

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Extra\Support;

if (! function_exists('blank')) {
    function blank(mixed $value): bool
    {
        return Support\Data::blank($value);
    }
}

if (! function_exists('filled')) {
    function filled(mixed $value): bool
    {
        return Support\Data::filled($value);
    }
}

if (! function_exists('value')) {
    function value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (! function_exists('with')) {
    function tap($value, \Closure $callback)
    {
        return $callback(value($value));
    }
}

if (! function_exists('tap')) {
    function tap($value, \Closure $callback)
    {
        $value = value($value);

        $callback($value);

        return $value;
    }
}

if (! function_exists('throw_if')) {
    function throw_if($condition, $exception = 'RuntimeException', ...$parameters)
    {
        if ($condition) {
            if ($exception instanceof Closure) {
                $exception = $exception(...$parameters);
            }

            if (is_string($exception) && class_exists($exception)) {
                $exception = new $exception(...$parameters);
            }

            throw is_string($exception) ? new RuntimeException($exception) : $exception;
        }

        return $condition;
    }
}

if (!function_exists('value_compare')){
    function value_compare(mixed $source, mixed $compare, bool $strict = true): bool
    {
        if ($strict) return $source === $compare;

        return strtolower((string) $source) === strtolower((string) $compare);
    }
}

if (!function_exists('array_wrap')){
    function array_wrap(mixed $value): array
    {
        return Support\Arr::wrap($value);
    }
}

if (!function_exists('array_unique_by')){
    function array_unique_by(array $array, \Closure $callback, int $mode = 0, bool $strict = true): array
    {
        return Support\Arr::uniqueBy($array, $callback, $mode, $strict);
    }
}

if (! function_exists('array_map_with_keys')) {
    function array_map_with_keys(array $array, callable $callback): array
    {
        return Support\Arr::mapWithKeys($array, $callback);
    }
}

if (! function_exists('array_flatten')) {
    function array_flatten(array $array, int|float $depth = INF): array
    {
        return Support\Arr::flatten($array, $depth);
    }
}

if (! function_exists('is_one_of')) {
    function is_one_of(string|object $objectOrClass, mixed $of, bool $allow_string = false): bool
    {
        $match = ModeMatch::ANY;
        return $match->matchesBy(
            of: $of,
            callback: static fn ($item) => is_a($objectOrClass, $item, $allow_string)
        );
    }
}

if (! function_exists('flags_has')) {
    function flags_has(int $flags, int|array $of, string|ModeMatch $match = ModeMatch::ANY): bool
    {
        $match = ModeMatch::make($match);
        return $match->matchesBy(
            of: $of,
            callback: static fn ($item) => ($flags & (int) $item) !== 0
        );
    }
}

if (! function_exists('str_segments')) {
    function str_segments(
        mixed $str,
        string $separator,
        int $limit = -1,
        int $flags = PREG_SPLIT_NO_EMPTY,
    ): array
    {
        return Support\Str::segments($str, $separator, $limit, $flags);
    }
}

if (! function_exists('data_key_segments')) {
    function data_key_segments(string|int|null $key): array
    {
        return str_segments($key, '.');
    }
}

if (! function_exists('data_key')) {
    function data_key(
        string|int $key,
        null|string|int $prefix = null,
        null|string|int $suffix = null,
    ): ?string
    {
        $key = trim((string) $key);
        $parts = preg_split('#\.#', $key, -1, PREG_SPLIT_NO_EMPTY);
        if (count($parts) === 0) return null;

        $prefix = ! is_null($prefix) ? trim((string) $prefix) : null;
        $prefix = $prefix && $prefix !== '' ? preg_split('#\.#', $prefix, -1, PREG_SPLIT_NO_EMPTY) : [];
        if (count($prefix) > 0) array_unshift($parts, ...$prefix);

        $suffix = ! is_null($suffix) ? trim((string) $suffix) : null;
        $suffix = $suffix && $suffix !== '' ? preg_split('#\.#', $suffix, -1, PREG_SPLIT_NO_EMPTY) : [];
        if (count($suffix) > 0) array_push($parts, ...$suffix);

        return implode('.', $parts);
    }
}
//
//if (! function_exists('data_key_new')) {
//    function data_key_new(
//        string|int $key,
//        null|string|int $prefix = null,
//        null|string|int $suffix = null,
//    ): array
//    {
//        $key = trim((string) $key);
//        $parts = preg_split('#\.#', $key, -1, PREG_SPLIT_NO_EMPTY);
////        if (count($parts) === 0) return [];
//
//        $prefix = ! is_null($prefix) ? trim((string) $prefix) : null;
//        $prefix = $prefix && $prefix !== '' ? preg_split('#\.#', $prefix, -1, PREG_SPLIT_NO_EMPTY) : [];
////        if (count($prefix) > 0) array_unshift($parts, ...$prefix);
//
//        $suffix = ! is_null($suffix) ? trim((string) $suffix) : null;
//        $suffix = $suffix && $suffix !== '' ? preg_split('#\.#', $suffix, -1, PREG_SPLIT_NO_EMPTY) : [];
////        if (count($suffix) > 0) array_push($parts, ...$suffix);
//
//        return [
//            'parts' => $parts,
//            'prefix' => $prefix,
//            'suffix' => $suffix,
//        ];
//    }
//}
//
//if (! function_exists('fs_path_segments')) {
//    function data_key_segments(string|null $key): array
//    {
//        return str_segments($key, DIRECTORY_SEPARATOR);
//    }
//}

