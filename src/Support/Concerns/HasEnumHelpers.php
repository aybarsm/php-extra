<?php

namespace Aybarsm\Extra\Support\Concerns;

trait HasEnumHelpers
{
    public static function getAllNames(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function getAllValues(): array
    {
        return array_column(static::cases(), 'value');
    }

    public static function toArray(): array
    {
        return array_combine(static::getAllValues(), static::getAllNames());
    }

    public static function toAssocArray(): array
    {
        return array_combine(static::getAllNames(), static::getAllValues());
    }

    // public static function byName(mixed $name): int|string|null
    // {
    //     return collect(static::cases())->firstWhere('name', $name)?->value;
    // }

    // public static function byValue(mixed $value): int|string|null
    // {
    //     return collect(static::cases())->firstWhere('value', $value)?->name;
    // }

    // public static function getFirst(mixed $search): ?static
    // {
    //     return collect(static::cases())->filter(fn ($item) => $item->value === $search || $item->name === $search)->first();
    // }

    // public static function getFirstName(mixed $search): int|string|null
    // {
    //     return static::getFirst($search)->name;
    // }

    // public static function getFirstValue(mixed $search): int|string|null
    // {
    //     return static::getFirst($search)->value;
    // }
}
