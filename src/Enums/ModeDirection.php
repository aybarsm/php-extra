<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Enums;

use Aybarsm\Extra\Concerns\HasEnumHelpers;
use Aybarsm\Extra\Concerns\Contracts\HasEnumHelpersContract;

enum ModeDirection implements HasEnumHelpersContract
{
    use HasEnumHelpers;

    case BOTH;
    case LEFT;
    case RIGHT;

    public function isBoth(): bool
    {
        return $this === self::BOTH;
    }

    public function isLeft(): bool
    {
        return $this === self::LEFT;
    }

    public function isRight(): bool
    {
        return $this === self::RIGHT;
    }

    public function isIncluded(string|ModeDirection $dir): bool
    {
        $dir = self::make($dir);
        return $this->isBoth() || $dir->isBoth() || ($this->isLeft() && $dir->isLeft()) || ($this->isRight() && $dir->isRight());
    }
}
