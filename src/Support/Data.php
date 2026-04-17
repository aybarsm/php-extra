<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Data
{
    public static function compare(
        mixed $source,
        mixed $compare,
        bool $strict = true,
        ?\Closure $formatUsing = null,
    ): bool
    {
        if ($strict) return $source === $compare;
        if ($source == $compare) return true;

        if (is_null($formatUsing)) {
            $formatUsing = static fn (mixed $val) => strtoupper(strval($val));
        }

        try {
            return $formatUsing($source) === $formatUsing($compare);
        }catch (\Throwable $e){
            return false;
        }
    }

    public static function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof \Countable) {
            return count($value) === 0;
        }

        if ($value instanceof \Stringable) {
            return trim((string) $value) === '';
        }

        return empty($value);
    }

    public static function filled(mixed $value): bool
    {
        return ! self::blank($value);
    }

    public static function keySegments(string|int|null|iterable|\Stringable ...$parts): array
    {
        return namespace\Arr::flatten(array_map(
            static fn ($part) => namespace\Str::segments((string) $part, '.'),
            namespace\Arr::whereFilled(namespace\Arr::flatten(namespace\Arr::wrap($parts))),
        ));
    }

    public static function key(
        string|int|null|iterable|\Stringable $key,
        string|int|null|iterable|\Stringable $prefix = null,
        string|int|null|iterable|\Stringable $suffix = null,
    ): ?string
    {
        $key = self::keySegments($key);
        $prefix = self::keySegments($prefix);
        $suffix = self::keySegments($suffix);

        if (self::filled($prefix)) $key = namespace\Arr::start($key, ...$prefix);
        if (self::filled($suffix)) $key = namespace\Arr::end($key, ...$suffix);

        if (self::blank($key)) return null;

        return implode('.', $key);
    }
}
