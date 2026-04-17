<?php

namespace Aybarsm\Extra\Concerns;
use Aybarsm\Extra\Contracts\Concerns\HasEnumHelpersContract;
use Aybarsm\Extra\Dto\Contracts\EnumMetaContract;
use Aybarsm\Extra\Dto\EnumMeta;
use Aybarsm\Extra\Enums\ModeMatch;

trait HasEnumHelpers
{
    public static function getMeta(): EnumMetaContract
    {
        return new EnumMeta(self::class);
    }
    public static function getNames(): array
    {
        return self::getMeta()->getNames();
    }
    public static function getValues(): array
    {
        return self::getMeta()->getValues();
    }
    public static function isBacked(): bool
    {
        return self::getMeta()->isBacked();
    }
    public static function isBackedString(): bool
    {
        return self::getMeta()->isBackedString();
    }
    public static function isBackedInt(): bool
    {
        return self::getMeta()->isBackedInt();
    }
    public static function isFlaggable(): bool
    {
        return self::getMeta()->isFlaggable();
    }
     public static function findByName(
         mixed $search,
         bool $strict = false,
         bool $includeAliases = true,
     ): ?self
     {
         return self::getMeta()->findCaseByName($search, $strict, $includeAliases);
     }
    public static function findByValue(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?self
    {
        return self::getMeta()->findCaseByValue($search, $strict, $includeAliases);
    }

    public static function find(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?self
    {
        return self::getMeta()->findCase($search, $strict, $includeAliases);
    }

    public static function filterByFlags(
        int|\BackedEnum ...$flags
    ): array
    {
        return self::getMeta()->findCasesByFlags(...$flags);
    }

     public static function make(
         mixed $value,
         bool $strict = false,
         bool $includeAliases = true,
         bool $throws = true,
     ): ?self
     {
        if (is_a($value, self::class)) return $value;

         $ret = self::find($value, $strict, $includeAliases);

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

    public function toArray(): array
    {
        $ret = ['name' => $this->name];

        if (self::isBacked()) {
            $ret['value'] = $this->value;
        }

        return $ret;
    }
}
