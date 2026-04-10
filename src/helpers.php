<?php

declare(strict_types=1);

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

if (!function_exists('array_unique_by')){
    function array_unique_by(array $array, \Closure $callback, int $mode = 0, bool $strict = true): array
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
}

