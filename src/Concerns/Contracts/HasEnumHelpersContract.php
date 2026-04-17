<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Contracts\Concerns;
use Aybarsm\Extra\Dto\Contracts\EnumMetaContract;

interface HasEnumHelpersContract
{
    public static function getMeta(): EnumMetaContract;
    public static function getNames(): array;
    public static function getValues(): array;
    public static function isBacked(): bool;
    public static function isBackedString(): bool;
    public static function isBackedInt(): bool;
    public static function isFlaggable(): bool;
    public static function getFlagsAll(): int;
    public static function flagsHas(
        int|\BackedEnum ...$flags
    ): bool;
    public static function firstName(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?static;
    public static function firstValue(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?static;

    public static function first(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?static;
    public static function allFlags(
        int|\BackedEnum ...$flags
    ): array;

    public static function make(
        mixed $value,
        bool $strict = false,
        bool $includeAliases = true,
        bool $throws = true,
    ): ?static;

    public static function makeAll(
        mixed $values,
        bool $strict = false,
        bool $includeAliases = true,
        bool $throws = true,
    ): array;

    public function toArray(): array;
}
