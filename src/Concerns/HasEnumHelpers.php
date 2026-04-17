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
    public static function getFlagsAll(): int
    {
        return self::getMeta()->getFlagsAll();
    }

    public static function flagsHas(
        int|\BackedEnum ...$flags
    ): bool
    {
        return count(self::allFlags(...$flags)) > 0;
    }

     public static function firstName(
         mixed $search,
         bool $strict = false,
         bool $includeAliases = true,
     ): ?self
     {
         return self::getMeta()->findCaseByName($search, $strict, $includeAliases);
     }
    public static function firstValue(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?self
    {
        return self::getMeta()->findCaseByValue($search, $strict, $includeAliases);
    }

    public static function first(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?self
    {
        return self::getMeta()->findCase($search, $strict, $includeAliases);
    }

    public static function allFlags(
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

         $ret = self::first($value, $strict, $includeAliases);

        throw_if(
            $throws && $ret === null,
            \InvalidArgumentException::class,
            sprintf('Could not make `%s` with value `%s`', self::class, $value)
        );

        return $ret;
     }
    public static function makeAll(
        mixed $values,
        bool $strict = false,
        bool $includeAliases = true,
        bool $throws = true,
    ): array
    {
        $values = array_wrap($values);

        $ret = array_map(
            static fn ($value) => self::make(
                value: $value,
                strict: $strict,
                includeAliases: $includeAliases,
                throws: $throws
            ),
            $values
        );

        if (self::isFlaggable()) {
            $flagSearch = array_filter(
                array_merge($ret, $values),
                static fn ($item): bool => is_int($item) || is_enum($item)
            );
            try{
                $ret = array_merge($ret, self::allFlags(...$flagSearch));
            }catch (\Throwable $e){
                if ($throws){
                    throw $e;
                }
            }
        }

        return array_unique_by($ret, static fn (\UnitEnum $item) => $item->name);
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
