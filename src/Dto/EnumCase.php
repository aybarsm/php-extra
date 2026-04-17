<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Dto;

use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Extra\Exceptions\EnumDtoException;
use function Pest\Laravel\instance;

final class EnumCase
{
    private static array $_metaData_ = [];

    public readonly string $class;
    public readonly string $name;
    public readonly mixed $value;
    public readonly bool $isBacked;
    public readonly bool $isBackedString;
    public readonly bool $isBackedInt;
    public readonly bool $isFlaggable;

    public readonly array $aliases;
    public function __construct(
        \UnitEnum $enum,
    ){
//        $this->class = get_class($enum);
//        $this->name = $enum->name;
//        $hasMeta = array_key_exists($this->class, self::$_metaData_);
//        $meta = $hasMeta ? self::$_metaData_[$this->class] : null;
//
//        if (!$hasMeta) {
//            $ref = new \ReflectionEnum($this->class);
//            $meta['isBacked'] = $ref->isBacked();
//            $cases = $ref->getCases();
//            $names = array_column($cases, 'value');
//            $values = $meta['isBacked'] ? array_column($cases, 'value') : [];
//            $meta['isBackedString'] = $meta['isBacked'] && $ref->isSubclassOf(\StringBackedEnum::class);
//            $meta['isBackedInt'] = $meta['isBacked'] && $ref->isSubclassOf( \IntBackedEnum::class);
//
//            $meta['isFlaggable'] = $meta['isBacked'] && ModeMatch::ALL->matchesBy(
//                $values,
//                static fn (mixed $val) => is_int($val) && $val > 0 && ($val & ($val - 1)) === 0
//            );
//
//            $meta['aliasMap'] = [];
//
//            foreach($ref->getConstants(\ReflectionClassConstant::IS_PUBLIC) as $name => $item){
//                if ($ref->hasCase($name) || ! $item instanceof $this->class) continue;
//            }
//
//            array_filter(
//                $ref->getConstants(\ReflectionClassConstant::IS_PUBLIC),
//                static fn ($val, string $name) => !in_array($name, $names, true) && ,
//                ARRAY_FILTER_USE_BOTH
//            );
//        }
//
//        $this->isBacked = $meta['isBacked'];
//        $this->isBackedString = $meta['isBackedString'];
//        $this->isBackedInt = $meta['isBackedInt'];
//        $this->isFlaggable = $meta['isFlaggable'];
//
//        if (!$hasMeta) self::$_metaData_[$this->class] = $meta;
    }

//    public static function collect(\UnitEnum $enum): array
//    {
//
//    }
}
