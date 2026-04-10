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
        if (!static::isBackedEnum()) {
            throw new \BadMethodCallException('Value extraction requires a BackedEnum');
        }
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

     public static function findByName(mixed $name, bool $strict = true): int|string|null
     {
         return array_find(
             static::cases(),
             static fn (\UnitEnum $item) => value_compare($item->name, $name, $strict)
         );
     }

     public static function findByValue(mixed $value, bool $strict = true): int|string|null
     {
         if (!static::isBackedEnum()) {
             throw new \BadMethodCallException('Find by value requires a BackedEnum');
         }
         return array_find(
             static::cases(),
             static fn (\BackedEnum $item) => value_compare($item->value, $value, $strict)
         );
     }

     public static function make(mixed $value, bool $strict = true, bool $throws = false): ?static
     {
        if (is_a($value, static::class)) return $value;
        $ret = static::findByName($value, $strict) ?? (static::isBackedEnum() ? static::findByValue($value) : null);

        if ($throws && $ret === null) {
            throw new \InvalidArgumentException(
                sprintf('Could not find matching`%s` by `%s`', static::class, (string) $value)
            );
        }

        return $ret;
     }
}
