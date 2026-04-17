<?php

namespace Aybarsm\Extra\Concerns;
use Aybarsm\Extra\Contracts\Concerns\HasEnumHelpersContract;
use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Extra\Enums\ModeStrCase;

trait HasEnumHelpers
{
    public static function getDtoCases(): array
    {
        return array_combine(($names = self::getNames()), (self::isBacked() ? self::getValues() : $names));
    }
    public static function getCases(): array
    {
        return array_combine(($names = self::getNames()), (self::isBacked() ? self::getValues() : $names));
    }
    public static function getNames(): array
    {
        return array_column(self::cases(), 'name');
    }
    public static function getValues(): array
    {
        throw_if(
            !self::isBacked(),
            \BadMethodCallException::class,
            'Value extraction requires a BackedEnum'
        );

        return array_column(self::cases(), 'value');
    }

    public static function getAllCases(): array
    {
        $cases = self::getCases();
        $flagsAll = self::isFlaggable() ? array_sum(array_values($cases)) : null;
        $constants = new \ReflectionClass(self::class)
            ->getConstants(\ReflectionClassConstant::IS_PUBLIC);

        foreach ($constants as $name => $item) {
            if (isset($cases[$name])) continue;

            if (is_a($item, self::class, true)){
                $cases[$name] = $item::isBacked() ? $item->value : $item->name;
            }elseif ($flagsAll && is_int($item) && $item > 0 && flags_has($flagsAll, $item)) {
                $cases[$name] = $item;
            }
        }

        return $cases;
    }

    public static function getAllNames(): array
    {
        return array_keys(self::getAllCases());
    }

    public static function getAllValues(): array
    {
        throw_if(
            !self::isBacked(),
            \BadMethodCallException::class,
            'Value extraction requires a BackedEnum'
        );

        return array_unique(array_values(self::getAllCases()));
    }
    public static function isBacked(): bool
    {
        return is_subclass_of(self::class, \BackedEnum::class);
    }

    public static function isBackedString(): bool
    {
        return is_subclass_of(self::class, \StringBackedEnum::class);
    }

    public static function isBackedInt(): bool
    {
        return is_subclass_of(self::class, \IntBackedEnum::class);
    }

    public static function isFlaggable(): bool
    {
        if (!self::isBacked()) return false;

        return ModeMatch::ALL->matchesBy(
            self::getValues(),
            static fn (mixed $val) => is_int($val) && $val > 0 && ($val & ($val - 1)) === 0
        );
    }

    public static function toArray(): array
    {
        return self::getAllCases();
    }

    public static function find(mixed $search, bool $strict = true): ?self
    {
        return array_find(
            self::getAllCases(),
            static function ($value, $name) use ($search, $strict) {
                $isMatch = value_compare($name, $search, $strict) || value_compare($value, $search, $strict);
                if (!$isMatch) return false;
                try{
                    return self::{$name};
                }catch (\Throwable $e){
                    try {
                        return self::{$value};
                    }catch (\Throwable $e){
                        return false;
                    }
                }
            },
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
             !self::isBacked(),
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

    public static function makeAll(
        bool $strict = true,
        bool $throws = true,
        bool $unique = false,
        ...$values,
    ): array
    {
        if (count($values) === 0) return [];

        $ret = array_map(static fn ($value) => self::make($value, $strict, $throws), $values);
        return $unique ? array_unique_by($ret, static fn (\UnitEnum $item) => $item->name) : $ret;
    }
}
