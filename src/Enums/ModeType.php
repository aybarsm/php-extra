<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Enums;

use Aybarsm\Extra\Concerns\HasEnumHelpers;
use Aybarsm\Extra\Concerns\Contracts\HasEnumHelpersContract;
use Aybarsm\Extra\Exceptions\NotImplementedException;
use Aybarsm\Extra\Support\Validate as AybarsmValidate;

enum ModeType implements HasEnumHelpersContract
{
    use HasEnumHelpers;
    case UNKNOWN;
    case BOOLEAN;
    case INTEGER;
    case FLOAT;
    case DOUBLE;
    case STRING;
    case STRINGABLE;
    case ITERABLE;
    case ARRAY;
    case OBJECT;
    case FS_PATH;
    case FS_PATH_EXISTS;
    case FS_PATH_DIRECTORY;
    case CLASS_EXISTS;
    final const ModeType BOOL = self::BOOLEAN;
    final const ModeType INT = self::INTEGER;
    final const ModeType STR = self::STRING;
    final const ModeType FS_PATH_DIR = self::FS_PATH_DIRECTORY;

    private static function asString(mixed $value): bool
    {
        if (self::isStringable($value)) $value = (string) $value;
        return $value;
    }
    private static function isStringable(mixed $value): bool
    {
        return is_object($value) && is_subclass_of($value, \Stringable::class);
    }

    private static function isFsPath(mixed $value): bool
    {
        $value = self::asString($value);
        return is_string($value) && AybarsmValidate::fsPath($value);
    }
    private static function isFsPathExists(mixed $value): bool
    {
        return self::isFsPath($value) && file_exists((string) $value);
    }
    private static function isFsPathDirectory(mixed $value): bool
    {
        return self::isFsPathExists($value) && is_dir((string) $value);
    }

    private static function isClassExists(mixed $value): bool
    {
        $value = self::asString($value);
        return is_string($value) && class_exists($value);
    }

    public static function is(mixed $value, ModeType|string ...$of): bool
    {
        return namespace\ModeMatch::ANY->matchesBy(
            self::makeAll(false, true, true, ...$of),
            static function (ModeType $type) use ($value) {
                return match($type) {
                    self::BOOLEAN => is_bool($value),
                    self::INTEGER => is_int($value),
                    self::FLOAT => is_float($value),
                    self::DOUBLE => is_double($value),
                    self::STRING => is_string($value),
                    self::STRINGABLE => self::isStringable($value),
                    self::ITERABLE => is_iterable($value),
                    self::ARRAY => is_array($value),
                    self::OBJECT => is_object($value),
                    self::FS_PATH => self::isFsPath($value),
                    self::FS_PATH_EXISTS => self::isFsPathExists($value),
                    self::FS_PATH_DIRECTORY => self::isFsPathDirectory($value),
                    self::CLASS_EXISTS => self::isClassExists($value),
                    default => false,
                };
            }
        );
    }

    public static function convertable(
        mixed $value,
        ModeType|string $to,
        bool $throws = true,
    ): bool
    {
        $to = self::make($to, false);
        $require = null;

        if ($to === self::STRING){
            $require = [
                self::STRING,
                self::STRINGABLE,
                self::BOOLEAN,
                self::INTEGER,
                self::FLOAT,
                self::DOUBLE,
            ];
        }elseif($to === self::CLASS_EXISTS){
            $require = [self::CLASS_EXISTS, self::OBJECT];
        }

        throw_if(
            is_null($require),
            NotImplementedException::class,
            sprintf('Value conversion to `%s` has not implemented yet.', $to->name)
        );

        $isConvertable = self::is($value, ...$require);
        throw_if(
            $throws && $isConvertable,
            \UnexpectedValueException::class,
            sprintf(
                'Could not convert value to `%s`, the value must be one of: `%s`',
                $to->name,
                array_join(array_column($require, 'name'), ', ', ' or ')
            ),
        );
        return $isConvertable;
    }

    public static function convert(
        mixed $value,
        ModeType|string $to,
        mixed $default = null,
        bool $throws = true,
    ): mixed
    {
        $isConvertable = self::convertable($value, $to, $throws);
        $to = self::make($to, false);

        if ($to === self::STRING){
            $using = static fn (mixed $value): string => (string) $value;
        }elseif ($to === self::CLASS_EXISTS){
            $using = static fn (mixed $value): string => is_object($value) ? get_class($value) : (string) $value;
        }

        return $isConvertable ? $using($value) : value($default);
    }
}
