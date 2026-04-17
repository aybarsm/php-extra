<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Enums;

use Aybarsm\Extra\Concerns\HasEnumHelpers;
use Aybarsm\Extra\Contracts\Concerns\HasEnumHelpersContract;

enum ModeStrCase: int implements HasEnumHelpersContract
{
    use HasEnumHelpers;
    case LOWER = 1 << 0;
    case UPPER = 1 << 1;
    case NUMBER = 1 << 2;
    case SYMBOL = 1 << 3;

    final const ModeStrCase LOWERCASE = self::LOWER;
    final const ModeStrCase UPPERCASE = self::UPPER;
    final const ModeStrCase NUMBERS = self::NUMBER;
    final const ModeStrCase SYMBOLS = self::SYMBOL;

    final const int ALL = self::LOWER->value | self::UPPER->value | self::NUMBER->value | self::SYMBOL->value;
    final const int LOWER_UPPER = self::LOWER->value | self::UPPER->value;
    final const int UPPER_LOWER = self::LOWER_UPPER;
    final const int LOWER_NUMBER = self::LOWER->value | self::NUMBER->value;
    final const int NUMBER_LOWER = self::LOWER_NUMBER;
    final const int LOWER_SYMBOL = self::LOWER->value | self::SYMBOL->value;
    final const int SYMBOL_LOWER = self::LOWER_SYMBOL;
    final const int UPPER_NUMBER = self::UPPER->value | self::NUMBER->value;
    final const int NUMBER_UPPER = self::UPPER_NUMBER;
    final const int UPPER_SYMBOL = self::UPPER->value | self::SYMBOL->value;
    final const int SYMBOL_UPPER = self::UPPER_SYMBOL;
    final const int NUMBER_SYMBOL = self::NUMBER->value | self::SYMBOL->value;
    final const int SYMBOL_NUMBER = self::NUMBER_SYMBOL;
    final const int LOWER_UPPER_NUMBER  = self::LOWER_UPPER | self::NUMBER->value;
    final const int UPPER_LOWER_NUMBER = self::LOWER_UPPER_NUMBER;
    final const int NUMBER_UPPER_LOWER = self::LOWER_UPPER_NUMBER;
    final const int NUMBER_LOWER_UPPER = self::LOWER_UPPER_NUMBER;
    final const int LOWER_UPPER_SYMBOL = self::LOWER_UPPER | self::SYMBOL->value;
    final const int UPPER_LOWER_SYMBOL = self::LOWER_UPPER_SYMBOL;
    final const int SYMBOL_UPPER_LOWER = self::LOWER_UPPER_SYMBOL;
    final const int SYMBOL_LOWER_UPPER = self::LOWER_UPPER_SYMBOL;
    final const int LOWER_NUMBER_SYMBOL = self::LOWER_NUMBER | self::SYMBOL->value;
    final const int NUMBER_LOWER_SYMBOL = self::LOWER_NUMBER_SYMBOL;
    final const int SYMBOL_NUMBER_LOWER = self::LOWER_NUMBER_SYMBOL;
    final const int SYMBOL_LOWER_NUMBER = self::LOWER_NUMBER_SYMBOL;
    final const int UPPER_NUMBER_SYMBOL = self::UPPER_NUMBER | self::SYMBOL->value;
    final const int NUMBER_UPPER_SYMBOL = self::UPPER_NUMBER_SYMBOL;
    final const int SYMBOL_NUMBER_UPPER = self::UPPER_NUMBER_SYMBOL;
    final const int SYMBOL_UPPER_NUMBER = self::UPPER_NUMBER_SYMBOL;

    public static function hasLower(int $flags): bool
    {
        return flags_has($flags, self::LOWER->value);
    }

    public static function hasUpper(int $flags): bool
    {
        return flags_has($flags, self::UPPER->value);
    }

    public static function hasNumber(int $flags): bool
    {
        return flags_has($flags, self::NUMBER->value);
    }

    public static function hasSymbol(int $flags): bool
    {
        return flags_has($flags, self::SYMBOL->value);
    }
}
