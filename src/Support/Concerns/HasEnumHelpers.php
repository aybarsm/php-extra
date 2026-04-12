<?php

namespace Aybarsm\Extra\Support\Concerns;

trait HasEnumHelpers
{
    public static function isBackedEnum(): bool
    {
        return (!is_a(static::class, \BackedEnum::class));
    }
    public static function getAllNames(): array
    {
        return array_column(static::cases(), 'name');
    }

    public static function getAllValues(): array
    {
        throw_if(
            !static::isBackedEnum(),
            \BadMethodCallException::class,
            'Value extraction requires a BackedEnum'
        );
        return array_column(static::cases(), 'value');
    }

    public static function toArray(): array
    {
        $names = static::getAllNames();
        $values = static::isBackedEnum() ? static::getAllValues() : $names;
        return array_combine($names, $values);
    }

    public static function find(mixed $search, bool $strict = true): ?\UnitEnum
    {
        return array_find(
            static::cases(),
            static fn (\UnitEnum $item) => value_compare($item->name, $search, $strict) || ($item::isisBackedEnum() && value_compare($item->value, $search, $strict)),
        );
    }

     public static function findByName(mixed $name, bool $strict = true): ?static
     {
         return array_find(
             static::cases(),
             static fn (\UnitEnum $item) => value_compare($item->name, $name, $strict)
         );
     }

     public static function findByValue(mixed $value, bool $strict = true): ?static
     {
         throw_if(
             !static::isBackedEnum(),
             \BadMethodCallException::class,
             'Find by value requires a BackedEnum'
         );
         return array_find(
             static::cases(),
             static fn (\BackedEnum $item) => value_compare($item->value, $value, $strict)
         );
     }

     public static function make(mixed $value, bool $strict = true, bool $throws = true): ?static
     {
        if (is_a($value, static::class)) return $value;

        $ret = static::findByName($value, $strict) ?? (static::isBackedEnum() ? static::findByValue($value, $strict) : null);

        throw_if(
            $throws && $ret === null,
            \InvalidArgumentException::class,
            sprintf('Could not make `%s` with value `%s`', static::class, $value)
        );

        return $ret;
     }

    public static function makeAll(bool $strict = true, bool $throws = true, bool $unique = false, ...$values): array
    {
        if (count($values) === 0) return [];

        $ret = array_map(static fn ($value) => static::make($value, $strict, $throws), $values);
        return $unique ? array_unique_by($ret, static fn (\UnitEnum $item) => $item->name) : $ret;
    }
}
