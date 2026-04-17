<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Dto;

use Aybarsm\Extra\Dto\Contracts\EnumMetaContract;
use Aybarsm\Extra\Exceptions\EnumDtoException;
use UnitEnum;
final class EnumMeta implements EnumMetaContract
{
    private static array $_metaData_ = [];

    public readonly string $class;
    public readonly string $file;
    /** @var array<UnitEnum> */
    public readonly array $cases;
    /** @var array<string> */
    public readonly array $names;
    /** @var array<string|int> */
    public readonly array $values;
    public readonly bool $isBacked;
    public readonly bool $isBackedString;
    public readonly bool $isBackedInt;
    public readonly bool $isFlaggable;
    public readonly ?int $flagsAll;

    /** @var array<string, string> */
    public readonly array $aliasMap;

    /** @var array<string, int> */
    public readonly array $flagMap;

    public function __construct(
        string|UnitEnum $enum
    ){
        $resolved = self::resolve($enum);
        $this->class = $resolved->class;
        $this->file = $resolved->file;

        $this->cases = $resolved->cases;
        $this->names = $resolved->names;
        $this->values = $resolved->values;

        $this->isBacked = $resolved->isBacked;
        $this->isBackedString = $resolved->isBackedString;
        $this->isBackedInt = $resolved->isBackedInt;
        $this->isFlaggable = $resolved->isFlaggable;
        $this->flagsAll = $resolved->flagsAll;

        $this->aliasMap = $resolved->aliasMap;
        $this->flagMap = $resolved->flagMap;
    }

    public static function resolve(
        string|UnitEnum $enum
    ): object
    {
        $class = is_string($enum) || $enum instanceof \Stringable ? (string)$enum : get_class($enum);

        throw_if_(
            ! enum_exists($class),
            EnumDtoException::class,
            sprintf('Enum `%s` does not exist.', $class)
        );

        $hasMeta = array_key_exists($class, self::$_metaData_);
        $meta = $hasMeta ? self::$_metaData_[$class] : [];
        if ($hasMeta) return (object) $meta;

        $ref = new \ReflectionEnum($class);
        $meta['class'] = $ref->getName();
        $meta['file'] = $ref->getFileName();
        $meta['cases'] = [];
        $meta['names'] = [];
        $meta['values'] = [];
        $meta['isBacked'] = $ref->isBacked();

        [$isBackedString, $isBackedInt, $isFlaggable, $flagsAll] = array_fill(0, 4, ($meta['isBacked'] ? null : false));

        foreach($ref->getCases() as $refCase){
            $case = $refCase->getValue();
            $meta['cases'][] = $case;
            $meta['names'][] = $case->name;

            if (!$meta['isBacked']) continue;

            $val = $refCase->getBackingValue();
            $meta['values'][] = $val;
            $isBackedString = $isBackedString !== false && is_string($val);
            $isBackedInt = $isBackedInt !== false && is_int($val);
            $isFlaggable = $isBackedInt === true && $val > 0 && ($val & ($val - 1)) === 0;
            $flagsAll = $isFlaggable === true ? ((int) $flagsAll | $val) : $flagsAll;
        }

        $meta = array_merge($meta, [
            'isBackedString' => $isBackedString,
            'isBackedInt' => $isBackedInt,
            'isFlaggable' => $isFlaggable,
            'flagsAll' => $flagsAll,
        ]);

        $meta['aliasMap'] = [];
        $meta['flagMap'] = [];

        $constants = $ref->getConstants(\ReflectionClassConstant::IS_PUBLIC);
        foreach($constants as $name => $item){
            if (! $ref->hasCase($name) && $item instanceof $class) {
                $meta['aliasMap'][$name] = $item->name;
            }elseif($meta['isFlaggable'] && is_int($item) && $item > 0 && flags_has($flagsAll, $item)){
                $meta['flagMap'][$name] = $item;
            }
        }

        self::$_metaData_[$class] = $meta;
        return (object) $meta;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getCases(): array
    {
        return $this->cases;
    }

    public function getNames(): array
    {
        return $this->names;
    }

    public function getValues(): array
    {
        throw_if_(
            ! $this->isBacked(),
            \BadMethodCallException::class,
            sprintf('Enum `%s` is not backed to have values.', $this->class)
        );

        return $this->values;
    }

    public function isBacked(): bool
    {
        return $this->isBacked;
    }

    public function isBackedString(): bool
    {
        return $this->isBackedString;
    }

    public function isBackedInt(): bool
    {
        return $this->isBackedInt;
    }

    public function isFlaggable(): bool
    {
        return $this->isFlaggable;
    }

    public function getFlagsAll(): int
    {
        throw_if_(
            ! $this->isFlaggable(),
            \BadMethodCallException::class,
            sprintf('Enum `%s` is not flaggable to have flags.', $this->class)
        );

        return $this->flagsAll;
    }

    public function getAliasMap(): array
    {
        return $this->aliasMap;
    }

    public function hasAliasMap(): bool
    {
        return count($this->getAliasMap()) > 0;
    }

    public function getFlagMap(): array
    {
        throw_if_(
            ! $this->isFlaggable(),
            \BadMethodCallException::class,
            sprintf('Enum `%s` is not flaggable to have flag mapping.', $this->class)
        );

        return $this->flagMap;
    }

    public function hasFlagMap(): bool
    {
        return $this->isFlaggable() && count($this->getFlagMap()) > 0;
    }
}
