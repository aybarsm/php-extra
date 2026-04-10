<?php

declare(strict_types=1);

if (!function_exists('value_compare')){
    function value_compare(mixed $source, mixed $compare, bool $strict): bool
    {
        if ($strict) return $source === $compare;

        return strtolower((string) $source) === strtolower((string) $compare);
    }
}

