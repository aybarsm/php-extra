<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Contracts\Concerns;
interface HasEnumHelpersContract
{
    public static function isBackedEnum(): bool;
    public static function getAllNames(): array;
    public static function getAllValues(): array;
    public static function toArray(): array;
    public static function getAllCases(): array;
    public static function find(mixed $search, bool $strict = true): ?self;
    public static function findByName(mixed $search, bool $strict = true): ?self;
    public static function findByValue(mixed $search, bool $strict = true): ?self;
    public static function make(mixed $value, bool $strict = true, bool $throws = true): ?self;
    public static function makeAll(bool $strict = true, bool $throws = true, bool $unique = false, ...$values): array;
}
