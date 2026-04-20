<?php

declare(strict_types=1);

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Extra\Support;

if (! function_exists('sentinel')) {
    function sentinel(): string
    {
        return Support\Utils::sentinel();
    }
}

if (! function_exists('is_sentinel')) {
    function is_sentinel(mixed $value): bool
    {
        return Support\Validate::sentinel($value);
    }
}

if (! function_exists('is_blank')) {
    function is_blank(mixed $value): bool
    {
        return Support\Data::blank($value);
    }
}

if (! function_exists('is_filled')) {
    function is_filled(mixed $value): bool
    {
        return Support\Data::filled($value);
    }
}

if (! function_exists('to_value')) {
    function to_value($value, ...$args)
    {
        if (is_callable($value)) {
            return call_user_func_array($value, $args);
        }
        return $value;
    }
}

if (! function_exists('with_')) {
    function with_($value, \Closure $callback)
    {
        return $callback(value($value));
    }
}

if (! function_exists('tap_')) {
    function tap_($value, \Closure $callback)
    {
        $value = value($value);

        $callback($value);

        return $value;
    }
}

if (! function_exists('throw_if_')) {
    function throw_if_(
        mixed $condition,
        ...$parameters
    ): bool
    {
        $condition = value($condition);
        if (!$condition || blank($parameters)) return false;

        $exception = null;

        if (is_object($parameters[0]) && is_subclass_of($parameters[0], \Throwable::class)) {
            $exception = array_shift($parameters);
        }elseif (is_string($parameters[0]) && class_exists($parameters[0]) && is_subclass_of($parameters[0], \Throwable::class)) {
            $exception = array_shift($parameters);
            $exception = new $exception(...$parameters);
        }elseif (is_callable($parameters[0])) {
            $exception = array_shift($parameters);
            $exception = value($exception, ...$parameters);
        }

        if ($exception instanceof \Throwable) throw $exception;

        throw new \RuntimeException(...$parameters);
    }
}

if (!function_exists('value_compare')){
    function value_compare(mixed $source, mixed $compare, bool $strict = true): bool
    {
        return Support\Data::compare($source, $compare, $strict);
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

if (! function_exists('array_join')) {
    function array_join(
        array $array,
        string $glue,
        string $finalGlue = '',
        string $prefix = '',
        string $suffix = '',
    ): string
    {
        return Support\Arr::join($array, $glue, $finalGlue, $prefix, $suffix);
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

if (! function_exists('data_key')) {
    function data_key(
        string|int|null|iterable|\Stringable $key,
        string|int|null|iterable|\Stringable $prefix = null,
        string|int|null|iterable|\Stringable $suffix = null,
    ): ?string
    {
        return Support\Data::key($key, $prefix, $suffix);
    }
}

if (! function_exists('fs_path')) {
    function fs_path(
        string|array|null|\Stringable ...$paths
    ): string
    {
        return Support\Filesystem::path(...$paths);
    }
}

if (! function_exists('is_truthy')) {
    function is_truthy(mixed $value, bool $trim = false): bool
    {
        return Support\Validate::truthy($value, $trim);
    }
}

if (! function_exists('is_falsy')) {
    function is_falsy(mixed $value, bool $trim = false): bool
    {
        return Support\Validate::falsy($value, $trim);
    }
}

if (! function_exists('is_enum')) {
    function is_enum(mixed $value, bool $allowString = false): bool
    {
        return Support\Validate::enum($value, $allowString);
    }
}

if (! function_exists('is_backed_enum')) {
    function is_backed_enum(mixed $value, bool $allowString = false): bool
    {
        return Support\Validate::backedEnum($value, $allowString);
    }
}

if (! function_exists('call_until')) {
    function call_until(\Closure $check, mixed $default, \Closure ...$calls): mixed
    {
        return Support\Utils::callUntil($check, $default, ...$calls);
    }
}
