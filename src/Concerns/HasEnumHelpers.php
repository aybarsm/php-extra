<?php

namespace Aybarsm\Extra\Concerns;
use Aybarsm\Extra\Contracts\Concerns\HasEnumHelpersContract;
trait HasEnumHelpers
{
    public static function getAllCases(): array
    {
        $cases = self::cases();

        $aliases = array_filter(
            array: new \ReflectionClass(self::class)->getConstants(\ReflectionClassConstant::IS_PUBLIC),
            callback: static fn ($item, $name) => is_a($item, self::class, true) && !isset($cases[$name]),
            mode: ARRAY_FILTER_USE_BOTH
        );

        return array_merge($cases, $aliases);
    }
    public static function isBackedEnum(): bool
    {
        return is_subclass_of(self::class, \BackedEnum::class);
    }
    public static function getAllNames(): array
    {
        return array_keys(self::getAllCases());
    }

    public static function getAllValues(): array
    {
        throw_if(
            !self::isBackedEnum(),
            \BadMethodCallException::class,
            'Value extraction requires a BackedEnum'
        );
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        return self::getAllCases();
    }

    public static function find(mixed $search, bool $strict = true): ?self
    {
        return array_find(
            self::getAllCases(),
            static fn (HasEnumHelpersContract $item, $name) => value_compare($name, $search, $strict) || ($item::isBackedEnum() && value_compare($item->value, $search, $strict)),
        );
    }
     public static function findByName(mixed $search, bool $strict = true): ?self
     {
         return array_find(
             self::getAllCases(),
             static fn (HasEnumHelpersContract $item, $name) => value_compare($search, $name, $strict)
         );
     }
     public static function findByValue(mixed $value, bool $strict = true): ?self
     {
         throw_if(
             !self::isBackedEnum(),
             \BadMethodCallException::class,
             'Find by value requires a BackedEnum'
         );

         return array_find(
             self::getAllCases(),
             static fn (\BackedEnum $item) => value_compare($item->value, $value, $strict)
         );
     }

     public static function make(mixed $value, bool $strict = true, bool $throws = true): ?self
     {
        if (is_a($value, self::class)) return $value;

         $ret = self::find($value, $strict);

        throw_if(
            $throws && $ret === null,
            \InvalidArgumentException::class,
            sprintf('Could not make `%s` with value `%s`', self::class, $value)
        );

        return $ret;
     }

    public static function makeAll(bool $strict = true, bool $throws = true, bool $unique = false, ...$values): array
    {
        if (count($values) === 0) return [];

        $ret = array_map(static fn ($value) => self::make($value, $strict, $throws), $values);
        return $unique ? array_unique_by($ret, static fn (\UnitEnum $item) => $item->name) : $ret;
    }
}
