<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

use Aybarsm\Extra\Enums\OsFamily;

final class Validate
{
    protected static function resolveBoolValue(mixed $value, bool $trim = false): ?string
    {
        $value = match(true){
            $value === true => 'true',
            $value === false => 'false',
            is_subclass_of($value, \Stringable::class) => strval($value),
            is_numeric($value) => ($value == 1 ? '1' : ($value == '0' ? '0' : null)),
            default => $value,
        };

        if (is_string($value) && $trim) $value = trim($value);

        return is_string($value) ? strtolower($value) : null;
    }

    public static function truthy(mixed $value, bool $trim = false): bool
    {
        return in_array(self::resolveBoolValue($value, $trim), ['yes', 'on', '1', 'true', 'enabled', 'enable'], true);
    }

    public static function falsy(mixed $value, bool $trim = false): bool
    {
        return in_array(self::resolveBoolValue($value, $trim), ['no', 'off', '0', 'false', 'disabled', 'disable'], true);
    }

    public static function enum(mixed $value, bool $allowString = false): bool
    {
        return is_subclass_of($value, \UnitEnum::class, $allowString);
    }

    public static function backedEnum(mixed $value, bool $allowString = false): bool
    {
        return is_subclass_of($value, \BackedEnum::class, $allowString);
    }

    public static function sentinel(mixed $value): bool
    {
        return $value === namespace\Utils::sentinel();
    }

    public static function fsPath(string|\Stringable $value, string|OsFamily|null $osFamily = null): bool
    {
        $value = (string)$value;
        $osFamily = is_null($osFamily) ? namespace\Os::family() : OsFamily::make($osFamily);
        if ($osFamily->isUnix()) return Str::isMatch('/^\/(?!\/)/', $value);
        return Str::isMatch('/^[a-zA-Z]\:(?!\\\\)/', $value) || Str::isMatch('/^\\\\(?!\\\\)/', $value);
    }

    public static function isClosureStatic(\Closure $callback): bool
    {
        try {
            $callback->bindTo(new \stdClass(), \stdClass::class) ?? throw new \RuntimeException();
            return false;
        } catch (\Throwable $e) {
            return true;
        }
    }
}
