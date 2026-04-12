<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

use Aybarsm\Extra\Enums\OsFamily;

final class Os
{
    public static function family(): OsFamily
    {
        return OsFamily::make(PHP_OS_FAMILY, false);
    }
}
