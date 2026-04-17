<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Enums;

use Aybarsm\Extra\Concerns\HasEnumHelpers;
use Aybarsm\Extra\Concerns\Contracts\HasEnumHelpersContract;

enum OsFamily implements HasEnumHelpersContract
{
    use HasEnumHelpers;
    case WINDOWS;
    case BSD;
    case DARWIN;
    case SOLARIS;
    case LINUX;
    case UNKNOWN;

    public function isWindows(): bool
    {
        return $this === self::WINDOWS;
    }

    public function isBsd(): bool
    {
        return $this === self::BSD;
    }

    public function isDarwin(): bool
    {
        return $this === self::DARWIN;
    }

    public function isSolaris(): bool
    {
        return $this === self::SOLARIS;
    }

    public function isLinux(): bool
    {
        return $this === self::LINUX;
    }

    public function isUnknown(): bool
    {
        return $this === self::UNKNOWN;
    }

    public function isUnix(): bool
    {
        return $this->isBsd() || $this->isDarwin() || $this->isSolaris() || $this->isLinux();
    }
}
